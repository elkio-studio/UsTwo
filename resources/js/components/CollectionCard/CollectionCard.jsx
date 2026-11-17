import { Link } from "@inertiajs/react";
import styles from "./CollectionCard.module.css";

export default function CollectionCard({ title, href, poster, people, ratings = [] }) {
  return (
    <li className={styles.card}>
      <Link className={styles.entry} href={href}>
        {poster}
        <span className={styles.caption}>
          <strong>{title}</strong>
          <span className={styles.scores}>
            {people.map((person) => (
              <span key={person.id}>
                <span>{person.name}</span>
                <b>{ratings.find((rating) => rating.user_id === person.id)?.score ?? "—"}</b>
              </span>
            ))}
          </span>
        </span>
      </Link>
    </li>
  );
}
