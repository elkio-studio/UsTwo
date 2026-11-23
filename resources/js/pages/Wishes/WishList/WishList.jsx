// Lista compartida de deseos sin imágenes ni acciones de edición.

import styles from "./WishList.module.css";

export default function WishList({ list }) {
  return (
    <section aria-labelledby="selected-wish-list">
      <h2 id="selected-wish-list">{list.name}</h2>
      {list.wishes.length ? (
        <ul className={styles.entries}>
          {list.wishes.map((wish) => (
            <li key={wish.id}>
              <a href={wish.url} target="_blank" rel="noopener noreferrer">
                {wish.title}
              </a>
              <p className={styles.url}>{wish.url}</p>
              {wish.description && (
                <p className={styles.description}>{wish.description}</p>
              )}
              <p>
                {wish.price !== null
                  ? `${wish.price} ${wish.currency}`
                  : "Sin precio indicado"}
              </p>
              <p>Publicado por @{wish.author} · Pendiente</p>
            </li>
          ))}
        </ul>
      ) : (
        <p>Todavía no hay deseos en esta lista.</p>
      )}
    </section>
  );
}
