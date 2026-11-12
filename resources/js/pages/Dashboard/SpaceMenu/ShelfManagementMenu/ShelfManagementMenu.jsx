// Menú de selección y gestión de la estantería.

import { router, useForm } from "@inertiajs/react";
import InviteToShelf from "../InviteToShelf/InviteToShelf";
import RenameShelf from "../RenameShelf/RenameShelf";
import ShelfMembers from "../ShelfMembers/ShelfMembers";
import styles from "./ShelfManagementMenu.module.css";

export default function ShelfManagementMenu({
  spaces,
  activeSpace,
  isOwner,
  outgoing,
  members,
  editing,
}) {
  const invite = useForm({ username: "" });
  const rename = useForm({ name: activeSpace.name });
  const spaceUrl = activeSpace ? `/espacios/${activeSpace.id}` : null;

  function deleteSpace() {
    if (
      window.confirm(
        "¿Borrar esta estantería definitivamente? También se borrarán sus películas y valoraciones.",
      )
    ) {
      router.delete(spaceUrl, { data: { confirmed: true } });
    }
  }

  function leaveSpace() {
    if (
      window.confirm(
        "¿Salir de esta estantería? Necesitarás otra invitación para volver.",
      )
    ) {
      router.delete(`${spaceUrl}/pertenencia`);
    }
  }

  return (
    <details
      name="ustwo-panels"
      className={styles.menu}
      open={editing || undefined}
    >
      <summary>Estantería</summary>
      <div className={styles.panel}>
        {activeSpace && (
          <label>
            Estantería activa
            <select
              value={activeSpace.slug}
              onChange={(event) => router.visit(`/${event.target.value}`)}
            >
              {spaces.map(({ id, name, slug }) => (
                <option key={id} value={slug}>
                  {name}
                </option>
              ))}
            </select>
          </label>
        )}
        {activeSpace && !isOwner && (
          <button type="button" onClick={leaveSpace}>
            Salir de la estantería
          </button>
        )}
        {isOwner && (
          <details
            open={editing || rename.hasErrors || invite.hasErrors || undefined}
          >
            <summary>Editar estantería</summary>
            <div className={styles.management}>
              <RenameShelf {...{ spaceUrl, rename }} />
              <InviteToShelf {...{ spaceUrl, invite }} />
            </div>
            <ShelfMembers {...{ spaceUrl, members, outgoing }} />
            <button
              type="button"
              className={styles.danger}
              onClick={deleteSpace}
            >
              Borrar estantería
            </button>
          </details>
        )}
      </div>
    </details>
  );
}
