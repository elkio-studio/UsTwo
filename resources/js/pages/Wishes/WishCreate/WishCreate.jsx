// Página de alta de un deseo con su lista y datos editables.

import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import WishForm from "../WishForm/WishForm";
import "../../../../css/global.css";

export default function WishCreate({ spaceId, lists }) {
  return (
    <JournalLayout
      theme="cinema"
      title="Añadir deseo"
      back={{ href: `/espacios/${spaceId}/deseos`, label: "Volver a Deseos" }}
    >
      <WishForm spaceId={spaceId} lists={lists} />
    </JournalLayout>
  );
}
