---
id: "006"
title: "Una casa compartida"
status: completed
---

# 006 — Una casa compartida

## Problema y objetivo

UsTwo reúne recuerdos y colecciones de dos personas. Su identidad debe
funcionar tanto para un diario de visionados como para una lista de sitios o
de enlaces. El panel es la entrada común y cada colección tiene una página
adaptada a lo que se consulta en ella.

## Alcance incluido

- Definir una identidad visual común para las pantallas existentes del MVP.
- Aplicarla al acceso, el panel y el recorrido de Películas,
  incluidos sus formularios, detalle y créditos.
- Usar Sitios y Compras como ejemplos estáticos de cómo se adapta el estilo.

## Alcance excluido

- Incorporar Sitios, Compras o Series al panel, la navegación o el MVP.
- Añadir rutas, integraciones, modelos de datos o acciones para los ejemplos.
- Cambiar los flujos, permisos, contenido o reglas de la especificación 005.

## Actores

- Ángel y Alejandra, las dos personas que comparten el espacio privado.

## Dirección visual

- Fondo piedra clara (`#F4F2ED`), superficies blanco cálido (`#FFFDFA`),
  texto tinta (`#252B2A`) y verde petróleo (`#24564F`) en enlaces y acciones
  principales.
- Georgia para encabezados de página y colección. Tipografía del sistema
  para entradas, notas y formularios. Cifras tabulares para las puntuaciones.
- Cabecera discreta con UsTwo y navegación existente. Títulos visibles al
  inicio de cada página. Separadores finos donde ayuden a recorrer una lista.
- Las reglas visuales y las variables se definen en `global.css`. Los
  `.module.css` solo enlazan las clases globales que usa cada componente.

## Flujo principal

1. La persona inicia sesión y reconoce la misma identidad visual al llegar
   al panel privado.
2. Desde el panel abre Películas vistas en su propia página.
3. Recorre el listado y abre una película para ver sus datos y acciones.

## Ejemplos visuales

Los títulos y puntuaciones de ejemplo son ilustrativos.

| Vista | Composición | Ejemplo de contenido |
|---|---|---|
| Películas · MVP | Título de página y acción de añadir. Filas separadas, con el título a la izquierda y ambas puntuaciones a la derecha. | **Arrival** · Ángel 92 · Alejandra 86. Al abrirla aparecen la fecha, el año si consta, las notas y las acciones. |
| Sitios · referencia | Misma cabecera. Mapa ilustrativo junto a una lista legible de lugares. En móvil se apilan. | **Jardín Botánico** y **Café del Mercado**, con sus nombres visibles en la lista junto a la representación del mapa. |
| Compras · referencia | Misma cabecera. Lista de enlaces donde el nombre del artículo precede a la tienda y al acceso. | **Lámpara de sobremesa**, con la tienda debajo y una acción ilustrativa «Abrir enlace». |

## Flujos alternativos y errores

- La lista vacía conserva el mensaje y la acción para añadir la primera
  película. Los estados de error de catálogo y guardado mantienen sus
  mensajes y alternativas del MVP.
- En formularios, los errores de campo aparecen junto a su control y se
  identifican con texto. Las acciones conservan etiquetas y foco visible.

## Reglas de negocio y casos límite

- La lista guardada de películas conserva el orden de visionado de más
  reciente a más antiguo. Cada fila muestra solo título y puntuaciones.
- En pantallas estrechas, un título largo puede ocupar varias líneas y las
  puntuaciones pasan debajo cuando no caben junto al título.
- Se conservan el tratamiento de las puntuaciones ausentes y las reglas de
  visibilidad y edición de la especificación 005.

## Criterios de aceptación

- **CA-01**. Acceso, panel y pantallas de Películas comparten la paleta,
  tipografía y tratamiento de controles y foco definidos para UsTwo.
- **CA-02**. Las películas aparecen de la más reciente a la más antigua.
  Cada fila muestra título y ambas puntuaciones. Fecha, año si consta,
  notas y acciones quedan en el detalle.
- **CA-03**. A 375 px, un título largo y ambas puntuaciones se leen sin
  desplazamiento horizontal. El panel, el listado, el detalle y sus
  acciones se pueden recorrer por teclado con foco visible.
- **CA-04**. El texto habitual y las etiquetas de botones alcanzan un
  contraste mínimo de 4,5:1. Los errores de campo se explican en texto
  junto al control y la lista vacía permite acceder al alta.
- **CA-05**. Las descripciones de Sitios y Compras reutilizan la cabecera,
  paleta y jerarquía tipográfica de la dirección visual. El panel del MVP
  ofrece solo Películas como colección y los ejemplos carecen de rutas y
  acciones operativas.
- **CA-06**. Las reglas de estilo se encuentran en `global.css` y cada
  `.module.css` se limita a enlazar clases globales.

## Decisiones aprobadas

- La dirección general es «Una casa compartida», válida para distintas
  colecciones. El usuario pidió ejemplos de Películas, Sitios y Compras.
- Películas es la única colección del MVP. Se mantienen sus flujos y
  reglas según la [especificación 005](../005-mvp-ustwo/spec.md).
- Los estilos se centralizan en `global.css`, según la convención del
  [README](../../../README.md).
- El desarrollador aprobó la especificación, incluidas la paleta, la
  tipografía y la composición, el 2026-11-09.

## Decisiones pendientes

- Ninguna.
