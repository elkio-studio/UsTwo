// Formulario de invitación a la estantería.

import Field from "../../../../components/Field/Field";

export default function InviteToShelf({ spaceUrl, invite }) {
  return (
    <form
      onSubmit={(event) => {
        event.preventDefault();
        invite.post(`${spaceUrl}/invitaciones`, {
          onSuccess: () => invite.reset(),
        });
      }}
    >
      <Field
        label="Invitar por @usuario"
        required
        maxLength={32}
        value={invite.data.username}
        onChange={(event) => invite.setData("username", event.target.value)}
        error={invite.errors.username}
      />
      <button disabled={invite.processing}>Invitar</button>
    </form>
  );
}
