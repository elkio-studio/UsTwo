---
id: "017"
title: "Rediseño del cajón de Sitios"
status: completed
---

# 017 — Rediseño del cajón de Sitios

Inicio: 15 de noviembre de 2026, 22:39 CET.
Cierre local: 15 de noviembre de 2026, 23:57 CET.
Tiempo transcurrido: 1 h 18 min. Valor estimado para GitHub Projects:
`1.25` h.
Mejora de interfaz solicitada fuera de las historias de usuario. Retoma
la propuesta de la [spec 014](../014-guardar-sitio-visitado/spec.md) y
traslada al panel las operaciones de [consulta](../015-consultar-sitios/spec.md)
y [edición y borrado](../016-editar-borrar-sitios/spec.md).

## Problema y objetivo

La vista actual reparte el ancho entre el mapa y el formulario y deja
las listas debajo. El rediseño da más espacio al mapa y reúne los
controles de Sitios en un panel accesible mientras se consulta el mapa.

## Alcance incluido

- Ampliar el mapa para que ocupe la mayor parte del espacio disponible.
- Usar el mapa para ver y seleccionar marcadores y elegir coordenadas.
  La ficha, los formularios y las acciones se muestran en el panel.
- Reunir la búsqueda, la consulta y el alta, edición y borrado de
  sitios y listas en el panel izquierdo de escritorio. En móvil, ese
  mismo panel se abre desde abajo.
- Aplicar la paleta cálida de UsTwo al panel y sus controles y mantener
  legibles los datos de los marcadores.
- Permitir arrastrar el botón flotante de invitaciones en las pantallas
  que usan esa bandeja compartida.

## Alcance excluido

- Mover sitios entre listas.
- Cambios de proveedor de mapas y de las reglas vigentes de guardado
  de sitios. Se conserva el aislamiento entre estanterías.
- Rediseño de la estantería y de otros cajones.

## Actores

- Miembros autenticados de la estantería, incluido su propietario.

## Flujo principal

1. La persona entra en Sitios y puede consultar el mapa de su estantería y
   acceder al panel desde un control visible.
2. Desde el panel puede buscar y añadir un sitio. Al elegir un resultado
   o un punto del mapa, el panel muestra el formulario con la ubicación
   y mantiene el punto visible en el mapa.
3. Al seleccionar un marcador, se abre la ficha en el panel con el
   nombre, las coordenadas y la nota. Desde la ficha se puede editar o
   eliminar el sitio. La confirmación de borrado se presenta en el panel.
4. La persona puede plegar y volver a abrir el panel mientras explora
   el mapa. Los datos escritos siguen disponibles.
5. Desde el panel se pueden abrir las listas, consultar sus sitios,
   añadir listas, editarlas y borrarlas.

## Reglas de gestión de listas

- Cualquier miembro de la estantería puede cambiar el nombre y el emoji
  de sus listas y solicitar su borrado.
- Una lista vacía se elimina tras una confirmación en el panel.
- Una lista con sitios requiere dos confirmaciones consecutivas en el
  panel. La segunda advierte de que también se eliminarán los sitios de
  la lista. La lista, sus sitios y sus marcadores desaparecen después de
  completar ambas confirmaciones.
- Cancelar cualquiera de los pasos conserva la lista y sus sitios.

## Flujos alternativos y errores

- Si falla el mapa base, se muestra el aviso y sigue disponible la
  entrada directa de coordenadas.
- Si falla una búsqueda o un guardado, el panel muestra el mensaje y
  conserva los campos introducidos.
- Si la estantería está vacía, se mantiene el flujo existente para crear
  la primera lista al guardar el primer sitio.

## Criterios de aceptación

- **CA-01.** En escritorio, el mapa ocupa todo el alto de la pantalla
  y el 60 % del ancho a la derecha. El panel empieza abierto en el 40 %
  izquierdo. La cabecera abarca todo el ancho, con UsTwo a la izquierda
  y el menú de cuenta a la derecha, ambos con margen. Al plegar el panel,
  quedan el mapa, la cabecera y «Abrir panel» alineado a la izquierda.
  El mapa aprovecha el espacio liberado sin perder los marcadores.
- **CA-02.** En móvil, el panel empieza plegado y abre desde abajo. La
  cabecera conserva su tamaño. Con el panel abierto, «Volver a la
  estantería» y «Plegar panel» comparten fila sobre el título «Sitios».
  El panel se puede recorrer y plegar por toque y los formularios se
  pueden completar sin desplazamiento horizontal.
- **CA-03.** Seleccionar un marcador abre su ficha en el panel. Desde
  el propio panel se puede buscar, añadir, consultar, editar y eliminar
  un sitio con confirmación. El mapa no abre popups de gestión.
  Plegar el panel no borra el formulario.
  Los campos llegan al margen derecho interior. La ficha muestra las
  coordenadas centradas y separadas por «|», con espacio a ambos lados.
  Editar, eliminar y añadir sitio comparten fila. «Volver a la estantería»
  queda sin subrayado al pasar el puntero.
- **CA-04.** El panel, los formularios y las acciones se pueden recorrer
  con teclado y foco visible. Los controles y la atribución del mapa
  se pueden consultar con el panel abierto o plegado.
  El panel y el botón cambian de posición con una transición breve.
  El panel puede desplazarse con su contenido, con la barra oculta.
  Se respeta la preferencia de movimiento reducido.
- **CA-05.** Con y sin mapa base, los avisos y las vías alternativas
  siguen disponibles. Al recargar se conservan los sitios guardados y
  solo se muestran los de la estantería autorizada.
- **CA-06.** Los controles de alta, consulta, edición y borrado de listas
  están en el panel y se limitan a la estantería autorizada. Cualquier
  miembro puede editar el nombre y el emoji de una lista.
  El botón «+» junto al título abre el formulario de creación y el menú
  de tres puntos junto al nombre reúne las acciones de cada lista.
  Al seleccionar un sitio de la lista, el mapa se centra y acerca a él.
  Los sitios se muestran sin fondo individual ni subrayado. Hay un
  espacio entre el formulario de sitio y el bloque de listas.
- **CA-07.** Borrar una lista vacía requiere una confirmación. Borrar una
  lista con sitios requiere dos y elimina también sus sitios y marcadores.
  Cancelar en cualquiera de los pasos conserva todos los registros.
- **CA-08.** El botón de invitaciones sigue libremente al puntero durante
  el arrastre con ratón o toque y, al soltarlo, se desliza al lateral más
  cercano a la altura elegida, dentro de la pantalla. También se puede
  mover con las flechas. Una pulsación abre la bandeja. Al plegar el panel
  de Sitios queda oculto y, con movimiento reducido, se omite el deslizamiento.

## Decisiones confirmadas

El mapa muestra los marcadores. Tanto la ficha de cada sitio como toda
la gestión de sitios y listas se realizan desde el panel.
Empieza abierto en escritorio y plegado en móvil.
