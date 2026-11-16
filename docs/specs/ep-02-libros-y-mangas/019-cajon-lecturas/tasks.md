---
spec: "019"
status: completed
---

# Tareas — Añadir y abrir el cajón de Lecturas

- [x] **T-01 — Persistir y proteger la posición de Lecturas**
  - Criterios: CA-01 y CA-03.
  - Evidencia: rutas protegidas, migración reversible y pruebas de
    colocación, movimiento, colisiones y aislamiento.
- [x] **T-02 — Integrar la fila animada en la estantería**
  - Criterios: CA-01 y CA-02.
  - Evidencia: un acceso llamado «Lecturas», lomos adaptados al hueco y
    gesto de inclinación con hover, foco y movimiento reducido.
- [x] **T-03 — Crear la vista inicial con tres pestañas**
  - Criterios: CA-01 y CA-04.
  - Evidencia: vista protegida con mensajes vacíos y enlace de vuelta.
- [x] **T-04 — Verificar y registrar evidencias**
  - Criterios: CA-01 a CA-04.
  - Evidencia: migración local aplicada, suite en SQLite, build, diff
    y pruebas manuales de Ángel en escritorio y móvil.

## Evidencias locales

- Se verificó la conexión a `database/database.sqlite` y se aplicó
  `2026_11_16_000008_add_reading_slot_to_spaces`. El estado es `Ran`.
- En la verificación de la implementación con PHP 8.5.12 y SQLite,
  PHPUnit pasó 37 pruebas con 914 aserciones
  y se omitieron dos casos exclusivos de PostgreSQL. Se verificaron
  colocación, movimiento, acceso, colisiones, aislamiento y reversión.
- En la última revisión se repitieron el build de Vite y `git diff --check`
  con éxito. La suite de PHP corresponde a la verificación anterior:
  en esta sesión, el PHP disponible en PATH es 8.2.12 y el ejecutable
  8.5.12 del proyecto no está accesible en la ruta documentada.
- El 16 de noviembre de 2026, Ángel confirmó la presentación en
  escritorio y móvil (CA-02), la apertura por toque y teclado (CA-01),
  el foco visible y el movimiento reducido (CA-02), y la navegación
  entre Libros, Manga y Cómics (CA-04).
- La fila de lomos usa un WebP transparente. El último libro se inclina
  hacia delante y deja ver el canto de las páginas. El letrero «Lecturas»
  es texto editable con un degradado vertical.
