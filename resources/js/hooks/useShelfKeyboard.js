// Recogida, selección de destino y cancelación de cajones mediante teclado.

import { useEffect } from "react";
import { nextSlot } from "../utils/shelfMovement";

export default function useShelfKeyboard(root, spaceId, bindings) {
  useEffect(() => {
    const element = root.current;
    function key(event) {
      const context = bindings.current;
      if (event.key === "Escape") {
        if (context.operation.current) {
          event.preventDefault();
          context.cancel();
        } else context.clearPending();
        return;
      }
      if (!element.contains(event.target)) return;
      if (!context.operation.current && event.key === "Enter")
        context.suppress.current = null;
      if (context.operation.current) {
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
          const state = context.operation.current;
          if (state.mode === "saving") return;
          if (event.key.startsWith("Arrow")) {
            const target = nextSlot(
              context.rectangles(),
              state.target ?? state.origin,
              event.key,
            );
            context.update({ ...state, target });
            context.setMessage(context.describe(target));
            root.current.querySelector(`[data-slot="${target}"]`)?.focus();
          } else if (event.key === "Enter") context.save(state.target);
        }
        return;
      }
      const node = context.slotNode(event.target);
      if (
        event.key === " " &&
        node?.dataset.drawer &&
        !context.latest.current.blocked
      ) {
        event.preventDefault();
        event.stopPropagation();
        context.clearPending();
        context.suppress.current = null;
        context.pick(
          node.dataset.drawer,
          Number(node.dataset.slot),
          "keyboard",
        );
      }
    }
    window.addEventListener("keydown", key, true);
    return () => window.removeEventListener("keydown", key, true);
  }, [root, spaceId, bindings]);
}
