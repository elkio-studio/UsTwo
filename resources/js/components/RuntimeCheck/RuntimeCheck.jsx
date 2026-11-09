import { useState } from 'react';
import styles from './RuntimeCheck.module.css';

export default function RuntimeCheck() {
  const [count, setCount] = useState(0);

  return (
    <div className={styles.counter}>
      <output className={styles.status} aria-live="polite">
        Contador: {count}
      </output>
      <button
        className={styles.button}
        type="button"
        onClick={() => setCount((currentCount) => currentCount + 1)}
      >
        Incrementar contador
      </button>
    </div>
  );
}
