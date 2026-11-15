// Formulario de alta y edición de listas de Sitios.

import { useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";

export default function PlaceListForm({ url, list, onCancel, onSaved }) {
  const form = useForm({ name: list?.name ?? "", emoji: list?.emoji ?? "" });

  return (
    <form
      onSubmit={(event) => {
        event.preventDefault();
        if (list) {
          form.patch(url, { preserveScroll: true, onSuccess: onSaved });
        } else {
          form.post(url, { preserveScroll: true, onSuccess: onSaved });
        }
      }}
    >
      <h2>{list ? "Editar lista" : "Crear lista"}</h2>
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
      <button disabled={form.processing}>{list ? "Guardar lista" : "Crear lista"}</button>
      {onCancel && <button type="button" disabled={form.processing} onClick={onCancel}>Cancelar</button>}
    </form>
  );
}
