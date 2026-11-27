// Entrada de ratón y táctil, incluida la supresión del clic residual.

import { useEffect } from "react";
import {
  createShelfGesture,
  gestureDistance,
  releaseAction,
  slotAt,
} from "../utils/shelfMovement";

const DISTANCE = 8;
const HOLD = 450;

export default function useShelfPointer(root, spaceId, bindings) {
  useEffect(() => {
    const element = root.current;
    const point = (event) => ({ x: event.clientX, y: event.clientY });

    function startMouse(event) {
      if (event.pointerType !== "mouse") return;
      const context = bindings.current;
      context.suppress.current = null;
      if (
        context.operation.current ||
        context.latest.current.blocked ||
        event.button !== 0 ||
        event.ctrlKey ||
        event.metaKey ||
        event.shiftKey ||
        event.altKey
      )
        return;
      const node = context.slotNode(event.target);
      if (!node?.dataset.drawer) return;
      context.pending.current = createShelfGesture(
        node.dataset.drawer,
        Number(node.dataset.slot),
        point(event),
        event.pointerId,
        "mouse",
      );
    }

    function mouseMove(event) {
      if (event.pointerType !== "mouse") return;
      const context = bindings.current;
      const candidate = context.pending.current;
      if (
        !candidate ||
        candidate.input !== "mouse" ||
        candidate.id !== event.pointerId
      )
        return;
      if (
        !context.operation.current &&
        gestureDistance(candidate.start, point(event)) >= DISTANCE
      ) {
        context.pick(
          candidate.path,
          candidate.origin,
          "dragging",
          point(event),
        );
      }
      if (context.operation.current) {
        event.preventDefault();
        context.move(point(event));
      }
    }

    function mouseUp(event) {
      const context = bindings.current;
      if (
        event.pointerType !== "mouse" ||
        context.pending.current?.input !== "mouse"
      )
        return;
      if (context.operation.current) {
        context.suppress.current = true;
        context.save(
          slotAt(context.rectangles(), event.clientX, event.clientY),
        );
      }
      context.clearPending();
    }

    function touchStart(event) {
      const context = bindings.current;
      context.suppress.current = null;
      if (event.touches.length !== 1) {
        context.cancel(false);
        return;
      }
      if (context.operation.current || context.latest.current.blocked) return;
      const node = context.slotNode(event.target);
      if (!node?.dataset.drawer) return;
      const touch = event.touches[0];
      const candidate = createShelfGesture(
        node.dataset.drawer,
        Number(node.dataset.slot),
        point(touch),
        touch.identifier,
        "touch",
      );
      context.pending.current = candidate;
      context.timer.current = setTimeout(() => {
        const latest = bindings.current;
        if (latest.pending.current === candidate)
          latest.pick(
            candidate.path,
            candidate.origin,
            "picked",
            candidate.start,
          );
      }, HOLD);
    }

    function touchMove(event) {
      const context = bindings.current;
      const candidate = context.pending.current;
      if (!candidate || candidate.input !== "touch") return;
      if (event.touches.length !== 1) {
        context.cancel(false);
        return;
      }
      const touch = [...event.touches].find(
        ({ identifier }) => identifier === candidate.id,
      );
      if (!touch) return;
      const distance = gestureDistance(candidate.start, point(touch));
      if (!context.operation.current) {
        if (distance >= DISTANCE) context.clearPending();
        return;
      }
      if (event.cancelable) event.preventDefault();
      else {
        context.cancel(false);
        return;
      }
      if (distance >= DISTANCE || context.operation.current.mode === "dragging")
        context.move(point(touch));
    }

    function touchEnd(event) {
      const context = bindings.current;
      const candidate = context.pending.current;
      if (candidate?.input !== "touch") return;
      const touch = [...event.changedTouches].find(
        ({ identifier }) => identifier === candidate.id,
      );
      if (!touch) return;
      const state = context.operation.current;
      context.clearPending();
      if (!state) return;
      context.suppress.current = true;
      const target = slotAt(context.rectangles(), touch.clientX, touch.clientY);
      if (releaseAction(state.mode, state.origin, target) === "wait") {
        context.removeGhost();
        context.update({ ...state, mode: "waiting" });
        context.setMessage(
          "Cajón recogido. Toca un destino; para cancelar, toca el cajón de origen.",
        );
      } else context.save(target);
    }

    function click(event) {
      const context = bindings.current;
      if (context.suppress.current) {
        event.preventDefault();
        event.stopPropagation();
        context.suppress.current = null;
        return;
      }
      if (!context.operation.current) return;
      event.preventDefault();
      event.stopPropagation();
      if (context.operation.current.mode !== "saving")
        context.save(
          Number(context.slotNode(event.target)?.dataset.slot) || null,
        );
    }

    function abort() {
      bindings.current.cancel(false);
    }
    function leaveWindow(event) {
      if (
        event.pointerType === "mouse" &&
        event.relatedTarget === null &&
        bindings.current.pending.current?.input === "mouse"
      )
        abort();
    }
    function contextMenu(event) {
      if (bindings.current.operation.current) event.preventDefault();
    }
    function drag(event) {
      if (bindings.current.slotNode(event.target)) event.preventDefault();
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
    element.addEventListener("contextmenu", contextMenu);
    element.addEventListener("dragstart", drag);
    window.addEventListener("blur", abort);
    window.addEventListener("pointerout", leaveWindow);
    return () => {
      element.removeEventListener("pointerdown", startMouse);
      window.removeEventListener("pointermove", mouseMove);
      window.removeEventListener("pointerup", mouseUp);
      window.removeEventListener("pointercancel", abort);
      element.removeEventListener("touchstart", touchStart);
      element.removeEventListener("touchmove", touchMove);
      element.removeEventListener("touchend", touchEnd);
      element.removeEventListener("touchcancel", abort);
      element.removeEventListener("click", click, true);
      element.removeEventListener("contextmenu", contextMenu);
      element.removeEventListener("dragstart", drag);
      window.removeEventListener("blur", abort);
      window.removeEventListener("pointerout", leaveWindow);
    };
  }, [root, spaceId, bindings]);
}
