// Estanterías de la cuenta, roles y acciones de pertenencia.

import { Link, router } from "@inertiajs/react";
import styles from "./AccountShelves.module.css";

export default function AccountShelves({ spaces, className }) {
  function leaveShelf(id, name) {
    if (
      window.confirm(
        `¿Salir de «${name}»? Necesitarás otra invitación para volver.`,
      )
    ) {
      router.delete(`/espacios/${id}/pertenencia`, {
        data: { from_account: true },
        preserveScroll: true,
      });
    }
  }

  return (
    <section className={className}>
      <h2>Tus estanterías</h2>
      {spaces.length === 0 ? (
        <p>Todavía no perteneces a ninguna estantería.</p>
      ) : (
        <ul className={styles.shelves}>
          {spaces.map(({ id, name, slug, isOwner }) => (
            <li key={id}>
              <strong>{name}</strong>
              <small>{isOwner ? "Propietario" : "Miembro"}</small>
              <div className={styles.shelfActions}>
                <Link href={`/${slug}`}>Entrar</Link>
                {isOwner ? (
                  <Link href={`/${slug}?editar=1`}>Editar estantería</Link>
                ) : (
                  <button type="button" onClick={() => leaveShelf(id, name)}>
                    Salir de la estantería
                  </button>
                )}
              </div>
            </li>
          ))}
        </ul>
      )}
      <Link href="/">Ver todas tus estanterías</Link>
    </section>
  );
}
