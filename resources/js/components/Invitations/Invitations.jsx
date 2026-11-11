// Bandeja persistente de invitaciones del usuario autenticado.

import { router, usePage } from "@inertiajs/react";
import styles from "./Invitations.module.css";

export default function Invitations() {
  const { invitations = [], errors } = usePage().props;

  function respond(id, response) {
    router.patch(`/invitaciones/${id}`, { response }, { preserveScroll: true });
  }

  return (
    <details name="ustwo-panels" className={styles.tray}>
      <summary className={styles.trigger}>
        <span className={styles.label} aria-live="polite">
          Invitaciones pendientes: {invitations.length}
        </span>
        <span aria-hidden="true">✉</span>
        {invitations.length > 0 && (
          <span className={styles.badge} aria-hidden="true">
            {invitations.length}
          </span>
        )}
      </summary>
      <section className={styles.panel} aria-label="Invitaciones pendientes">
        <strong>Invitaciones pendientes</strong>
        {errors?.invitation && <p role="alert">{errors.invitation}</p>}
        {invitations.length === 0 ? (
          <p>No tienes invitaciones pendientes.</p>
        ) : (
          <ul>
            {invitations.map(({ id, space, inviter }) => (
              <li key={id}>
                <p>
                  <strong>{space}</strong>
                  <br />
                  Te ha invitado @{inviter}
                </p>
                <div className={styles.actions}>
                  <button type="button" onClick={() => respond(id, "accepted")}>
                    Aceptar
                  </button>
                  <button type="button" onClick={() => respond(id, "rejected")}>
                    Rechazar
                  </button>
                </div>
              </li>
            ))}
          </ul>
        )}
      </section>
    </details>
  );
}
