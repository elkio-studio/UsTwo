// Edición de un deseo compartido conservando su autoría y lista de origen.

import JournalLayout from "../../../layouts/JournalLayout/JournalLayout";
import WishForm from "../WishForm/WishForm";
import "../../../../css/global.css";
import styles from "./WishEdit.module.css";

export default function WishEdit({ spaceId, lists, wish }) {
  const back = `/espacios/${spaceId}/deseos?list=${wish.list_id}`;
  return (
    <JournalLayout
      theme="cinema"
      title="Editar deseo"
      className={styles.page}
      back={{ href: back, label: "Volver a Deseos" }}
    >
      <WishForm
        key={wish.id}
        spaceId={spaceId}
        lists={lists}
        wish={wish}
        cancelHref={back}
      />
    </JournalLayout>
  );
}
