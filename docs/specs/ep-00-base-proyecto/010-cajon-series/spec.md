---
id: "010"
title: "Cajón de Series"
status: completed
---

# 010 — Cajón de Series

## Objetivo

Añadir a la estantería una colección de series vistas. Cada entrada
representa la serie completa y admite una puntuación y una nota por persona.
Series comparte la dirección visual de Películas y usa TMDB para buscar
series y recuperar sus portadas y sinopsis cuando estén disponibles.

## Alcance

- Cajón Series, listado de series vistas, alta y ficha de cada serie.
- Portadas en perspectiva en escritorio y completamente visibles en
  dispositivos táctiles, ficha cálida y navegación de vuelta al cajón.
- Búsqueda de series en TMDB. Se muestran solo resultados de televisión.
- Cuadrícula compacta para los resultados de «Añadir película», con tres
  columnas en escritorio, dos en tablet y una en móvil.
- Acceso privado por pertenencia a la estantería. Sus miembros pueden
  consultar las valoraciones y editar la suya.
- Quedan fuera el seguimiento por temporadas y episodios, las listas
  de pendientes, las recomendaciones y otros tipos de cajón.

## Actores y flujos

1. Un miembro abre el cajón Series de una estantería a la que pertenece.
   Ve sus series o un estado vacío desde el que puede añadir una.
2. Busca una serie por título, distingue los resultados mediante el año de
   estreno cuando consta y abre la ficha guardada al seleccionarla.
3. La ficha muestra título, portada y sinopsis disponibles. Cada miembro
   puede consultar las aportaciones de los demás y editar la propia.
4. Desde la ficha vuelve al listado y desde el listado a la misma
   estantería. Si añadió la serie desde el listado, Atrás vuelve al
   listado actualizado. Series y Películas conservan sus colecciones separadas.

## Reglas compartidas con Películas

- Un cajón Series por estantería. Sus miembros pueden colocarlo o moverlo a un hueco vacío
  desde el botón `+`, sin sustituir otros cajones ni perder sus series.
- Al pulsar un resultado de TMDB, guardar la serie con la fecha de hoy
  y abrir su ficha. Los miembros pueden editar el título, el año de
  estreno y la fecha de visionado.
- Permitir el alta manual con título y fecha de visionado obligatorios
  y año de estreno opcional. Está disponible si la serie no aparece o
  falla el catálogo. Si ya existe el mismo título, pedir el año para
  distinguir las fichas. Si una entrada manual coincide en título y año,
  ofrecer abrir la existente o confirmar que se quiere guardar otra.
- Las puntuaciones son enteros de 0 a 100. La puntuación y la nota son
  opcionales y cada persona edita las suyas.
- Si el título y el año coinciden con una entrada manual, o coincide el
  título y falta algún año, pedir confirmación antes de vincular. Si hay
  varias candidatas, permitir elegir una o continuar con otra ficha.
  Vincular conserva la fecha y las aportaciones de la entrada existente.
- Cualquier miembro puede borrar una serie tras confirmar. Se eliminan
  la ficha y sus aportaciones.
- Mostrar un único registro por identificador de serie de TMDB en cada
  estantería, orden descendente por fecha de visionado y 12 por página.

## Errores y casos límite

- Si TMDB no aporta portada, se muestra una alternativa tipográfica.
  Si falta la sinopsis, se omite ese apartado.
- Si no hay huecos libres, se conservan los cajones en sus posiciones.
- Un identificador de una película nunca se interpreta como un
  identificador de serie. Una cuenta ajena no puede leer, modificar ni
  valorar series de la estantería mediante una URL directa.

## Criterios de aceptación

- **CA-01.** Series puede coexistir con Películas en la misma estantería.
  Colocar o mover el cajón a un hueco libre conserva sus series y su
  posición se ve al recargar, en escritorio y móvil.
- **CA-02.** Buscar en TMDB devuelve series. Seleccionar una guarda su
  ficha con fecha de hoy, portada y sinopsis cuando constan. Volver a seleccionar la
  misma serie abre la existente. La unicidad se comprueba por estantería.
- **CA-03.** Un miembro ve las valoraciones de la serie y puede guardar
  únicamente la propia. Una cuenta ajena no puede leer ni escribir.
  Una serie solo se resuelve bajo su propia estantería, aunque la cuenta
  pertenezca también a otra.
- **CA-04.** En escritorio, la portada se pone de frente con ratón o foco.
  En móvil, se ve completa sin depender del hover. El listado y la ficha
  son utilizables a 375 px sin desplazamiento horizontal.
- **CA-05.** El listado muestra hasta 12 series por página, ordenadas
  de la fecha de visionado más reciente a la más antigua.
- **CA-06.** Los miembros pueden editar los datos compartidos. La
  valoración admite una nota opcional y solo puntuaciones enteras entre
  0 y 100. Vincular una entrada conserva sus aportaciones y fecha.
- **CA-07.** Se puede guardar una serie manualmente si el catálogo falla
  o no la encuentra. Vincular, guardar otra alta manual coincidente o
  borrar una ficha requiere confirmación.
- **CA-08.** Los resultados de «Añadir película» se muestran en tres
  columnas en escritorio, dos en tablet y una a 375 px. Cada resultado
  mantiene su miniatura, título y año si consta. Pulsarlo conserva el
  alta directa y las comprobaciones de coincidencias.

## Decisiones aprobadas

- Usar TMDB como catálogo de Series.
- Una ficha por serie vista, con puntuación y nota por persona.
- Mantener el estilo de Películas.
- Heredar la funcionalidad de Películas, adaptada a series completas.
- El desarrollador aprobó la spec 010 y añadió el cambio de columnas
  para el buscador de Películas al pedir el plan.

## Referencias

- [Búsqueda de series en TMDB](https://developer.themoviedb.org/reference/search-tv)
- [Detalle de una serie en TMDB](https://developer.themoviedb.org/reference/tv-series-details)
