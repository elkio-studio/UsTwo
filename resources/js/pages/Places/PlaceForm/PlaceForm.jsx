// Formulario de alta de un sitio visitado.

import { useId } from "react";
import Field from "../../../components/Field/Field";

export default function PlaceForm({ form, lists, onSave }) {
  const { data, errors, setData } = form;
  const listId = useId();
  const update = (key) => (event) => setData(key, event.target.value);
  function updateCoordinate(key, value) {
    setData({ ...data, [key]: value, mode: "manual" });
  }

  return (
    <form onSubmit={onSave}>
      <h2>Guardar sitio visitado</h2>
      <Field
        label="Nombre del sitio"
        required={data.mode !== "map"}
        maxLength={255}
        value={data.name}
        onChange={update("name")}
        error={errors.name}
      />
      <Field
        label="Latitud"
        type="number"
        required
        step="any"
        min={-90}
        max={90}
        value={data.latitude}
        onChange={(event) => updateCoordinate("latitude", event.target.value)}
        error={errors.latitude}
      />
      <Field
        label="Longitud"
        type="number"
        required
        step="any"
        min={-180}
        max={180}
        value={data.longitude}
        onChange={(event) => updateCoordinate("longitude", event.target.value)}
        error={errors.longitude}
      />
      <Field
        label="Nota (opcional)"
        multiline
        maxLength={5000}
        value={data.note}
        onChange={update("note")}
        error={errors.note}
      />
      {lists.length ? (
        <p>
          <label htmlFor={listId}>Lista </label>
          <select
            id={listId}
            required
            value={data.list_id}
            onChange={update("list_id")}
            aria-invalid={!!errors.list_id}
            aria-describedby={errors.list_id ? listId + "-error" : undefined}
          >
            <option value="">Elige una lista</option>
            {lists.map((list) => (
              <option key={list.id} value={list.id}>
                {list.emoji ? list.emoji + " " : ""}
                {list.name}
              </option>
            ))}
          </select>
          {errors.list_id && (
            <span id={listId + "-error"} role="alert">
              {errors.list_id}
            </span>
          )}
        </p>
      ) : (
        <>
          <Field
            label="Nombre de la primera lista"
            required
            maxLength={255}
            value={data.list_name}
            onChange={update("list_name")}
            error={errors.list_name}
          />
          <Field
            label="Emoji de la primera lista (opcional)"
            maxLength={32}
            value={data.list_emoji}
            onChange={update("list_emoji")}
            error={errors.list_emoji}
          />
          {errors.list_id && <p role="alert">{errors.list_id}</p>}
        </>
      )}
      {errors.save && <p role="alert">{errors.save}</p>}
      <button disabled={form.processing}>Guardar sitio</button>
    </form>
  );
}
