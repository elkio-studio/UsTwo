// Acciones de un deseo y confirmación contextual de borrado.

import { useEffect, useRef, useState } from "react";
import { Link, useForm } from "@inertiajs/react";
import styles from "./WishActions.module.css";

export default function WishActions({ spaceId, wish, onDeleted }) {
  const [open, setOpen] = useState(false);
  const [confirming, setConfirming] = useState(false);
  const trigger = useRef(null);
  const cancel = useRef(null);
  const deletion = useForm({ confirmed: true });
  const id = `wish-actions-${wish.id}`;
  const url = `/espacios/${spaceId}/deseos/${wish.id}`;

  useEffect(() => {
    if (confirming) cancel.current?.focus();
  }, [confirming]);

  function close() {
    if (deletion.processing) return;
    setOpen(false);
    setConfirming(false);
    deletion.clearErrors();
    trigger.current?.focus();
  }

  return (
    <div
      className={styles.actions}
      onKeyDown={(event) => {
        if (event.key === "Escape" && open) {
          event.preventDefault();
          close();
        }
      }}
    >
      <button
        ref={trigger}
        type="button"
        aria-label={`Opciones de ${wish.title}`}
        aria-expanded={open}
        aria-controls={id}
        disabled={deletion.processing}
        onClick={() => (open ? close() : setOpen(true))}
      >
        ⋯
      </button>
      {open && (
        <div id={id} className={styles.panel}>
          {confirming ? (
            <form
              onSubmit={(event) => {
                event.preventDefault();
                if (deletion.processing) return;
                deletion.delete(url, {
                  preserveScroll: true,
                  onSuccess: onDeleted,
                });
              }}
            >
              <p>Se eliminará definitivamente «{wish.title}».</p>
              <div className={styles.buttons}>
                <button
                  ref={cancel}
                  type="button"
                  disabled={deletion.processing}
                  onClick={close}
                >
                  Cancelar
                </button>
                <button disabled={deletion.processing}>
                  {deletion.processing ? "Eliminando…" : "Confirmar borrado"}
                </button>
              </div>
              {(deletion.errors.delete || deletion.errors.confirmed) && (
                <p role="alert">
                  {deletion.errors.delete || deletion.errors.confirmed}
                </p>
              )}
            </form>
          ) : (
            <div className={styles.buttons}>
              <Link href={`${url}/editar`}>Editar</Link>
              <button type="button" onClick={() => setConfirming(true)}>
                Eliminar
              </button>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
