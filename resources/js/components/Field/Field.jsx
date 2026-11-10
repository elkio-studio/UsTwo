// Campo de formulario reutilizable con etiqueta y mensaje de error.

import { useId } from 'react';
import styles from './Field.module.css';

export default function Field({ label, error, multiline, ...props }) {
  const id = useId();
  const Input = multiline ? 'textarea' : 'input';

  return (
    <div className={styles.field}>
      <label htmlFor={id}>{label}</label>
      <Input id={id} aria-invalid={!!error} aria-describedby={error ? id + '-error' : undefined} {...props} />
      {error && <span id={id + '-error'} role="alert" className={styles.error}>{error}</span>}
    </div>
  );
}
