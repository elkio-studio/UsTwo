// Anclaje y foco compartidos por los menús de deseos y listas.

import { useEffect, useId, useLayoutEffect, useRef, useState } from "react";
import styles from "./WishMenu.module.css";

export default function WishMenu({
  label,
  open,
  onOpen,
  onClose,
  processing,
  wide,
  view,
  trigger,
  children,
}) {
  const root = useRef(null);
  const panel = useRef(null);
  const [position, setPosition] = useState(null);
  const id = useId();
  const placed = position !== null;
  useLayoutEffect(() => {
    if (!open) setPosition(null);
  }, [open]);
  useLayoutEffect(() => {
    if (!open) return;
    function place() {
      if (!root.current || !trigger.current || !panel.current) return;
      const origin = root.current.getBoundingClientRect();
      const button = trigger.current.getBoundingClientRect();
      const box = panel.current.getBoundingClientRect();
      const gap = 8;
      const height = document.documentElement.clientHeight;
      const width = document.documentElement.clientWidth;
      const below = Math.max(0, height - button.bottom - gap * 2);
      const above = Math.max(0, button.top - gap * 2);
      const upward = below < panel.current.scrollHeight && above > below;
      const available = Math.max(
        1,
        Math.min(height - gap * 2, upward ? above : below),
      );
      const actualHeight = Math.min(
        panel.current.scrollHeight +
          panel.current.offsetHeight -
          panel.current.clientHeight,
        available,
      );
      const left = Math.max(
        gap,
        Math.min(button.right - box.width, width - box.width - gap),
      );
      const top = Math.max(
        gap,
        Math.min(
          upward ? button.top - actualHeight - gap : button.bottom + gap,
          height - actualHeight - gap,
        ),
      );
      const next = {
        left: left - origin.left,
        top: top - origin.top,
        maxHeight: available,
      };
      setPosition((previous) =>
        previous &&
        previous.left === next.left &&
        previous.top === next.top &&
        previous.maxHeight === next.maxHeight
          ? previous
          : next,
      );
    }
    place();
    const observer = new ResizeObserver(place);
    observer.observe(panel.current);
    observer.observe(root.current);
    const row = root.current.closest("li");
    if (row) observer.observe(row);
    window.addEventListener("resize", place);
    document.addEventListener("scroll", place, true);
    return () => {
      observer.disconnect();
      window.removeEventListener("resize", place);
      document.removeEventListener("scroll", place, true);
    };
  }, [open, view]);

  useEffect(() => {
    if (!open || !placed) return;
    panel.current
      ?.querySelector("[data-initial-focus], a[href], button:not([disabled])")
      ?.focus();
  }, [open, view, placed]);

  useEffect(() => {
    if (!open) return;
    function outside(event) {
      if (!root.current?.contains(event.target)) onClose();
    }
    function escape(event) {
      if (event.key === "Escape") {
        event.preventDefault();
        onClose();
      }
    }
    document.addEventListener("pointerdown", outside);
    document.addEventListener("keydown", escape);
    return () => {
      document.removeEventListener("pointerdown", outside);
      document.removeEventListener("keydown", escape);
    };
  }, [open, onClose]);

  return (
    <div ref={root} className={styles.actions}>
      <button
        ref={trigger}
        type="button"
        aria-label={label}
        aria-expanded={open}
        aria-controls={open ? id : undefined}
        disabled={processing}
        onClick={() => (open ? onClose() : onOpen())}
      >
        ⋯
      </button>
      {open && (
        <div
          id={id}
          ref={panel}
          className={`${styles.panel} ${wide ? styles.confirmation : styles.menu}`}
          style={{ ...position, visibility: position ? "visible" : "hidden" }}
        >
          {children}
        </div>
      )}
    </div>
  );
}
