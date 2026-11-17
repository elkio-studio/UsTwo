---
spec: "022"
status: completed
---

# Tareas — Gestionar las fichas compartidas de Lecturas

- [x] **T-01 — Proteger las rutas de edición y borrado**
  - Criterios: CA-04.
  - Evidencia: las dos rutas exigen sesión y membresía y resuelven la
    ficha dentro de su estantería.
- [x] **T-02 — Guardar los cambios permitidos**
  - Criterios: CA-01, CA-02, CA-05.
  - Evidencia: edición manual, campos vacíos de catálogo, rechazo de
    modificaciones protegidas y persistencia sin cambios parciales.
- [x] **T-03 — Borrar con confirmación**
  - Criterios: CA-03, CA-05.
  - Evidencia: confirmación validada en el servidor, regreso a la
    categoría de origen y conservación de la ficha si falla.
- [x] **T-04 — Activar los controles del detalle**
  - Criterios: CA-01, CA-02, CA-03, CA-05.
  - Evidencia: edición, errores, cancelación y confirmación manejables
    en móvil y con teclado.
  - Ángel comprobó en local la edición y el borrado.
- [x] **T-05 — Verificar y registrar evidencias**
  - Criterios: CA-01 a CA-05.
  - Evidencia: pruebas SQLite, build, comprobación del diff, validación
    manual y horas registradas para GitHub Projects al cerrar.

## Verificación local

- PHPUnit con PHP 8.5.12 y SQLite en memoria. Se ejecutaron 50 casos,
  48 superados y 2 exclusivos de PostgreSQL omitidos, con 1.134
  aserciones. Las nuevas pruebas cubren edición manual, campos de
  catálogo protegidos, escritura simultánea, confirmación, permisos y
  fallos de persistencia.
- La compilación de Vite y la comprobación del diff pasaron. El cambio
  utiliza las columnas existentes de Lecturas.
- Una petición parcial de Inertia al listado devuelve el título recién
  editado. La navegación por historial solicita datos actuales y
  conserva la pestaña activa.
- El 17 de noviembre de 2026, Ángel confirmó manualmente la edición,
  el borrado y la vuelta al listado con el botón lateral del ratón.
  La prueba manual registrada cubre ese recorrido. Móvil, teclado,
  foco y cancelación carecen de un resultado manual individual.
