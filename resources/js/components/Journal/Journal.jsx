import { Head, Link } from '@inertiajs/react';
import styles from './Journal.module.css';

export default function Journal({ title, children, back, privatePage = true }) {
  return (
    <main className={styles.journal}>
      <Head title={title} />
      <header className={styles.header}>
        <Link href={privatePage ? '/' : '/acceso'} className={styles.wordmark}>UsTwo</Link>
        {privatePage && <Link href="/salir" method="post" as="button">Cerrar sesión</Link>}
      </header>
      {back && <Link href={back.href}>{back.label}</Link>}
      <h1>{title}</h1>
      {children}
    </main>
  );
}
