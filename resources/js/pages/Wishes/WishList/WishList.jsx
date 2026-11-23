// Lista compartida de deseos con acciones y enlaces externos seguros.

import { useRef } from "react";
import WishActions from "../WishActions/WishActions";
import styles from "./WishList.module.css";

export default function WishList({ list, spaceId }) {
  const heading = useRef(null);
  return (
    <section aria-labelledby="selected-wish-list">
      <h2 id="selected-wish-list" ref={heading} tabIndex={-1}>
        {list.name}
      </h2>
      {list.wishes.length ? (
        <ul className={styles.entries}>
          {list.wishes.map((wish) => (
            <li key={wish.id}>
              <div className={styles.heading}>
                <a href={wish.url} target="_blank" rel="noopener noreferrer">
                  {wish.title}
                </a>
                <WishActions
                  spaceId={spaceId}
                  wish={wish}
                  onDeleted={() => heading.current?.focus()}
                />
              </div>
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
