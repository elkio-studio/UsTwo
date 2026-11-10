// Página de créditos y atribución del catálogo de películas.

import Journal from '../../components/Journal/Journal';
import '../../../css/global.css';
import styles from './Credits.module.css';

export default function Credits() {
  return (
    <Journal title="Créditos" back={{ href: '/', label: 'Volver al espacio compartido' }}>
      <p className={styles.attribution}>El catálogo y las portadas de películas proceden de <a href="https://www.themoviedb.org">The Movie Database (TMDB)</a>.</p>
      <img src="/tmdb.svg" width="72" alt="TMDB" />
      <p lang="en">This product uses the TMDB API but is not endorsed or certified by TMDB.</p>
    </Journal>
  );
}
