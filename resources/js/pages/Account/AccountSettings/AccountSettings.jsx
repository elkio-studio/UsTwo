// Página de cuenta con las credenciales y las estanterías de la sesión.

import AccountShelves from "../AccountShelves/AccountShelves";
import CredentialsForm from "../CredentialsForm/CredentialsForm";
import JournalLayout from "../../../components/Journal/JournalLayout/JournalLayout";
import "../../../../css/global.css";
import styles from "./AccountSettings.module.css";

export default function AccountSettings({ spaces }) {
  return (
    <JournalLayout
      theme="cinema"
      className={styles.page}
      title="Mi cuenta"
      back={{ href: "/", label: "Volver a tus estanterías" }}
    >
      <CredentialsForm className={styles.section} />
      <AccountShelves spaces={spaces} className={styles.section} />
    </JournalLayout>
  );
}
