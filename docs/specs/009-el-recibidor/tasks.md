---
spec: "009"
status: completed
---

# Tareas — 009

- [x] **T-01 — Preparar el esquema y la transición de datos**
  - Criterios: CA-02, CA-05, CA-06, CA-07.
  - Evidencia: migración en bases limpias y heredadas, conservación de
    IDs, hashes y valoraciones, propietario y nombres iniciales correctos.
- [x] **T-02 — Aislar rutas y operaciones de Películas por espacio**
  - Criterios: CA-03, CA-05, CA-06, CA-07, CA-08.
  - Evidencia: pruebas de autorización cruzada, altas independientes,
    candidatas acotadas y flujos existentes dentro de su espacio.
- [x] **T-03 — Integrar El recibidor y el registro**
  - Criterios: CA-01, CA-02, CA-03, CA-05.
  - Evidencia: pantallas en escritorio y móvil, sesión automática tras
    el registro, duplicados rechazados y continuidad del acceso previo.
- [x] **T-04 — Integrar espacios, selector e invitaciones**
  - Criterios: CA-03, CA-04, CA-06, CA-07, CA-08.
  - Evidencia: estado vacío, alta, selección, bandeja de invitaciones,
    rechazo, retirada, aceptación idempotente y borrado confirmado.
- [x] **T-05 — Verificar y documentar la transición**
  - Criterios: CA-01–CA-14.
  - Evidencia: build y suites aisladas, revisión visual, compatibilidad
    entre versiones durante el despliegue y procedimiento de recuperación.
- [x] **T-06 — Ampliar la administración y las rutas de estanterías**
  - Criterios: CA-04, CA-08, CA-09, CA-10, CA-11.
  - Evidencia: `SharedSpacesTest` comprueba acceso por URL, colisiones de
    nombres, invitaciones, cambios de rol, retirada de acceso y edición
    de credenciales con contraseña actual.
- [x] **T-07 — Incorporar el selector de cajón**
  - Criterios: CA-12.
  - Evidencia: `SharedSpacesTest` verifica pertenencia, límites de hueco y
    persistencia de la nueva posición.
- [x] **T-08 — Renovar Películas y completar los metadatos de TMDB**
  - Criterios: CA-07, CA-13, CA-14.
  - Evidencia: `MvpTest` verifica el orden paginado, el alta directa, la
    fecha por defecto y la vinculación conservando las valoraciones.
    SQLite local recibió la portada y sinopsis reales de El viaje de
    Chihiro. El favicon de luna se referencia desde la plantilla base.

## Evidencia local

- CA-02–CA-08: pruebas de registro, sesión, permisos, invitaciones,
  aislamiento de colecciones, selector y borrado. Se verificó la
  migración en SQLite con cuentas, contraseñas, películas y valoraciones
  heredadas. La migración también se aplicó sobre una copia temporal
  del archivo local, dejando el original intacto.
- PHP 8.5.12: suite SQLite con 18 pruebas aprobadas, 472 aserciones y
  2 omisiones exclusivas de PostgreSQL. Suite PostgreSQL 17 aislada con
  20 pruebas aprobadas y 491 aserciones. `npm run build` y
  `git diff --check` correctos.
- Ensayo de publicación en PostgreSQL aislado: la migración se bloqueó
  mientras el rol de aplicación conservaba acceso. Tras revocarlo, una
  conexión abierta y una nueva rechazaron lecturas y escrituras. La
  migración pudo completarse tras la revocación.
- El desarrollador confirmó la revisión local del recibidor, los cajones
  y las portadas en escritorio y móvil (CA-01, CA-12 y CA-13).
- El desarrollador confirmó que, tras añadir una película desde el
  listado, Atrás vuelve al listado y muestra la nueva entrada (CA-14).
  El agente realizó las comprobaciones automatizadas sin abrir el
  navegador.
