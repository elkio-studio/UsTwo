import { useEffect, useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import '../../../css/global.css';
import styles from './Dashboard.module.css';

export default function Dashboard() {
  const [opening, setOpening] = useState(false);
  const navigation = useRef(null);

  useEffect(() => () => window.clearTimeout(navigation.current), []);

  function openDrawer(event) {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey
      || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    event.preventDefault();
    if (navigation.current !== null) return;

    const destination = event.currentTarget.href;
    setOpening(true);
    navigation.current = window.setTimeout(() => {
      router.visit(destination, { onFinish: () => {
        navigation.current = null;
        setOpening(false);
      } });
    }, 220);
  }

  return (
    <Journal title="Nuestra estantería">
      <p className={styles.subtitle}>Lo que guardamos juntos</p>
      <nav aria-label="Colecciones de UsTwo" className={styles.shelf}>
        <span className={styles.candle} aria-hidden="true" />
        <span className={styles.candle} aria-hidden="true" />
        <Link href="/peliculas" className={styles.drawer} data-opening={opening} onClick={openDrawer}>
          <span>Películas</span>
        </Link>
      </nav>
    </Journal>
  );
}
