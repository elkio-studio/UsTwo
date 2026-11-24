// Fila de deseo con datos alineados y descripción desplegable independiente.

import { useId, useState } from "react";
import WishActions from "../WishActions/WishActions";
import styles from "./WishRow.module.css";

export default function WishRow({ wish, spaceId, onDeleted }) {
  const [expanded, setExpanded] = useState(false);
  const descriptionId = useId();
  const description = wish.description?.trim();
  const preview = description?.replace(/\s+/g, " ");
  return (
    <li className={styles.row}>
      <a
        className={styles.title}
        data-link-kind="external"
        href={wish.url}
        target="_blank"
        rel="noopener noreferrer"
      >
        {wish.title}
      </a>
      <div className={styles.summary}>
        {description && (
          <button
            type="button"
            className={styles.descriptionToggle}
            aria-expanded={expanded}
            aria-controls={descriptionId}
            aria-label={`${expanded ? "Plegar" : "Desplegar"} descripción de ${wish.title}`}
            onClick={() => setExpanded((current) => !current)}
          >
            <span>
              {expanded
                ? "Ocultar descripción"
                : preview.length > 90
                  ? `${preview.slice(0, 90)}…`
                  : preview}
            </span>
            <svg
              aria-hidden="true"
              viewBox="0 0 16 16"
              className={expanded ? styles.up : undefined}
            >
              <path d="m4 6 4 4 4-4" />
            </svg>
          </button>
        )}
      </div>
      <span className={styles.price}>
        {wish.price !== null
          ? `${wish.price} ${wish.currency}`
          : "Sin precio indicado"}
      </span>
      <span className={styles.author}>@{wish.author}</span>
      <span className={styles.status}>Pendiente</span>
      <div className={styles.actions}>
        <WishActions spaceId={spaceId} wish={wish} onDeleted={onDeleted} />
      </div>
      {description && (
        <p id={descriptionId} className={styles.description} hidden={!expanded}>
          {wish.description}
        </p>
      )}
    </li>
  );
}
