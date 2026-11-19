---
spec: "026"
status: completed
---

# Tareas — Editar fichas manuales

- [x] **T-01 — Revisar y ampliar cobertura de edición manual**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05.
  - Cubrir en MvpTest, SeriesTest y ReadingsTest los casos de la matriz
    del plan, reutilizando infraestructura y evitando pruebas duplicadas.
  - Evidencia: resultados para las cinco categorías y casos de catálogo,
    permisos, errores, persistencia y conservación de aportaciones.

- [x] **T-02 — Completar el guardado de sinopsis manual**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05.
  - Actualizar CollectionController usando columnas existentes, validación
    completa y guardado único; gestionar errores de persistencia.
  - Evidencia: sinopsis manual aceptada, vaciable y limitada; edición nueva
    no permitida en catálogo; campos ajenos y datos anteriores conservados.

- [x] **T-03 — Integrar el campo en la interfaz existente**
  - Criterios: CA-01, CA-04, CA-05, CA-06.
  - Añadir Field multilínea solo para fichas manuales en MovieMetadataForm,
    integrándolo en cambios enviados, errores y estado tras guardar.
  - Evidencia: formulario compartido de Películas y Series actualizado,
    sin rediseño, y sin pérdida del texto ante error.

- [x] **T-04 — Verificar la implementación y registrar evidencias**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06.
  - Ejecutar pruebas SQLite, build y comprobación del diff; comprobar
    interfaz manualmente cuando se autorice y realizar revisión final.
  - Evidencia: comandos, resultados y comprobaciones del desarrollador,
    con cualquier pendiente explícito y sin borrar datos locales.

- [x] **T-05 — Cerrar la spec tras la verificación**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06.
  - Registrar revisión final, horario y esfuerzo GitHub Projects; mantener
    en MEMORY.md únicamente pendientes operativos pertinentes.
  - Evidencia: cierre documentado, sin commit ni publicación no autorizados.
