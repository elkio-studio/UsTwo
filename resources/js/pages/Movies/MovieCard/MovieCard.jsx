// Tarjeta de una película o serie con portada y puntuaciones.

import { Link } from "@inertiajs/react";
import CollectionPoster from "../../../components/CollectionPoster/CollectionPoster";
import styles from "./MovieCard.module.css";

export default function MovieCard({ movie, people, url }) {
  return (
    <li className={styles.card}>
      <Link className={styles.entry} href={`${url}/${movie.id}`}>
        <CollectionPoster path={movie.poster_path} title={movie.title} />
        <span className={styles.caption}>
          <strong>{movie.title}</strong>
          <span className={styles.scores}>
            {people.map((person) => (
              <span key={person.id}>
                <span>{person.name}</span>
                <b>
                  {movie.ratings.find((rating) => rating.user_id === person.id)
                    ?.score ?? "—"}
                </b>
              </span>
            ))}
          </span>
        </span>
      </Link>
    </li>
  );
}
