// Vista inicial del cajón de Lecturas con sus tres pestañas.

import { useId, useRef, useState } from "react";
import JournalLayout from "../../components/Journal/JournalLayout/JournalLayout";
import "../../../css/global.css";
import styles from "./Readings.module.css";

const tabs = [
  { title: "Libros", empty: "Todavía no hay libros en esta estantería." },
  { title: "Manga", empty: "Todavía no hay manga en esta estantería." },
  { title: "Cómics", empty: "Todavía no hay cómics en esta estantería." },
];

export default function Readings({ spaceSlug }) {
  const [active, setActive] = useState(0);
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
            ref={(button) => { buttons.current[index] = button; }}
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
      {tabs.map(({ title, empty }, index) => (
        <section
          key={title}
          role="tabpanel"
          id={`${id}-panel-${index}`}
          aria-labelledby={`${id}-tab-${index}`}
          tabIndex={0}
          hidden={active !== index}
        >
          <p>{empty}</p>
        </section>
      ))}
    </JournalLayout>
  );
}
