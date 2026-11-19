// Edición de los datos compartidos según la procedencia de cada campo.
import { useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";

const fields = [
  { name: "title", label: "Título", required: true, maxLength: 255 },
  {
    name: "release_year",
    label: "Año de estreno (opcional)",
    type: "number",
    min: 1000,
    max: 9999,
  },
  {
    name: "watched_on",
    label: "Fecha de visionado",
    type: "date",
    required: true,
  },
  {
    name: "overview",
    label: "Sinopsis (opcional)",
    multiline: true,
    maxLength: 20000,
  },
];

export default function MovieMetadataForm({
  movie,
  editableMetadata,
  url,
  className,
}) {
  const values = Object.fromEntries(
    fields.map(({ name }) => [name, movie[name] ?? ""]),
  );
  const form = useForm(values);
  const changes = Object.fromEntries(
    Object.entries(form.data).filter(
      ([name, value]) =>
        editableMetadata.includes(name) && String(value) !== String(values[name]),
    ),
  );

  return (
    <details className={className} open={form.hasErrors || undefined}>
      <summary>Editar datos comunes</summary>
      <p>
        Los datos del catálogo están protegidos. Puedes completar los que faltan y
        corregir los añadidos a mano.
      </p>
      <form
        onSubmit={(event) => {
          event.preventDefault();
          if (Object.keys(changes).length === 0) return;
          form.transform(() => ({
            ...changes,
            metadata_version: movie.metadata_version,
          }));
          form.patch(url, { preserveScroll: true });
        }}
      >
        {fields.map(({ name, ...field }) => (
          <Field
            key={name}
            {...field}
            disabled={!editableMetadata.includes(name) || form.processing}
            value={editableMetadata.includes(name) ? form.data[name] : values[name]}
            onChange={(event) => form.setData(name, event.target.value)}
            error={form.errors[name]}
          />
        ))}
        {form.errors.save && <p role="alert">{form.errors.save}</p>}
        {form.errors.metadata_version && (
          <p role="alert">Recarga la ficha antes de guardar.</p>
        )}
        <button disabled={form.processing || Object.keys(changes).length === 0}>
          Guardar datos comunes
        </button>
      </form>
    </details>
  );
}
