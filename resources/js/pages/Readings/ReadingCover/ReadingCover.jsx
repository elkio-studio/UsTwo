import { useState } from "react";
import styles from "./ReadingCover.module.css";

export default function ReadingCover({ title, url, eager = false }) {
  const [failedUrl, setFailedUrl] = useState(null);

  return (
    <div data-collection-poster className={styles.cover} aria-hidden="true">
      {url && url !== failedUrl ? (
        <img src={url} alt="" loading={eager ? "eager" : "lazy"} decoding="async" onError={() => setFailedUrl(url)} />
      ) : (
        <span>{title}</span>
      )}
    </div>
  );
}
