// Creación explícita de una lista sin navegar ni perder el deseo en preparación.

import { useEffect, useId, useRef, useState } from "react";
import Field from "../../../components/Field/Field";
import wishRequest from "../../../utils/wishRequest";
import styles from "./WishListForm.module.css";

export default function WishListForm({ url, onCreated, onCancel }) {
  const [name, setName] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const dialog = useRef(null);
  const titleId = useId();

  useEffect(() => {
    const popup = dialog.current;
    popup.showModal();
    return () => popup.close();
  }, []);

  function cancel() {
    if (loading) return;
    dialog.current.close();
    onCancel();
  }

  async function create(event) {
    event.preventDefault();
    if (loading) return;
    setLoading(true);
    setError("");
    try {
      const list = await wishRequest(url, { name });
      dialog.current.close();
      onCreated(list);
    } catch (failure) {
      setError(
        failure.fields?.name?.[0] ||
          failure.fields?.save?.[0] ||
          failure.message,
      );
    } finally {
      setLoading(false);
    }
  }

  return (
    <dialog
      id="wish-list-creation"
      ref={dialog}
      className={styles.dialog}
      aria-labelledby={titleId}
      onCancel={(event) => {
        event.preventDefault();
        cancel();
      }}
    >
      <form onSubmit={create} className={styles.form} aria-busy={loading}>
        <h2 id={titleId}>Crear lista</h2>
        <Field
          label="Nombre de la lista"
          autoFocus
          required
          maxLength={255}
          value={name}
          onChange={(event) => setName(event.target.value)}
          error={error}
        />
        <div className={styles.actions}>
          <button disabled={loading}>
            {loading ? "Creando…" : "Crear lista"}
          </button>
          <button type="button" disabled={loading} onClick={cancel}>
            Cancelar
          </button>
        </div>
      </form>
    </dialog>
  );
}
