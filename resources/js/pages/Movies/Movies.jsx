import { Link } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import '../../../css/global.css';
import styles from './Movies.module.css';

export default function Movies({ movies, people }) {
  return (
    <Journal title="Películas vistas" back={{ href: '/', label: 'Volver al cuaderno' }}>
      <Link className={styles.add} href="/peliculas/nueva">{movies.length ? 'Añadir película' : 'Añadir la primera película'}</Link>
      {movies.length === 0 ? <p>Aún no hay películas registradas.</p> : (
        <ul className={styles.list}>
          {movies.map((movie) => (
            <li key={movie.id}>
              <Link className={styles.entry} href={`/peliculas/${movie.id}`}>
                <strong>{movie.title}</strong>
                <span className={styles.scores}>
                  {people.map((person) => (
                    <span key={person.id}>
                      {person.name} {movie.ratings.find((rating) => rating.user_id === person.id)?.score ?? '—'}
                    </span>
                  ))}
                </span>
              </Link>
            </li>
          ))}
        </ul>
      )}
      <Link href="/creditos">Créditos</Link>
    </Journal>
  );
}
