// Estructura visual del recibidor de acceso y registro.

import { Head } from "@inertiajs/react";
import styles from "./AccessLayout.module.css";

export default function AccessLayout({ registering, children }) {
  return (
    <main className={styles.receivingRoom} data-registering={registering}>
      <Head title={registering ? "Crear cuenta" : "Iniciar sesión"} />
      <div className={styles.welcome}>
        <span className={styles.moon} aria-hidden="true" />
        <p className={styles.wordmark}>UsTwo</p>
        <p>Lo que guardamos juntos</p>
      </div>
      {children}
    </main>
  );
}
