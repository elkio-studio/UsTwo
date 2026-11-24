// Formulario manual de un deseo con lista obligatoria y precio en euros.

import { useEffect, useRef, useState } from "react";
import { Link, useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";
import WishListForm from "../WishListForm/WishListForm";
import styles from "./WishForm.module.css";

export default function WishForm({
  spaceId,
  lists: initialLists,
  wish = null,
  cancelHref = null,
}) {
  const [lists, setLists] = useState(initialLists);
  const [creating, setCreating] = useState(false);
  const form = useForm({
    list_id: wish ? String(wish.list_id) : "",
    url: wish?.url ?? "",
    title: wish?.title ?? "",
    description: wish?.description ?? "",
    price: wish?.price ?? "",
  });
  const listControl = useRef(null);
  const createButton = useRef(null);
  const listNotice = useRef(null);
  const listCreation = useRef(null);
  const base = `/espacios/${spaceId}/deseos`;

  useEffect(() => {
    if (form.errors.list_id) listNotice.current?.focus();
  }, [form.errors.list_id]);

  function openListCreation() {
    form.clearErrors("list_id");
    setCreating(true);
    listCreation.current?.querySelector("input")?.focus();
  }
  function change(key, value) {
    form.setData(key, value);
    form.clearErrors(key);
  }

  function validateList(event) {
    if (form.data.list_id) return true;
    event.preventDefault();
    form.setError(
      "list_id",
      lists.length
        ? "Elige una lista para guardar el deseo."
        : "Crea una lista para poder guardar el deseo.",
    );
    listNotice.current?.focus();
    return false;
  }

  function save(event) {
    event.preventDefault();
    if (form.processing) return;
    if (!validateList(event)) return;
    if (wish) form.patch(`${base}/${wish.id}`, { preserveScroll: true });
    else form.post(base, { preserveScroll: true });
  }

  return (
    <div className={styles.layout}>
      <form key="wish-details" onSubmit={save} className={styles.form}>
        <div className={styles.listChoice}>
          <label htmlFor="wish-list">Lista</label>
          <div className={styles.select}>
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
              <option value="" disabled hidden>
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
          </div>
          {!wish && (
            <button
              ref={createButton}
              type="button"
              onClick={openListCreation}
              aria-expanded={creating}
              aria-haspopup="dialog"
              aria-controls="wish-list-creation"
            >
              Crear lista
            </button>
          )}
          {form.errors.list_id && (
            <p id="wish-list-error" className={styles.listError}>
              {form.errors.list_id}
            </p>
          )}
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
        {form.errors.list_id && (
          <div
            className={styles.notice}
            role="alert"
            tabIndex={-1}
            ref={listNotice}
          >
            <strong>No se ha guardado el deseo</strong>
            <p>{form.errors.list_id}</p>
            <button
              type="button"
              onClick={() => {
                if (lists.length) listControl.current?.focus();
                else openListCreation();
              }}
            >
              {lists.length ? "Elegir lista" : "Crear lista"}
            </button>
          </div>
        )}
        {form.errors.save && <p role="alert">{form.errors.save}</p>}
        <div className={styles.submitActions}>
          <button disabled={form.processing} onClick={validateList}>
            {form.processing
              ? "Guardando…"
              : wish
                ? "Guardar cambios"
                : "Guardar deseo"}
          </button>
          {cancelHref && <Link href={cancelHref}>Cancelar</Link>}
        </div>
      </form>
      {creating && (
        <div ref={listCreation} key="create-wish-list">
          <WishListForm
            key="create-wish-list"
            url={`${base}/listas`}
            onCancel={() => {
              setCreating(false);
              createButton.current?.focus();
            }}
            onCreated={(list) => {
              setLists((current) => [...current, list]);
              form.setData((current) => ({
                ...current,
                list_id: String(list.id),
              }));
              form.clearErrors("list_id");
              setCreating(false);
              listControl.current?.focus();
            }}
          />
        </div>
      )}
    </div>
  );
}
