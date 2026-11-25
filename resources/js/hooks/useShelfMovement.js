import { useEffect, useRef, useState } from "react";
import { router } from "@inertiajs/react";
import {
  nextSlot,
  positionsFor,
  releaseAction,
  slotAt,
} from "../utils/shelfMovement";

const DISTANCE = 8;
const HOLD = 450;

export default function useShelfMovement(activeSpace, drawers, blocked) {
  const root = useRef(null);
  const current = useRef(null);
  const pending = useRef(null);
  const timer = useRef(null);
  const ghost = useRef(null);
  const suppress = useRef(null);
  const latest = useRef({ activeSpace, blocked });
  latest.current = { activeSpace, blocked };
  const [movement, setMovement] = useState(null);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");

  const update = (value) => {
    current.current = value;
    setMovement(value);
  };
  function clearPending() {
    clearTimeout(timer.current);
    timer.current = null;
    pending.current = null;
  }
  function removeGhost() {
    ghost.current?.remove();
    ghost.current = null;
  }
  function focusDrawer(path) {
    requestAnimationFrame(() =>
      root.current?.querySelector(`[data-drawer="${path}"] a`)?.focus(),
    );
  }
  function cancel(focus = true) {
    if (current.current?.mode === "saving") return;
    const path = current.current?.path;
    if (path) suppress.current = true;
    clearPending();
    removeGhost();
    update(null);
    if (path) {
      setMessage("Movimiento cancelado.");
      if (focus) focusDrawer(path);
    }
  }
  function rectangles() {
    return [...(root.current?.querySelectorAll("[data-slot]") ?? [])].map(
      (node) => {
        const box = node.getBoundingClientRect();
        return {
          slot: Number(node.dataset.slot),
          left: box.left,
          right: box.right,
          top: box.top,
          bottom: box.bottom,
        };
      },
    );
  }
  function describe(slot, state = current.current) {
    if (!slot || !state) return "Fuera de la estantería. Suelta para cancelar.";
    if (slot === state.origin)
      return "Hueco de origen. Confirma para cancelar.";
    const other = drawers.find(
      ({ position }) => state.expected[position] === slot,
    );
    return other
      ? `Intercambiar con ${other.label}.`
      : `Mover a hueco ${slot}.`;
  }
  function pick(path, origin, mode, point) {
    const state = {
      path,
      origin,
      target: origin,
      mode,
      expected: positionsFor(latest.current.activeSpace, drawers),
    };
    update(state);
    setError("");
    setMessage(
      `${drawers.find((drawer) => drawer.path === path).label} recogido. Elige destino o cancela.`,
    );
    if (point) showGhost(point);
  }
  function showGhost(point) {
    if (!ghost.current) {
      const source = root.current?.querySelector(
        `[data-drawer="${current.current.path}"]`,
      );
      if (!source) return;
      const box = source.getBoundingClientRect();
      const clone = source.cloneNode(true);
      // Canvas no se clona: convertir su imagen actual sin crear otra animación.
      const originals = source.querySelectorAll("canvas");
      clone.querySelectorAll("canvas").forEach((canvas, index) => {
        try {
          const img = document.createElement("img");
          img.src = originals[index].toDataURL();
          img.style.cssText = canvas.style.cssText;
          img.className = canvas.className;
          canvas.replaceWith(img);
        } catch {
          canvas.remove();
        }
      });
      clone.removeAttribute("data-slot");
      clone.removeAttribute("data-drawer");
      clone
        .querySelectorAll("[id]")
        .forEach((node) => node.removeAttribute("id"));
      clone.setAttribute("aria-hidden", "true");
      clone.inert = true;
      clone.dataset.shelfGhost = "true";
      Object.assign(clone.style, {
        position: "fixed",
        width: `${box.width}px`,
        height: `${box.height}px`,
        margin: "0",
        zIndex: "1000",
        pointerEvents: "none",
        opacity: ".8",
        transform: "translate(-50%, -50%)",
      });
      root.current.append(clone);
      ghost.current = clone;
    }
    Object.assign(ghost.current.style, {
      left: `${point.x}px`,
      top: `${point.y}px`,
    });
  }
  function move(point) {
    const state = current.current;
    if (!state || state.mode === "saving") return;
    const target = slotAt(rectangles(), point.x, point.y);
    update({ ...state, mode: "dragging", target });
    showGhost(point);
    if (target !== state.target) setMessage(describe(target));
  }
  function save(target) {
    const state = current.current;
    if (!state || state.mode === "saving") return;
    if (!target || target === state.origin) {
      cancel();
      return;
    }
    removeGhost();
    clearPending();
    update({ ...state, target, mode: "saving" });
    setMessage("Guardando posiciones…");
    router.put(
      `/espacios/${latest.current.activeSpace.id}/cajones/mover`,
      {
        drawer: state.path,
        slot: target,
        expected_positions: state.expected,
      },
      {
        preserveScroll: true,
        onSuccess: () => {
          update(null);
          setMessage("Posiciones guardadas.");
          focusDrawer(state.path);
        },
        onError: (errors) => {
          update(null);
          setError(
            errors.movement ??
              Object.values(errors)[0] ??
              "No se pudo mover el cajón.",
          );
          router.reload({
            only: ["activeSpace"],
            onFinish: () => focusDrawer(state.path),
          });
        },
        onCancel: () => {
          update(null);
          setError("No se completó el movimiento. Revisa la distribución.");
        },
        onFinish: () => {
          if (current.current?.mode === "saving") {
            update(null);
            setError(
              "No se pudo confirmar el guardado. Recarga antes de reintentar.",
            );
          }
        },
      },
    );
  }

  useEffect(() => {
    const element = root.current;
    function slotNode(target) {
      return target instanceof Element ? target.closest("[data-slot]") : null;
    }
    function point(event) {
      return { x: event.clientX, y: event.clientY };
    }
    function startMouse(event) {
      // Un clic físico posterior ya no pertenece al gesto anterior.
      if (event.pointerType !== "mouse") return;
      suppress.current = null;
      if (
        current.current ||
        latest.current.blocked ||
        event.button !== 0 ||
        event.ctrlKey ||
        event.metaKey ||
        event.shiftKey ||
        event.altKey
      )
        return;
      const node = slotNode(event.target);
      if (!node?.dataset.drawer) return;
      pending.current = {
        path: node.dataset.drawer,
        origin: Number(node.dataset.slot),
        start: point(event),
        id: event.pointerId,
        input: "mouse",
      };
    }
    function mouseMove(event) {
      if (event.pointerType !== "mouse") return;
      const candidate = pending.current;
      if (
        !candidate ||
        candidate.input !== "mouse" ||
        candidate.id !== event.pointerId
      )
        return;
      if (
        !current.current &&
        Math.hypot(
          event.clientX - candidate.start.x,
          event.clientY - candidate.start.y,
        ) >= DISTANCE
      ) {
        pick(candidate.path, candidate.origin, "dragging", point(event));
      }
      if (current.current) {
        event.preventDefault();
        move(point(event));
      }
    }
    function mouseUp(event) {
      if (event.pointerType !== "mouse" || pending.current?.input !== "mouse")
        return;
      if (current.current) {
        suppress.current = true;
        save(slotAt(rectangles(), event.clientX, event.clientY));
      }
      clearPending();
    }
    function touchStart(event) {
      suppress.current = null;
      if (event.touches.length !== 1) {
        cancel(false);
        return;
      }
      if (current.current || latest.current.blocked) return;
      const node = slotNode(event.target);
      if (!node?.dataset.drawer) return;
      const touch = event.touches[0];
      const candidate = {
        path: node.dataset.drawer,
        origin: Number(node.dataset.slot),
        start: point(touch),
        id: touch.identifier,
        input: "touch",
      };
      pending.current = candidate;
      timer.current = setTimeout(() => {
        if (pending.current === candidate)
          pick(candidate.path, candidate.origin, "picked", candidate.start);
      }, HOLD);
    }
    function touchMove(event) {
      const candidate = pending.current;
      if (!candidate || candidate.input !== "touch") return;
      if (event.touches.length !== 1) {
        cancel(false);
        return;
      }
      const touch = [...event.touches].find(
        ({ identifier }) => identifier === candidate.id,
      );
      if (!touch) return;
      const distance = Math.hypot(
        touch.clientX - candidate.start.x,
        touch.clientY - candidate.start.y,
      );
      if (!current.current) {
        if (distance >= DISTANCE) clearPending();
        return;
      }
      if (event.cancelable) event.preventDefault();
      else {
        cancel(false);
        return;
      }
      if (distance >= DISTANCE || current.current.mode === "dragging")
        move(point(touch));
    }
    function touchEnd(event) {
      if (pending.current?.input !== "touch") return;
      const candidate = pending.current;
      const touch = [...event.changedTouches].find(
        ({ identifier }) => identifier === candidate.id,
      );
      if (!touch) return;
      const state = current.current;
      clearPending();
      if (!state) return;
      suppress.current = true;
      const target = slotAt(rectangles(), touch.clientX, touch.clientY);
      if (releaseAction(state.mode, state.origin, target) === "wait") {
        removeGhost();
        update({ ...state, mode: "waiting" });
        setMessage(
          "Cajón recogido. Toca un destino; para cancelar, toca el cajón de origen.",
        );
      } else save(target);
    }
    function click(event) {
      if (suppress.current) {
        event.preventDefault();
        event.stopPropagation();
        suppress.current = null;
        return;
      }
      if (!current.current) return;
      event.preventDefault();
      event.stopPropagation();
      if (current.current.mode !== "saving")
        save(Number(slotNode(event.target)?.dataset.slot) || null);
    }
    function key(event) {
      if (event.key === "Escape") {
        if (current.current) {
          event.preventDefault();
          cancel();
        } else clearPending();
        return;
      }
      if (!element.contains(event.target)) return;
      if (!current.current && event.key === "Enter") suppress.current = null;
      if (current.current) {
        if (
          [
            " ",
            "Enter",
            "ArrowLeft",
            "ArrowRight",
            "ArrowUp",
            "ArrowDown",
          ].includes(event.key)
        ) {
          event.preventDefault();
          event.stopPropagation();
          const state = current.current;
          if (state.mode === "saving") return;
          if (event.key.startsWith("Arrow")) {
            const target = nextSlot(
              rectangles(),
              state.target ?? state.origin,
              event.key,
            );
            update({ ...state, target });
            setMessage(describe(target));
            root.current.querySelector(`[data-slot="${target}"]`)?.focus();
          } else if (event.key === "Enter") save(state.target);
        }
        return;
      }
      const node = slotNode(event.target);
      if (
        event.key === " " &&
        node?.dataset.drawer &&
        !latest.current.blocked
      ) {
        event.preventDefault();
        event.stopPropagation();
        clearPending();
        suppress.current = null;
        pick(node.dataset.drawer, Number(node.dataset.slot), "keyboard");
      }
    }
    function abort() {
      cancel(false);
    }
    function leaveWindow(event) {
      if (
        event.pointerType === "mouse" &&
        event.relatedTarget === null &&
        pending.current?.input === "mouse"
      )
        cancel(false);
    }
    function context(event) {
      if (current.current) event.preventDefault();
    }
    function drag(event) {
      if (slotNode(event.target)) event.preventDefault();
    }
    element.addEventListener("pointerdown", startMouse);
    window.addEventListener("pointermove", mouseMove, { passive: false });
    window.addEventListener("pointerup", mouseUp);
    window.addEventListener("pointercancel", abort);
    element.addEventListener("touchstart", touchStart, { passive: true });
    element.addEventListener("touchmove", touchMove, { passive: false });
    element.addEventListener("touchend", touchEnd);
    element.addEventListener("touchcancel", abort);
    element.addEventListener("click", click, true);
    window.addEventListener("keydown", key, true);
    element.addEventListener("contextmenu", context);
    element.addEventListener("dragstart", drag);
    window.addEventListener("blur", abort);
    window.addEventListener("pointerout", leaveWindow);
    return () => {
      clearPending();
      removeGhost();
      current.current = null;
      element.removeEventListener("pointerdown", startMouse);
      window.removeEventListener("pointermove", mouseMove);
      window.removeEventListener("pointerup", mouseUp);
      window.removeEventListener("pointercancel", abort);
      element.removeEventListener("touchstart", touchStart);
      element.removeEventListener("touchmove", touchMove);
      element.removeEventListener("touchend", touchEnd);
      element.removeEventListener("touchcancel", abort);
      element.removeEventListener("click", click, true);
      window.removeEventListener("keydown", key, true);
      element.removeEventListener("contextmenu", context);
      element.removeEventListener("dragstart", drag);
      window.removeEventListener("blur", abort);
      window.removeEventListener("pointerout", leaveWindow);
    };
  }, [activeSpace.id]);

  return { root, movement, message, error };
}
