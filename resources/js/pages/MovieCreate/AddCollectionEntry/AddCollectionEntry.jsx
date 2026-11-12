// Página para añadir una película desde el catálogo o de forma manual.

import collectionUrl from "../../../utils/collectionUrl";
import { useState } from "react";
import { useForm } from "@inertiajs/react";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import CatalogSearchForm from "../CatalogSearch/CatalogSearchForm/CatalogSearchForm";
import CollectionEntryEditor from "../MovieEditor/CollectionEntryEditor/CollectionEntryEditor";
import "../../../../css/global.css";

export default function AddCollectionEntry({
  candidates,
  spaceId,
  series = false,
}) {
  const [manual, setManual] = useState(false);
  const [selected, setSelected] = useState(null);
  const form = useForm({ title: "", release_year: "", watched_on: "" });
  const matches = form.errors.match ? candidates : [];

  const item = series ? "serie" : "película";
  const moviesUrl = collectionUrl(spaceId, series);

  function save(extra = {}, movie = selected) {
    form.transform((data) => ({
      ...data,
      tmdb_id: manual ? null : movie.tmdb_id,
      watched_on: manual ? data.watched_on : null,
      ...extra,
    }));
    form.post(moviesUrl, { preserveScroll: true, replace: true });
  }

  return (
    <JournalLayout
      theme="cinema"
      title={`Añadir ${item}`}
      back={{ href: moviesUrl, label: `Volver a ${item}s vistas` }}
    >
      <CatalogSearchForm
        {...{ manual, moviesUrl, item, selected }}
        hasMatches={matches.length > 0}
        error={form.errors.catalog || form.errors.save || form.errors.match}
        processing={form.processing}
        onSearch={() => {
          setSelected(null);
          form.clearErrors();
        }}
        onSelect={(movie) => {
          setSelected(movie);
          form.clearErrors();
          save({}, movie);
        }}
        onManual={() => {
          setManual(true);
          form.clearErrors();
        }}
      />
      {manual && (
        <button
          type="button"
          disabled={form.processing}
          onClick={() => {
            setManual(false);
            setSelected(null);
            form.clearErrors();
          }}
        >
          Volver al catálogo
        </button>
      )}
      {(manual || (selected && matches.length > 0)) && (
        <CollectionEntryEditor
          {...{ form, manual, selected, matches, item }}
          onSave={save}
          onBack={() => {
            setSelected(null);
            form.clearErrors();
          }}
        />
      )}
    </JournalLayout>
  );
}
