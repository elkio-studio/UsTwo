import { Link } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import '../../../css/global.css';
import styles from './Dashboard.module.css';

export default function Dashboard() {
  return (
    <Journal title="Nuestro cuaderno">
      <nav aria-label="Listas de UsTwo" className={styles.lists}>
        <Link href="/peliculas">Películas vistas</Link>
      </nav>
    </Journal>
  );
}
