---
spec: "031"
status: completed
---

# Tareas — 031

Revisión aprobada por Ángel: solo alta manual y euros. Se retiran tareas de
extracción; no se conservan servicios, rutas, hooks o pruebas sin consumidor.

- [x] **T-01 — Confirmar revisión y dependencias**
  - Criterios: CA-04, CA-05, CA-10.
  - Rastrear consumidores de extracción y conservar los usados por alta/listas.
  - Evidencia: retirada autorizada, sin nuevas dependencias ni red externa.
- [x] **T-02 — Persistencia y relaciones**
  - Criterios: CA-01, CA-02, CA-10.
  - Tablas/FK aditivas, autor y pending, listas sin defaults, sin tocar wish_slot.
- [x] **T-03 — Alta/listas y permisos**
  - Criterios: CA-01, CA-02, CA-03, CA-06.
  - Rutas autorizadas, validación, transacción, autoría real y retorno a lista.
  - Evidencia: tests de miembros/ajenos, input conservado y sin filas parciales.
- [x] **T-04 — URL manual sin consultas**
  - Criterios: CA-04, CA-05, CA-07.
  - Validar sintaxis segura, eliminar endpoint/transporte/parser de extracción.
  - Evidencia: alta sin red, esquemas inválidos rechazados y metadatos 404.
- [x] **T-05 — Restringir precio a euros**
  - Criterios: CA-03, CA-08, CA-10.
  - Sin selector, EUR derivado en servidor, precisión exacta; nueva restricción
    que no convierte ni borra registros previos.
  - Evidencia: rechazo de USD/GBP en app/base y prueba de guardia de migración.
- [x] **T-06 — Formulario manual y listas**
  - Criterios: CA-01, CA-03, CA-09.
  - WishCreate/WishForm/WishListForm, creación sin anidar ni perder datos;
    errores JSON/CSRF comprensibles y eliminación del hook de extracción.
  - Evidencia: código y test Node; observación de interfaz pendiente de T-10.
- [x] **T-07 — Listado y navegación**
  - Criterios: CA-02, CA-06, CA-07, CA-08, CA-09.
  - Lista seleccionada, texto/enlace seguro, EUR o ausencia y autor real.
- [x] **T-08 — Migrar conservando datos**
  - Criterios: CA-08, CA-10.
  - Copia consistente antes de cada migración, migrate:status y comparación.
  - Evidencia: lotes 6 y 7, valores previos idénticos, lista/deseo EUR intactos.
- [x] **T-09 — Revisar regresión final**
  - Criterios: CA-01 a CA-08, CA-10.
  - Suite SQLite/Node, build, Prettier, diff y auditoría de solo lectura del
    alcance manual final; PG/CI se declaran pendientes si no se ejecutan.
  - Evidencia: 81 pruebas/2280 aserciones, sin fallos y dos omisiones PG;
    Node y build (692 módulos) correctos. Revisión sin defectos adicionales.
- [x] **T-10 — Validar interfaz y registrar cierre**
  - Criterios: CA-03, CA-08, CA-09, CA-10.
  - Comprobar formulario final sin selector/extracción, crear/elegir lista,
    errores con datos conservados y regreso en escritorio/móvil/teclado.
  - Evidencia: confirmación atribuida, revisión PASS, inicio/cierre y horas
    GitHub Projects. No cierre ni publicación sin evidencias/autorización.

Ángel confirma el alta manual final en euros en escritorio, móvil y teclado.
T-10/CA-09 resueltos; revisión final de cierre local PASS. Horario y horas
GitHub Projects registrados en spec.md. No se atribuyen pruebas visuales al
agente ni se considera comprobado CI/PostgreSQL por este cierre.

No se registra tiempo por tarea interna. Sin commit ni subidas autorizados.
