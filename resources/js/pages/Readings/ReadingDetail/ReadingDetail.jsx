import CollectionRatingForm from "../../../components/CollectionRatingForm/CollectionRatingForm";
import CollectionReviews from "../../../components/CollectionReviews/CollectionReviews";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import DeleteReading from "../DeleteReading/DeleteReading";
import ReadingMetadataForm from "../ReadingMetadataForm/ReadingMetadataForm";
import ReadingInformation from "../ReadingInformation/ReadingInformation";
import "../../../../css/global.css";
import styles from "./ReadingDetail.module.css";

export default function ReadingDetail({ reading, editableMetadata, people, spaceId, viewerId }) {
  const collectionUrl = `/espacios/${spaceId}/lecturas`;
  const url = `${collectionUrl}/${reading.id}`;
  const ownRating = reading.ratings?.find((rating) => rating.user_id === viewerId);

  return (
    <JournalLayout
      theme="cinema"
      title={reading.title}
      back={{
        href: `${collectionUrl}?category=${reading.category}`,
        label: "Volver a Lecturas",
      }}
    >
      <ReadingInformation reading={reading} />
      <CollectionReviews people={people} ratings={reading.ratings} className={styles.section} />
      <CollectionRatingForm ownRating={ownRating} url={url} className={styles.section} />
      <ReadingMetadataForm reading={reading} editableMetadata={editableMetadata} url={url} className={styles.section} />
      <DeleteReading url={url} className={styles.section} />
    </JournalLayout>
  );
}
