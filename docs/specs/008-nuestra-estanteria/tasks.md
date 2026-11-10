---
spec: "008"
status: completed
---

# Tareas — 008

- [x] **T-01 — Preparar las composiciones horizontal y vertical**
  - Criterios: CA-01, CA-02, CA-04.
  - Evidencia esperada: dos escenas locales con proporciones distintas,
    huecos abiertos, zonas de luz separadas y frente del cajón independiente.
- [x] **T-02 — Integrar la estantería y el cajón en el panel**
  - Criterios: CA-01, CA-02, CA-03, CA-05.
  - Evidencia esperada: cajón alineado, texto real, foco visible y acceso
    funcional también con imágenes bloqueadas.
- [x] **T-03 — Integrar iluminación y apertura**
  - Criterios: CA-03, CA-04, CA-05.
  - Evidencia esperada: reflejos localizados, contraste medido y apertura
    por clic, toque y teclado, con alternativa de movimiento reducido.
- [x] **T-04 — Ajustar el retorno y comprobar Películas**
  - Criterios: CA-02, CA-03, CA-06.
  - Evidencia esperada: retorno al panel, cajón con lista vacía y flujos de
    alta, consulta y valoración operativos con SQLite local.
- [x] **T-05 — Verificar el conjunto y registrar evidencias**
  - Criterios: CA-01–CA-06.
  - Evidencia esperada: revisión en 1280 y 375 px y anchos intermedios,
    teclado, tacto, contraste, fallo de imagen, movimiento reducido,
    compilación y pruebas existentes. Registrar resultados y limitaciones.

## Evidencia local sin navegador

- Dos escenas WebP de fondo completo, con la estantería suspendida sobre
  una pared cálida. La horizontal tiene cuatro columnas y dos filas, y
  la vertical tiene dos columnas y cuatro filas. El frente del cajón es
  independiente y se alinea con el hueco de cada escena.
- El contraste calculado de la etiqueta opaca es de 10,71:1.
- `npm run build` pasó con el ajuste de las velas y generó los tres recursos.
  Tras retirar el diagnóstico inicial, PHPUnit con SQLite pasó 11 pruebas
  con 192 aserciones y omitió dos exclusivas de PostgreSQL.

## Pruebas manuales en local

- El desarrollador revisó las vistas de escritorio, tablet y móvil y pidió
  subir la frase de escritorio. El ajuste de espaciado está aplicado.
- **CA-05 comprobado por el desarrollador.** Con movimiento reducido, las
  luces quedan fijas y el cajón abre sin animación. Al bloquear la imagen,
  desaparece la estantería y el cajón sigue visible y abre Películas.
- **CA-03 comprobado por el desarrollador.** El cajón muestra el foco,
  abre con Tab y Enter y con toque, y puede abrirse otra vez después de
  volver a la estantería.
- **CA-04 comprobado por el desarrollador.** El movimiento suavizado de
  las velas se aprecia en las vistas horizontal y vertical.
- Para CA-01 y CA-03 se calculó la geometría de los dos encuadres en diez
  tamaños, con tablet en ambas orientaciones. El cajón conserva su
  posición relativa y su objetivo táctil supera los 44 × 44 px.
- Auditoría final: PASS. Las comprobaciones visuales e interactivas fueron
  realizadas por el desarrollador.
