// Superficie de la Tierra que gira dentro del aro del cajón de Sitios.

import { useEffect, useRef } from "react";
import sepiaTexture from "../../../../images/estanteria/textura-tierra.webp";
import colorTexture from "../../../../images/estanteria/textura-tierra-color.webp";
import styles from "./GlobeSurface.module.css";

const size = 192;
const fullTurn = 2 * Math.PI;
const axisTilt = Math.PI / 12;

const textures = {
  sepia: sepiaTexture,
  color: colorTexture,
  "sepia-dark": sepiaTexture,
};

export default function GlobeSurface({ spinning, palette = "color" }) {
  const canvas = useRef(null);
  const angle = useRef(-0.23);

  useEffect(() => {
    const animate =
      spinning &&
      !window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const brightness = palette === "sepia-dark" ? 0.78 : 1;

    const surface = canvas.current;
    const context = surface.getContext("2d");
    if (!context) return;

    const map = new Image();
    map.crossOrigin = "anonymous";
    let frame;
    let previous;
    map.onload = () => {
      const source = document.createElement("canvas");
      source.width = map.width;
      source.height = map.height;
      const sourceContext = source.getContext("2d");
      sourceContext.drawImage(map, 0, 0);
      const pixels = sourceContext.getImageData(
        0,
        0,
        map.width,
        map.height,
      ).data;
      const output = context.createImageData(size, size);
      const samples = [];

      for (let y = 0; y < size; y++) {
        const vertical = 1 - (2 * (y + 0.5)) / size;
        for (let x = 0; x < size; x++) {
          const horizontal = (2 * (x + 0.5)) / size - 1;
          const depthSquared = 1 - horizontal ** 2 - vertical ** 2;
          if (depthSquared <= 0) continue;

          const depth = Math.sqrt(depthSquared);
          // Inclina el eje de rotación para seguir el soporte del globo.
          const latitude =
            vertical * Math.cos(axisTilt) + horizontal * Math.sin(axisTilt);
          const across =
            horizontal * Math.cos(axisTilt) - vertical * Math.sin(axisTilt);
          samples.push({
            target: (y * size + x) * 4,
            row: Math.floor((0.5 - Math.asin(latitude) / Math.PI) * map.height),
            longitude: 1.5 + Math.atan2(across, depth) / fullTurn,
            light:
              brightness *
              (0.4 +
                0.57 * Math.max(0, depth - 0.3 * horizontal + 0.2 * vertical)),
          });
        }
      }

      function draw(time) {
        if (previous !== undefined) {
          angle.current =
            (angle.current + Math.min(time - previous, 40) / 6500) % 1;
        }
        previous = time;

        for (const { target, row, longitude, light } of samples) {
          const column = Math.floor(
            ((longitude + angle.current) % 1) * map.width,
          );
          const index = (row * map.width + column) * 4;
          output.data[target] = pixels[index] * light;
          output.data[target + 1] = pixels[index + 1] * light;
          output.data[target + 2] = pixels[index + 2] * light;
          output.data[target + 3] = 255;
        }
        context.putImageData(output, 0, 0);
        surface.classList.add(styles.ready);
        if (animate) frame = window.requestAnimationFrame(draw);
      }

      frame = window.requestAnimationFrame(draw);
    };
    map.src = textures[palette];

    return () => {
      map.onload = null;
      window.cancelAnimationFrame(frame);
    };
  }, [spinning, palette]);

  return (
    <canvas
      ref={canvas}
      className={styles.surface}
      width={size}
      height={size}
      aria-hidden="true"
    />
  );
}
