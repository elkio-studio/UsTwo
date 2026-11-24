// Lista compartida de deseos con acciones y enlaces externos seguros.

import { useRef } from "react";
import WishRow from "../WishRow/WishRow";
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
            <WishRow
              key={wish.id}
              wish={wish}
              spaceId={spaceId}
              onDeleted={() => heading.current?.focus()}
            />
          ))}
        </ul>
      ) : (
        <p>Todavía no hay deseos en esta lista.</p>
      )}
    </section>
  );
}
