---
id: "023"
title: "Valorar las lecturas compartidas"
status: completed
---

# 023 — Valorar las lecturas compartidas

Historia propuesta para v0.2 de la [épica EP-02](https://github.com/elkio-studio/UsTwo/issues/24).
Continúa la [consulta de Lecturas](../021-consulta-lecturas/spec.md) y la
[gestión de fichas](../022-gestionar-lecturas/spec.md).

Inicio: 18 de noviembre de 2026, 12:37 CET.
Cierre local: 18 de noviembre de 2026, 13:02 CET.
Tiempo transcurrido: 25 min.
GitHub Projects: **0.5 h** (estimación basada en el tiempo transcurrido).

Cierre previsto en Sprint 2. Capacidad de referencia de 4 h/día y
20 h por sprint. Esfuerzo y asignación pendientes de validar.

## Alcance

Cada miembro de una estantería puede guardar y modificar su propia
valoración de un libro, manga o cómic de esa estantería. La aportación
se asocia a una sola lectura y a su autor. Se conservan las
valoraciones individuales, tal como en Películas y Series.

La ficha activa el formulario «Tu valoración» con los valores que ya
guardó quien la consulta. «Vuestras valoraciones» muestra las
puntuaciones y notas de los miembros actuales. En las tarjetas del
listado se ven las puntuaciones individuales, con el mismo patrón de
las otras colecciones. Quedan fuera el progreso de lectura y la
valoración global.

## Diseño futuro

El cajón de Lecturas podría empezar con un único botón «+». Al pulsarlo,
se abriría un selector como el de los cajones de la estantería, con las
opciones Libros, Manga y Cómics. Al elegir una categoría, esta aparecería
como pestaña y se retiraría del selector. Si se activan las tres, el
botón «+» dejaría de mostrarse. Así, cada estantería podría mostrar
solo las categorías que usa.

Esta propuesta se guarda para una futura historia. La spec 023 conserva
las tres pestañas visibles.

## Reglas y errores

- Puntuación opcional, entera entre 0 y 100, ambos incluidos. El cero
  es una puntuación válida. La nota es opcional y admite hasta 5.000
  caracteres. Ambos campos se pueden dejar o volver a dejar vacíos.
- El servidor identifica al autor por la sesión y comprueba que la
  lectura pertenece a la estantería de la ruta. Se rechaza una
  petición que intente indicar otro autor o actuar desde otra
  estantería, incluso si la cuenta pertenece a ambas.
- Guardar de nuevo actualiza la aportación existente. Si falla la
  validación o el guardado, se indica el error, se conserva lo escrito
  en el formulario y la valoración persistida permanece intacta.
- Al guardar correctamente, se vuelve al listado de la colección.
  En Lecturas se abre la pestaña de Libros, Manga o Cómics que
  corresponda. Películas y Series vuelven a sus respectivos listados.

## Criterios de aceptación

- **CA-01.** Un miembro puede crear y modificar su puntuación y su
  nota desde la ficha. Tras guardar, se muestran los valores nuevos.
- **CA-02.** Dos miembros pueden valorar la misma lectura por
  separado. Cada uno puede consultar ambas aportaciones en el detalle
  y las puntuaciones individuales en el listado.
- **CA-03.** Editar una aportación propia conserva la ajena. Un intento
  de suplantar a otro miembro se rechaza. Sin sesión o membresía, o
  desde la ruta de otra estantería, la operación se impide.
- **CA-04.** Tras recargar, se mantienen los cambios en la lectura
  valorada y las demás fichas conservan sus datos. Volver al listado
  muestra las puntuaciones actualizadas en la pestaña correspondiente.
- **CA-05.** Se aceptan 0, 100 y valores vacíos. Se rechazan los
  decimales, los valores fuera de rango y las notas que superan
  5.000 caracteres. Ante un fallo se conserva la última aportación
  guardada. El formulario es utilizable con toque y teclado.
