// Puntuaciones y notas visibles de los miembros.

import styles from "./MovieReviews.module.css";

export default function MovieReviews({ people, ratings, className }) {
  return (
    <section className={className}>
      <h2>Vuestras valoraciones</h2>
      <div className={styles.reviews}>
        {people.map((person) => {
          const contribution = ratings.find(
            (entry) => entry.user_id === person.id,
          );
          return (
            <article className={styles.contribution} key={person.id}>
              <h3>{person.name}</h3>
              <p>
                <strong>
                  {contribution?.score == null
                    ? "Sin puntuación"
                    : `${contribution.score} / 100`}
                </strong>
              </p>
              {contribution?.note && <p>{contribution.note}</p>}
            </article>
          );
        })}
      </div>
    </section>
  );
}
