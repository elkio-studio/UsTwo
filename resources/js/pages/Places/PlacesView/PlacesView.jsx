// Vista de Sitios de una estantería.

import { useEffect, useLayoutEffect, useMemo, useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import PlaceForm from "../PlaceForm/PlaceForm";
import PlaceLists from "../PlaceLists/PlaceLists";
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
  const [panelOpen, setPanelOpen] = useState(
    () => typeof window === "undefined" || window.matchMedia("(min-width: 52rem)").matches,
  );
  const panelToggle = useRef(null);
  const toggleStart = useRef(null);
  const [selectedId, setSelectedId] = useState(null);
  const [destination, setDestination] = useState(null);
  const [confirmDelete, setConfirmDelete] = useState(false);
  const selected = lists.flatMap((list) => list.places).find((place) => place.id === selectedId);
  const deletion = useForm({});
  const nameField = useRef(null);
  const placeHeading = useRef(null);
  const cancelDeleteButton = useRef(null);
  const reverse = useRef(null);
  const suggestedName = useRef(null);
  const current = useRef(form.data);
  current.current = form.data;

  useEffect(() => () => reverse.current?.abort(), []);
  useLayoutEffect(() => {
    const from = toggleStart.current;
    toggleStart.current = null;
    if (!from || window.matchMedia("(prefers-reduced-motion: reduce)").matches)
      return;

    const button = panelToggle.current;
    const to = button.getBoundingClientRect();
    button.animate(
      [
        { transform: `translate(${from.left - to.left}px, ${from.top - to.top}px)` },
        { transform: "translate(0, 0)" },
      ],
      { duration: 180, easing: "ease-out" },
    );
  }, [panelOpen]);
  useEffect(() => {
    if (!panelOpen) return;
    if (editing !== null) nameField.current?.focus();
    else if (selectedId !== null) placeHeading.current?.focus();
  }, [editing, panelOpen, selectedId]);
  useEffect(() => {
    if (confirmDelete && panelOpen) cancelDeleteButton.current?.focus();
  }, [confirmDelete, panelOpen]);

  function chooseResult(result) {
    setSelectedId(null);
    setConfirmDelete(false);
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
    setPanelOpen(true);
    setSelectedId(null);
    setConfirmDelete(false);
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
    setSelectedId(null);
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

  function selectPlace(place) {
    if (form.processing || deletion.processing) return;
    clearForm();
    deletion.clearErrors();
    setSelectedId(place.id);
    setConfirmDelete(false);
    setPanelOpen(true);
  }

  function removePlace(place) {
    if (form.processing || deletion.processing) return;
    deletion.clearErrors();
    deletion.delete(`${url}/${place.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        setConfirmDelete(false);
        setSelectedId(null);
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
      className={styles.page}
      title="Sitios"
      back={{ href: "/" + spaceSlug, label: "Volver a la estantería" }}
    >
      <button
        type="button"
        aria-expanded={panelOpen}
        aria-controls="places-panel"
        ref={panelToggle}
        className={styles.toggle}
        onClick={(event) => {
          toggleStart.current = event.currentTarget.getBoundingClientRect();
          setPanelOpen(!panelOpen);
        }}
      >
        {panelOpen ? "Plegar panel" : "Abrir panel"}
      </button>
      <div className={styles.layout} data-panel-open={panelOpen}>
        <PlacesMap
          tilesKey={tilesKey}
          lists={lists}
          point={point}
          onPick={choosePoint}
          onSelect={selectPlace}
          destination={destination}
        />
        <aside id="places-panel" className={styles.panel} inert={!panelOpen} aria-label="Gestionar sitios y listas">
          <PlaceSearch url={url + "/buscar"} onSelect={chooseResult} />
          {reverseMessage && <p role="status">{reverseMessage}</p>}
          {deletion.errors.save && <p role="alert">{deletion.errors.save}</p>}
          {selected && (
            <section className={styles.details}>
              <h2 tabIndex={-1} ref={placeHeading}>{selected.name}</h2>
              <p className={styles.coordinates}>
                <span>Latitud {selected.latitude}</span>
                <span aria-hidden="true">|</span>
                <span>Longitud {selected.longitude}</span>
              </p>
              {selected.note && <p className={styles.note}>{selected.note}</p>}
              {confirmDelete ? (
                <>
                  <p role="status">¿Eliminar definitivamente este sitio?</p>
                  <button type="button" disabled={deletion.processing} onClick={() => removePlace(selected)}>Confirmar borrado</button>
                  <button type="button" ref={cancelDeleteButton} disabled={deletion.processing} onClick={() => {
                    setConfirmDelete(false);
                    placeHeading.current?.focus();
                  }}>Cancelar</button>
                </>
              ) : (
                <div className={styles.siteActions}>
                  <button type="button" onClick={() => editPlace(selected)}>Editar sitio</button>
                  <button type="button" onClick={() => setConfirmDelete(true)}>Eliminar sitio</button>
                  <button type="button" onClick={() => setSelectedId(null)}>Añadir sitio</button>
                </div>
              )}
            </section>
          )}
          <div hidden={!!selected}>
            <PlaceForm
              form={form}
              lists={lists}
              onSave={save}
              editing={editing !== null}
              onCancel={clearForm}
              nameRef={nameField}
            />
          </div>
          {lists.length > 0 && (
            <PlaceLists lists={lists} url={url + "/listas"} onSelect={(place) => {
              if (form.processing || deletion.processing) return;
              selectPlace(place);
              setDestination({ latitude: place.latitude, longitude: place.longitude });
            }} />
          )}
        </aside>
      </div>
    </JournalLayout>
  );
}
