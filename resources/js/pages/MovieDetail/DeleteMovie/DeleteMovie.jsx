// Confirmación y borrado de una ficha.

import { useState } from "react";
import { useForm } from "@inertiajs/react";
import styles from "./DeleteMovie.module.css";

export default function DeleteMovie({ item, url, className }) {
  const [confirming, setConfirming] = useState(false);
  const deletion = useForm({ confirmed: true });

  return (
    <section className={className} aria-label={`Borrar ${item}`}>
      {confirming ? (
        <>
          <p role="alert">
            Se borrarán definitivamente la {item}, sus puntuaciones y sus notas.
          </p>
          <div className={styles.actions}>
            <button
              autoFocus
              disabled={deletion.processing}
              onClick={() => setConfirming(false)}
            >
              Cancelar
            </button>
            <button
              className={styles.danger}
              disabled={deletion.processing}
              onClick={() => deletion.delete(url)}
            >
              Confirmar borrado
            </button>
          </div>
          {(deletion.errors.save || deletion.errors.confirmed) && (
            <p role="alert">
              {deletion.errors.save || deletion.errors.confirmed}
            </p>
          )}
        </>
      ) : (
        <button type="button" onClick={() => setConfirming(true)}>
          Borrar {item}
        </button>
      )}
    </section>
  );
}
