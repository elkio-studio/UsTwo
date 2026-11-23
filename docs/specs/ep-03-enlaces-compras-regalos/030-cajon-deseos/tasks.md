---
spec: "030"
status: completed
---

# Tareas — 030

- [x] **T-01 — Confirmar contratos y salvaguarda local**
  - Criterios: CA-05, CA-07.
  - Revisar cinco posiciones, pruebas/CI y preparar copia consistente antes
    de migrar; confirmar SQLite local sin mostrar secretos.
  - Evidencia: ruta verificada, copia previa y comparación prevista.
- [x] **T-02 — Persistir posición y proteger ocupación**
  - Criterios: CA-01, CA-05, CA-07.
  - Migración aditiva wish_slot y restricciones SQLite/PostgreSQL; cast y
    actualización de todos los llamadores de colocación y activeSpace.
  - Evidencia: tests bidireccionales, rechazos sin cambios y posición null inicial.
- [x] **T-03 — Añadir acceso autorizado y vista vacía**
  - Criterios: CA-04, CA-06.
  - Controlador index, rutas protegidas, mapping Inertia y WishesView con
    JournalLayout, vuelta, botón disabled y aviso aprobado.
  - Evidencia: tests de archivo Inertia, props, acceso y ausencia de alta.
- [x] **T-04 — Crear caja y animación de tarjeta**
  - Criterios: CA-02, CA-03.
  - Recursos WebP y WishBox; texto separado, tarjeta eslabones, hover/foco,
    toque directo y movimiento reducido. Sin dependencias adicionales.
  - Evidencia: build correcto y comprobación visual confirmada por Ángel.
- [x] **T-05 — Integrar en la estantería**
  - Criterios: CA-01, CA-02, CA-03, CA-05.
  - Selector añadir/mover Deseos, posición persistida y enlace único;
    conservar apariencia y comportamiento de tipos existentes.
  - Evidencia: colocación/movimiento y ajuste visual a huecos y tamaños.
- [x] **T-06 — Verificar regresión, seguridad y migración**
  - Criterios: CA-01, CA-04, CA-05, CA-06, CA-07.
  - WishesDrawerTest y expectativas previas; invitado/ajeno, manipulación,
    ocupación, validación y conservación. Migrar local con copia y comparar
    datos anteriores; migrate:status, suite, build y diff check.
  - Evidencia: resultados reales, bases aisladas para tests, sin pérdidas.
- [x] **T-07 — Validar interacción y documentar cierre**
  - Criterios: CA-02, CA-03, CA-04, CA-07.
  - Escritorio/móvil, hover/foco/toque, movimiento reducido, estado vacío
    y regreso. Auditoría final de solo lectura y registro de evidencias.
  - Evidencia: confirmación manual atribuida a quien la realiza, revisión PASS,
    inicio/cierre y horas GitHub Projects. Sin publicación automática.

Plan y propuesta técnica aprobados; implementación solicitada expresamente
por Ángel el 23 de noviembre de 2026, sin navegador ni publicación.
No se registra tiempo por tarea interna.

T-01 a T-06 implementadas y con controles automáticos correctos. Ángel confirma
la comprobación local en escritorio y móvil, teclado y movimiento reducido,
completando la validación manual de T-07. Revisión final: PASS, sin bloqueos
para cierre local; no se atribuye al agente haber observado el navegador.
Horario y GitHub Projects registrados en spec.md. Publicación y CI externos
quedan pendientes; no se realizan por marcar estas tareas completadas.
