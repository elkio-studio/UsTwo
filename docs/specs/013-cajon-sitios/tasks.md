---
spec: "013"
status: completed
---

# Tareas — Añadir y abrir el cajón de Sitios

- [x] **T-01 — Persistir la posición por estantería**
  - Criterios: CA-01, CA-03, CA-04.
  - Evidencia esperada: migración y reversión conservan posiciones previas,
    y la base de datos rechaza posiciones duplicadas entre tipos.
- [x] **T-02 — Ampliar la colocación y proteger las rutas**
  - Criterios: CA-01, CA-03, CA-04.
  - Evidencia esperada: pruebas de rango, permisos, colisión, movimiento y
    aislamiento. La actualización rechazada conserva la distribución.
- [x] **T-03 — Añadir el tipo, el globo y la vista de destino**
  - Criterios: CA-01, CA-02.
  - Evidencia esperada: selector y globo operativos, página «Sitios»
    resuelta por Inertia y enlace a la estantería de origen.
- [x] **T-04 — Verificar regresiones y registrar resultados**
  - Criterios: CA-01 a CA-04.
  - Evidencia esperada: pruebas SQLite, build, comparación con Películas y
    Series y comprobaciones de recarga, foco y activación en escritorio y
    móvil. Registrar las comprobaciones pendientes.

## Evidencias locales

- PHPUnit con PHP 8.5.12 y SQLite en memoria ejecutó 26 pruebas.
  Pasaron 24 con 631 aserciones. Las dos omisiones corresponden al seeder
  exclusivo de PostgreSQL.
- Las pruebas de Sitios cubren posición tras recarga, movimiento,
  reutilización del hueco anterior, colisiones, reversión de la migración,
  acceso directo y aislamiento. Inertia comprobó el JSX de la vista y el
  slug de la estantería de origen.
- El build de Vite pasó con el diseño final. Ángel confirmó que el globo
  quedó bien y autorizó el cierre. No consta una comprobación específica
  del foco y la activación con teclado y toque en ambas orientaciones.
- Ángel comprobará el CI de dev, incluida la ejecución con PostgreSQL.
