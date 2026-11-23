// Acceso a Deseos con una caja fija y una tarjeta que asoma al interactuar.

import { Link } from "@inertiajs/react";
import box from "../../../../images/estanteria/deseos/caja-deseos.webp";
import card from "../../../../images/estanteria/deseos/tarjeta-enlace.webp";
import styles from "./WishBox.module.css";

export default function WishBox({ href }) {
  return (
    <Link href={href} className={styles.link}>
      <span className={styles.box}>
        <img className={styles.back} src={box} alt="" />
        <img className={styles.card} src={card} alt="" />
        <img className={styles.front} src={box} alt="" />
        <span className={styles.label}>Deseos</span>
      </span>
    </Link>
  );
}
