// Bandeja persistente de invitaciones del usuario autenticado.

import { router, usePage } from "@inertiajs/react";
import { useLayoutEffect, useRef, useState } from "react";
import styles from "./Invitations.module.css";

export default function Invitations() {
  const { invitations = [], errors } = usePage().props;
  const [position, setPosition] = useState({ side: "left", height: 1 });
  const [floating, setFloating] = useState(null);
  const tray = useRef(null);
  const drag = useRef(null);
  const suppressClick = useRef(false);
  const releasePoint = useRef(null);
  const slide = useRef(null);

  useLayoutEffect(() => {
    const from = releasePoint.current;
    releasePoint.current = null;
    if (!from || window.matchMedia("(prefers-reduced-motion: reduce)").matches)
      return;

    const to = tray.current.getBoundingClientRect();
    slide.current = tray.current.animate(
      [
        { transform: `translate(${from.left - to.left}px, ${from.top - to.top}px)` },
        { transform: "translate(0, 0)" },
      ],
      { duration: 160, easing: "ease-out" },
    );
  }, [position]);

  function startDrag(event) {
    if (!event.isPrimary || event.button !== 0) return;
    const bounds = event.currentTarget.getBoundingClientRect();
    slide.current?.cancel();
    suppressClick.current = false;
    drag.current = {
      id: event.pointerId,
      x: event.clientX,
      y: event.clientY,
      offset: event.clientY - bounds.top,
      offsetX: event.clientX - bounds.left,
      inset: bounds.left < window.innerWidth / 2
        ? bounds.left
        : window.innerWidth - bounds.right,
    };
    event.currentTarget.setPointerCapture(event.pointerId);
  }

  function moveDrag(event) {
    const start = drag.current;
    if (start?.id !== event.pointerId) return;
    if (!suppressClick.current &&
      Math.hypot(event.clientX - start.x, event.clientY - start.y) < 6) return;

    suppressClick.current = true;
    tray.current.open = false;
    setFloating({
      left: event.clientX - start.offsetX,
      top: event.clientY - start.offset,
    });
  }

  function finishDrag(event) {
    const start = drag.current;
    if (start?.id !== event.pointerId || !suppressClick.current) return;
    releasePoint.current = tray.current.getBoundingClientRect();
    const travel = window.innerHeight - event.currentTarget.offsetHeight - start.inset * 2;
    const height = Math.min(1, Math.max(0,
      (event.clientY - start.offset - start.inset) / Math.max(1, travel),
    ));
    setPosition({ side: event.clientX < window.innerWidth / 2 ? "left" : "right", height });
    setFloating(null);
  }

  function moveWithKeyboard(event) {
    const { key } = event;
    if (!["ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown"].includes(key)) return;
    event.preventDefault();
    setPosition(({ side, height }) => ({
      side: key === "ArrowLeft" ? "left" : key === "ArrowRight" ? "right" : side,
      height: key === "ArrowUp" ? Math.max(0, height - 0.1)
        : key === "ArrowDown" ? Math.min(1, height + 0.1) : height,
    }));
  }

  function respond(id, response) {
    router.patch(`/invitaciones/${id}`, { response }, { preserveScroll: true });
  }

  return (
    <details
      ref={tray}
      name="ustwo-panels"
      className={styles.tray}
      data-side={position.side}
      data-panel-direction={position.height < 0.5 ? "down" : "up"}
      style={{
        "--invitation-height": position.height,
        ...(floating && { left: floating.left, top: floating.top, right: "auto" }),
      }}
    >
      <summary
        className={styles.trigger}
        onPointerDown={startDrag}
        onPointerMove={moveDrag}
        onPointerUp={finishDrag}
        onLostPointerCapture={() => {
          drag.current = null;
          setFloating(null);
        }}
        onKeyDown={moveWithKeyboard}
        onClick={(event) => {
          if (event.detail && suppressClick.current) event.preventDefault();
          suppressClick.current = false;
        }}
      >
        <span className={styles.label} aria-live="polite">
          Invitaciones pendientes: {invitations.length}
        </span>
        <span aria-hidden="true">✉</span>
        {invitations.length > 0 && (
          <span className={styles.badge} aria-hidden="true">
            {invitations.length}
          </span>
        )}
      </summary>
      <section className={styles.panel} aria-label="Invitaciones pendientes">
        <strong>Invitaciones pendientes</strong>
        {errors?.invitation && <p role="alert">{errors.invitation}</p>}
        {invitations.length === 0 ? (
          <p>No tienes invitaciones pendientes.</p>
        ) : (
          <ul>
            {invitations.map(({ id, space, inviter }) => (
              <li key={id}>
                <p>
                  <strong>{space}</strong>
                  <br />
                  Te ha invitado @{inviter}
                </p>
                <div className={styles.actions}>
                  <button type="button" onClick={() => respond(id, "accepted")}>
                    Aceptar
                  </button>
                  <button type="button" onClick={() => respond(id, "rejected")}>
                    Rechazar
                  </button>
                </div>
              </li>
            ))}
          </ul>
        )}
      </section>
    </details>
  );
}
