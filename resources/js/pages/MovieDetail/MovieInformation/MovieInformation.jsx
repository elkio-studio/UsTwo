// Portada, datos y sinopsis de la ficha.

import CollectionPoster from "../../../components/CollectionPoster/CollectionPoster";
import styles from "./MovieInformation.module.css";

export default function MovieInformation({ movie, series }) {
  return (
    <div className={styles.feature}>
      <CollectionPoster
        path={movie.poster_path}
        title={movie.title}
        size="w500"
        eager
      />
      <dl>
        <dt>Fecha de visionado</dt>
        <dd>
          <time dateTime={movie.watched_on}>
            {movie.watched_on.split("-").reverse().join("/")}
          </time>
        </dd>
        {movie.release_year && (
          <>
            <dt>Año de estreno</dt>
            <dd>{movie.release_year}</dd>
          </>
        )}
        <dt>Ficha</dt>
        <dd>
          {movie.tmdb_id ? (
            <a
              href={`https://www.themoviedb.org/${series ? "tv" : "movie"}/${movie.tmdb_id}`}
            >
              Ver en TMDB
            </a>
          ) : (
            "Añadida a mano"
          )}
        </dd>
      </dl>
      {movie.overview && (
        <section className={styles.overview}>
          <h2>Sinopsis</h2>
          <p>{movie.overview}</p>
        </section>
      )}
    </div>
  );
}
