// Mapa de Sitios y marcadores de la estantería.

import { useEffect, useRef, useState } from "react";
import {
  divIcon,
  latLngBounds,
  layerGroup,
  map,
  marker,
  tileLayer,
} from "leaflet";
import "leaflet/dist/leaflet.css";
import styles from "./PlacesMap.module.css";

export default function PlacesMap({ tilesKey, lists, point, onPick }) {
  const container = useRef(null);
  const mapRef = useRef(null);
  const saved = useRef(null);
  const pending = useRef(null);
  const fitted = useRef(false);
  const [tileError, setTileError] = useState(false);

  useEffect(() => {
    if (!tilesKey) return;

    const instance = map(container.current).setView([40.4, -3.7], 5);
    const tiles = tileLayer(
      "https://maps.geoapify.com/v1/tile/osm-carto/{z}/{x}/{y}.png?apiKey=" +
        encodeURIComponent(tilesKey),
      {
        maxZoom: 20,
        attribution:
          'Powered by <a href="https://www.geoapify.com/">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      },
    );
    tiles.on("tileerror", () => setTileError(true));
    tiles.addTo(instance);
    mapRef.current = instance;
    saved.current = layerGroup().addTo(instance);
    pending.current = layerGroup().addTo(instance);
    fitted.current = false;

    return () => {
      instance.remove();
      mapRef.current = null;
    };
  }, [tilesKey]);

  useEffect(() => {
    const instance = mapRef.current;
    if (!instance) return;
    const pick = ({ latlng }) => {
      const { lat, lng } = latlng.wrap();
      onPick(lat, lng);
    };
    instance.on("click", pick);
    return () => instance.off("click", pick);
  }, [tilesKey, onPick]);

  useEffect(() => {
    const instance = mapRef.current;
    if (!instance) return;
    saved.current.clearLayers();
    const points = [];
    for (const list of lists) {
      for (const place of list.places) {
        const position = [place.latitude, place.longitude];
        points.push(position);
        const emoji = document.createElement("span");
        emoji.textContent = list.emoji || "📍";
        const popup = document.createElement("div");
        popup.className = styles.popup;
        const name = document.createElement("strong");
        name.textContent = place.name;
        popup.append(name);
        if (place.note?.trim()) {
          const note = document.createElement("div");
          note.className = styles.popupNote;
          note.textContent = place.note;
          popup.append(note);
        }
        const pin = marker(position, {
          title: place.name,
          icon: divIcon({
            html: emoji,
            className: styles.marker,
            iconSize: [28, 28],
          }),
        })
          .bindPopup(popup, { maxHeight: 160 })
          .addTo(saved.current);
        pin.getElement()?.setAttribute("aria-label", place.name);
      }
    }
    if (!fitted.current && points.length) {
      instance.fitBounds(latLngBounds(points), {
        padding: [30, 30],
        maxZoom: 15,
      });
      fitted.current = true;
    }
  }, [tilesKey, lists]);

  useEffect(() => {
    const instance = mapRef.current;
    if (!instance) return;
    pending.current.clearLayers();
    if (!point) return;
    marker([point.latitude, point.longitude], {
      icon: divIcon({
        html: "＋",
        className: styles.marker,
        iconSize: [28, 28],
      }),
      title: "Ubicación seleccionada",
    }).addTo(pending.current);
    if (point.mode !== "map") {
      instance.setView(
        [point.latitude, point.longitude],
        Math.max(instance.getZoom(), 14),
      );
    }
  }, [tilesKey, point]);

  return (
    <section aria-label="Mapa de sitios">
      {tilesKey && <div ref={container} className={styles.map} />}
      {(!tilesKey || tileError) && (
        <p role="status">
          El mapa base no está disponible. Puedes introducir las coordenadas.
        </p>
      )}
    </section>
  );
}
