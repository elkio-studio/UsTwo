---
spec: "023"
status: completed
---

# Tareas — Valorar las lecturas compartidas

- [x] **T-01 — Persistir una aportación por lectura y miembro**
  - Criterios: CA-01, CA-04, CA-05.
  - Evidencia: migración reversible con clave única y borrado en cascada,
    aplicada en SQLite local y confirmada como ejecutada.
- [x] **T-02 — Proteger y validar el guardado**
  - Criterios: CA-01, CA-03, CA-04, CA-05.
  - Evidencia: pruebas de actualización, permisos, límites y conservación
    de la última aportación ante errores.
- [x] **T-03 — Mostrar las aportaciones en la ficha y las tarjetas**
  - Criterios: CA-02, CA-04.
  - Evidencia: dos aportaciones visibles en el detalle, puntuaciones
    actualizadas al volver al listado y datos aislados por lectura.
- [x] **T-04 — Activar y verificar el formulario compartido**
  - Criterios: CA-01, CA-02, CA-04, CA-05.
  - Evidencia: formulario con valores propios, errores visibles,
    navegación con teclado y uso táctil.
  - Ángel confirmó en local que el formulario guarda y vuelve al listado
    correcto.
- [x] **T-05 — Ejecutar controles y registrar el cierre**
  - Criterios: CA-01 a CA-05.
  - Evidencia: resultados de SQLite, Vite, comprobación del diff y pruebas
    manuales, con el esfuerzo registrado para GitHub Projects.

## Verificación local

- La migración `2026_11_18_000010_create_reading_ratings_table` se aplicó
  en `database/database.sqlite` y figura como `Ran`.
- PHPUnit con PHP 8.5.12 y SQLite en memoria: 51 pruebas superadas,
  2 exclusivas de PostgreSQL omitidas y 1.224 aserciones.
  Se verificaron dos aportaciones independientes, edición, límites,
  permisos, recarga parcial, retirada de miembros, borrado en cascada
  y conservación de datos y del borrador ante un fallo de escritura.
  También se verificó el regreso al listado al guardar una valoración
  en Películas, Series y las tres pestañas de Lecturas.
- Vite y la comprobación del diff pasaron.
- El 18 de noviembre de 2026, Ángel confirmó manualmente que las
  valoraciones se guardan y la aplicación vuelve al listado correcto.
  La confirmación cubre ese recorrido. Toque, teclado y foco no tienen
  una comprobación manual individual registrada.
