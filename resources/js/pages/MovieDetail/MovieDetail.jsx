// Ficha de película con valoraciones, datos compartidos y opciones de edición.

import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import Field from '../../components/Field/Field';
import MoviePoster from '../../components/MoviePoster/MoviePoster';
import '../../../css/global.css';
import styles from './MovieDetail.module.css';

export default function MovieDetail({ movie, people, viewerId, spaceId, series = false }) {
  const [confirming, setConfirming] = useState(false);
  const item = series ? 'serie' : 'película';
  const moviesUrl = `/espacios/${spaceId}/${series ? 'series' : 'peliculas'}`;
  const url = `${moviesUrl}/${movie.id}`;
  const ownRating = movie.ratings.find((rating) => rating.user_id === viewerId);
  const common = useForm({ title: movie.title, release_year: movie.release_year ?? '', watched_on: movie.watched_on });
  const [savedCommon, setSavedCommon] = useState(common.data);
  const rating = useForm({ score: ownRating?.score ?? '', note: ownRating?.note ?? '' });
  const deletion = useForm({ confirmed: true });

  return (
    <Journal className={styles.cinema} title={movie.title} back={{ href: moviesUrl, label: `Volver a ${item}s vistas` }}>
      <div className={styles.feature}>
        <MoviePoster path={movie.poster_path} title={movie.title} size="w500" eager />
        <dl>
          <dt>Fecha de visionado</dt><dd><time dateTime={movie.watched_on}>{movie.watched_on.split('-').reverse().join('/')}</time></dd>
          {movie.release_year && <><dt>Año de estreno</dt><dd>{movie.release_year}</dd></>}
          <dt>Ficha</dt><dd>{movie.tmdb_id ? <a href={`https://www.themoviedb.org/${series ? 'tv' : 'movie'}/${movie.tmdb_id}`}>Ver en TMDB</a> : 'Añadida a mano'}</dd>
        </dl>
        {movie.overview && <section className={styles.overview}>
          <h2>Sinopsis</h2>
          <p>{movie.overview}</p>
        </section>}
      </div>
      <section className={styles.section}>
        <h2>Vuestras valoraciones</h2>
        <div className={styles.reviews}>{people.map((person) => {
          const contribution = movie.ratings.find((entry) => entry.user_id === person.id);
          return <article className={styles.contribution} key={person.id}>
            <h3>{person.name}</h3>
            <p><strong>{contribution?.score == null ? 'Sin puntuación' : `${contribution.score} / 100`}</strong></p>
            {contribution?.note && <p>{contribution.note}</p>}
          </article>;
        })}</div>
      </section>
      <details className={styles.section} open={rating.hasErrors || undefined}>
        <summary>Tu valoración</summary>
        <form onSubmit={(event) => {
          event.preventDefault();
          rating.put(url + '/valoracion', { preserveScroll: true });
        }}>
          <Field label="Tu puntuación (0 a 100)" type="number" min="0" max="100" step="1" value={rating.data.score}
            onChange={(event) => rating.setData('score', event.target.value)} error={rating.errors.score} />
          <Field label="Tu nota" multiline maxLength={5000} value={rating.data.note}
            onChange={(event) => rating.setData('note', event.target.value)} error={rating.errors.note} />
          {rating.errors.save && <p role="alert">{rating.errors.save}</p>}
          <button disabled={rating.processing}>Guardar mi valoración</button>
        </form>
      </details>
      <details className={styles.section} open={common.hasErrors || undefined}>
        <summary>Editar datos comunes</summary>
        <form onSubmit={(event) => {
          event.preventDefault();
          common.transform((data) => Object.fromEntries(Object.entries(data).filter(([key, value]) =>
            String(value) !== String(savedCommon[key]),
          )));
          common.patch(url, { preserveScroll: true, onSuccess: () => setSavedCommon(common.data) });
        }}>
          <Field label="Título" required maxLength={255} value={common.data.title}
            onChange={(event) => common.setData('title', event.target.value)} error={common.errors.title} />
          <Field label="Año de estreno (opcional)" type="number" min="1000" max="9999" value={common.data.release_year}
            onChange={(event) => common.setData('release_year', event.target.value)} error={common.errors.release_year} />
          <Field label="Fecha de visionado" type="date" required value={common.data.watched_on}
            onChange={(event) => common.setData('watched_on', event.target.value)} error={common.errors.watched_on} />
          {common.errors.save && <p role="alert">{common.errors.save}</p>}
          <button disabled={common.processing}>Guardar datos comunes</button>
        </form>
      </details>
      <section className={styles.section} aria-label={`Borrar ${item}`}>
        {confirming ? <>
          <p role="alert">Se borrarán definitivamente la {item}, sus puntuaciones y sus notas.</p>
          <div className={styles.actions}>
            <button autoFocus disabled={deletion.processing} onClick={() => setConfirming(false)}>Cancelar</button>
            <button className={styles.danger} disabled={deletion.processing}
              onClick={() => deletion.delete(url)}>Confirmar borrado</button>
          </div>
          {(deletion.errors.save || deletion.errors.confirmed) && <p role="alert">{deletion.errors.save || deletion.errors.confirmed}</p>}
        </> : <button type="button" onClick={() => setConfirming(true)}>Borrar {item}</button>}
      </section>
    </Journal>
  );
}
