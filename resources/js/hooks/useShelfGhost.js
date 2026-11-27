// Copia visual inerte que acompaña al cajón durante el arrastre.

import { useCallback, useEffect, useRef } from "react";

export default function useShelfGhost(root, spaceId) {
  const ghost = useRef(null);
  const remove = useCallback(() => {
    ghost.current?.remove();
    ghost.current = null;
  }, []);

  const show = useCallback(
    (path, point) => {
      if (!ghost.current) {
        const source = root.current?.querySelector(`[data-drawer="${path}"]`);
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
        clone.removeAttribute("data-origin");
        clone.removeAttribute("data-target");
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
    },
    [root],
  );

  useEffect(() => () => remove(), [spaceId, remove]);
  return { show, remove };
}
