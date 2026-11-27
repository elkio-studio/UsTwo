---
spec: "038"
status: approved
---

# Plan técnico — 038

## Resumen y límites

Refactor técnico **sin US asociada**, según [spec aprobada](spec.md). Trasladar
layouts, extraer la estructura del recibidor y separar el movimiento en cuatro
hooks, conservando diseño, DOM, estado y comportamiento. No limpiar candidatos
del informe de código muerto, añadir servicios, dependencias o funcionalidades.
Plan aprobado y [tareas en curso](tasks.md).
Ángel aprueba el plan y solicita implementar y verificar,
sin navegador ni publicación; autorización registrada el 27/11/2026.

## Layouts y composición

1. Trasladar JournalLayout.jsx y JournalLayout.module.css a
   resources/js/layouts/JournalLayout. Mantener nombre y props; adaptar imports
   relativos de AccountMenu/Invitations y actualizar todos sus consumidores.
   No dejar reexports de compatibilidad ni una segunda copia en components.
2. Crear resources/js/layouts/AccessLayout con JSX y módulo CSS. Su contrato
   es `{ registering, children }`: main con data-registering, Head con el
   título actual según modo, bienvenida, luna y children en el mismo orden.
   Mover íntegro el CSS de LoginAccessForm a AccessLayout, sin modificar reglas,
   valores, media queries o ámbito de variables --recibidor-*.
3. LoginAccessForm mantiene useState y AccessForm con registering/onModeChange,
   envuelto en AccessLayout. Conservar import global de CSS en la página. No key
   por modo ni condiciones que desmonten formulario/layout al alternar. Eliminar
   el módulo CSS de la página solo cuando su contenido esté trasladado y nadie
   lo importe. Actualizar comentarios JSX conforme a su nueva responsabilidad.
   No añadir keys al layout o a AccessForm; conservar la key interna actual
   del elemento form dentro de AccessForm y su semántica.
4. AccountMenu permanece en components/Journal/AccountMenu e Invitations en su
   ubicación actual; no extraer otra cabecera ni mover menús sin necesidad.

No cambiar la resolución de páginas de app.jsx ni adoptar layouts persistentes.
Mantener privatePage y todas las variantes de JournalLayout, aunque alguna no
tenga consumidores activos; su limpieza queda fuera de este refactor.

## Movimiento: propiedad del estado e interfaces internas

Salida pública de useShelfMovement inalterada: `{root, movement, message, error}`.
Shelf no conoce la división interna ni cambia sus indicadores o payloads.

| Pieza | Responsabilidad y comunicación |
| --- | --- |
| useShelfMovement | Conserva refs de operación, candidato pendiente, timer, supresión y datos recientes; estado React, snapshot, anuncios, pick/move/save/cancel y retorno de foco. Único escritor del estado de operación. |
| useShelfPointer | Eventos ratón/touch/clic, longpress, bloqueo contextual/drag nativo y cancelación por sistema/blur/salida. Actualiza refs temporales compartidas y solicita acciones al coordinador. Sin copia del estado React de movimiento. |
| useShelfKeyboard | Listener keydown y comportamiento actual Espacio/flechas/Enter/Escape; usa las mismas acciones, geometría y supresión compartida. |
| useShelfGhost | Ref privada del clon, creación/posición/retirada y limpieza DOM. Sin estado de operación, listeners de entrada, peticiones ni anuncios. |

Los cuatro archivos se mantienen directamente en resources/js/hooks, sin un
framework de gestos ni hooks adicionales para tareas triviales.

### Wiring y referencias vigentes

- useShelfPointer y useShelfKeyboard reciben root, spaceId y una referencia
  privada estable `bindings` creada en el coordinador y actualizada en cada
  render. Contiene las refs compartidas y únicamente acciones/datos que usan
  estos adaptadores. Cada evento consulta bindings.current, no captura una
  operación o un callback obsoleto. No es un store público ni módulo singleton.
- Refs de operación/pending/timer/supresión tienen una sola instancia por Shelf.
  Las entradas no guardan estados paralelos de picked/dragging/saving.
- useShelfGhost recibe root y spaceId; ofrece `show(path, point)` y `remove()`
  con referencias estables. Recibe identidad explícita, no obtiene el movimiento
  de otra copia de estado. El coordinador llama remove en guardar/cancelar.
- Los efectos de entradas dependen de root/spaceId; no de objetos de acciones
  recreados, movement, message o punto actual. Un render ordinario no retira
  listeners ni limpia timers. No usar useEffectEvent, nuevo contexto o reducer
  como patrón adicional para resolver esta extracción.
- El coordinador conserva la limpieza del timer/candidato y operación al
  desmontar/cambiar de estantería; cada hook de entrada retira sus listeners;
  el hook visual retira su clon. No setState ni peticiones en limpiezas.
- Mantener la key por activeSpace.id existente en ShelfDashboard y la semántica
  actual de los callbacks de Inertia; sin nuevos reintentos o gestión de red.

### Eventos y comportamiento que se conservan

Pointer conserva los 13 listeners actuales excepto keydown: pointerdown local,
pointermove/up/cancel de ventana, touchstart/move/end/cancel locales, click en
captura local, contextmenu/dragstart locales y blur/pointerout de ventana.
Keyboard conserva keydown de ventana en captura. Mantener passive:false de
pointermove/touchmove, passive:true de touchstart y la misma fase de captura.

450ms y8px, detección del dedo/puntero activo, modificadores, segundo dedo,
scroll, clic sintetizado y supresión sin caducidad permanecen iguales. La
supresión se comparte con teclado y cancelación; no encerrarla en Pointer.
Conservar los finales separados: mouseUp coloca/cancela, touchEnd sin drag
mantiene recogida para el siguiente toque. Escape fuera del root y Enter en
reposo siguen teniendo el comportamiento vigente.

Ghost conserva cloneNode, conversión de canvas a imagen con fallback actual,
inert/aria-hidden/pointer-events y retirada de IDs/data-slot/data-drawer/
data-origin/data-target. Mantener geometría, opacidad, orden de capas y CSS;
no montar otro globo ni introducir una animación.

### Duplicación y utilidades

Añadir en utils/shelfMovement.js solo dos cálculos comunes utilizados por ambas
ramas: distancia entre punto inicial/actual y creación del candidato con path,
origin, copia del punto inicial, id e input. Conservar nombres/campos actuales,
sin validar otra vez reglas de producto ni normalizar IDs de forma distinta.
Los helpers no importan React, DOM o router. Ya existen utilidades de destino,
teclado, snapshot y release; no duplicarlas ni fusionar finales táctiles/ratón.

No exigir una reducción fija de líneas totales. La extracción y los imports
pueden añadir líneas; el criterio es evitar lógica repetida y responsabilidades
mezcladas, no comprimir o introducir abstracciones para ocultar longitud.

## Fases y verificación

1. Confirmar aprobaciones, registro Git y solicitud explícita; conservar diff
   base y revisar diseño antes de modificar interfaz.
2. Reubicar JournalLayout y extraer AccessLayout; comprobar imports/build antes
   de cambiar el movimiento, para aislar regresiones de composición.
3. Extraer ghost, después entradas de puntero/teclado y finalmente helpers
   repetidos, manteniendo la coordinación central. Revisar ciclo de efectos.
4. Ejecutar controles automáticos y revisión posterior de diseño; validación
   humana de equivalencia y auditoría final con test_reviewer.

| Criterios | Verificación prevista |
| --- | --- |
| CA-01 | Imports y build; páginas con temas shelf/shelf-index/cinema y Sitios; cabecera, paneles, enlaces y cierre exterior/teclado sin cambios. |
| CA-02 | Alternar acceso/registro con campos escritos y errores: formulario no desmontado, contraseña/visibilidad según comportamiento vigente; título, variables CSS y responsive iguales. |
| CA-03 | Diff de propiedad del estado y contrato público; una sola operación/snapshot/supresión; listeners no reinstalados en renders ordinarios. |
| CA-04 | Node para distancia en/reposo/bajo/sobre umbral, candidato ratón/táctil con id correcto y copia estable del punto; Node existente de release espera/guarda/cancela. Prueba manual de longpress vs drag. |
| CA-05 | Ratón/touch/toque/teclado en cinco tipos, Escape antes/después de recoger, cancelación manteniendo pulsado sin límite, siguiente clic legítimo, segundo dedo, fuera/origen y cambio de estantería; doble envío/conflictos y foco. |
| CA-06 | Ghost y señales 037: origen discontinuo, destino sólido distinto, sin indicadores clonados; globo/libros/caja no cambian ni ocultan destino. Reduced motion y scroll. |
| CA-07 | Node, build, Prettier y diff; grafo de imports sin rutas antiguas, CSS viejo sin consumidores antes de borrarlo, ausencia de !important/dependencias/backend nuevo y convenciones actualizadas. |

Comandos derivados del proyecto: `node --test tests/Frontend/shelfMovement.test.js`,
`npm.cmd run build`, Prettier de archivos modificados y `git diff --check`.
No añadir tests que comparen texto JSX/CSS ni snapshots que copien implementación.
Sin instalar runner de hooks/DOM; Node valida funciones puras, no el ciclo real
de React, foco o interacción. Esos límites se cubren por revisión y prueba humana.

No ejecutar seeders ni migraciones o crear registros para revisar estilos.
PHP no cambia y no necesita repetir su suite completa por este refactor; si un
hallazgo exige comprobar backend, revisar alcance y usar guardia previa con
testing/SQLite :memory:/URL vacía/sin caché. Nunca usar .env, SQLite local o
Supabase para pruebas destructivas. No navegador salvo permiso específico.

## Documentación, entrega y riesgos

Actualizar docs/convenciones-frontend.md: layouts como estructura compartida,
components como piezas, pages como coordinadoras; sustituir el ejemplo de
JournalLayout dentro de components y describir responsabilidades de los cuatro
hooks sin duplicar un inventario del código. Sin otro documento de arquitectura.
Registrar resultados en spec/tasks/MEMORY y horas GitHub Projects bajo título al
cerrar. No cerrar sin equivalencia visual y de interacción confirmada.

Riesgos principales: perder herencia CSS del recibidor, remontar formularios,
callbacks obsoletos, supresión partida y timers limpiados por render. Las
medidas anteriores forman parte obligatoria de la implementación, no mejoras
opcionales. No modificar capacidad privatePage ni otros candidatos del informe.

Sin migración ni despliegue especial. Conservar .env, SQLite y servidores.
Commit/publicación/integración en dev, CI y traslado a documentation quedan
para permisos posteriores. La implementación está autorizada expresamente;
las tareas y evidencias se registran en tasks.md. Sin US asociada.

Revisión previa de diseño/arquitectura: APTO sin bloqueos. Requiere mantener
bindings local/vigente, entradas sin limpieza de estado duplicada, identificadores
de puntero/dedo y ancestro CSS del recibidor como se especifica arriba.
Revisión plan_reviewer: PASS, sin bloqueos. Plan aprobado por Ángel;
los riesgos de interacción y visuales se acreditarán con la validación prevista.
