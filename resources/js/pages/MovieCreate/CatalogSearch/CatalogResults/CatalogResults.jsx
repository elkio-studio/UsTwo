// Resultados de TMDB y selección de una ficha.

import CollectionPoster from "../../../../components/CollectionPoster/CollectionPoster";
import styles from "./CatalogResults.module.css";

export default function CatalogResults({
  results,
  item,
  selected,
  processing,
  onSelect,
}) {
  return (
    <>
      <p role="status">
        {results.length
          ? `Pulsa una ${item} para añadirla con la fecha de hoy.`
          : "No hay resultados."}
      </p>
      <ul className={styles.choices}>
        {results.map((movie) => (
          <li key={movie.tmdb_id}>
            <button
              type="button"
              disabled={processing}
              aria-pressed={selected?.tmdb_id === movie.tmdb_id}
              onClick={() => onSelect(movie)}
            >
              <CollectionPoster
                path={movie.poster_path}
                title={movie.title}
                size="w185"
              />
              <span>
                {movie.title}
                {movie.release_year ? ` (${movie.release_year})` : ""}
              </span>
            </button>
          </li>
        ))}
      </ul>
    </>
  );
}
