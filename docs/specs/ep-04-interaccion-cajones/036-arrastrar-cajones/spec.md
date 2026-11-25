---
id: "036"
title: "Mover e intercambiar cajones mediante arrastre"
status: completed
---

# 036 — Mover e intercambiar cajones mediante arrastre

**GitHub Projects: `0.75` h estimadas.**

Inicio registrado: 25/11/2026, 08:22 CET, al crear esta especificación.
Las indicaciones de producto se recogieron previamente en la conversación;
no se dispone de hora inicial verificable para esa parte.
Épica: [EP-04, issue #26](https://github.com/elkio-studio/UsTwo/issues/26).
Rama: feat/arrastrar-cajones, desde dev 0923c66.
Sprint de cierre previsto: Sprint 2. Capacidad orientativa: 4 h/día,
lunes a viernes, 20 h/sprint; estimación pendiente de validación técnica.

## Problema y objetivo

Reorganizar visualmente la estantería sin abrir el diálogo para mover un
cajón. Arrastrar a un hueco vacío mueve el cajón; arrastrar a uno ocupado
intercambia ambos. Las colecciones conservan su contenido y sus permisos.

## Contexto existente

Shelf presenta ocho huecos y cinco tipos: Películas, Series, Sitios, Lecturas
y Deseos. Un clic abre la colección; los huecos vacíos muestran un + cuyo
diálogo permite añadir tipos o «Mover [nombre] aquí». El servidor actual
rechaza destinos ocupados: todavía no implementa intercambios.

Lecturas y Deseos usan componentes de enlace propios. El nuevo comportamiento
debe cubrir los cinco tipos y conservar sus representaciones y animaciones.

## Alcance incluido

- Arrastrar un cajón colocado con ratón.
- En móvil, iniciar arrastre manteniendo pulsado el cajón; el desplazamiento
  normal de la página no debe iniciar movimientos.
- Mover a vacío o intercambiar con ocupado dentro de la misma estantería.
- Persistir posiciones únicamente tras completar una operación válida.
- Retirar «Mover [nombre] aquí» del diálogo actual.
- Mantener el alta de tipos no colocados mediante el +, sin duplicar tipos.
- Mantener los + en los huecos vacíos incluso con todos los tipos colocados;
  sin tipos disponibles, el diálogo informa y permite cancelar, sin duplicados.
- Manejo con teclado y alternativa de tocar destino tras recoger el cajón.
- Cancelación, errores, conservación de contenido, permisos y accesibilidad.

## Alcance excluido

- Mover entre estanterías, borrar cajones, cambiar capacidad o configurar tipos.
- Rediseñar imágenes, globo, libros o caja de Deseos.
- Historial, deshacer persistente o movimientos múltiples simultáneos.
- Elegir librerías, cambios de esquema o contratos técnicos durante esta fase.

## Actores

- Miembro de la estantería: reorganiza sus cajones con los permisos vigentes.
- Usuario no miembro o no autenticado: no puede modificar sus posiciones.

## Flujo principal

1. El miembro inicia arrastre desde un cajón existente. Con ratón, un clic
   ordinario continúa abriendo la colección; el movimiento debe distinguirse
   de ese clic. En móvil, es necesaria la pulsación mantenida.
2. Durante el gesto se identifica el cajón y el destino sin modificar datos.
3. Al soltar en un vacío, el origen queda vacío y el cajón ocupa el destino.
4. Al soltar en un ocupado, ambos cajones intercambian sus posiciones.
5. Tras éxito, la estantería muestra la distribución guardada. Recargar o salir
   y volver conserva esa distribución y el acceso a las mismas colecciones.

### Teclado y alternativa por toques

- Con el cajón enfocado, Espacio lo recoge; flechas eligen destino según la
  disposición visual actual; Enter confirma mover/intercambiar; Escape cancela.
  Enter sin recogida abre la colección. No añadir un botón por cajón ni
  recuperar «Mover aquí». Anunciar recogida, destino y resultado; tras
  éxito, el foco sigue al cajón movido a su nueva posición.
- En móvil, mantener pulsado recoge el cajón. Si se levanta el dedo sin haber
  empezado a arrastrar, permanece recogido hasta tocar destino o cancelar.
  Durante este estado, tocar un cajón destino intercambia en vez de abrirlo;
  tocar vacío lo coloca allí. Tocar el origen cancela; no hay botón de cancelar.
  Ignorar el clic sintetizado al terminar esa pulsación mantenida; solo una
  pulsación posterior elige destino. Tocar el origen cancela sin escritura.
- Si se arrastra, soltar sobre destino válido completa la operación; soltar
  fuera o en origen cancela. No confundir la retirada del dedo para elegir
  por toque con soltar un arrastre iniciado.

## Flujos alternativos y errores

- Cancelar o soltar fuera de un destino válido no cambia posiciones ni datos.
  Soltar sobre el propio origen no realiza una operación de escritura.
- Escape, tocar el origen y cancelación del sistema/puntero limpian la
  recogida sin petición; navegar fuera o desmontar no guarda el gesto pendiente.
- El gesto de mover no debe abrir la colección al terminar ni activar el +.
- Un error de guardado comunica el fallo y deja una distribución válida, sin
  mostrar un intercambio parcial ni perder contenido.
- Si otro miembro cambia posiciones durante el gesto, no sobrescribir cambios
  inadvertidamente ni intercambiar con un destino distinto del mostrado;
  informar y renovar la distribución antes de reintentar. El contrato concreto
  de detección de conflictos se propondrá y aprobará durante la planificación.
- El desplazamiento táctil normal debe seguir funcionando fuera de un arrastre.
- Perder permisos impide guardar; no se revelan ni modifican estanterías ajenas.

## Reglas de negocio y casos límite

- Se conserva la identidad de cada colección; solo cambian posiciones.
- Un intercambio se guarda como una única operación atómica: ambos o ninguno.
- No existen dos cajones en el mismo hueco ni un mismo tipo en dos posiciones.
- No mover tipos todavía no colocados mediante este gesto: se añaden desde +.
- Impedir operaciones repetidas mientras se guarda; conservar errores y foco
  comprensibles. No desactivar toda la web por iniciar un gesto.
- Respetar movimiento reducido al dar feedback y mantener controles utilizables
  con teclado según el esquema aprobado, sin interceptar flechas/Espacio
  fuera del contexto de reorganización.

## Criterios de aceptación

- **CA-01.** Los cinco tipos colocados permiten iniciar arrastre con ratón sin
  impedir que un clic normal abra su colección.
- **CA-02.** Soltar en vacío cambia solo la posición del cajón y persiste tras recarga.
- **CA-03.** Soltar en ocupado intercambia ambos cajones, conservando sus
  identidades y colecciones, con persistencia y sin cambios parciales.
- **CA-04.** Cancelar, destino inválido u origen mantienen la distribución previa;
  terminar un arrastre no navega ni añade un cajón.
- **CA-05.** En móvil, mantener pulsado inicia arrastre; deslizar normalmente
  desplaza la página sin reorganizar. Se valida en dispositivo o entorno táctil.
  También se puede recoger, levantar el dedo sin arrastrar y tocar el destino;
  tocar el origen cancela conservando posiciones y esos toques no abren colecciones.
- **CA-06.** Desaparece «Mover [nombre] aquí»; el alta sigue ofreciendo solo
  tipos todavía no colocados, sin duplicados ni pérdida del acceso a añadir.
  Con todos colocados permanecen los + y el diálogo informa de que no quedan
  tipos disponibles. Los huecos vacíos siguen siendo
  destinos válidos de arrastre, toque y teclado.
- **CA-07.** Miembros autorizados pueden reorganizar; miembros ajenos no pueden
  hacerlo ni mediante una petición manipulada. El contenido no cambia al mover.
- **CA-08.** Error, conflicto o pérdida de permiso no dejan distribución parcial;
  se informa y se permite reintentar con posiciones vigentes.
- **CA-09.** Espacio recoge el cajón enfocado, flechas eligen según la
  disposición visual, Enter mueve/intercambia y Escape cancela. Enter sin
  recoger abre la colección; foco y anuncios contextuales son comprensibles.
- **CA-10.** Destino, estado de guardado y resultado se perciben sin depender
  exclusivamente del color; se respetan las preferencias de movimiento reducido.

## Decisiones aprobadas

- Crear la rama y redactar esta spec sin implementar.
- Vacío mueve; ocupado intercambia; conservar contenido.
- Retirar el mecanismo «Mover aquí», sustituyendo la exclusión inicial de la US
  que pedía mantenerlo. Esta decisión expresa posterior prevalece.
- Pulsación mantenida sobre el cajón en móvil para evitar movimientos al hacer scroll.
- Ángel confirma el manejo de teclado, la alternativa de tocar destino y
  inicialmente ocultar los + el 25/11/2026. Durante la prueba revoca esa última
  decisión: los + permanecen incluso con todos los tipos colocados.

## Revisión de diseño y decisiones consolidadas

Revisión de solo lectura por design035: distinguir clic/arrastre en los cinco
tipos, bloquear el clic residual y evitar confundir los hovers con el gesto.
El intercambio y la retirada de Mover aquí son decisiones de Ángel, aunque
la primera propuesta del revisor recomendara conservarlos fuera de alcance.
La propuesta de un control de arrastre táctil separado no se adopta: Ángel
ha elegido mantener pulsado el cajón.

Decisiones confirmadas por Ángel:

1. Teclado: con el cajón enfocado, Espacio lo recoge, flechas eligen destino,
   Enter mueve/intercambia y Escape cancela. Enter sin recoger sigue abriendo
   la colección. No recupera «Mover aquí» ni añade un botón por cajón.
2. Alternativa sin arrastre preciso: tras mantener pulsado y recoger, permitir
   elegir destino mediante toque, con cancelación explícita y estado recogido
   conservado al levantar el dedo sin iniciar arrastre.
3. Mantener los + con todos los tipos colocados; en ese caso el diálogo muestra
   aviso de disponibilidad y Cancelar. Los huecos siguen siendo destinos.
4. Por indicación durante la prueba local, guía y estados informativos se
   ocultan visualmente, conservando descripción/anuncios accesibles; errores
   permanecen visibles. Por corrección posterior de Ángel, se retira el botón
   Cancelar movimiento y su lógica exclusiva; se conserva cancelación por gesto.

Umbral de ratón y duración de pulsación son ajustes técnicos a concretar y
validar en el plan; no se fijan valores inventados como aprobados.

## Validación prevista y estado

Pruebas aisladas de persistencia, conservación de contenido, intercambio
atómico, permisos, destinos inválidos y conflictos; errores sin cambios parciales.
Validación manual de ratón, táctil, scroll, clic posterior, teclado, foco,
animaciones y recarga. No usar la base local para pruebas destructivas.

Spec aprobada expresamente por Ángel el 25/11/2026. Solicita preparar el plan
en feat/arrastrar-cajones sin tocar código. La aprobación no autoriza
implementar, crear commits ni publicar.
Posteriormente Ángel aprueba plan/tareas y solicita implementarlas. Código y
controles automáticos realizados, evidencias en tasks.md. Ángel confirma mover
e intercambiar en escritorio, móvil y teclado; cierre local registrado, sin
publicación ni navegador del agente.

La primera revisión spec_reviewer quedó BLOCKED por las tres decisiones,
ahora confirmadas por Ángel. Reauditoría del texto consolidado: PASS, sin bloqueos.
Revisión de diseño consolidada coherente, sin nuevas decisiones imprescindibles;
revisiones solo de lectura, sin interfaz
renderizada. Scroll/contexto táctil y cancelación del sistema se comprobarán
durante la implementación autorizada; cancelar no genera escrituras.

## Cierre — 25/11/2026

Inicio registrado: 08:22 CET. Cierre: 08:59 CET, mismo día. Transcurrido:
37 minutos, sin pausas comunicadas. Estimación por tiempo transcurrido, no
medición de trabajo efectivo; no incluye conversación previa sin hora conocida.
Redondeo: dos cuartos completos y siete minutos restantes, que suben al
siguiente cuarto según el proceso SDD. Valor final al principio del documento.

Ángel confirma que mover e intercambiar funciona en escritorio, móvil y teclado,
tras los últimos ajustes de textos, + persistentes y retirada del botón Cancelar.
Se atribuye la validación manual a Ángel; el agente no abrió navegador.
Revisión de diseño previa/posterior APTO; auditoría final test_reviewer PASS.

Controles vigentes: suite SQLite :memory: con guardia previa, 96 pruebas y
2736 aserciones sin fallos; dos omisiones que requieren PostgreSQL aislado.
Node: tres pruebas correctas; build705, formato y diff correctos. Matriz final
en tasks.md. Sin migración ni pérdida de contenido; no se recreó la base local.

Completada la implementación y validación local. Commit, publicación,
integración en dev, CI y traslado a documentation pendientes de autorización.

## Ajustes de revisión posteriores al cierre

Ángel pide no usar !important en CSS, separar hooks en resources/js/hooks y
retirar la ayuda estática oculta y su referencia accesible. Se conserva la
región viva de resultados y el comportamiento aprobado. Se documenta el
repaso transversal solicitado en [revisión de código](../../../revision-codigo-sin-uso.md).
La excepción de ocupado del alta sigue protegiendo ese contrato, distinto del
movimiento/intercambio. No se han retirado protecciones ni cambiado esa regla.

## Seguimiento en el comentario de la US

Ángel añadirá un comentario en la US para proponer y redactar una nueva
historia dedicada a verificar el código sin uso identificado en el
[informe de revisión](../../../revision-codigo-sin-uso.md). Esa historia
comprobará consumidores, entradas indirectas, compatibilidad e impacto antes
de eliminar cada candidato confirmado como innecesario, con pruebas de regresión.
No borrar protecciones, variantes intencionales o contratos activos solo por
carecer de una referencia directa desde la interfaz.

Este seguimiento queda pendiente de registrar por Ángel en GitHub; no se ha
publicado ningún comentario ni creado una US desde esta tarea. La limpieza
transversal queda fuera de la spec 036 y no bloquea su cierre funcional local.

Por petición explícita de Ángel, la [US #46](https://github.com/elkio-studio/UsTwo/issues/46)
se ha cerrado en GitHub el 25/11/2026 tras su validación local. La publicación
del código, integración en dev y CI siguen pendientes; cerrar la issue no
acredita esos pasos. El comentario de seguimiento se entrega como borrador,
sin publicarlo automáticamente.
