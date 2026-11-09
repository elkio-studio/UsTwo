import { Head } from '@inertiajs/react';
import RuntimeCheck from '../../components/RuntimeCheck/RuntimeCheck';
import '../../../css/global.css';
import styles from './StackCheck.module.css';

export default function StackCheck({ message }) {
  return (
    <main className={styles.page}>
      <Head title="Comprobación local de UsTwo" />
      <h1>Comprobación del stack</h1>
      <p className={styles.status}>{message}</p>
      <RuntimeCheck />
      <p className={styles.note}>
        Esta página comprueba la instalación local. El producto y su diseño
        siguen pendientes de definición.
      </p>
    </main>
  );
}
