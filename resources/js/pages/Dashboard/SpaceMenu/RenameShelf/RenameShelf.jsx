// Formulario de nombre de la estantería.

import Field from "../../../../components/Field/Field";

export default function RenameShelf({ spaceUrl, rename }) {
  return (
    <form
      onSubmit={(event) => {
        event.preventDefault();
        rename.patch(spaceUrl, { preserveScroll: true });
      }}
    >
      <Field
        label="Nombre"
        required
        maxLength={100}
        value={rename.data.name}
        onChange={(event) => rename.setData("name", event.target.value)}
        error={rename.errors.name}
      />
      <button disabled={rename.processing}>Guardar nombre</button>
    </form>
  );
}
