// Acceso a Lecturas representado por una fila de lomos.

import { Link } from "@inertiajs/react";
import styles from "./ReadingSpines.module.css";

export default function ReadingSpines({ href, variant }) {
  return (
    <Link href={href} className={styles.shelf}>
      <span className={styles.label}>Lecturas</span>
      <span className={styles.row} data-variant={variant} aria-hidden="true">
        <span className={styles.book}>
          <span className={styles.leftCover} />
          <span className={styles.rightCover} />
          <span className={styles.pageEdge} />
        </span>
      </span>
    </Link>
  );
}
