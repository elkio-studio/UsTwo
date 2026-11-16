// Acceso a Lecturas representado por una fila de lomos.

import { Link } from "@inertiajs/react";
import styles from "./ReadingSpines.module.css";

const titles = [
  "El atlas de la niebla",
  "Cartas al invierno",
  "El jardín cerrado",
  "La última librería",
  "Islas de papel",
];

export default function ReadingSpines({ href }) {
  return (
    <Link href={href} className={styles.shelf} aria-label="Lecturas">
      {titles.map((title) => (
        <span key={title} className={styles.book} aria-hidden="true">
          {title}
        </span>
      ))}
    </Link>
  );
}
