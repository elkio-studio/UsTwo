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
  const [editing, setEditing] = useState(null);
  const deletion = useForm({});
  const nameField = useRef(null);
  const reverse = useRef(null);
  const suggestedName = useRef(null);
  const current = useRef(form.data);
  current.current = form.data;

  useEffect(() => () => reverse.current?.abort(), []);
  useEffect(() => {
    if (editing !== null) nameField.current?.focus();
  }, [editing]);

  function chooseResult(result) {
    reverse.current?.abort();
    setReverseMessage("");
    if (!editing) suggestedName.current = result.name;
    form.setData({
      ...current.current,
      latitude: result.latitude,
      longitude: result.longitude,
      mode: "search",
      name: editing ? current.current.name : result.name,
    });
  }

  async function choosePoint(latitude, longitude) {
    reverse.current?.abort();
    if (editing) {
      form.setData({ ...current.current, latitude, longitude, mode: "map" });
      return;
    }
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

  function clearForm() {
    reverse.current?.abort();
    suggestedName.current = null;
    setReverseMessage("");
    setEditing(null);
    form.reset();
    form.clearErrors();
  }

  function editPlace(place) {
    if (form.processing || deletion.processing) return;
    clearForm();
    setEditing(place.id);
    form.setData({
      ...current.current,
      name: place.name,
      latitude: place.latitude,
      longitude: place.longitude,
      note: place.note ?? "",
      mode: "manual",
    });
  }

  function removePlace(place) {
    if (form.processing || deletion.processing) return;
    if (!window.confirm(`¿Eliminar el sitio «${place.name}»?`)) return;
    deletion.clearErrors();
    deletion.delete(`${url}/${place.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        if (editing === place.id) clearForm();
      },
    });
  }

  function save(event) {
    event.preventDefault();
    if (deletion.processing) return;
    reverse.current?.abort();
    form.clearErrors();
    const options = {
      preserveScroll: true,
      onSuccess: clearForm,
    };
    if (editing) form.patch(`${url}/${editing}`, options);
    else form.post(url, options);
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
          onEdit={editPlace}
          onDelete={removePlace}
        />
        <div>
          <PlaceSearch url={url + "/buscar"} onSelect={chooseResult} />
          {reverseMessage && <p role="status">{reverseMessage}</p>}
          {deletion.errors.save && <p role="alert">{deletion.errors.save}</p>}
          <PlaceForm
            form={form}
            lists={lists}
            onSave={save}
            editing={editing !== null}
            onCancel={clearForm}
            nameRef={nameField}
          />
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
