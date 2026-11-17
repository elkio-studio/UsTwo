import styles from "./CollectionGrid.module.css";

export default function CollectionGrid({ children }) {
  return <ul className={styles.grid}>{children}</ul>;
}
