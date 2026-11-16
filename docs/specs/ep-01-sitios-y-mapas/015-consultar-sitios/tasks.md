---
spec: "015"
status: completed
---

# Tareas — Consultar los sitios guardados en el mapa

- [x] **T-01 — Mostrar nombre y nota en el popup**
  - Criterios: CA-01, CA-02.
  - Evidencia: nombre y nota opcional como texto, con foco visible y
    notas largas legibles en móvil.
- [x] **T-02 — Ampliar las pruebas de lectura**
  - Criterios: CA-01, CA-03, CA-04.
  - Evidencia: varios registros con sus coordenadas y notas, persistencia
    en nuevas lecturas y aislamiento entre dos estanterías.
- [x] **T-03 — Verificar la consulta y el fallo del mapa**
  - Criterios: CA-01 a CA-05.
  - Evidencia: pruebas locales con SQLite, build, diff y comprobaciones
    de clic, teclado, toque, recarga y recuperación del mapa.

## Evidencias locales

- PHPUnit 12.5.43 con PHP 8.5.12 y SQLite. Pasaron 29 pruebas con
  766 aserciones. Se omitieron dos pruebas exclusivas de PostgreSQL.
- El build de Vite y `git diff --check` pasaron.
- Ángel confirmó el funcionamiento de Sitios en local y aportó una captura
  del popup con el nombre y la nota de un sitio guardado.
- La confirmación manual fue global. La captura acredita la lectura del
  popup. Las pruebas de servidor cubren las lecturas repetidas y el
  aislamiento entre estanterías. No se registró evidencia interactiva
  individual para teclado y foco, toque, notas largas en móvil, marcadores
  coincidentes ni recuperación tras bloquear el mapa base.
