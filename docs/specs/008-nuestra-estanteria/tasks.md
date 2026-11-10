---
spec: "008"
status: in_progress
---

# Tareas — 008

- [ ] **T-01 — Preparar las composiciones horizontal y vertical**
  - Criterios: CA-01, CA-02, CA-04.
  - Evidencia esperada: dos escenas locales con proporciones distintas,
    huecos abiertos, zonas de luz separadas y frente del cajón independiente.
- [ ] **T-02 — Integrar la estantería y el cajón en el panel**
  - Criterios: CA-01, CA-02, CA-03, CA-05.
  - Evidencia esperada: cajón alineado, texto real, foco visible y acceso
    funcional también con imágenes bloqueadas.
- [ ] **T-03 — Integrar iluminación y apertura**
  - Criterios: CA-03, CA-04, CA-05.
  - Evidencia esperada: reflejos localizados, contraste medido y apertura
    por clic, toque y teclado, con alternativa de movimiento reducido.
- [ ] **T-04 — Ajustar el retorno y comprobar Películas**
  - Criterios: CA-02, CA-03, CA-06.
  - Evidencia esperada: retorno al panel, cajón con lista vacía y flujos de
    alta, consulta y valoración operativos con SQLite local.
- [ ] **T-05 — Verificar el conjunto y registrar evidencias**
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
- `npm run build` pasó y generó los tres recursos. PHPUnit con SQLite
  pasó 11 pruebas con 195 aserciones y omitió dos exclusivas de PostgreSQL.
- Queda pendiente comprobar la composición renderizada y recorrer con
  teclado y tacto la apertura, el retorno, el bloqueo de imágenes y la
  preferencia de movimiento reducido.
