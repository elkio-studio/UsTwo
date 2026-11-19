// Ficha de película con valoraciones, datos compartidos y opciones de edición.

import collectionUrl from "../../../utils/collectionUrl";
import MovieInformation from "../MovieInformation/MovieInformation";
import CollectionReviews from "../../../components/CollectionReviews/CollectionReviews";
import CollectionRatingForm from "../../../components/CollectionRatingForm/CollectionRatingForm";
import MovieMetadataForm from "../MovieMetadataForm/MovieMetadataForm";
import DeleteMovie from "../DeleteMovie/DeleteMovie";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import "../../../../css/global.css";
import styles from "./CollectionEntryDetail.module.css";

export default function CollectionEntryDetail({
  movie,
  editableMetadata,
  people,
  viewerId,
  spaceId,
  series = false,
}) {
  const item = series ? "serie" : "película";
  const moviesUrl = collectionUrl(spaceId, series);
  const url = `${moviesUrl}/${movie.id}`;
  const ownRating = movie.ratings.find((rating) => rating.user_id === viewerId);

  return (
    <JournalLayout
      theme="cinema"
      title={movie.title}
      back={{ href: moviesUrl, label: "Volver a " + item + "s vistas" }}
    >
      <MovieInformation movie={movie} series={series} />
      <CollectionReviews
        people={people}
        ratings={movie.ratings}
        className={styles.section}
      />
      <CollectionRatingForm ownRating={ownRating} url={url} className={styles.section} />
      <MovieMetadataForm movie={movie} editableMetadata={editableMetadata} url={url} className={styles.section} />
      <DeleteMovie item={item} url={url} className={styles.section} />
    </JournalLayout>
  );
}
