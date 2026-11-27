---
id: "038"
title: "Reorganizar layouts y separar responsabilidades del movimiento de cajones"
status: completed
---

# 038 — Reorganizar layouts y separar responsabilidades del movimiento de cajones

**GitHub Projects: 0.5 h.** Tiempo transcurrido estimado: 27/11/2026,
12:52–13:16 CET (24 minutos), redondeado al cuarto de hora según el proceso.
Sin US asociada; registrar en la tarea de refactor técnico correspondiente.

Inicio registrado: 27/11/2026, 12:52 CET, al crear esta especificación.
La definición conversacional fue previa y no dispone de hora inicial registrada.
Rama: refactor/layouts-movimiento-cajones, desde dev c6a9751.
Refactor técnico transversal, sin nueva funcionalidad de producto.
Referencias: [frontend modular 011](../011-frontend-modular/spec.md),
[movimiento 036](../../ep-04-interaccion-cajones/036-arrastrar-cajones/spec.md),
[indicadores 037](../../ep-04-interaccion-cajones/037-indicadores-destino/spec.md)
y [convenciones frontend](../../../convenciones-frontend.md).

## Problema y objetivo

JournalLayout representa la estructura compartida de la mayoría de páginas,
pero está agrupado con componentes pequeños. El recibidor de acceso/registro
mezcla composición visual con coordinación de su modo. useShelfMovement tiene
457 líneas y reúne estado, persistencia, ratón, táctil, teclado y copia visual.

Separar esas responsabilidades de manera explícita sin cambiar diseño,
comportamiento, contratos públicos ni datos. No perseguir una cantidad fija de
líneas: reducir duplicación real y facilitar lectura y pruebas, sin fragmentar
por tamaño ni crear un sistema genérico de drag and drop.

## Alcance incluido

- Mover JournalLayout y su CSS a resources/js/layouts/JournalLayout, actualizando
  todos los imports y conservando sus props, temas, DOM y comportamiento.
- Crear AccessLayout con registering/children para main, título del documento,
  bienvenida/luna y estilos del recibidor. Mantener su data-registering y las
  variables CSS en el mismo ancestro del formulario.
- La página de acceso conserva useState y la coordinación del modo. AccessForm
  conserva formularios, errores, visibilidad de contraseña y envíos; permanece
  montado al alternar acceso/registro, sin key por modo ni ramas que lo remonten.
- AccountMenu e Invitations siguen siendo componentes con sus responsabilidades
  actuales. Layouts sigue siendo composición normal, sin layouts persistentes
  de Inertia ni cambios al registro de páginas de app.jsx.
- Separar useShelfMovement en coordinador, useShelfPointer, useShelfKeyboard y
  useShelfGhost dentro de resources/js/hooks. Funciones puras en utils.
- Unificar cálculo de distancia y creación común del gesto pendiente cuando
  aporten reutilización real. Conservar diferencias de ratón y táctil.
- Actualizar las convenciones existentes para incluir layouts y sus límites.

## Alcance excluido

- Nuevas páginas, funcionalidades, gestos, controles o cambios visuales.
- Cambios de backend, rutas, payloads, permisos, esquema, migraciones o APIs.
- Dependencias nuevas, renderer de globo/cajones alternativo o framework de estado.
- Limpieza de privatePage, readOnly u otros candidatos del informe de código
  sin uso; esa revisión funcional corresponde a otra historia.
- Unificar el aspecto distinto del acceso con las páginas privadas, o crear
  más variantes de layout para Estantería, Sitios o colecciones.
- Abrir navegador, publicar, integrar, desplegar o crear commits en esta fase.

## Actores y flujos conservados

- Visitante alterna acceso/registro y usa formularios sin perder datos ni errores
  como consecuencia de trasladar la estructura visual.
- Miembro navega por las páginas privadas con los mismos títulos, cabecera,
  menús, paneles, enlaces de vuelta, temas y bandeja de invitaciones.
- Miembro mueve/intercambia con ratón, pulsación mantenida, toque y teclado
  según 036/037; el origen y los destinos conservan sus señales diferenciadas.

## Reglas de separación e interfaces

- JournalLayout mantiene title/children/back/headerActions/privatePage/className/
  theme. Trasladar archivos no añade wrappers DOM ni altera jerarquía CSS.
- AccessLayout recibe registering y children; deriva el mismo título actual.
  No recibe datos de cuentas ni envía formularios. Eliminar el CSS anterior
  del recibidor solo cuando esté completamente trasladado y sin consumidores.
- useShelfMovement conserva su salida root/movement/message/error. Es la única
  autoridad del estado de operación, snapshot, guardado, errores y cancelación.
- useShelfPointer gestiona ratón/táctil y clic; useShelfKeyboard maneja teclas;
  useShelfGhost administra creación, posición y retirada de la copia.
- No duplicar el movimiento en estados independientes. La supresión del clic
  residual es compartida con teclado y cancelación, no privada e inaccesible
  en el hook del puntero.
- Referencias/callbacks vigentes y ciclo de listeners estable: un render o un
  cambio de destino no reinstala listeners ni limpia el timer del gesto activo.
- Ratón y táctil comparten operaciones pertinentes, pero soltar un arrastre
  y levantar el dedo sin arrastrar siguen teniendo resultados diferentes.
- Mantener ghost inerte, canvas convertido a imagen y ausencia de indicadores
  de origen/destino en la copia; no iniciar otra animación ni otro canvas.
- Conservar 450ms/8px, anuncios, errores, foco, cancelación, prevención del clic
  residual, scroll normal y preferencias de movimiento reducido.

## Errores y casos límite

- Alternar formularios no los remonta ni pierde sus valores. Mantener los
  estados existentes sin incorporar una persistencia adicional.
- Escape antes/después de recoger, origen/fuera, segundo dedo, salida de ventana,
  cambio de estantería y desmontaje limpian el gesto como hasta ahora, sin
  guardar por efecto de la limpieza ni afectar al siguiente clic legítimo.
- Guardado único, errores, snapshot desfasado y retorno de foco conservan la
  semántica de 036. No cambiar el comportamiento del servidor para adaptar hooks.
- Conservación de imports CSS, variables heredadas, temas, responsive y foco
  durante la reubicación. No introducir !important para arreglar regresiones.

## Criterios de aceptación

- **CA-01.** JournalLayout vive en layouts con CSS asociado y todos sus
  consumidores funcionan con las mismas props, temas, DOM y paneles.
- **CA-02.** AccessLayout contiene la composición visual del recibidor; página
  y AccessForm mantienen modo, campos, validaciones y envíos sin remontaje al
  alternar acceso/registro ni cambios visuales en escritorio/móvil.
- **CA-03.** Los cuatro hooks tienen las responsabilidades definidas y hay
  una única fuente de estado/snapshot/supresión de clic; Shelf conserva la
  misma interfaz pública de useShelfMovement.
- **CA-04.** El cálculo de distancia y creación común del gesto no se duplican
  innecesariamente; ratón, longpress sin arrastre y arrastre táctil conservan
  sus transiciones, incluido el segundo toque de destino.
- **CA-05.** Teclado, cancelación, foco, clic residual, scroll, listeners y
  limpieza al cambiar de estantería funcionan como en 036/037, sin guardados
  dobles ni reinicios de timer causados por renders.
- **CA-06.** Ghost e indicadores conservan representación, inert, conversión
  de canvas, límites y limpieza; origen distinguible y destino reconocible.
- **CA-07.** Node existente y casos relevantes de cálculos extraídos, build,
  formato e imports pasan; no hay copias abandonadas ni CSS sin consumidor
  creado por la extracción. Convenciones actualizadas, sin dependencias ni
  cambios de backend, contratos o datos.

## Decisiones confirmadas y estado SDD

Ángel acepta la separación de hooks propuesta y elige incluir el recibidor
en los layouts. Después solicita ejecutar el plan conversacional, que exige
formalizar el alcance mediante SDD antes de implementar. Autoriza expresamente
crear esta rama y la spec 038 desde dev.

Esta versión transcribe ese alcance sin nuevas decisiones de producto. Estado
approved: Ángel aprueba expresamente la spec el 27/11/2026 como refactor técnico
sin US asociada y solicita preparar plan/tareas en la rama indicada sin tocar
código. La autorización no incluye ejecución de código, commits o publicación.
Ángel aprueba posteriormente el plan SDD y solicita implementar y verificar,
incluida revisión de diseño, sin navegador ni publicación. Implementación y
controles automáticos registrados en tasks.md. Ángel confirma que todo se ve
bien en local y solicita cerrar y subir la rama el 27/11/2026. Validación humana
aceptada para la equivalencia visual y de interacción; no es una prueba de
navegador realizada por el agente. Spec completada tras auditoría final.

## Validación prevista

Revisión de diseño/arquitectura de solo lectura antes y después; sin alterar
aspecto para justificar la nueva organización. Revisar imports y consumidores.
Node y build no acreditan equivalencia visual ni ciclo real de eventos: confirmar
acceso/registro sin pérdidas, temas/paneles y movimientos de cinco tipos con
ratón, táctil y teclado, cancelación/clic residual, cambio de estantería y errores.
Sin navegador del agente salvo autorización, puede validar Ángel.

No pruebas destructivas ni migraciones. Si fuese necesario ejecutar pruebas PHP,
guardia previa para testing/SQLite :memory:/URL vacía/sin caché, nunca datos
locales ni Supabase. No instalar otro runner por iniciativa propia.

Revisión de diseño/arquitectura: APTO, sin decisiones nuevas ni bloqueos.
Revisión spec_reviewer: PASS; quedan riesgos de regresión cubiertos por los
criterios, no decisiones funcionales pendientes. No sustituye aprobación humana.
