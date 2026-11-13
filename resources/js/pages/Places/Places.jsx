// Vista de Sitios de una estantería.

import JournalLayout from "../../components/Journal/JournalLayout/JournalLayout";
import "../../../css/global.css";

export default function Places({ spaceSlug }) {
  return (
    <JournalLayout
      theme="cinema"
      title="Sitios"
      back={{ href: `/${spaceSlug}`, label: "Volver a la estantería" }}
    />
  );
}
