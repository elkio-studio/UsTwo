// Estructura compartida de las páginas con cabecera y navegación.

import { Head, Link } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import Invitations from "../../Invitations/Invitations";
import AccountMenu from "../AccountMenu/AccountMenu";
import styles from "./JournalLayout.module.css";

export default function JournalLayout({
  title,
  children,
  back,
  headerActions,
  privatePage = true,
  className,
  theme,
}) {
  const journal = useRef(null);

  useEffect(() => {
    if (!privatePage) return;

    function closeOutside(event) {
      const openPanel = journal.current?.querySelector(
        'details[name="ustwo-panels"][open]',
      );
      if (openPanel && !openPanel.contains(event.target)) {
        openPanel.open = false;
      }
    }

    document.addEventListener("click", closeOutside, true);
    return () => document.removeEventListener("click", closeOutside, true);
  }, [privatePage]);

  return (
    <main
      ref={journal}
      data-theme={theme}
      className={className ? `${styles.journal} ${className}` : styles.journal}
    >
      <Head title={title} />
      <header className={styles.header}>
        <Link href={privatePage ? "/" : "/acceso"} className={styles.wordmark}>
          UsTwo
        </Link>
        {privatePage && (
          <div className={styles.actions}>
            {headerActions}
            <AccountMenu />
          </div>
        )}
      </header>
      {back && (
        <Link className={styles.back} href={back.href}>
          <span aria-hidden="true">←</span>
          {back.label}
        </Link>
      )}
      <h1>{title}</h1>
      {children}
      {privatePage && <Invitations />}
    </main>
  );
}
