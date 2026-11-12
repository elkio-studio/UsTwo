// Confirmación de coincidencias antes de guardar o vincular.

import styles from "./MovieMatches.module.css";

export default function MovieMatches({
  matches,
  item,
  manual,
  processing,
  onSave,
  onBack,
}) {
  return (
    <fieldset className={styles.candidates}>
      <legend>¿Es la misma {item}?</legend>
      {matches.map((movie) => (
        <button
          key={movie.id}
          type="button"
          disabled={processing}
          onClick={() => onSave({ candidate_id: movie.id })}
        >
          {manual ? "Abrir" : "Vincular con"} {movie.title} ·{" "}
          {movie.release_year ?? "Año desconocido"} · vista el{" "}
          {movie.watched_on}
        </button>
      ))}
      <button
        disabled={processing}
        type="button"
        onClick={() =>
          onSave({
            distinct: true,
            candidate_ids: matches.map((movie) => movie.id),
          })
        }
      >
        Ninguna corresponde, guardar otra
      </button>
      {!manual && (
        <button disabled={processing} type="button" onClick={onBack}>
          Volver a los resultados
        </button>
      )}
    </fieldset>
  );
}
