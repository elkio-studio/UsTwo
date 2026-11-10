// Listado de películas vistas y acceso a sus fichas.

import { Link } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import MoviePoster from '../../components/MoviePoster/MoviePoster';
import '../../../css/global.css';
import styles from './Movies.module.css';

export default function Movies({ movies, people, spaceId, spaceSlug, pagination }) {
  return (
    <Journal className={styles.cinema} title="Películas vistas" back={{ href: `/${spaceSlug}`, label: 'Volver a la estantería' }}>
      <p className={styles.intro}>Las películas que hemos visto, con nuestras propias puntuaciones.</p>
      <Link className={styles.add} href={`/espacios/${spaceId}/peliculas/nueva`}>{movies.length ? 'Añadir película' : 'Añadir la primera película'}</Link>
      {movies.length === 0 ? <p className={styles.empty}>Todavía no hay películas en esta colección.</p> : (
        <ul className={styles.list}>
          {movies.map((movie) => (
            <li key={movie.id}>
              <Link className={styles.entry} href={`/espacios/${spaceId}/peliculas/${movie.id}`}>
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
      {pagination.last > 1 && <nav className={styles.pagination} aria-label="Paginación de películas">
        {pagination.previous && <Link href={pagination.previous} rel="prev">← Anterior</Link>}
        <span>Página {pagination.page} de {pagination.last}</span>
        {pagination.next && <Link href={pagination.next} rel="next">Siguiente →</Link>}
      </nav>}
      <Link href="/creditos">Créditos</Link>
    </Journal>
  );
}
