// Edición de la valoración personal.

import { useForm } from "@inertiajs/react";
import Field from "../Field/Field";

export default function CollectionRatingForm({ ownRating, url, className, readOnly = false }) {
  const rating = useForm({
    score: ownRating?.score ?? "",
    note: ownRating?.note ?? "",
  });

  return (
    <details className={className} open={rating.hasErrors || undefined}>
      <summary>Tu valoración</summary>
      <form
        onSubmit={(event) => {
          event.preventDefault();
          if (!readOnly) rating.put(url + "/valoracion", { preserveScroll: true });
        }}
      >
        <Field
          label="Tu puntuación (0 a 100)"
          type="number"
          min="0"
          max="100"
          step="1"
          disabled={readOnly}
          value={rating.data.score}
          onChange={(event) => rating.setData("score", event.target.value)}
          error={rating.errors.score}
        />
        <Field
          label="Tu nota"
          multiline
          disabled={readOnly}
          maxLength={5000}
          value={rating.data.note}
          onChange={(event) => rating.setData("note", event.target.value)}
          error={rating.errors.note}
        />
        {rating.errors.save && <p role="alert">{rating.errors.save}</p>}
        <button disabled={readOnly || rating.processing}>Guardar mi valoración</button>
      </form>
    </details>
  );
}
