import { useEffect, useRef, useState } from "react";
import Field from "../../../components/Field/Field";
import ReadingCover from "../ReadingCover/ReadingCover";
import styles from "./ReadingSearch.module.css";

export default function ReadingSearch({ category, url, onSelect, onManual }) {
  const [query, setQuery] = useState("");
  const [volume, setVolume] = useState(null);
  const [results, setResults] = useState(null);
  const [error, setError] = useState("");
  const [busy, setBusy] = useState(false);
  const request = useRef(null);
  const field = useRef(null);
  const comics = category === "comics";
  const choosingVolume = comics && !volume;

  useEffect(() => () => request.current?.abort(), []);

  async function load(path, params, onSuccess) {
    request.current?.abort();
    const controller = new AbortController();
    request.current = controller;
    setBusy(true);
    setError("");

    try {
      const response = await fetch(
        `${url}/catalogo${path}?${new URLSearchParams({ category, ...params })}`,
        {
          headers: { Accept: "application/json" },
          signal: controller.signal,
        },
      );
      const data = await response.json();
      if (!response.ok) {
        throw new Error(
          data.errors?.catalog?.[0] ??
            data.errors?.q?.[0] ??
            "No se pudo consultar el catálogo.",
        );
      }
      onSuccess(data);
    } catch (error) {
      if (!controller.signal.aborted)
        setError(
          error instanceof SyntaxError
            ? "No se pudo consultar el catálogo."
            : error.message,
        );
    } finally {
      if (!controller.signal.aborted) setBusy(false);
    }
  }

  function search(term, page = 1, selectedVolume = volume) {
    if (page === 1) setResults(null);
    load(
      "",
      {
        q: term,
        page,
        ...(selectedVolume ? { volume: selectedVolume.id } : {}),
      },
      (data) => {
        setResults({ ...data, term, page });
      },
    );
  }

  function choose(item) {
    if (choosingVolume) {
      setVolume(item);
      setQuery("");
      field.current?.focus();
      search("", 1, item);
      return;
    }
    load("/ficha", { id: item.id }, onSelect);
  }

  return (
    <div className={styles.search}>
      <form
        onSubmit={(event) => {
          event.preventDefault();
          search(query.trim());
        }}
      >
        {volume && (
          <p>
            <strong>{volume.title}</strong> {volume.context}{" "}
            <button
              type="button"
              disabled={busy}
              onClick={() => {
                setVolume(null);
                setQuery("");
                setResults(null);
                setError("");
                field.current?.focus();
              }}
            >
              Cambiar colección
            </button>
          </p>
        )}
        <Field
          ref={field}
          autoFocus
          label={
            volume
              ? "Número (opcional)"
              : choosingVolume
                ? "Nombre de la colección"
                : "Título"
          }
          value={query}
          maxLength={volume ? 24 : 120}
          onChange={(event) => setQuery(event.target.value)}
        />
        <button disabled={busy || (!volume && !query.trim())}>Buscar</button>
      </form>
      {comics && (
        <a data-link-kind="external" href="https://comicvine.gamespot.com/">
          Comic Vine
        </a>
      )}
      {category === "manga" && (
        <a data-link-kind="external" href="https://www.mangaupdates.com/">
          MangaUpdates
        </a>
      )}
      {busy && <p role="status">Consultando el catálogo…</p>}
      {error && (
        <p role="alert">{error} Puedes añadir la lectura manualmente.</p>
      )}
      {results && (
        <div aria-busy={busy}>
          {results.items.length ? (
            <ul className={styles.results}>
              {results.items.map((item) => (
                <li key={item.id}>
                  <button
                    type="button"
                    disabled={busy}
                    onClick={() => choose(item)}
                  >
                    <ReadingCover title={item.title} url={item.cover_url} />
                    <span>
                      {item.title}
                      {item.context && <small>{item.context}</small>}
                    </span>
                  </button>
                </li>
              ))}
            </ul>
          ) : (
            <p role="status">Sin coincidencias.</p>
          )}
          <div className={styles.pagination}>
            {results.page > 1 && (
              <button
                type="button"
                disabled={busy}
                onClick={() => search(results.term, results.page - 1)}
              >
                Anterior
              </button>
            )}
            {results.hasMore && results.page < 100 && (
              <button
                type="button"
                disabled={busy}
                onClick={() => search(results.term, results.page + 1)}
              >
                Siguiente
              </button>
            )}
          </div>
        </div>
      )}
      <div>
        <button type="button" onClick={onManual}>
          Añadir a mano
        </button>
      </div>
    </div>
  );
}
