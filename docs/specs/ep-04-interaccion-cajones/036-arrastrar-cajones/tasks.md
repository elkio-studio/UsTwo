---
spec: "036"
status: completed
---

# Tareas — 036

Plan y tareas aprobados; implementación solicitada expresamente por Ángel.
Sin permiso para commits, publicación o navegador.

- [x] **T-01 — Confirmar precondiciones y revisar diseño**
  - Criterios: CA-01–CA-10.
  - Aprobar plan/contratos y obtener petición explícita de implementar; revisar
    registro Git, diff, tipos, triggers y consumidores. Diseño previo solo lectura.
  - Evidencia: autorización y lista real de impactos, sin decisiones encubiertas.
- [x] **T-02 — Implementar movimiento e intercambio atómicos**
  - Criterios: CA-02, CA-03, CA-07, CA-08.
  - Ruta protegida, enum/snapshot, transacción/lock, revalidación, UPDATE único,
    error/redirect; coordinar altas con bloqueo conservando su contrato.
  - Evidencia: pruebas Feature aisladas de pares/vacío/permisos/conflictos/rollback,
    sin tocar tablas de colecciones ni introducir migraciones.
- [x] **T-03 — Implementar gestos y cancelación uniformes**
  - Criterios: CA-01, CA-04, CA-05, CA-08.
  - Hook local, umbrales aprobados, ratón/touch, recogida vs arrastre, destino,
    clic residual, petición única y limpieza; conservar cinco representaciones.
  - Evidencia: Node de transiciones relevantes y manual de clic/scroll/longpress,
    fuera/origen y cancelación; sin escritura antes de confirmar.
- [x] **T-04 — Añadir teclado, toque, feedback y alta depurada**
  - Criterios: CA-04, CA-05, CA-06, CA-09, CA-10.
  - Flechas geométricas, destinos vacíos, tocar destino/origen para colocar/cancelar,
    anuncios/foco/errores, ghost y hover/reduced motion; quitar Mover aquí y
    filtrar tipos del + y conservarlo con aviso si no quedan tipos disponibles.
  - Evidencia: equivalencia de mover/intercambiar por entradas, contenido intacto,
    aperturas normales y accesibilidad manual, revisión posterior de diseño.
- [x] **T-05 — Ejecutar regresión y revisión técnica**
  - Criterios: CA-01–CA-10.
  - Guardia SQLite memoria antes de tests; Feature de cajones/movimientos,
    Node, build, formato/diff y revisión de rastreados/no rastreados.
  - Evidencia: comandos/resultados reales y omisiones, sin datos locales tocados.
- [x] **T-06 — Validar y cerrar con evidencias**
  - Criterios: CA-01–CA-10.
  - Confirmación humana ratón/móvil/teclado/recarga/errores/reduced motion;
    ustwo-verify y test_reviewer solo lectura. Actualizar spec/tasks/MEMORY,
    horas GitHub Projects al principio y limitaciones/publicación pendiente.
  - Evidencia: PASS final con matriz CA y pruebas, sin atribuir al agente una
    observación renderizada inexistente ni publicar sin permiso.

## Evidencias de implementación — 25/11/2026

T-01: plan/contratos/tareas aprobados y ejecución solicitada por Ángel.
Rama/registro coinciden con feat/arrastrar-cajones, base0923c66. Revisión previa
de diseño APTO, principal único escritor. Sin migraciones ni dependencias.

T-02: PUT mover protegido; snapshot de cinco columnas validado/comparado bajo
bloqueo, membresía revalidada y UPDATE único para mover/intercambiar. Altas
existentes usan el mismo bloqueo conservando su contrato. Pruebas cubren
20 pares ordenados entre los cinco tipos, vacíos, origen, entrada inválida,
ajenos/revocados, snapshot desfasado, fallo SQL con rollback y conservación de
las cinco colecciones y puntuaciones/notas. La simulación de conflicto no
acredita concurrencia PostgreSQL real; queda para su entorno aislado.

T-03/T-04: hook local distingue espera de450ms, recogida, drag, destino por
toque y guardado; tolerancia8px. Cancela fuera/origen/Escape/sistema/salida de
ventana, evita drag nativo, limpia timers/listeners y snapshot al cambiar de
estantería mediante key por ID en ShelfDashboard. Supresión del clic residual
sin caducidad hasta consumirlo o iniciar otra acción física; no expira mientras
se mantiene pulsado tras cancelar. Flechas usan geometría, huecos vacíos siguen
accesibles sin +, anuncios/foco. Ghost inerte no interactivo,
canvas convertido a imagen estática; no nuevo renderer/animación. Hovers pausados.
Se retira Mover aquí y se muestran únicamente tipos no colocados en el alta.
Marcadas tareas de implementación, no validación manual de esos comportamientos.

T-05: guardia previa a PHPUnit exige testing, SQLite :memory:, URL vacía y sin
caché de configuración. Suite completa: **96 pruebas/2736 aserciones**, sin
fallos, **2 omitidas** porque los seeders necesitan PostgreSQL aislado.
Primeros intentos detectaron datos de fixture inválidos y snapshot de modelo
sin refrescar; corregidos sin relajar restricciones ni cambiar reglas de producto.
Node shelfMovement: **3 pruebas correctas** (geometría, límites, transiciones
y snapshot estable). Build: **705 módulos**, correcto; Prettier/diff correctos.
Sin uso de la base local ni Supabase. No era necesaria migración.

Diseño posterior detectó clic residual tras Escape; corregido sin temporizador
de caducidad y con guardia de salida de ventana. Auditoría test_reviewer detectó
estado heredado al cambiar estantería; resuelto con key por ID y confirmado por
lectura. Sin otro defecto técnico bloqueante; cierre BLOCKED por validación
manual pendiente. Reevaluación del delta de diseño: bloqueos resueltos por
lectura, sin nuevos defectos concretos. No sustituye la prueba manual.

T-06 pendiente: comprobar cinco tipos con ratón, clic corto/drag/cancelar;
en móvil scroll, mantener pulsado/arrastrar/levantar y tocar destino, origen,
cancelación del sistema y clic residual; teclado/foco, cambio de estantería,
conservar +, recarga/errores y reduced motion. Sin observación renderizada del agente.

Delta solicitado en prueba: guía/status solo accesibles, sin texto superpuesto;
errores visibles. + de vacíos permanece con todos los tipos colocados;
diálogo informa y permite cancelar sin Mover aquí ni duplicados. Diseño previo
coherente y posterior APTO por lectura, sin bloqueos; build705 y diff correctos.
Comprobación visual/foco pendientes de Ángel.

Ángel solicita retirar Cancelar movimiento: eliminados botón, callback público
del hook y CSS exclusivo; también el retorno describe no consumido. Se conserva
cancelación interna Escape/origen/fuera/sistema. Anuncio accesible ya indica
tocar origen para cancelar, sin nombrar un control eliminado.
Comprobación inicial por terminal: puertos8000/5173 no respondían. Primer intento
de arranque bloqueado por límite de la revisión automática de permisos, sin
ejecutarse. Tras «sigue» de Ángel, PHP y Vite iniciados con lanzador existente
que exige SQLite local y no crea/sustituye la base. Sin navegador, commit o
publicación.
Comprobación posterior por terminal: /acceso en8000 y /@vite/client en5173
responden HTTP200. Se dejan ambos procesos activos.

## Cierre y matriz final — 25/11/2026, 08:59 CET

Los pendientes manuales descritos arriba son históricos: resueltos mediante
confirmación final de Ángel en escritorio, móvil y teclado. T-06 completada;
reauditoría test_reviewer PASS. No se atribuye observación visual al agente.

| Criterios | Evidencia final |
| --- | --- |
| CA-01, CA-04, CA-05 | Validación humana de mover/intercambiar en escritorio y móvil, revisión de gestos/cancelación y Node de transiciones/límites. |
| CA-02, CA-03 | Feature de vacíos y 20 pares ordenados, persistencia GET, atomicidad y contenido/valoraciones conservados. |
| CA-06 | + persistentes, tipos filtrados, aviso sin disponibles y ausencia de Mover aquí; revisión de diseño y validación humana. |
| CA-07, CA-08 | Feature de permisos, manipulación, conflicto vigente y rollback; sin cambios parciales. |
| CA-09 | Validación de teclado por Ángel y revisión de geometría/foco/aislamiento del gesto por estantería. |
| CA-10 | Revisión de estados accesibles y movimiento reducido; validación humana de la interfaz, sin mediciones renderizadas del agente. |

Suite96/2736 sin fallos, 2 omisiones por PostgreSQL aislado; Node3, build705,
formato y diff correctos. Horas/cálculo en spec.md; sin tiempos por tarea.
Pendientes publicación/integración/CI/documentation, sin permiso para realizarlos.

## Ajustes de revisión tras cierre

Por petición de Ángel: hook a resources/js/hooks con imports actualizados,
retirada de ayuda estática y aria-describedby, y eliminación de los dos
!important mediante orden/especificidad. Norma registrada en AGENTS y
convenciones frontend. No se cambia la guarda de ocupado del alta, que sigue
teniendo consumidores y protege un contrato diferente al arrastre.
Auditoría transversal en docs/revision-codigo-sin-uso.md; hallazgos adicionales
solo documentados. Node3/build705/Prettier/diff correctos, cero !important en
CSS propio y revisión posterior de diseño APTO por lectura. Sin backend nuevo,
navegador, commits ni publicación. No altera el cómputo de cierre ya registrado.
