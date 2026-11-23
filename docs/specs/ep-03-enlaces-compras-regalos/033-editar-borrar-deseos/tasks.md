---
spec: "033"
status: completed
---

# Tareas — 033

Plan de referencia: [plan.md](plan.md). Plan y contratos aprobados;
implementación solicitada, sin autorización de publicación.

- [x] **T-01 — Confirmar precondiciones y límites del cambio**
  - Criterios: CA-01–CA-08.
  - Comprobar aprobación del plan, rama/registro y diff; revisar consumidores
    del formulario, patrones de confirmación y configuración aislada de pruebas.
  - Evidencia esperada: alcance de archivos y guardia de pruebas confirmados;
    no tocar .env, datos, servidores ni migraciones.

- [x] **T-02 — Preparar contratos de edición y reglas compartidas**
  - Criterios: CA-02, CA-03, CA-06, CA-07.
  - Añadir GET de edición y PATCH en el grupo autorizado, resolución del deseo
    por espacio y props mínimas. Compartir validación con alta, WishUrl y WishPrice.
  - Evidencia esperada: fichero Inertia real, precarga correcta, validación igual
    al alta y rechazo de lectura/edición ajena sin fuga de información.

- [x] **T-03 — Guardar edición y movimiento atómicos**
  - Criterios: CA-02, CA-03, CA-05, CA-06, CA-07.
  - Transacción/bloqueo siguiendo store, revalidar miembro y lista destino;
    actualizar campos permitidos/FK sin alterar identidad, autor ni estado.
  - Evidencia esperada: mismo deseo movido sin duplicación, lista anterior
    conservada y redirección al destino; fallo SQL revierte y conserva input.

- [x] **T-04 — Añadir borrado individual con confirmación en servidor**
  - Criterios: CA-04, CA-05, CA-06, CA-07.
  - DELETE exige confirmed accepted y revalida pertenencia/membresía dentro
    de transacción. Error delete comprensible; redirigir con lista origen.
  - Evidencia esperada: petición sin confirmar no borra; fallo conserva todo;
    éxito borra solo un deseo y no su lista, tampoco al quedar vacía.

- [x] **T-05 — Reutilizar formulario y montar página de edición**
  - Criterios: CA-02, CA-03, CA-07, CA-08.
  - Extender WishForm con modo/valores iniciales sin romper alta/crear lista;
    en edición solo listas existentes. WishEdit con JournalLayout y retorno
    a lista origen. Registrar mappings en app.jsx y tests/TestCase.php.
  - Evidencia esperada: POST/PATCH correctos, precio cero/null distinguible,
    input retenido, Cancelar sin escritura y estilo móvil coherente.

- [x] **T-06 — Incorporar acciones contextuales y foco**
  - Criterios: CA-01, CA-04, CA-07, CA-08.
  - WishActions junto al título externo: tres puntos, Editar y Eliminar;
    confirmación contextual con Cancelar inicial, Escape y restauración.
    Deshabilitar durante envío y enfocar control estable después de borrar.
  - Evidencia esperada: ningún DELETE al abrir/cancelar, enlace externo intacto,
    errores visibles y uso por toque/teclado sin depender de hover.

- [x] **T-07 — Actualizar selección y navegación por historial**
  - Criterios: CA-03, CA-04, CA-05, CA-08.
  - Pasar contexto a WishList y recargar lists/selectedListId al volver por
    historial a Wishes/Wishes, siguiendo listener existente y sin bucles.
  - Evidencia esperada: lista destino tras mover, origen tras borrar, estados
    vacíos conservados y atrás/adelante sin valores o deseos antiguos.

- [x] **T-08 — Ampliar pruebas y ejecutar controles automáticos**
  - Criterios: CA-01–CA-08, con los límites manuales del plan.
  - Extender WishesTest para permisos entre miembros/espacios, manipulación,
    validaciones, atomicidad, confirmación, redirecciones y ausencia de red.
    Ejecutar suite aislada, Node existente, build, formato y diff.
  - Evidencia esperada: resultados y limitaciones reales, alta/listas y otros
    cajones sin regresión. No usar ni limpiar datos locales para tests.

- [x] **T-09 — Validar interacción y revisar evidencias**
  - Criterios: CA-01–CA-08.
  - Recoger comprobación de Ángel en escritorio/móvil/teclado: editar/mover,
    precio, cancelar/borrar, enlace, errores, foco y recarga/historial.
    Aplicar ustwo-verify y revisión test_reviewer de solo lectura.
  - Evidencia esperada: matriz por criterio y PASS o pendientes explícitos;
    no abrir navegador ni publicar sin autorización.

- [x] **T-10 — Registrar cierre documental y horas**
  - Criterios: CA-01–CA-08.
  - Solo tras verificar: completar spec/tasks y MEMORY con evidencias;
    anotar inicio/cierre, pausas comunicadas y horas GitHub Projects según regla.
  - Evidencia esperada: cierre trazable sin confundir verificación local con CI;
    commit, publicación, integración y copia a documentation pendientes de permiso.

## Evidencias de implementación y verificación automática — 23/11/2026

- T-01: rama/registro feat/editar-borrar-deseos coincidentes, base 2204d67,
  índice vacío, solo cambios de esta historia. Plan/contratos aprobados y
  reauditoría plan_reviewer PASS. Sin migración nueva ni cambios de datos locales.
- T-02–T-04: WishController comparte validación, campos y bloqueo con alta;
  las tres rutas son numéricas y quedan bajo el middleware existente.
  WishesTest prueba miembro distinto del autor, movimiento sin duplicación,
  conservación de autor/estado, precio cero/null/límite, aislamiento incluso
  perteneciendo a ambos espacios, invitado/ajeno/revocado y confirmación requerida.
  Inyección de QueryException en updated/deleted, después del SQL: rollback
  conserva todos los atributos y listas. Http::assertNothingSent en flujos nuevos.
- T-05–T-07: WishForm reutilizado con modo edición sin crear listas;
  WishEdit registrado en app y finder de tests. WishActions conserva enlace
  externo y ofrece confirmación, foco inicial Cancelar, Escape y restauración;
  título de lista enfocable tras éxito. Listener history recarga solo listas y
  selección de Wishes/Wishes, como el patrón existente. Revisión de código,
  sin afirmar todavía que foco/historial han sido observados en un navegador.
- T-08: preflight de configuración efectiva antes de arrancar PHPUnit:
  testing, SQLite :memory:, DB_URL vacía, configuración sin caché.
  `vendor/bin/phpunit --display-skipped`: 86 pruebas, 2456 aserciones, sin
  fallos; 2 omitidas específicas del seeder PostgreSQL. Primera ejecución
  detectó comparación por orden de atributos en dos pruebas nuevas; se corrigió
  la captura inicial leyendo fresh(), y pasó la suite completa posterior.
- `node --test tests/Frontend/wishRequest.test.js`: 1 prueba correcta.
  `npm.cmd run build`: correcto, 695 módulos. Prettier de la extensión aplicado
  a JSX/CSS cambiados. `git diff --check`: correcto. Sin paquetes nuevos.
- Web http://127.0.0.1:8000 HTTP 200; Vite /@vite/client y componente nuevo
  HTTP 200 en 5173. Servidores conservados sin reiniciar. No se abrió navegador.
- Auditoría UI por lectura de código y referencias locales de Impeccable:
  variables existentes, grid adaptable, confirmación en flujo sin overlay
  recortado, botones nativos/etiquetas/errores, sin nuevas animaciones.
  No se ejecutó motor descargable ni se midieron contraste/layout renderizados.
- Primera revisión test_reviewer: bloqueo de cierre por comprobación manual
  pendiente, sin bloqueos técnicos adicionales. Reauditoría solicitada tras
  la confirmación de Ángel indicada a continuación.

## Validación comunicada por Ángel — 23/11/2026

Ángel confirma que editar, mover y borrar deseos funciona en escritorio,
móvil y teclado y solicita cerrar la spec. La evidencia manual procede del
desarrollador, no de una sesión de navegador del agente. No se añaden detalles
de pulsaciones, tamaños, contraste medido o trazas que no haya proporcionado.

| Criterios | Evidencia final disponible |
| --- | --- |
| CA-01, CA-04, CA-08 | Interacción de edición/movimiento/borrado confirmada por Ángel en los tres contextos; menú, confirmación y gestión de foco revisados en código. |
| CA-02, CA-03 | Confirmación manual de edición/movimiento más pruebas de campos, identidad, autoría, estado, listas y opcionales. |
| CA-05 | Flujo manual confirmado, redirecciones y GET repetidos probados; listener de historial revisado. Sin traza independiente del navegador. |
| CA-06, CA-07 | Pruebas aisladas de permisos, pertenencia, payload manipulado, validación y rollback tras SQL. |

Ajuste posterior solicitado por Ángel: wishRequest trasladado a
resources/js/utils/wishRequest.js, imports de WishListForm y prueba actualizados.
Carpetas vacías Wishes/utils y Readings/ReadingCard eliminadas tras comprobar
ausencia de contenido y referencias. Node (1), build (695 módulos) y diff
comprobados de nuevo correctamente; no cambia el comportamiento probado en PHP.

## Cierre

Reauditoría test_reviewer PASS, sin bloqueos tras la confirmación de Ángel.
T-09/T-10 completadas; spec completada localmente, no publicada ni integrada.
Inicio 20:07 y cierre local 20:26 CET del 23/11/2026: 19 minutos, sin pausas
comunicadas; GitHub Projects **0.25 h estimadas** según la regla acordada.
MEMORY actualizado. No commit, push, merge ni navegador. PHP/Vite conservados.
