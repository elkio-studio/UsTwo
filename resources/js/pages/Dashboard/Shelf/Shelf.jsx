// Escena de la estantería, apertura y colocación de cajones.

import { useEffect, useRef, useState } from "react";
import { Link, router, useForm } from "@inertiajs/react";
import styles from "./Shelf.module.css";
import GlobeSurface from "../GlobeSurface/GlobeSurface";
import placesGlobe from "../../../../images/estanteria/globo-sitios.webp";
import woodenDrawer from "../../../../images/estanteria/cajon-madera.webp";

const drawers = [
  {
    label: "Películas",
    path: "peliculas",
    position: "movie_slot",
    image: woodenDrawer,
  },
  {
    label: "Series",
    path: "series",
    position: "series_slot",
    image: woodenDrawer,
  },
  {
    label: "Sitios",
    path: "sitios",
    position: "places_slot",
    image: placesGlobe,
  },
];

export default function Shelf({ activeSpace }) {
  const [opening, setOpening] = useState(false);
  const [globeHovered, setGlobeHovered] = useState(false);
  const navigation = useRef(null);
  const drawerDialog = useRef(null);
  const placement = useForm({ slot: null });

  useEffect(() => () => window.clearTimeout(navigation.current), []);

  function openDrawer(event) {
    if (
      event.defaultPrevented ||
      event.button !== 0 ||
      event.metaKey ||
      event.ctrlKey ||
      event.shiftKey ||
      event.altKey ||
      window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ) {
      return;
    }

    event.preventDefault();
    if (navigation.current !== null) return;

    const destination = event.currentTarget.href;
    setOpening(new URL(destination).pathname);
    navigation.current = window.setTimeout(() => {
      router.visit(destination, {
        onFinish: () => {
          navigation.current = null;
          setOpening(false);
        },
      });
    }, 220);
  }

  return (
    <>
      <p className={styles.subtitle}>Lo que guardamos juntos</p>
      <nav aria-label="Colecciones de UsTwo" className={styles.shelf}>
        <div className={styles.scene}>
          <span className={styles.candle} aria-hidden="true" />
          <span className={styles.candle} aria-hidden="true" />
          <div className={styles.slots}>
            {Array.from({ length: 8 }, (_, index) => {
              const slot = index + 1;
              const drawer = drawers.find(
                ({ position }) => activeSpace[position] === slot,
              );
              return (
                <div className={styles.slot} key={slot}>
                  {drawer ? (
                    <Link
                      href={`/espacios/${activeSpace.id}/${drawer.path}`}
                      className={
                        drawer.path === "sitios"
                          ? styles.globeDrawer
                          : styles.drawer
                      }
                      data-opening={
                        opening === `/espacios/${activeSpace.id}/${drawer.path}`
                      }
                      onClick={openDrawer}
                      onMouseEnter={
                        drawer.path === "sitios"
                          ? () => setGlobeHovered(true)
                          : undefined
                      }
                      onMouseLeave={
                        drawer.path === "sitios"
                          ? () => setGlobeHovered(false)
                          : undefined
                      }
                    >
                      <span
                        className={
                          drawer.path === "sitios"
                            ? styles.globe
                            : styles.drawerFront
                        }
                      >
                        <img src={drawer.image} alt="" />
                        {drawer.path === "sitios" && (
                          <GlobeSurface
                            spinning={globeHovered}
                            palette="color"
                          />
                        )}
                        <span className={styles.drawerLabel}>
                          {drawer.label}
                        </span>
                      </span>
                    </Link>
                  ) : (
                    <button
                      type="button"
                      className={styles.addDrawer}
                      aria-label={`Añadir cajón en el hueco ${slot}`}
                      onClick={() => {
                        placement.setData("slot", slot);
                        placement.clearErrors();
                        drawerDialog.current.showModal();
                      }}
                    />
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </nav>
      <dialog
        ref={drawerDialog}
        className={styles.drawerDialog}
        aria-labelledby="drawer-dialog-title"
      >
        <h2 id="drawer-dialog-title">Elige un cajón</h2>
        <p>Si mueves un cajón, su colección se conserva.</p>
        {placement.errors.slot && <p role="alert">{placement.errors.slot}</p>}
        <div className={styles.drawerActions}>
          {drawers.map(({ label, path, position }) => (
            <button
              key={path}
              type="button"
              disabled={placement.processing}
              onClick={() => {
                placement.put(`/espacios/${activeSpace.id}/cajones/${path}`, {
                  preserveScroll: true,
                  onSuccess: () => drawerDialog.current?.close(),
                });
              }}
            >
              {activeSpace[position]
                ? `Mover ${label} aquí`
                : `Añadir ${label}`}
            </button>
          ))}
          <button type="button" onClick={() => drawerDialog.current.close()}>
            Cancelar
          </button>
        </div>
      </dialog>
    </>
  );
}
