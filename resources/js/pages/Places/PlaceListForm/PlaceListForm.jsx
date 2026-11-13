// Formulario para crear listas adicionales de Sitios.

import { useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";

export default function PlaceListForm({ url }) {
  const form = useForm({ name: "", emoji: "" });

  return (
    <form
      onSubmit={(event) => {
        event.preventDefault();
        form.post(url, { preserveScroll: true, onSuccess: () => form.reset() });
      }}
    >
      <h2>Crear lista</h2>
      <Field
        label="Nombre de la lista"
        required
        maxLength={255}
        value={form.data.name}
        onChange={(event) => form.setData("name", event.target.value)}
        error={form.errors.name}
      />
      <Field
        label="Emoji de la lista (opcional)"
        maxLength={32}
        value={form.data.emoji}
        onChange={(event) => form.setData("emoji", event.target.value)}
        error={form.errors.emoji}
      />
      {form.errors.save && <p role="alert">{form.errors.save}</p>}
      <button disabled={form.processing}>Crear lista</button>
    </form>
  );
}
