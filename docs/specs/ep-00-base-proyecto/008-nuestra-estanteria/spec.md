---
id: "008"
title: "Nuestra estantería"
status: completed
---

# 008 — Nuestra estantería

## Problema y objetivo

El acceso actual a las colecciones se presenta como un listado. El espacio
compartido pasa a representarse como un mueble de madera con luz cálida.
El frente de cada cajón identifica una colección y permite abrirla. Los
huecos abiertos permiten distinguir dónde hay una colección disponible.

## Alcance incluido

- Rediseño del espacio compartido del MVP y de su acceso a Películas.
- Estantería baja y horizontal en escritorio, alta y vertical en móvil.
- Luz cálida de una lámpara y velas, con reflejos localizados y movimiento
  suave cerca de las velas.
- Acceso a la colección existente y retorno a la estantería.

## Alcance excluido

- Rediseño del acceso, del listado de Películas, de las fichas y formularios.
- Nuevas colecciones, administración de huecos e invitaciones al espacio.
- Cambios en permisos, cuentas, datos o reglas del MVP 005.

## Actores

- Personas con acceso al espacio compartido. En el MVP son Ángel y
  Alejandra. El texto de la interfaz no presupone un número de personas.

## Dirección visual

- «Nuestra estantería» y «Lo que guardamos juntos» identifican el espacio.
- El mueble sigue la maqueta aprobada como referencia de ambiente y material.
  La madera de referencia es roble tostado, con vetas visibles, tiradores
  de latón y etiquetas de papel. La versión móvil corrige la orientación
  horizontal de la maqueta y reorganiza los huecos en altura.
- La escena ocupa todo el fondo del espacio compartido. La estantería
  está suspendida sobre la pared cálida, sin suelo ni anclajes visibles.
- La lámpara produce una zona cálida amplia y estable. Las velas producen
  focos locales independientes, con reflejos suaves en las superficies
  cercanas. Los huecos conservan profundidad y las etiquetas, contraste
  estable. El número de huecos y la posición exacta de las luces pueden
  ajustarse al componer cada encuadre.

## Flujo principal

1. La persona entra en el espacio compartido y distingue el cajón de
   Películas entre los huecos abiertos.
2. Pulsa el frente del cajón o lo activa con el teclado. El cajón sale
   ligeramente y da paso al listado existente de Películas vistas.
3. Desde Películas usa «Volver a la estantería» y regresa al espacio compartido.

## Flujos alternativos y errores

- Si las imágenes de la escena no cargan, el acceso a Películas conserva
  una etiqueta visible y puede activarse con teclado y tacto.
- Con movimiento reducido, las luces quedan estáticas y el acceso se
  realiza sin animación.
- Si aún no hay películas registradas, Películas conserva su cajón y
  abre el estado vacío actual del listado.

## Reglas de negocio y casos límite

- Cada colección disponible se representa mediante un cajón. Un hueco
  sin colección muestra el interior del mueble, carece de tirador y no
  recibe foco ni se puede activar. Películas es el único cajón del MVP.
- Las etiquetas son texto legible y el frente completo del cajón es
  pulsable. El cambio de orientación mantiene disponibles el cajón y
  los huecos abiertos, sin desplazamiento horizontal.
- Se mantiene la convención de estilos compartidos y módulos del proyecto.

## Criterios de aceptación

- **CA-01.** Si la ventana es más ancha que alta, se muestra la estantería
  horizontal, con más columnas que filas. Si la ventana es más alta que
  ancha o cuadrada, se muestra la vertical, con más filas que columnas.
  Ambas imágenes mantienen sus proporciones y se ven completas. La pared
  se prolonga hasta los bordes y el cajón sigue alineado con su hueco,
  sin desbordamiento horizontal al redimensionar o girar la pantalla.
- **CA-02.** En ambas vistas aparece exactamente un cajón etiquetado
  «Películas». Los huecos restantes muestran su interior y quedan fuera
  del recorrido del teclado. Una lista sin películas mantiene su cajón.
- **CA-03.** El frente de Películas tiene un objetivo táctil de al menos
  44 × 44 px, foco visible y abre el listado existente por clic, toque o
  teclado. La acción de retorno recupera la estantería.
- **CA-04.** En ambos encuadres se distingue una zona de luz estable de
  lámpara y al menos un reflejo localizado de vela en otra zona. Solo
  fluctúan la llama y los reflejos cercanos. La etiqueta mantiene una
  relación de contraste de al menos 4,5:1 durante todo el ciclo.
- **CA-05.** Con movimiento reducido, las luces permanecen fijas y el cajón
  abre el listado sin animación. Si falla la imagen, el acceso identificado
  como «Películas» sigue visible y operativo.
- **CA-06.** Tras abrir el cajón, siguen disponibles los flujos de alta,
  consulta y valoración de la spec 005. Series, Sitios y Compras no
  aparecen como cajones habilitados.

## Decisiones aprobadas

- La estantería toma como referencia el prototipo aceptado y cambia a
  una composición vertical en móvil.
- Solo los huecos con colección muestran cajón. La iluminación combina
  lámpara y velas, con distintas zonas de influencia.
- El lenguaje del espacio es inclusivo y el acceso a Películas conserva
  los flujos del MVP vigente.
- El desarrollador aprobó la especificación y ambas orientaciones.

## Decisiones pendientes

- Ninguna.
