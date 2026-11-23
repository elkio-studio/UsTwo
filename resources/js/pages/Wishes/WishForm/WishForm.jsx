// Formulario manual de un deseo con lista obligatoria y precio en euros.

import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";
import WishListForm from "../WishListForm/WishListForm";
import styles from "./WishForm.module.css";

export default function WishForm({ spaceId, lists: initialLists }) {
  const [lists, setLists] = useState(initialLists);
  const [creating, setCreating] = useState(false);
  const form = useForm({
    list_id: "",
    url: "",
    title: "",
    description: "",
    price: "",
  });
  const listControl = useRef(null);
  const createButton = useRef(null);
  const base = `/espacios/${spaceId}/deseos`;
  function change(key, value) {
    form.setData(key, value);
  }

  function save(event) {
    event.preventDefault();
    if (form.processing) return;
    form.post(base, { preserveScroll: true });
  }

  return (
    <div className={styles.layout}>
      {creating && (
        <WishListForm
          url={`${base}/listas`}
          onCancel={() => {
            setCreating(false);
            createButton.current?.focus();
          }}
          onCreated={(list) => {
            setLists((current) => [...current, list]);
            change("list_id", String(list.id));
            setCreating(false);
            listControl.current?.focus();
          }}
        />
      )}
      <form onSubmit={save} className={styles.form}>
        <div className={styles.listChoice}>
          <label htmlFor="wish-list">Lista</label>
          <select
            ref={listControl}
            id="wish-list"
            required
            value={form.data.list_id}
            onChange={(event) => change("list_id", event.target.value)}
            aria-invalid={!!form.errors.list_id}
            aria-describedby={
              form.errors.list_id ? "wish-list-error" : undefined
            }
          >
            <option value="">
              {lists.length
                ? "Elige una lista"
                : "Crea una lista para guardar tu deseo"}
            </option>
            {lists.map((list) => (
              <option key={list.id} value={list.id}>
                {list.name}
              </option>
            ))}
          </select>
          {form.errors.list_id && (
            <p id="wish-list-error" role="alert">
              {form.errors.list_id}
            </p>
          )}
          <button
            ref={createButton}
            type="button"
            onClick={() => setCreating(true)}
            disabled={creating}
          >
            Crear lista
          </button>
        </div>
        <Field
          label="URL del enlace"
          type="url"
          required
          maxLength={2048}
          value={form.data.url}
          onChange={(event) => change("url", event.target.value)}
          error={form.errors.url}
        />
        <Field
          label="Título"
          required
          maxLength={255}
          value={form.data.title}
          onChange={(event) => change("title", event.target.value)}
          error={form.errors.title}
        />
        <Field
          label="Descripción (opcional)"
          multiline
          maxLength={5000}
          value={form.data.description}
          onChange={(event) => change("description", event.target.value)}
          error={form.errors.description}
        />
        <Field
          label="Precio en euros (opcional)"
          inputMode="decimal"
          maxLength={11}
          value={form.data.price}
          onChange={(event) => {
            change("price", event.target.value);
          }}
          error={form.errors.price}
        />
        <p>El precio es orientativo y no se actualiza automáticamente.</p>
        {form.errors.save && <p role="alert">{form.errors.save}</p>}
        <button disabled={form.processing || !lists.length}>
          {form.processing ? "Guardando…" : "Guardar deseo"}
        </button>
      </form>
    </div>
  );
}
