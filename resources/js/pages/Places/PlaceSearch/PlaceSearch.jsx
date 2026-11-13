// Búsqueda de sitios por nombre o dirección.

import { useEffect, useRef, useState } from "react";
import Field from "../../../components/Field/Field";

export default function PlaceSearch({ url, onSelect }) {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState([]);
  const [message, setMessage] = useState("");
  const [loading, setLoading] = useState(false);
  const request = useRef(null);

  useEffect(() => () => request.current?.abort(), []);

  async function search(event) {
    event.preventDefault();
    const controller = new AbortController();
    request.current = controller;
    setLoading(true);
    setMessage("");
    setResults([]);

    try {
      const response = await fetch(
        url + "?" + new URLSearchParams({ q: query }),
        {
          signal: controller.signal,
          headers: { Accept: "application/json" },
        },
      );
      if (response.status === 429)
        throw new Error(
          "Se alcanzó el límite de búsquedas. Inténtalo más tarde.",
        );
      const data = await response.json();
      if (!response.ok)
        throw new Error(
          data.errors?.search?.[0] || "No se pudo buscar el lugar.",
        );
      setResults(data);
      if (!data.length)
        setMessage(
          "Sin resultados. Puedes elegir un punto o introducir las coordenadas.",
        );
    } catch (error) {
      if (!controller.signal.aborted) {
        setMessage(
          error.message + " También puedes introducir las coordenadas.",
        );
      }
    } finally {
      if (!controller.signal.aborted) setLoading(false);
    }
  }

  return (
    <section aria-label="Buscar una ubicación">
      <form onSubmit={search}>
        <Field
          label="Nombre o dirección"
          type="search"
          required
          maxLength={255}
          value={query}
          onChange={(event) => setQuery(event.target.value)}
        />
        <button disabled={loading}>{loading ? "Buscando…" : "Buscar"}</button>
      </form>
      {message && <p role="status">{message}</p>}
      {results.length > 0 && (
        <ul>
          {results.map((result, index) => (
            <li key={index}>
              <button type="button" onClick={() => onSelect(result)}>
                {result.address}
              </button>
            </li>
          ))}
        </ul>
      )}
    </section>
  );
}
