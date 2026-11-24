// Colección de deseos de la estantería y selección de lista.

import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import { Link } from "@inertiajs/react";
import { useRef } from "react";
import WishList from "../WishList/WishList";
import WishListActions from "../WishListActions/WishListActions";
import "../../../../css/global.css";
import styles from "./WishesView.module.css";

export default function WishesView({
  spaceId,
  spaceSlug,
  lists,
  selectedListId,
}) {
  const selected = lists.find((list) => list.id === selectedListId);
  const navigation = useRef(null);
  const addLink = useRef(null);
  function focusAfterDelete() {
    requestAnimationFrame(() => {
      const target =
        navigation.current?.querySelector('[aria-current="page"]') ??
        addLink.current;
      target?.focus();
    });
  }
  return (
    <JournalLayout
      theme="cinema"
      title="Deseos"
      back={{ href: "/" + spaceSlug, label: "Volver a la estantería" }}
    >
      <Link
        ref={addLink}
        href={`/espacios/${spaceId}/deseos/nuevo`}
        className={styles.add}
      >
        Añadir enlace
      </Link>
      {lists.length > 0 && (
        <nav
          ref={navigation}
          aria-label="Listas de deseos"
          className={styles.lists}
        >
          <div className={styles.listChoices}>
            {lists.map((list) => (
              <Link
                key={list.id}
                href={`/espacios/${spaceId}/deseos?list=${list.id}`}
                aria-current={selectedListId === list.id ? "page" : undefined}
              >
                {list.name}
              </Link>
            ))}
          </div>
          {selected && (
            <WishListActions
              key={selected.id}
              list={selected}
              spaceId={spaceId}
              selectedListId={selectedListId}
              onDeleted={focusAfterDelete}
            />
          )}
        </nav>
      )}
      {selected ? (
        <WishList list={selected} spaceId={spaceId} />
      ) : (
        <section className={styles.empty} aria-label="Tu colección de deseos">
          <h2>Todavía no hay deseos en esta estantería</h2>
          <p>
            Aquí podrás reunir los enlaces de las cosas que te gustaría comprar.
          </p>
          <p>Crea tu primera lista al añadir un enlace.</p>
        </section>
      )}
    </JournalLayout>
  );
}
