// Mapa de Sitios y marcadores de la estantería.

import { useEffect, useRef, useState } from "react";
import {
  divIcon,
  control,
  latLngBounds,
  layerGroup,
  map,
  marker,
  tileLayer,
} from "leaflet";
import "leaflet/dist/leaflet.css";
import styles from "./PlacesMap.module.css";

export default function PlacesMap({ tilesKey, lists, point, onPick, onSelect, destination }) {
  const container = useRef(null);
  const mapRef = useRef(null);
  const saved = useRef(null);
  const pending = useRef(null);
  const fitted = useRef(false);
  const actions = useRef(onSelect);
  actions.current = onSelect;
  const [tileError, setTileError] = useState(false);

  useEffect(() => {
    if (!tilesKey) return;

    const instance = map(container.current, { zoomControl: false }).setView([40.4, -3.7], 5);
    control.zoom({ position: "topright" }).addTo(instance);
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

    const resize = new ResizeObserver(() =>
      instance.invalidateSize({ pan: true, debounceMoveend: true }),
    );
    resize.observe(container.current);

    return () => {
      resize.disconnect();
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
        const pin = marker(position, {
          title: place.name,
          icon: divIcon({
            html: emoji,
            className: styles.marker,
            iconSize: [28, 28],
          }),
        }).addTo(saved.current);
        pin.on("click", () => actions.current(place));
        const element = pin.getElement();
        element.setAttribute("aria-label", place.name);
        element.addEventListener("keydown", (event) => {
          if (event.key === " " || event.key === "Enter") {
            event.preventDefault();
            actions.current(place);
          }
        });
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
      keyboard: false,
      interactive: false,
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

  useEffect(() => {
    const instance = mapRef.current;
    if (instance && destination) {
      instance.setView(
        [destination.latitude, destination.longitude],
        Math.max(instance.getZoom(), 16),
      );
    }
  }, [tilesKey, destination]);

  return (
    <section className={styles.region} aria-label="Mapa de sitios">
      {tilesKey && <div ref={container} className={styles.map} />}
      {(!tilesKey || tileError) && (
        <p className={styles.notice} role="status">
          El mapa base no está disponible. Puedes introducir las coordenadas.
        </p>
      )}
    </section>
  );
}
