// Miembros de la estantería e invitaciones enviadas.

import { router } from "@inertiajs/react";

export default function ShelfMembers({ spaceUrl, members, outgoing }) {
  function removeMember(id, username) {
    if (window.confirm(`¿Retirar el acceso a @${username}?`)) {
      router.delete(`${spaceUrl}/miembros/${id}`, { preserveScroll: true });
    }
  }

  return (
    <>
      <h3>Miembros</h3>
      {members.length > 0 ? (
        <ul aria-label="Miembros de la estantería">
          {members.map(({ id, username }) => (
            <li key={id}>
              <span>@{username}</span>
              <button type="button" onClick={() => removeMember(id, username)}>
                Retirar acceso
              </button>
            </li>
          ))}
        </ul>
      ) : (
        <p>Todavía no hay otros miembros.</p>
      )}
      {outgoing.length > 0 && (
        <>
          <h3>Invitaciones pendientes</h3>
          <ul aria-label="Invitaciones enviadas">
            {outgoing.map(({ id, username }) => (
              <li key={id}>
                <span>@{username}</span>
                <button
                  type="button"
                  onClick={() =>
                    router.delete(`${spaceUrl}/invitaciones/${id}`)
                  }
                >
                  Retirar
                </button>
              </li>
            ))}
          </ul>
        </>
      )}
    </>
  );
}
