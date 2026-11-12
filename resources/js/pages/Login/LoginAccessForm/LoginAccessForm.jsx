// Recibidor de acceso y registro de UsTwo.

import { useState } from "react";
import { Head } from "@inertiajs/react";
import AccessForm from "../AccessForm/AccessForm";
import "../../../../css/global.css";
import styles from "./LoginAccessForm.module.css";

export default function LoginAccessForm() {
  const [registering, setRegistering] = useState(false);

  return (
    <main className={styles.receivingRoom} data-registering={registering}>
      <Head title={registering ? "Crear cuenta" : "Iniciar sesión"} />
      <div className={styles.welcome}>
        <span className={styles.moon} aria-hidden="true" />
        <p className={styles.wordmark}>UsTwo</p>
        <p>Lo que guardamos juntos</p>
      </div>
      <AccessForm registering={registering} onModeChange={setRegistering} />
    </main>
  );
}
