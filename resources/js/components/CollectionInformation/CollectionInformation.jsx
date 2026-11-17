import styles from "./CollectionInformation.module.css";

export default function CollectionInformation({ poster, children, synopsis }) {
  return (
    <div className={styles.feature}>
      {poster}
      <dl>{children}</dl>
      {synopsis && (
        <section className={styles.overview}>
          <h2>Sinopsis</h2>
          {synopsis.split(/\r?\n(?:[ \t]*\r?\n)+/).map((paragraph, index) => (
            <p key={index}>{paragraph}</p>
          ))}
        </section>
      )}
    </div>
  );
}
