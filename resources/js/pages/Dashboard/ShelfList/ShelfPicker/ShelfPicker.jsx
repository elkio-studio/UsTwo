// Selección de estantería y acceso al formulario de alta.

import { Link } from "@inertiajs/react";
import AddShelf from "../AddShelf/AddShelf";
import styles from "./ShelfPicker.module.css";

export default function ShelfPicker({ spaces }) {
  return (
    <>
      <p>
        {spaces.length > 0
          ? "Elige en qué estantería quieres entrar."
          : "Todavía no tienes estanterías. Crea una o acepta una invitación."}
      </p>
      <ul className={styles.choices}>
        {spaces.map(({ id, name, slug }) => (
          <li key={id}>
            <Link href={`/${slug}`}>{name}</Link>
          </li>
        ))}
        <li>
          <AddShelf />
        </li>
      </ul>
    </>
  );
}
