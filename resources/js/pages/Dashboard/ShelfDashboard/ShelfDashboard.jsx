// Espacio compartido con la estantería y el acceso a las colecciones.

import Shelf from "../Shelf/Shelf";
import ShelfPicker from "../ShelfList/ShelfPicker/ShelfPicker";
import ShelfManagementMenu from "../SpaceMenu/ShelfManagementMenu/ShelfManagementMenu";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import "../../../../css/global.css";
import styles from "./ShelfDashboard.module.css";

export default function ShelfDashboard({
  spaces,
  activeSpace,
  isOwner,
  outgoing,
  members,
  editing,
}) {
  return (
    <JournalLayout
      className={!activeSpace ? styles.index : undefined}
      theme={activeSpace ? "shelf" : "shelf-index"}
      title={activeSpace?.name ?? "Tus estanterías"}
      headerActions={
        activeSpace && (
          <ShelfManagementMenu
            key={activeSpace.id}
            {...{ spaces, activeSpace, isOwner, outgoing, members, editing }}
          />
        )
      }
    >
      {activeSpace ? (
        <Shelf activeSpace={activeSpace} />
      ) : (
        <ShelfPicker spaces={spaces} />
      )}
    </JournalLayout>
  );
}
