---
spec: "028"
status: completed
---

# Tareas — Aplicar reglas de metadatos de catálogo

- [x] **T-01 — Preparar cobertura y fixtures previos a la adaptación**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06.
  - Ampliar pruebas existentes de catálogo, edición, vinculación y permisos,
    incluyendo las versiones y los casos de migración en base aislada.
  - Evidencia: casos de la matriz reproducibles, sin tocar datos de desarrollo.

- [x] **T-02 — Persistir procedencia y versión sin perder datos**
  - Criterios: CA-01, CA-03, CA-05, CA-06.
  - Crear migración aditiva, casts y trait acotado a política por campo;
    adaptar datos históricos en lotes sin catálogos ni modelos de aplicación.
  - Evidencia: datos y relaciones conservados, campos presentes importados
    solo en catálogos y manuales posteriores sin reclasificación.

- [x] **T-03 — Integrar altas, ediciones y vinculación seguras**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-06.
  - Actualizar controladores existentes con validación de campos y versión,
    escritura única y protección frente a carreras; preservar valoraciones.
  - Evidencia: política aplicada en servidor, conflictos y errores atómicos.

- [x] **T-04 — Adaptar los formularios de detalle**
  - Criterios: CA-02, CA-03, CA-07.
  - Usar permisos y versión del servidor, explicar protección y permitir
    completar/corregir campos manuales, conservando componentes y navegación.
  - Evidencia: peticiones mínimas válidas y campos externos no editables.

- [x] **T-05 — Migrar SQLite local y ejecutar verificación**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06, CA-07.
  - Confirmar SQLite, respaldar archivo, ejecutar migrate y migrate:status;
    ejecutar suite, build, diff y comprobaciones manuales autorizadas.
  - Evidencia: migración aplicada sin pérdida de datos y resultados reales
    por criterio; auditoría final sin bloqueos para cerrar.

- [x] **T-06 — Documentar cierre y pendientes de despliegue**
  - Criterios: CA-05, CA-06, CA-07.
  - Registrar evidencias, horario, esfuerzo y pendientes operativos,
    incluida la ventana de despliegue sin escrituras.
  - Evidencia: spec y tareas cerradas tras verificación, sin commits,
    publicaciones o acciones en producción no autorizados.
