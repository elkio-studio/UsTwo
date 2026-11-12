// Menú de la cuenta y acciones de la sesión.

import { Link, usePage } from "@inertiajs/react";
import styles from "./AccountMenu.module.css";

export default function AccountMenu() {
  const { sessionUsername } = usePage().props;

  return (
    <details name="ustwo-panels" className={styles.accountMenu}>
      <summary>
        <span aria-hidden="true">☰</span> @{sessionUsername}
      </summary>
      <div className={styles.accountActions}>
        <Link href="/mi/cuenta">Editar cuenta</Link>
        <Link href="/salir" method="post" as="button">
          Cerrar sesión
        </Link>
      </div>
    </details>
  );
}
