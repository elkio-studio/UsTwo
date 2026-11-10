// Espacio compartido con la estantería y el acceso a las colecciones.

import { useEffect, useRef, useState } from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import Field from '../../components/Field/Field';
import Journal from '../../components/Journal/Journal';
import '../../../css/global.css';
import styles from './Dashboard.module.css';

export default function Dashboard({ spaces, activeSpace, isOwner, outgoing, members, editing }) {
  const [opening, setOpening] = useState(false);
  const navigation = useRef(null);
  const drawerDialog = useRef(null);
  const placement = useForm({ slot: null });

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
    <Journal className={!activeSpace ? styles.index : undefined} title={activeSpace?.name ?? 'Tus estanterías'}
      headerActions={activeSpace && <SpaceMenu key={activeSpace.id} {...{ spaces, activeSpace, isOwner, outgoing, members, editing }} />}>
      {!activeSpace && <>
        <p>{spaces.length > 0 ? 'Elige en qué estantería quieres entrar.' : 'Todavía no tienes estanterías. Crea una o acepta una invitación.'}</p>
        <ul className={styles.choices}>
          {spaces.map(({ id, name, slug }) => (
            <li key={id}><Link href={`/${slug}`}>{name}</Link></li>
          ))}
          <li><AddShelf /></li>
        </ul>
      </>}
      {activeSpace && <>
        <p className={styles.subtitle}>Lo que guardamos juntos</p>
        <nav aria-label="Colecciones de UsTwo" className={styles.shelf}>
          <div className={styles.scene}>
            <span className={styles.candle} aria-hidden="true" />
            <span className={styles.candle} aria-hidden="true" />
            <div className={styles.slots}>
              {Array.from({ length: 8 }, (_, index) => {
                const slot = index + 1;
                return (
                  <div className={styles.slot} key={slot}>
                    {activeSpace.movie_slot === slot ? (
                      <Link href={`/espacios/${activeSpace.id}/peliculas`} className={styles.drawer} data-opening={opening} onClick={openDrawer}>
                        <span>Películas</span>
                      </Link>
                    ) : (
                      <button type="button" className={styles.addDrawer} aria-label={`Añadir cajón en el hueco ${slot}`}
                        onClick={() => {
                          placement.setData('slot', slot);
                          placement.clearErrors();
                          drawerDialog.current.showModal();
                        }} />
                    )}
                  </div>
                );
              })}
            </div>
          </div>
        </nav>
        <dialog ref={drawerDialog} className={styles.drawerDialog} aria-labelledby="drawer-dialog-title">
          <h2 id="drawer-dialog-title">Elige un cajón</h2>
          <p>{activeSpace.movie_slot ? 'Mueve Películas a este hueco. La colección se conserva.' : 'Añade la colección de Películas a este hueco.'}</p>
          {placement.errors.slot && <p role="alert">{placement.errors.slot}</p>}
          <div className={styles.drawerActions}>
            <button type="button" disabled={placement.processing} onClick={() => {
              placement.put(`/espacios/${activeSpace.id}/cajones/peliculas`, {
                preserveScroll: true,
                onSuccess: () => drawerDialog.current?.close(),
              });
            }}>{activeSpace.movie_slot ? 'Mover Películas aquí' : 'Añadir Películas'}</button>
            <button type="button" onClick={() => drawerDialog.current.close()}>Cancelar</button>
          </div>
        </dialog>
      </>}
    </Journal>
  );
}

function AddShelf() {
  const create = useForm({ name: '' });

  return (
    <details open={create.hasErrors || undefined}>
      <summary>+ Añadir estantería</summary>
      <form onSubmit={(event) => {
        event.preventDefault();
        create.post('/espacios');
      }}>
        <Field label="Nombre de la estantería" required maxLength={100} value={create.data.name}
          onChange={(event) => create.setData('name', event.target.value)} error={create.errors.name} />
        <button disabled={create.processing}>Crear estantería</button>
      </form>
    </details>
  );
}

function SpaceMenu({ spaces, activeSpace, isOwner, outgoing, members, editing }) {
  const invite = useForm({ username: '' });
  const rename = useForm({ name: activeSpace.name });
  const spaceUrl = activeSpace ? `/espacios/${activeSpace.id}` : null;

  function deleteSpace() {
    if (window.confirm('¿Borrar esta estantería definitivamente? También se borrarán sus películas y valoraciones.')) {
      router.delete(spaceUrl, { data: { confirmed: true } });
    }
  }

  function leaveSpace() {
    if (window.confirm('¿Salir de esta estantería? Necesitarás otra invitación para volver.')) {
      router.delete(`${spaceUrl}/pertenencia`);
    }
  }

  function removeMember(id, username) {
    if (window.confirm(`¿Retirar el acceso a @${username}?`)) {
      router.delete(`${spaceUrl}/miembros/${id}`, { preserveScroll: true });
    }
  }

  return (
    <details name="ustwo-panels" className={styles.menu} open={editing || undefined}>
      <summary>Estantería</summary>
      <div className={styles.panel}>
        {activeSpace && <label>
          Estantería activa
          <select value={activeSpace.slug} onChange={(event) => router.visit(`/${event.target.value}`)}>
            {spaces.map(({ id, name, slug }) => <option key={id} value={slug}>{name}</option>)}
          </select>
        </label>}
        {activeSpace && !isOwner && (
          <button type="button" onClick={leaveSpace}>Salir de la estantería</button>
        )}
        {isOwner && <details open={editing || rename.hasErrors || invite.hasErrors || undefined}>
          <summary>Editar estantería</summary>
          <div className={styles.management}>
            <form onSubmit={(event) => {
              event.preventDefault();
              rename.patch(spaceUrl, { preserveScroll: true });
            }}>
              <Field label="Nombre" required maxLength={100} value={rename.data.name}
                onChange={(event) => rename.setData('name', event.target.value)} error={rename.errors.name} />
              <button disabled={rename.processing}>Guardar nombre</button>
            </form>
            <form onSubmit={(event) => {
              event.preventDefault();
              invite.post(`${spaceUrl}/invitaciones`, { onSuccess: () => invite.reset() });
            }}>
              <Field label="Invitar por @usuario" required maxLength={32} value={invite.data.username}
                onChange={(event) => invite.setData('username', event.target.value)} error={invite.errors.username} />
              <button disabled={invite.processing}>Invitar</button>
            </form>
          </div>
          <h3>Miembros</h3>
          {members.length > 0 ? <ul aria-label="Miembros de la estantería">
            {members.map(({ id, username }) => <li key={id}>
              <span>@{username}</span><button type="button" onClick={() => removeMember(id, username)}>Retirar acceso</button>
            </li>)}
          </ul> : <p>Todavía no hay otros miembros.</p>}
          {outgoing.length > 0 && <>
          <h3>Invitaciones pendientes</h3>
          <ul aria-label="Invitaciones enviadas">
            {outgoing.map(({ id, username }) => <li key={id}>
              <span>@{username}</span><button type="button" onClick={() => router.delete(`${spaceUrl}/invitaciones/${id}`)}>Retirar</button>
            </li>)}
          </ul></>}
          <button type="button" className={styles.danger} onClick={deleteSpace}>Borrar estantería</button>
        </details>}
      </div>
    </details>
  );
}
