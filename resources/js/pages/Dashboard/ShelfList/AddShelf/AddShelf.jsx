// Formulario para crear una estantería.

import { useForm } from "@inertiajs/react";
import Field from "../../../../components/Field/Field";
import styles from "./AddShelf.module.css";

export default function AddShelf() {
  const create = useForm({ name: "" });

  return (
    <details open={create.hasErrors || undefined}>
      <summary className={styles.summary}>+ Añadir estantería</summary>
      <form
        className={styles.form}
        onSubmit={(event) => {
          event.preventDefault();
          create.post("/espacios");
        }}
      >
        <Field
          label="Nombre de la estantería"
          required
          maxLength={100}
          value={create.data.name}
          onChange={(event) => create.setData("name", event.target.value)}
          error={create.errors.name}
        />
        <button disabled={create.processing}>Crear estantería</button>
      </form>
    </details>
  );
}
