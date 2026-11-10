// Estructura compartida de las páginas con cabecera y navegación.

import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import Invitations from '../Invitations/Invitations';
import styles from './Journal.module.css';

export default function Journal({ title, children, back, headerActions, privatePage = true, className }) {
  const journal = useRef(null);
  const { sessionUsername } = usePage().props;

  useEffect(() => {
    if (!privatePage) return;

    function closeOutside(event) {
      const openPanel = journal.current?.querySelector('details[name="ustwo-panels"][open]');
      if (openPanel && !openPanel.contains(event.target)) {
        openPanel.open = false;
      }
    }

    document.addEventListener('click', closeOutside, true);
    return () => document.removeEventListener('click', closeOutside, true);
  }, [privatePage]);

  return (
    <main ref={journal} className={className ? `${styles.journal} ${className}` : styles.journal}>
      <Head title={title} />
      <header className={styles.header}>
        <Link href={privatePage ? '/' : '/acceso'} className={styles.wordmark}>UsTwo</Link>
        {privatePage && <div className={styles.actions}>
          {headerActions}
          <details name="ustwo-panels" className={styles.accountMenu}>
            <summary><span aria-hidden="true">☰</span> @{sessionUsername}</summary>
            <div className={styles.accountActions}>
              <Link href="/mi/cuenta">Editar cuenta</Link>
              <Link href="/salir" method="post" as="button">Cerrar sesión</Link>
            </div>
          </details>
        </div>}
      </header>
      {back && <Link className={styles.back} href={back.href}><span aria-hidden="true">←</span>{back.label}</Link>}
      <h1>{title}</h1>
      {children}
      {privatePage && <Invitations />}
    </main>
  );
}
