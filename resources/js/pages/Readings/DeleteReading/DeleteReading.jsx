import { useEffect, useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import styles from "./DeleteReading.module.css";

export default function DeleteReading({ url, className }) {
  const [confirming, setConfirming] = useState(false);
  const trigger = useRef(null);
  const restoreFocus = useRef(false);
  const deletion = useForm({ confirmed: true });

  useEffect(() => {
    if (!confirming && restoreFocus.current) {
      trigger.current?.focus();
      restoreFocus.current = false;
    }
  }, [confirming]);

  return (
    <section className={className} aria-label="Borrar lectura">
      <button
        ref={trigger}
        type="button"
        disabled={confirming}
        aria-expanded={confirming}
        onClick={() => setConfirming(true)}
      >
        Borrar lectura
      </button>
      {confirming && (
        <form onSubmit={(event) => {
          event.preventDefault();
          deletion.delete(url, { preserveScroll: true });
        }}>
          <p role="alert">Se borrará definitivamente esta lectura.</p>
          <div className={styles.actions}>
            <button
              type="button"
              autoFocus
              disabled={deletion.processing}
              onClick={() => {
                deletion.clearErrors();
                restoreFocus.current = true;
                setConfirming(false);
              }}
            >
              Cancelar
            </button>
            <button className={styles.danger} disabled={deletion.processing}>
              Confirmar borrado
            </button>
          </div>
          {(deletion.errors.delete || deletion.errors.confirmed) && (
            <p role="alert">{deletion.errors.delete || deletion.errors.confirmed}</p>
          )}
        </form>
      )}
    </section>
  );
}
