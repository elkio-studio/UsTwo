// Coordinación única del estado y guardado del movimiento de cajones.

import { useEffect, useRef, useState } from "react";
import { router } from "@inertiajs/react";
import { positionsFor, slotAt } from "../utils/shelfMovement";

import useShelfGhost from "./useShelfGhost";
import useShelfPointer from "./useShelfPointer";
import useShelfKeyboard from "./useShelfKeyboard";

export default function useShelfMovement(activeSpace, drawers, blocked) {
  const root = useRef(null);
  const current = useRef(null);
  const pending = useRef(null);
  const timer = useRef(null);
  const suppress = useRef(null);
  const latest = useRef({ activeSpace, blocked });
  latest.current = { activeSpace, blocked };
  const [movement, setMovement] = useState(null);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  const bindings = useRef(null);
  const { show: showGhost, remove: removeGhost } = useShelfGhost(
    root,
    activeSpace.id,
  );

  const update = (value) => {
    current.current = value;
    setMovement(value);
  };
  function clearPending() {
    clearTimeout(timer.current);
    timer.current = null;
    pending.current = null;
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
    if (point) showGhost(path, point);
  }
  function move(point) {
    const state = current.current;
    if (!state || state.mode === "saving") return;
    const target = slotAt(rectangles(), point.x, point.y);
    update({ ...state, mode: "dragging", target });
    showGhost(state.path, point);
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

  function slotNode(target) {
    return target instanceof Element ? target.closest("[data-slot]") : null;
  }

  bindings.current = {
    operation: current,
    pending,
    timer,
    suppress,
    latest,
    update,
    setMessage,
    clearPending,
    removeGhost,
    pick,
    move,
    save,
    cancel,
    rectangles,
    describe,
    slotNode,
  };
  useShelfPointer(root, activeSpace.id, bindings);
  useShelfKeyboard(root, activeSpace.id, bindings);

  useEffect(
    () => () => {
      clearTimeout(timer.current);
      timer.current = null;
      pending.current = null;
      current.current = null;
    },
    [activeSpace.id],
  );

  return { root, movement, message, error };
}
