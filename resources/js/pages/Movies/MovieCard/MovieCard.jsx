// Tarjeta de una película o serie con portada y puntuaciones.

import CollectionCard from "../../../components/CollectionCard/CollectionCard";
import CollectionPoster from "../../../components/CollectionPoster/CollectionPoster";

export default function MovieCard({ movie, people, url }) {
  return (
    <CollectionCard
      title={movie.title}
      href={url + "/" + movie.id}
      poster={<CollectionPoster path={movie.poster_path} title={movie.title} />}
      people={people}
      ratings={movie.ratings}
    />
  );
}
