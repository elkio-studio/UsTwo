// Página de cuenta con las credenciales y las estanterías de la sesión.

import { Link, router, useForm, usePage } from '@inertiajs/react';
import Field from '../../components/Field/Field';
import Journal from '../../components/Journal/Journal';
import '../../../css/global.css';
import styles from './Account.module.css';

export default function Account({ spaces }) {
  const { sessionUsername } = usePage().props;
  const form = useForm({ username: sessionUsername, current_password: '', password: '', password_confirmation: '' });

  function leaveShelf(id, name) {
    if (window.confirm(`¿Salir de «${name}»? Necesitarás otra invitación para volver.`)) {
      router.delete(`/espacios/${id}/pertenencia`, { data: { from_account: true }, preserveScroll: true });
    }
  }

  return (
    <Journal className={styles.page} title="Mi cuenta" back={{ href: '/', label: 'Volver a tus estanterías' }}>
      <section className={styles.section}>
        <h2>Editar cuenta</h2>
        <form onSubmit={(event) => {
          event.preventDefault();
          form.patch('/mi/cuenta', { preserveScroll: true, onSuccess: () => form.reset('current_password', 'password', 'password_confirmation') });
        }}>
          <Field label="Nombre de usuario" required maxLength={32} autoComplete="username" value={form.data.username}
            onChange={(event) => form.setData('username', event.target.value)} error={form.errors.username || form.errors.username_key} />
          <Field label="Contraseña actual" type="password" required autoComplete="current-password" value={form.data.current_password}
            onChange={(event) => form.setData('current_password', event.target.value)} error={form.errors.current_password} />
          <Field label="Nueva contraseña (opcional, mínimo 12 caracteres)" type="password" autoComplete="new-password" value={form.data.password}
            onChange={(event) => form.setData('password', event.target.value)} error={form.errors.password} />
          {form.data.password && <Field label="Repite la nueva contraseña" type="password" required autoComplete="new-password"
            value={form.data.password_confirmation} onChange={(event) => form.setData('password_confirmation', event.target.value)} />}
          <button disabled={form.processing}>Guardar cambios</button>
          {form.recentlySuccessful && <p role="status">Cambios guardados.</p>}
        </form>
      </section>
      <section className={styles.section}>
        <h2>Tus estanterías</h2>
        {spaces.length === 0 ? <p>Todavía no perteneces a ninguna estantería.</p> : (
          <ul className={styles.shelves}>
            {spaces.map(({ id, name, slug, isOwner }) => (
              <li key={id}>
                <strong>{name}</strong>
                <small>{isOwner ? 'Propietario' : 'Miembro'}</small>
                <div className={styles.shelfActions}>
                  <Link href={`/${slug}`}>Entrar</Link>
                  {isOwner ? <Link href={`/${slug}?editar=1`}>Editar estantería</Link> : (
                    <button type="button" onClick={() => leaveShelf(id, name)}>Salir de la estantería</button>
                  )}
                </div>
              </li>
            ))}
          </ul>
        )}
        <Link href="/">Ver todas tus estanterías</Link>
      </section>
    </Journal>
  );
}
