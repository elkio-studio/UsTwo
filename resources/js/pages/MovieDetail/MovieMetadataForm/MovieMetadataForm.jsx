// Edición de los datos compartidos de la ficha.

import { useState } from "react";
import { useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";

export default function MovieMetadataForm({ movie, url, className }) {
  const common = useForm({
    title: movie.title,
    release_year: movie.release_year ?? "",
    watched_on: movie.watched_on,
  });
  const [savedCommon, setSavedCommon] = useState(common.data);

  return (
    <details className={className} open={common.hasErrors || undefined}>
      <summary>Editar datos comunes</summary>
      <form
        onSubmit={(event) => {
          event.preventDefault();
          common.transform((data) =>
            Object.fromEntries(
              Object.entries(data).filter(
                ([key, value]) => String(value) !== String(savedCommon[key]),
              ),
            ),
          );
          common.patch(url, {
            preserveScroll: true,
            onSuccess: () => setSavedCommon(common.data),
          });
        }}
      >
        <Field
          label="Título"
          required
          maxLength={255}
          value={common.data.title}
          onChange={(event) => common.setData("title", event.target.value)}
          error={common.errors.title}
        />
        <Field
          label="Año de estreno (opcional)"
          type="number"
          min="1000"
          max="9999"
          value={common.data.release_year}
          onChange={(event) =>
            common.setData("release_year", event.target.value)
          }
          error={common.errors.release_year}
        />
        <Field
          label="Fecha de visionado"
          type="date"
          required
          value={common.data.watched_on}
          onChange={(event) => common.setData("watched_on", event.target.value)}
          error={common.errors.watched_on}
        />
        {common.errors.save && <p role="alert">{common.errors.save}</p>}
        <button disabled={common.processing}>Guardar datos comunes</button>
      </form>
    </details>
  );
}
