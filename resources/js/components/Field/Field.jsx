// Campo de formulario reutilizable con etiqueta y mensaje de error.

import { useId } from 'react';
import styles from './Field.module.css';

export default function Field({ label, error, multiline, action, ...props }) {
  const id = useId();
  const Input = multiline ? 'textarea' : 'input';
  const control = <Input id={id} aria-invalid={!!error} aria-describedby={error ? id + '-error' : undefined} {...props} />;

  return (
    <div className={styles.field}>
      <label htmlFor={id}>{label}</label>
      {action ? <div className={styles.withAction}>{control}{action}</div> : control}
      {error && <span id={id + '-error'} role="alert" className={styles.error}>{error}</span>}
    </div>
  );
}
