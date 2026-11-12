// Formulario de alta manual y confirmación de coincidencias.

import Field from "../../../../components/Field/Field";
import MovieMatches from "../MovieMatches/MovieMatches";
import styles from "./CollectionEntryEditor.module.css";

export default function CollectionEntryEditor({
  form,
  manual,
  selected,
  matches,
  item,
  onSave,
  onBack,
}) {
  function changeIdentity(key, value) {
    form.clearErrors("match", key);
    form.setData(key, value);
  }

  return (
    <form
      className={styles.editor}
      onSubmit={(event) => {
        event.preventDefault();
        onSave();
      }}
    >
      <h2>{manual ? "Alta manual" : selected.title}</h2>
      {manual && (
        <>
          <Field
            label="Título"
            required
            maxLength={255}
            value={form.data.title}
            onChange={(event) => changeIdentity("title", event.target.value)}
            error={form.errors.title}
          />
          <Field
            label="Año de estreno (opcional)"
            type="number"
            min="1000"
            max="9999"
            value={form.data.release_year}
            onChange={(event) =>
              changeIdentity("release_year", event.target.value)
            }
            error={form.errors.release_year}
          />
          <Field
            label="Fecha de visionado"
            type="date"
            required
            value={form.data.watched_on}
            onChange={(event) => form.setData("watched_on", event.target.value)}
            error={form.errors.watched_on}
          />
        </>
      )}
      {(form.errors.catalog || form.errors.save || form.errors.match) && (
        <p role="alert">
          {form.errors.catalog || form.errors.save || form.errors.match}
        </p>
      )}
      {matches.length > 0 ? (
        <MovieMatches
          {...{ matches, item, manual }}
          processing={form.processing}
          onSave={onSave}
          onBack={onBack}
        />
      ) : (
        <button disabled={form.processing}>Guardar {item}</button>
      )}
    </form>
  );
}
