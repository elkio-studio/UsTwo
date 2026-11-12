// Búsqueda en TMDB y estados de carga y error.

import { useState } from "react";
import Field from "../../../../components/Field/Field";
import CatalogResults from "../CatalogResults/CatalogResults";
import styles from "./CatalogSearchForm.module.css";

export default function CatalogSearchForm({
  manual,
  moviesUrl,
  item,
  selected,
  hasMatches,
  error,
  processing,
  onSearch,
  onSelect,
  onManual,
}) {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState(null);
  const [searching, setSearching] = useState(false);
  const [searchError, setSearchError] = useState("");

  async function search(event) {
    event.preventDefault();
    setSearching(true);
    setSearchError("");
    setResults(null);
    onSearch();
    try {
      const response = await fetch(
        `${moviesUrl}/catalogo?` + new URLSearchParams({ q: query }),
        {
          headers: { Accept: "application/json" },
        },
      );
      const payload = await response.json();
      if (!response.ok)
        throw new Error(
          payload.errors?.catalog?.[0] ??
            "No se ha podido consultar el catálogo.",
        );
      setResults(payload.movies);
    } catch (error) {
      setSearchError(error.message);
    } finally {
      setSearching(false);
    }
  }

  if (manual) return null;

  return (
    <>
      <form onSubmit={search}>
        <Field
          label="Buscar un título en TMDB"
          type="search"
          required
          maxLength={255}
          value={query}
          onChange={(event) => setQuery(event.target.value)}
          error={searchError}
        />
        <button disabled={searching || processing}>
          {searching ? "Buscando…" : "Buscar"}
        </button>
      </form>
      {!hasMatches && error && <p role="alert">{error}</p>}
      {results && !hasMatches && (
        <CatalogResults
          {...{ results, item, selected, processing, onSelect }}
        />
      )}
      <button
        type="button"
        className={styles.manual}
        disabled={searching || processing}
        onClick={onManual}
      >
        Añadir a mano
      </button>
    </>
  );
}
