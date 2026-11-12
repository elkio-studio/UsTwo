// Portada de TMDB con una alternativa para películas y series sin imagen.

import { useState } from "react";
import styles from "./CollectionPoster.module.css";

export default function CollectionPoster({
  path,
  title,
  size = "w342",
  eager = false,
}) {
  const [failedPath, setFailedPath] = useState(null);
  const available = path && failedPath !== path;

  return (
    <span data-collection-poster className={styles.poster} aria-hidden="true">
      {available ? (
        <img
          src={`https://image.tmdb.org/t/p/${size}${path}`}
          alt=""
          width="342"
          height="513"
          loading={eager ? "eager" : "lazy"}
          decoding="async"
          onError={() => setFailedPath(path)}
        />
      ) : (
        <span data-poster-placeholder className={styles.placeholder}>
          <span>{title}</span>
          <small>Sin portada</small>
        </span>
      )}
    </span>
  );
}
