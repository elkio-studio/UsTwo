// Pantalla de inicio de sesión de UsTwo.

import { useForm } from '@inertiajs/react';
import Journal from '../../components/Journal/Journal';
import Field from '../../components/Field/Field';
import '../../../css/global.css';
import styles from './Login.module.css';

export default function Login() {
  const form = useForm({ email: '', password: '' });

  return (
    <Journal title="Entrar en UsTwo" privatePage={false}>
      <p>Vuestro cuaderno privado de películas vistas.</p>
      <form className={styles.form} onSubmit={(event) => {
        event.preventDefault();
        form.post('/acceso', { onFinish: () => form.reset('password') });
      }}>
        <Field label="Correo" type="email" autoComplete="username" required value={form.data.email}
          onChange={(event) => form.setData('email', event.target.value)} error={form.errors.email} />
        <Field label="Contraseña" type="password" autoComplete="current-password" required value={form.data.password}
          onChange={(event) => form.setData('password', event.target.value)} error={form.errors.password} />
        <button disabled={form.processing}>Entrar</button>
      </form>
    </Journal>
  );
}
