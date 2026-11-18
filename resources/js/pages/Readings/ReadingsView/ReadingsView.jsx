// Vista inicial del cajón de Lecturas con sus tres pestañas.

import { useId, useRef } from "react";
import { Link, useRemember } from "@inertiajs/react";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import CollectionGrid from "../../../components/CollectionGrid/CollectionGrid";
import CollectionCard from "../../../components/CollectionCard/CollectionCard";
import ReadingCover from "../ReadingCover/ReadingCover";
import "../../../../css/global.css";
import styles from "./ReadingsView.module.css";

const tabs = [
  { category: "books", title: "Libros", empty: "Todavía no hay libros en esta estantería." },
  { category: "manga", title: "Manga", empty: "Todavía no hay manga en esta estantería." },
  { category: "comics", title: "Cómics", empty: "Todavía no hay cómics en esta estantería." },
];

export default function ReadingsView({ spaceId, spaceSlug, readings, people, activeCategory }) {
  const [active, setActive] = useRemember(
    Math.max(0, tabs.findIndex((tab) => tab.category === activeCategory)),
    `Readings:${spaceId}`,
  );
  const url = `/espacios/${spaceId}/lecturas`;
  const id = useId();
  const buttons = useRef([]);

  function navigate(event, index) {
    const destinations = {
      ArrowLeft: (index + tabs.length - 1) % tabs.length,
      ArrowRight: (index + 1) % tabs.length,
      Home: 0,
      End: tabs.length - 1,
    };
    if (!Object.hasOwn(destinations, event.key)) return;
    event.preventDefault();
    const next = destinations[event.key];
    setActive(next);
    buttons.current[next]?.focus();
  }

  return (
    <JournalLayout
      theme="cinema"
      title="Lecturas"
      back={{ href: "/" + spaceSlug, label: "Volver a la estantería" }}
    >
      <div className={styles.tabs} role="tablist" aria-label="Tipos de lectura">
        {tabs.map(({ title }, index) => (
          <button
            key={title}
            ref={(button) => {
              buttons.current[index] = button;
            }}
            type="button"
            role="tab"
            id={`${id}-tab-${index}`}
            aria-selected={active === index}
            aria-controls={`${id}-panel-${index}`}
            tabIndex={active === index ? 0 : -1}
            onClick={() => setActive(index)}
            onKeyDown={(event) => navigate(event, index)}
          >
            {title}
          </button>
        ))}
      </div>
      {tabs.map(({ category, title, empty }, index) => {
        const entries = readings.filter((reading) => reading.category === category);
        return (
          <section
            key={category}
            role="tabpanel"
            id={`${id}-panel-${index}`}
            aria-labelledby={`${id}-tab-${index}`}
            tabIndex={0}
            hidden={active !== index}
          >
            <Link className={styles.add} href={`${url}/nueva/${category}`}>
              {{ books: "Añadir libro", manga: "Añadir manga", comics: "Añadir cómic" }[category]}
            </Link>
            {entries.length ? (
              <CollectionGrid>
                {entries.map((reading) => (
                  <CollectionCard
                    key={reading.id}
                    title={reading.title}
                    href={`${url}/${reading.id}`}
                    poster={<ReadingCover title={reading.title} url={reading.cover_url} />}
                    people={people}
                    ratings={reading.ratings}
                  />
                ))}
              </CollectionGrid>
            ) : <p className={styles.empty}>{empty}</p>}
          </section>
        );
      })}
    </JournalLayout>
  );
}
