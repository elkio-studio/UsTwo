---
id: "019"
title: "Añadir y abrir el cajón de Lecturas"
status: completed
---

# 019 — Añadir y abrir el cajón de Lecturas

Historia propuesta para v0.2 de la [épica EP-02, issue #24](https://github.com/elkio-studio/UsTwo/issues/24).
Referencia: [investigación de Lecturas](../../investigaciones/lecturas.md).

Inicio: 16 de noviembre de 2026, 18:28 CET.
Cierre local: 16 de noviembre de 2026, 19:56 CET.
Tiempo transcurrido: 1 h 28 min.
GitHub Projects: **1.5 h** (estimación basada en el tiempo transcurrido).

Estimación orientativa: 1,5 h en Sprint 1 y 0,5 h para terminar y cerrar
en Sprint 2. Capacidad de referencia: 4 h/día de lunes a viernes, 20 h
por sprint. Sprint y responsable pendientes de asignación.

## Objetivo y alcance

Un miembro puede colocar el cajón de Lecturas en un hueco libre,
moverlo y abrirlo desde su estantería. Al abrirlo, puede volver a la
estantería de origen. Quedan fuera el buscador de catálogos, el alta
de lecturas, el seguimiento de progreso y los cajones configurables.

## Diseño

Una fila de lomos apoyados en la balda ocupa el ancho del hueco y se
adapta a su tamaño en escritorio y móvil. Los lomos tienen alturas y
grosores variados y reciben la luz cálida de la estantería. La fila
se representa con un WebP transparente de lomos ornamentados. Encima de
los libros, el letrero «Lecturas» queda centrado en el hueco y tiene un
degradado cálido, más oscuro arriba y más claro abajo. La fila y el
letrero forman un único acceso a Lecturas.

Al pasar el cursor por la fila, el último libro se inclina suavemente
hacia delante desde su base y deja asomar por arriba el canto de las
páginas entre las tapas. La base sigue apoyada en la balda. Al retirar
el cursor, el libro vuelve a su sitio. El foco de teclado
reproduce el gesto y conserva un indicador visible. En pantallas
táctiles, un toque abre Lecturas directamente. Con movimiento reducido,
la fila permanece estática y el foco sigue siendo visible.

## Flujo y permisos

1. Un miembro abre el selector de un hueco libre y elige Lecturas.
2. La fila de lomos aparece en el hueco. Al activarla, se abre la vista
   «Lecturas» de la misma estantería.
3. Si Lecturas ya está colocado, elegirlo desde otro hueco libre mueve
   el cajón y libera el hueco anterior. Hay un cajón de Lecturas por
   estantería, siguiendo las reglas existentes.

El servidor verifica la membresía para abrir, colocar y mover el cajón.
Se rechazan huecos inválidos y ocupados, incluso si otro miembro ocupa el
destino mientras el selector está abierto. Un rechazo conserva la
distribución guardada. Películas, Series y Sitios respetan también los
huecos ocupados por Lecturas.

## Criterios de aceptación

- **CA-01.** Lecturas aparece en el selector de huecos libres. Al elegirlo,
  ocupa el hueco y abre la vista de la misma estantería con ratón, toque
  y teclado, con foco visible y enlace de vuelta.
- **CA-02.** La fila de lomos se adapta al hueco en escritorio y móvil.
  Los lomos quedan dentro de sus bordes, conservan sus proporciones y
  funcionan como un único acceso.
  Con el cursor o el foco, solo la parte superior de un libro se proyecta
  hacia delante y vuelve a su posición al salir. Con movimiento reducido,
  el acceso conserva su indicador de foco sin animarse.
- **CA-03.** Colocar y mover conserva la posición tras recargar. Los
  conflictos de ocupación se rechazan. Con dos estanterías, los cambios
  de una no afectan a la otra. Una cuenta ajena o sin sesión carece de
  acceso por URL directa y de permiso para modificar la posición.
- **CA-04.** La vista presenta las pestañas Libros, Manga y Cómics. Cada
  pestaña indica que todavía no hay entradas de su tipo. Desde cualquiera
  se puede volver a la estantería.

## Decisiones aprobadas

Ángel aprobó la animación del libro inclinado y las tres pestañas con
su estado vacío. El alta y el progreso se definirán en sus propias historias.
