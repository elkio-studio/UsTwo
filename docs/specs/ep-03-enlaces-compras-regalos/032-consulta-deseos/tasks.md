---
spec: "032"
status: completed
---

# Tareas — 032

- [x] **T-01 — Inventariar consulta y cobertura existente**
  - Criterios: CA-01 a CA-06.
  - Revisar controladores, relaciones, permisos, vistas y tests, sin modificarlos.
  - Evidencia: matriz de implementación observada y lagunas, sin afirmar ejecución.
- [x] **T-02 — Ejecutar pruebas existentes en SQLite aislado**
  - Criterios: CA-01, CA-03, CA-04, CA-06.
  - Antes de ejecutar, confirmar testing/sqlite/:memory:/URL vacía sin caché
    de configuración. WishesTest y WishesDrawerTest en memoria; conservar resultados.
  - Evidencia: comando y resultado, sin migrar ni escribir en la base de la web.
- [x] **T-03 — Completar comprobaciones efímeras de consulta**
  - Criterios: CA-01, CA-03, CA-04, CA-05, CA-06.
  - Test temporal fuera del repositorio, salvaguarda en beforeRefreshingDatabase
    antes de migrar/transaccionar y fixtures únicamente con SQLite en memoria:
    datos completos/opcionales, listas vacías, dos espacios/roles, manipulaciones,
    selección/recarga y destino .invalid consultado sin contactar con él.
  - Evidencia: aserciones reales del payload y rechazos; sin tests persistentes
    ni datos locales creados. Archivo temporal retirado tras la comprobación.
- [x] **T-04 — Revisar seguridad y presentación del frontend**
  - Criterios: CA-01, CA-02, CA-03, CA-05, CA-06.
  - Enlace HTTP(S), target/rel, texto escapado, ausencia de incrustación/red,
    estados vacíos, ajuste de URLs largas y foco según las fuentes actuales.
  - Evidencia: rutas/líneas inspeccionadas y límites de no observar el navegador.
- [x] **T-05 — Registrar comprobación manual de Ángel**
  - Criterios: CA-01, CA-02, CA-03, CA-06.
  - Listas/datos/opcionales, apertura, recarga, regreso y móvil/teclado/foco.
  - Evidencia: confirmación atribuida a Ángel; agente sin abrir navegador.
- [x] **T-06 — Auditar evidencias y cerrar documentación**
  - Criterios: CA-01 a CA-06.
  - Revisar diff/enlaces y auditoría final de solo lectura; informar cualquier
    defecto o laguna antes de reparar, sin modificar aplicación o tests del repo.
  - Evidencia: PASS y cobertura completa, estados de cierre, horario y horas
    GitHub Projects; publicación e integración pendientes de permiso posterior.

Plan aprobado y ejecución solicitada expresamente por Ángel, con SQLite en
memoria, sin modificar código, abrir navegador ni publicar.
No se registra tiempo por tarea interna ni se autoriza código por crear el plan.

T-01–T-04 verificadas: 12 pruebas/267 aserciones existentes y 3 pruebas/133
aserciones temporales, todas correctas. Inspección de fuentes sin cambios.
Ángel confirma consulta en escritorio/móvil/teclado, apertura en otra pestaña
y recarga, completando T-05. Revisión final de cierre local PASS; T-06 completa.
Horario y GitHub Projects registrados en spec.md. Archivos temporales de
prueba y preflight retirados tras auditarlos, sin borrar carpetas ni bases.
Sin publicación ni integración realizadas por este cierre.
