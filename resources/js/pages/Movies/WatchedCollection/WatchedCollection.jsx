// Listado de películas vistas y acceso a sus fichas.

import collectionUrl from "../../../utils/collectionUrl";
import { Link } from "@inertiajs/react";
import MovieCard from "../MovieCard/MovieCard";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import "../../../../css/global.css";
import styles from "./WatchedCollection.module.css";

export default function WatchedCollection({
  movies,
  people,
  spaceId,
  spaceSlug,
  pagination,
  series = false,
}) {
  const collection = series ? "series" : "películas";
  const item = series ? "serie" : "película";
  const url = collectionUrl(spaceId, series);

  return (
    <JournalLayout
      theme="cinema"
      title={series ? "Series vistas" : "Películas vistas"}
      back={{ href: `/${spaceSlug}`, label: "Volver a la estantería" }}
    >
      <p className={styles.intro}>
        Las {collection} que hemos visto, con nuestras propias puntuaciones.
      </p>
      <Link className={styles.add} href={`${url}/nueva`}>
        Añadir {movies.length ? item : `la primera ${item}`}
      </Link>
      {movies.length === 0 ? (
        <p className={styles.empty}>
          Todavía no hay {collection} en esta colección.
        </p>
      ) : (
        <ul className={styles.list}>
          {movies.map((movie) => (
            <MovieCard key={movie.id} {...{ movie, people, url }} />
          ))}
        </ul>
      )}
      {pagination.last > 1 && (
        <nav
          className={styles.pagination}
          aria-label={`Paginación de ${collection}`}
        >
          {pagination.previous && (
            <Link href={pagination.previous} rel="prev">
              ← Anterior
            </Link>
          )}
          <span>
            Página {pagination.page} de {pagination.last}
          </span>
          {pagination.next && (
            <Link href={pagination.next} rel="next">
              Siguiente →
            </Link>
          )}
        </nav>
      )}
      <Link href="/creditos">Créditos</Link>
    </JournalLayout>
  );
}
