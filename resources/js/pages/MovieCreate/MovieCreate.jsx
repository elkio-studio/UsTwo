import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import Field from '../../components/Field/Field';
import '../../../css/global.css';
import styles from './MovieCreate.module.css';

export default function MovieCreate({ candidates }) {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState(null);
  const [searching, setSearching] = useState(false);
  const [searchError, setSearchError] = useState('');
  const [manual, setManual] = useState(false);
  const [selected, setSelected] = useState(null);
  const form = useForm({ title: '', release_year: '', watched_on: '' });
  const matches = form.errors.match ? candidates : [];

  async function search(event) {
    event.preventDefault();
    setSearching(true);
    setSearchError('');
    setResults(null);
    setSelected(null);
    form.clearErrors();
    try {
      const response = await fetch('/peliculas/catalogo?' + new URLSearchParams({ q: query }), {
        headers: { Accept: 'application/json' },
      });
      const payload = await response.json();
      if (!response.ok) throw new Error(payload.errors?.catalog?.[0] ?? 'No se ha podido consultar el catálogo.');
      setResults(payload.movies);
    } catch (error) {
      setSearchError(error.message);
    } finally {
      setSearching(false);
    }
  }

  function save(extra = {}) {
    form.transform((data) => ({
      ...data,
      tmdb_id: manual ? null : selected.tmdb_id,
      ...extra,
    }));
    form.post('/peliculas', { preserveScroll: true });
  }

  function changeIdentity(key, value) {
    form.clearErrors('match', key);
    form.setData(key, value);
  }

  return (
    <Journal title="Añadir película" back={{ href: '/peliculas', label: 'Volver a películas vistas' }}>
      {!manual && (
        <>
          <form onSubmit={search}>
            <Field label="Buscar un título en TMDB" type="search" required maxLength={255} value={query}
              onChange={(event) => setQuery(event.target.value)} error={searchError} />
            <button disabled={searching || form.processing}>{searching ? 'Buscando…' : 'Buscar'}</button>
          </form>
          {results && <p role="status">{results.length ? 'Elige la película entre los resultados.' : 'No hay resultados.'}</p>}
          {results && <ul className={styles.choices}>
            {results.map((movie) => <li key={movie.tmdb_id}>
              <button type="button" disabled={form.processing} aria-pressed={selected?.tmdb_id === movie.tmdb_id}
                onClick={() => { setSelected(movie); form.clearErrors(); }}>
                {movie.title}{movie.release_year ? ` (${movie.release_year})` : ''}
              </button>
            </li>)}
          </ul>}
          {(results || searchError || form.errors.catalog) && <button type="button" disabled={form.processing}
            onClick={() => { setManual(true); form.clearErrors(); }}>Añadir a mano</button>}
        </>
      )}
      {manual && <button type="button" disabled={form.processing}
        onClick={() => { setManual(false); setSelected(null); form.clearErrors(); }}>Volver al catálogo</button>}
      {(manual || selected) && (
        <form className={styles.editor} onSubmit={(event) => { event.preventDefault(); save(); }}>
          <h2>{manual ? 'Alta manual' : selected.title}</h2>
          {manual && <>
            <Field label="Título" required maxLength={255} value={form.data.title}
              onChange={(event) => changeIdentity('title', event.target.value)} error={form.errors.title} />
            <Field label="Año de estreno (opcional)" type="number" min="1000" max="9999" value={form.data.release_year}
              onChange={(event) => changeIdentity('release_year', event.target.value)} error={form.errors.release_year} />
          </>}
          <Field label="Fecha de visionado" type="date" required value={form.data.watched_on}
            onChange={(event) => form.setData('watched_on', event.target.value)} error={form.errors.watched_on} />
          {(form.errors.catalog || form.errors.save || form.errors.match) &&
            <p role="alert">{form.errors.catalog || form.errors.save || form.errors.match}</p>}
          {matches.length > 0 ? (
            <fieldset className={styles.candidates}>
              <legend>¿Es la misma película?</legend>
              {matches.map((movie) => <button key={movie.id} type="button" disabled={form.processing}
                onClick={() => save({ candidate_id: movie.id })}>
                {manual ? 'Abrir' : 'Vincular con'} {movie.title} · {movie.release_year ?? 'Año desconocido'} · vista el {movie.watched_on}
              </button>)}
              <button disabled={form.processing} type="button"
                onClick={() => save({ distinct: true, candidate_ids: matches.map((movie) => movie.id) })}>
                Ninguna corresponde, guardar otra
              </button>
            </fieldset>
          ) : <button disabled={form.processing}>Guardar película</button>}
        </form>
      )}
    </Journal>
  );
}
