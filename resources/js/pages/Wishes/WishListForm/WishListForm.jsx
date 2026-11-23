// Creación explícita de una lista sin navegar ni perder el deseo en preparación.

import { useState } from "react";
import Field from "../../../components/Field/Field";
import wishRequest from "../utils/wishRequest";

export default function WishListForm({ url, onCreated, onCancel }) {
  const [name, setName] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function create(event) {
    event.preventDefault();
    if (loading) return;
    setLoading(true);
    setError("");
    try {
      onCreated(await wishRequest(url, { name }));
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
    <form onSubmit={create}>
      <h2>Crear lista</h2>
      <Field
        label="Nombre de la lista"
        autoFocus
        required
        maxLength={255}
        value={name}
        onChange={(event) => setName(event.target.value)}
        error={error}
      />
      <button disabled={loading}>{loading ? "Creando…" : "Crear lista"}</button>{" "}
      <button type="button" disabled={loading} onClick={onCancel}>
        Cancelar
      </button>
    </form>
  );
}
