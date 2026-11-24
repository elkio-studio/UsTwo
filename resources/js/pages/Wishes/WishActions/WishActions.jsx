// Acciones de un deseo y confirmación sin modificar datos de otras listas.

import { useCallback, useRef, useState } from "react";
import { Link, useForm } from "@inertiajs/react";
import WishMenu from "../WishMenu/WishMenu";
import styles from "./WishActions.module.css";

export default function WishActions({ spaceId, wish, onDeleted }) {
  const [open, setOpen] = useState(false);
  const [confirming, setConfirming] = useState(false);
  const trigger = useRef(null);
  const deletion = useForm({ confirmed: true });
  const url = `/espacios/${spaceId}/deseos/${wish.id}`;
  const close = useCallback(() => {
    if (deletion.processing) return;
    setOpen(false);
    setConfirming(false);
    deletion.clearErrors();
    trigger.current?.focus();
  }, [deletion.processing, deletion.clearErrors]);
  return (
    <WishMenu
      label={`Opciones de ${wish.title}`}
      open={open}
      onOpen={() => setOpen(true)}
      onClose={close}
      processing={deletion.processing}
      wide={confirming}
      view={confirming}
      trigger={trigger}
    >
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
              data-initial-focus
              type="button"
              disabled={deletion.processing}
              onClick={close}
            >
              Cancelar
            </button>
            <button className={styles.danger} disabled={deletion.processing}>
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
          <button
            className={styles.danger}
            type="button"
            onClick={() => setConfirming(true)}
          >
            Eliminar
          </button>
        </div>
      )}
    </WishMenu>
  );
}
