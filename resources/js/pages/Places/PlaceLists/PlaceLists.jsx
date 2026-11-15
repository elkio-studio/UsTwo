// Consulta y acciones de las listas de la estantería.

import { useEffect, useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import PlaceListForm from "../PlaceListForm/PlaceListForm";
import styles from "./PlaceLists.module.css";

export default function PlaceLists({ lists, url, onSelect }) {
  const [editing, setEditing] = useState(null);
  const [creating, setCreating] = useState(false);
  const [menu, setMenu] = useState(null);
  const [confirmation, setConfirmation] = useState(null);
  const deletion = useForm({});
  const cancelButton = useRef(null);
  const deleteTrigger = useRef(null);
  const editingList = lists.find((list) => list.id === editing);

  useEffect(() => {
    if (confirmation) cancelButton.current?.focus();
  }, [confirmation]);

  function confirmDelete() {
    if (confirmation.count > 0 && confirmation.step === 1) {
      setConfirmation({ ...confirmation, step: 2 });
      return;
    }

    deletion.transform(() => ({ confirmed_count: confirmation.count }));
    deletion.delete(`${url}/${confirmation.id}`, {
      preserveScroll: true,
      onSuccess: () => setConfirmation(null),
      onError: () => setConfirmation(null),
    });
  }

  return (
    <section className={styles.lists}>
      <div className={styles.heading}>
        <h2>Listas de esta estantería</h2>
        <button type="button" aria-label="Añadir lista" aria-expanded={creating} onClick={() => {
          setEditing(null);
          setCreating(!creating);
        }}>+</button>
      </div>
      {creating && <PlaceListForm url={url} onCancel={() => setCreating(false)} onSaved={() => setCreating(false)} />}
      {lists.map((list) => (
        <div className={styles.list} key={list.id}>
        <details>
          <summary>{list.emoji || "📍"} {list.name} ({list.places.length})</summary>
          {list.places.length > 0 && (
            <ul>
              {list.places.map((place) => (
                <li key={place.id}>
                  <button className={styles.place} type="button" onClick={() => onSelect(place)}>{place.name}</button>
                </li>
              ))}
            </ul>
          )}
        </details>
        <button className={styles.more} type="button" aria-label={`Opciones de ${list.name}`}
          aria-expanded={menu === list.id} onClick={() => setMenu(menu === list.id ? null : list.id)}>⋯</button>
        {menu === list.id && (
          <div className={styles.menu}>
            <button type="button" onClick={() => {
              setCreating(false);
              setEditing(list.id);
              setMenu(null);
            }}>Editar lista</button>
            <button type="button" disabled={deletion.processing} onClick={(event) => {
              deleteTrigger.current = event.currentTarget;
              deletion.clearErrors();
              setConfirmation({ id: list.id, name: list.name, count: list.places.length, step: 1 });
            }}>Eliminar lista</button>
          </div>
        )}
        </div>
      ))}
      {confirmation && (
        <section aria-label="Confirmar borrado de lista">
          <p role="status">
            {confirmation.step === 1
              ? `¿Eliminar la lista «${confirmation.name}»?`
              : `También se eliminarán definitivamente sus ${confirmation.count} sitios.`}
          </p>
          <div className={styles.actions}>
            <button type="button" disabled={deletion.processing} onClick={confirmDelete}>
              {confirmation.count > 0 && confirmation.step === 1 ? "Continuar" : "Eliminar definitivamente"}
            </button>
            <button type="button" ref={cancelButton} disabled={deletion.processing} onClick={() => {
              setConfirmation(null);
              deleteTrigger.current?.focus();
            }}>Cancelar</button>
          </div>
        </section>
      )}
      {(deletion.errors.confirmed_count || deletion.errors.save) && (
        <p role="alert">{deletion.errors.confirmed_count || deletion.errors.save}</p>
      )}
      {editingList && (
        <PlaceListForm key={editingList.id} list={editingList} url={`${url}/${editingList.id}`}
          onCancel={() => setEditing(null)} onSaved={() => setEditing(null)} />
      )}
    </section>
  );
}
