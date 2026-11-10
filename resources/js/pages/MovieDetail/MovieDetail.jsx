// Ficha de película con valoraciones, datos compartidos y opciones de edición.

import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import Field from '../../components/Field/Field';
import '../../../css/global.css';
import styles from './MovieDetail.module.css';

export default function MovieDetail({ movie, people, viewerId }) {
  const [confirming, setConfirming] = useState(false);
  const url = `/peliculas/${movie.id}`;
  const ownRating = movie.ratings.find((rating) => rating.user_id === viewerId);
  const common = useForm({ title: movie.title, release_year: movie.release_year ?? '', watched_on: movie.watched_on });
  const [savedCommon, setSavedCommon] = useState(common.data);
  const rating = useForm({ score: ownRating?.score ?? '', note: ownRating?.note ?? '' });
  const deletion = useForm({ confirmed: true });

  return (
    <Journal title={movie.title} back={{ href: '/peliculas', label: 'Volver a películas vistas' }}>
      <dl>
        <dt>Fecha de visionado</dt><dd>{movie.watched_on}</dd>
        {movie.release_year && <><dt>Año de estreno</dt><dd>{movie.release_year}</dd></>}
      </dl>
      <section className={styles.section}>
        <h2>Vuestras valoraciones</h2>
        {people.map((person) => {
          const contribution = movie.ratings.find((entry) => entry.user_id === person.id);
          return <article className={styles.contribution} key={person.id}>
            <h3>{person.name} · {contribution?.score == null ? 'Sin puntuación' : `${contribution.score} / 100`}</h3>
            {contribution?.note && <p>{contribution.note}</p>}
          </article>;
        })}
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
      </section>
      <section className={styles.section}>
        <h2>Editar datos comunes</h2>
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
      </section>
      <section className={styles.section} aria-label="Borrar película">
        {confirming ? <>
          <p role="alert">Se borrarán definitivamente la película, las puntuaciones y las notas de ambos.</p>
          <div className={styles.actions}>
            <button autoFocus disabled={deletion.processing} onClick={() => setConfirming(false)}>Cancelar</button>
            <button className={styles.danger} disabled={deletion.processing}
              onClick={() => deletion.delete(url)}>Confirmar borrado</button>
          </div>
          {(deletion.errors.save || deletion.errors.confirmed) && <p role="alert">{deletion.errors.save || deletion.errors.confirmed}</p>}
        </> : <button type="button" onClick={() => setConfirming(true)}>Borrar película</button>}
      </section>
    </Journal>
  );
}
