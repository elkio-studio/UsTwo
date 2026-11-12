// Ficha de película con valoraciones, datos compartidos y opciones de edición.

import collectionUrl from "../../../utils/collectionUrl";
import MovieInformation from "../MovieInformation/MovieInformation";
import MovieReviews from "../MovieReviews/MovieReviews";
import RatingForm from "../RatingForm/RatingForm";
import MovieMetadataForm from "../MovieMetadataForm/MovieMetadataForm";
import DeleteMovie from "../DeleteMovie/DeleteMovie";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import "../../../../css/global.css";
import styles from "./CollectionEntryDetail.module.css";

export default function CollectionEntryDetail({
  movie,
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
      <MovieReviews
        people={people}
        ratings={movie.ratings}
        className={styles.section}
      />
      <RatingForm ownRating={ownRating} url={url} className={styles.section} />
      <MovieMetadataForm movie={movie} url={url} className={styles.section} />
      <DeleteMovie item={item} url={url} className={styles.section} />
    </JournalLayout>
  );
}
