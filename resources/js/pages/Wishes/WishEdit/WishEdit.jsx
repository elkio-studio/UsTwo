// Edición de un deseo compartido conservando su autoría y lista de origen.

import { Link } from "@inertiajs/react";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import WishForm from "../WishForm/WishForm";
import "../../../../css/global.css";

export default function WishEdit({ spaceId, lists, wish }) {
  const back = `/espacios/${spaceId}/deseos?list=${wish.list_id}`;
  return (
    <JournalLayout
      theme="cinema"
      title="Editar deseo"
      back={{ href: back, label: "Volver a Deseos" }}
    >
      <WishForm key={wish.id} spaceId={spaceId} lists={lists} wish={wish} />
      <Link href={back}>Cancelar</Link>
    </JournalLayout>
  );
}
