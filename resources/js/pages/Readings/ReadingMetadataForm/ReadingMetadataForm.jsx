import { useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";

const fields = [
  { name: "title", label: "Título", maxLength: 255 },
  { name: "author", label: "Autoría", maxLength: 255 },
  { name: "publication_date", label: "Fecha de publicación", type: "date" },
  { name: "description", label: "Descripción", multiline: true, maxLength: 20000 },
];

export default function ReadingMetadataForm({ reading, url, className }) {
  const original = Object.fromEntries(
    fields.map(({ name }) => [name, reading[name] ?? ""]),
  );
  const form = useForm(original);
  const catalog = reading.provider !== null;
  const isEditable = (name) =>
    !catalog || (name !== "title" && reading[name] === null);
  const changes = Object.fromEntries(
    Object.entries(form.data).filter(
      ([name, value]) => isEditable(name) && value !== original[name],
    ),
  );

  return (
    <details className={className} open={form.hasErrors || undefined}>
      <summary>Editar datos comunes</summary>
      <form
        onSubmit={(event) => {
          event.preventDefault();
          if (Object.keys(changes).length === 0) return;
          form.transform(() => changes);
          form.patch(url, { preserveScroll: true });
        }}
      >
        {fields.map(({ name, type, ...field }) => (
          <Field
            key={name}
            {...field}
            name={name}
            type={isEditable(name) ? type : undefined}
            required={name === "title" && isEditable(name)}
            disabled={!isEditable(name) || form.processing}
            value={isEditable(name) ? form.data[name] : original[name]}
            onChange={(event) => form.setData(name, event.target.value)}
            error={form.errors[name]}
          />
        ))}
        {form.errors.save && <p role="alert">{form.errors.save}</p>}
        <button disabled={form.processing || Object.keys(changes).length === 0}>
          Guardar datos comunes
        </button>
      </form>
    </details>
  );
}
