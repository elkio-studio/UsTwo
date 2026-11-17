import CollectionRatingForm from "../../../components/CollectionRatingForm/CollectionRatingForm";
import CollectionReviews from "../../../components/CollectionReviews/CollectionReviews";
import Field from "../../../components/Field/Field";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import ReadingInformation from "../ReadingInformation/ReadingInformation";
import "../../../../css/global.css";
import styles from "./ReadingDetail.module.css";

export default function ReadingDetail({ reading, people, spaceId }) {
  return (
    <JournalLayout
      theme="cinema"
      title={reading.title}
      back={{
        href: `/espacios/${spaceId}/lecturas?category=${reading.category}`,
        label: "Volver a Lecturas",
      }}
    >
      <ReadingInformation reading={reading} />
      <CollectionReviews people={people} className={styles.section} />
      <CollectionRatingForm readOnly className={styles.section} />
      <details className={styles.section}>
        <summary>Editar datos comunes</summary>
        <Field label="Título" value={reading.title} disabled />
        <Field label="Autoría" value={reading.author ?? ""} disabled />
        <Field label="Fecha de publicación" value={reading.publication_date ?? ""} disabled />
        <Field label="Descripción" value={reading.description ?? ""} multiline disabled />
        <button type="button" disabled>Guardar datos comunes</button>
      </details>
      <section className={styles.section} aria-label="Borrar lectura">
        <button type="button" disabled>Borrar lectura</button>
      </section>
    </JournalLayout>
  );
}
