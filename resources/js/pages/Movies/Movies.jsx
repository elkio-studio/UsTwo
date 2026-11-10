// Listado de películas vistas y acceso a sus fichas.

import { Link } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import MoviePoster from '../../components/MoviePoster/MoviePoster';
import '../../../css/global.css';
import styles from './Movies.module.css';

export default function Movies({ movies, people, spaceId, spaceSlug, pagination, series = false }) {
  const collection = series ? 'series' : 'películas';
  const item = series ? 'serie' : 'película';
  const url = `/espacios/${spaceId}/${series ? 'series' : 'peliculas'}`;

  return (
    <Journal className={styles.cinema} title={series ? 'Series vistas' : 'Películas vistas'} back={{ href: `/${spaceSlug}`, label: 'Volver a la estantería' }}>
      <p className={styles.intro}>Las {collection} que hemos visto, con nuestras propias puntuaciones.</p>
      <Link className={styles.add} href={`${url}/nueva`}>Añadir {movies.length ? item : `la primera ${item}`}</Link>
      {movies.length === 0 ? <p className={styles.empty}>Todavía no hay {collection} en esta colección.</p> : (
        <ul className={styles.list}>
          {movies.map((movie) => (
            <li key={movie.id}>
              <Link className={styles.entry} href={`${url}/${movie.id}`}>
                <MoviePoster path={movie.poster_path} title={movie.title} />
                <span className={styles.caption}>
                  <strong>{movie.title}</strong>
                  <span className={styles.scores}>
                    {people.map((person) => (
                      <span key={person.id}>
                        <span>{person.name}</span>
                        <b>{movie.ratings.find((rating) => rating.user_id === person.id)?.score ?? '—'}</b>
                      </span>
                    ))}
                  </span>
                </span>
              </Link>
            </li>
          ))}
        </ul>
      )}
      {pagination.last > 1 && <nav className={styles.pagination} aria-label={`Paginación de ${collection}`}>
        {pagination.previous && <Link href={pagination.previous} rel="prev">← Anterior</Link>}
        <span>Página {pagination.page} de {pagination.last}</span>
        {pagination.next && <Link href={pagination.next} rel="next">Siguiente →</Link>}
      </nav>}
      <Link href="/creditos">Créditos</Link>
    </Journal>
  );
}
