// Portada, datos y sinopsis de la ficha.

import CollectionPoster from "../../../components/CollectionPoster/CollectionPoster";
import CollectionInformation from "../../../components/CollectionInformation/CollectionInformation";

export default function MovieInformation({ movie, series }) {
  return (
    <CollectionInformation synopsis={movie.overview} poster={<CollectionPoster
        path={movie.poster_path}
        title={movie.title}
        size="w500"
        eager
      />}>
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
    </CollectionInformation>
  );
}
