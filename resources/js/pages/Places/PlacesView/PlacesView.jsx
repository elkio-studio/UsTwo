// Vista de Sitios de una estantería.

import { useEffect, useMemo, useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import PlaceForm from "../PlaceForm/PlaceForm";
import PlaceListForm from "../PlaceListForm/PlaceListForm";
import PlaceSearch from "../PlaceSearch/PlaceSearch";
import PlacesMap from "../PlacesMap/PlacesMap";
import "../../../../css/global.css";
import styles from "./PlacesView.module.css";

export default function PlacesView({ spaceSlug, spaceId, lists, tilesKey }) {
  const url = "/espacios/" + spaceId + "/sitios";
  const form = useForm({
    name: "",
    latitude: "",
    longitude: "",
    mode: "manual",
    note: "",
    list_id: "",
    list_name: "",
    list_emoji: "",
  });
  const [reverseMessage, setReverseMessage] = useState("");
  const reverse = useRef(null);
  const suggestedName = useRef(null);
  const current = useRef(form.data);
  current.current = form.data;

  useEffect(() => () => reverse.current?.abort(), []);

  function chooseResult(result) {
    reverse.current?.abort();
    setReverseMessage("");
    suggestedName.current = result.name;
    form.setData({
      ...current.current,
      latitude: result.latitude,
      longitude: result.longitude,
      mode: "search",
      name: result.name,
    });
  }

  async function choosePoint(latitude, longitude) {
    reverse.current?.abort();
    const controller = new AbortController();
    reverse.current = controller;
    setReverseMessage("");
    const initialName =
      current.current.name === suggestedName.current
        ? ""
        : current.current.name;
    suggestedName.current = null;
    form.setData({
      ...current.current,
      name: initialName,
      latitude,
      longitude,
      mode: "map",
    });
    try {
      const response = await fetch(
        url + "/inversa?" + new URLSearchParams({ latitude, longitude }),
        {
          headers: { Accept: "application/json" },
          signal: controller.signal,
        },
      );
      if (!response.ok) throw new Error();
      const [result] = await response.json();
      if (
        controller.signal.aborted ||
        current.current.mode !== "map" ||
        current.current.latitude !== latitude ||
        current.current.longitude !== longitude
      )
        return;
      if (!result) {
        setReverseMessage("Este punto no tiene un nombre sugerido.");
      } else if (!initialName && !current.current.name) {
        suggestedName.current = result.name;
        form.setData("name", result.name);
      }
    } catch {
      if (
        !controller.signal.aborted &&
        current.current.mode === "map" &&
        current.current.latitude === latitude &&
        current.current.longitude === longitude
      ) {
        setReverseMessage(
          "No se pudo identificar el punto. Puedes escribir el nombre o guardar las coordenadas como nombre.",
        );
      }
    }
  }

  function save(event) {
    event.preventDefault();
    reverse.current?.abort();
    form.clearErrors();
    form.post(url, {
      preserveScroll: true,
      onSuccess: () => {
        suggestedName.current = null;
        form.reset();
        setReverseMessage("");
      },
    });
  }

  const { latitude, longitude, mode } = form.data;
  const point = useMemo(
    () =>
      latitude !== "" &&
      longitude !== "" &&
      Math.abs(Number(latitude)) <= 90 &&
      Math.abs(Number(longitude)) <= 180
        ? { latitude: Number(latitude), longitude: Number(longitude), mode }
        : null,
    [latitude, longitude, mode],
  );

  return (
    <JournalLayout
      theme="cinema"
      title="Sitios"
      back={{ href: "/" + spaceSlug, label: "Volver a la estantería" }}
    >
      <div className={styles.layout}>
        <PlacesMap
          tilesKey={tilesKey}
          lists={lists}
          point={point}
          onPick={choosePoint}
        />
        <div>
          <PlaceSearch url={url + "/buscar"} onSelect={chooseResult} />
          {reverseMessage && <p role="status">{reverseMessage}</p>}
          <PlaceForm form={form} lists={lists} onSave={save} />
        </div>
      </div>
      {lists.length > 0 && (
        <section>
          <h2>Listas de esta estantería</h2>
          <ul>
            {lists.map((list) => (
              <li key={list.id}>
                {list.emoji || "📍"} {list.name}
                <ul>
                  {list.places.map((place) => (
                    <li key={place.id}>
                      {place.name} · Visitado{place.note && <p>{place.note}</p>}
                    </li>
                  ))}
                </ul>
              </li>
            ))}
          </ul>
          <PlaceListForm url={url + "/listas"} />
        </section>
      )}
    </JournalLayout>
  );
}
