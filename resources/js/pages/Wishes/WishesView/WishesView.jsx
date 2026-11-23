// Vista inicial del cajón de Deseos, pendiente del alta de enlaces.

import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import "../../../../css/global.css";
import styles from "./WishesView.module.css";

export default function WishesView({ spaceSlug }) {
  return (
    <JournalLayout
      theme="cinema"
      title="Deseos"
      back={{ href: "/" + spaceSlug, label: "Volver a la estantería" }}
    >
      <section className={styles.empty} aria-label="Tu colección de deseos">
        <h2>Todavía no hay deseos en esta estantería</h2>
        <p>
          Aquí podrás reunir los enlaces de las cosas que te gustaría comprar.
        </p>
        <button type="button" disabled aria-describedby="wish-create-notice">
          Añadir enlace
        </button>
        <p id="wish-create-notice">
          Podrás añadir enlaces en una próxima actualización.
        </p>
      </section>
    </JournalLayout>
  );
}
