import { useEffect, useRef, useState } from "react";
import { Link, useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import ReadingSearch from "../ReadingSearch/ReadingSearch";
import ReadingInformation from "../ReadingInformation/ReadingInformation";
import "../../../../css/global.css";
import styles from "./ReadingCreate.module.css";

export default function ReadingCreate({ category, spaceId }) {
  const url = `/espacios/${spaceId}/lecturas`;
  const backUrl = `${url}?category=${category}`;
  const title = { books: "Añadir libro", manga: "Añadir manga", comics: "Añadir cómic" }[category];
  const [manual, setManual] = useState(false);
  const [selected, setSelected] = useState(null);
  const previewHeading = useRef(null);
  const form = useForm({
    category,
    selection: "",
    title: "",
    author: "",
    description: "",
    publication_date: "",
    confirm_title: false,
  });

  useEffect(() => {
    if (selected) previewHeading.current?.focus();
  }, [selected]);

  function changeMode(value) {
    setManual(value);
    setSelected(null);
    form.setData((data) => ({ ...data, selection: "", confirm_title: false }));
    form.clearErrors();
  }

  function changeField(event) {
    const { name, value } = event.target;
    form.setData((data) => ({ ...data, [name]: value, confirm_title: false }));
    form.clearErrors(name, "confirm_title");
  }

  function save(event) {
    event.preventDefault();
    form.post(url, { preserveScroll: true, replace: true });
  }

  return (
    <JournalLayout theme="cinema" title={title} back={{ href: backUrl, label: "Volver a Lecturas" }}>
      {!manual && !selected && (
        <ReadingSearch category={category} url={url} onManual={() => changeMode(true)} onSelect={({ reading, selection }) => {
          setSelected(reading);
          form.setData((data) => ({ ...data, selection, confirm_title: false }));
          form.clearErrors();
        }} />
      )}
      {manual && <button type="button" disabled={form.processing} onClick={() => changeMode(false)}>Volver al catálogo</button>}
      {(manual || selected) && (
        <form className={styles.form} onSubmit={save}>
          {selected ? (
            <div>
              <h3 ref={previewHeading} tabIndex={-1}>Revisar ficha</h3>
              <ReadingInformation reading={selected} />
            </div>
          ) : (
            <>
              <Field label="Título" name="title" required autoFocus maxLength={255} value={form.data.title} onChange={changeField} error={form.errors.title} />
              <Field label="Autoría (opcional)" name="author" maxLength={255} value={form.data.author} onChange={changeField} error={form.errors.author} />
              <Field label="Fecha de publicación (opcional)" name="publication_date" type="date" value={form.data.publication_date} onChange={changeField} error={form.errors.publication_date} />
              <Field label="Descripción (opcional)" name="description" multiline maxLength={20000} value={form.data.description} onChange={changeField} error={form.errors.description} />
            </>
          )}
          {form.errors.selection && <p role="alert">{form.errors.selection}</p>}
          {form.errors.save && <p role="alert">{form.errors.save}</p>}
          {form.errors.confirm_title && (
            <div role="alert">
              <p>{form.errors.confirm_title}</p>
              <label>
                <input type="checkbox" checked={form.data.confirm_title} onChange={(event) => form.setData("confirm_title", event.target.checked)} />
                {" "}Guardar igualmente
              </label>
            </div>
          )}
          <div className={styles.actions}>
            <button disabled={form.processing || (Boolean(form.errors.confirm_title) && !form.data.confirm_title)}>
              {form.processing ? "Guardando…" : "Guardar lectura"}
            </button>
            {selected && <button type="button" disabled={form.processing} onClick={() => changeMode(false)}>Elegir otra ficha</button>}
            <Link href={backUrl}>Cancelar</Link>
          </div>
        </form>
      )}
    </JournalLayout>
  );
}
