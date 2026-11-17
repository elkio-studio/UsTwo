---
id: "021"
title: "Consultar las lecturas compartidas de una estantería"
status: completed
---

# 021 — Consultar las lecturas compartidas de una estantería

Historia para v0.2 de la [épica EP-02](https://github.com/elkio-studio/UsTwo/issues/24).
Usa la consulta ya integrada en la [spec 020](../020-alta-lecturas/spec.md).

Inicio: 17 de noviembre de 2026, 18:53 CET.
Cierre local: 17 de noviembre de 2026, 19:04 CET.
Tiempo transcurrido: 11 min.
GitHub Projects: **0.25 h** (estimación basada en el tiempo transcurrido).

Cierre previsto en Sprint 2. Capacidad de referencia de 4 h/día y
20 h por sprint. Esfuerzo y asignación pendientes de validar.

## Alcance

Un miembro abre el cajón, elige una pestaña y consulta las lecturas
compartidas de la estantería actual. Puede abrir una tarjeta y ver
la portada, sus datos disponibles y el origen de la ficha.
La consulta no incluye recomendaciones automáticas ni edición.

## Criterios de aceptación

- **CA-01.** El listado muestra el título de cada lectura. La pestaña
  identifica el tipo de lectura. Tras guardar una ficha, se muestra
  la colección de su tipo, y la lectura sigue visible tras recargar.
- **CA-02.** Cada tarjeta abre su ficha. Se muestran los datos
  disponibles y el origen, con su enlace de catálogo o el texto
  «Añadida a mano». Volver abre la pestaña correspondiente.
- **CA-03.** Si falta la portada o falla al cargar, se muestra la
  portada de reemplazo con el título. Sinopsis, autoría y fecha
  ausentes quedan vacías y se omiten al consultar la ficha. Se podrán
  completar cuando exista edición.
- **CA-04.** El listado contiene solo lecturas de la estantería
  actual. Abrir por URL una ficha de otra estantería devuelve 404,
  incluso si la persona pertenece a ambas. Una persona ajena no
  puede consultar la estantería.
- **CA-05.** Se pueden cambiar pestañas, abrir una ficha y volver en
  móvil y con teclado. El foco es visible.

## Evidencia existente y cierre

El 17 de noviembre de 2026 se ejecutó `ReadingsTest` con SQLite en
memoria. Pasaron 5 pruebas con 137 aserciones.

| Criterio | Evidencia |
| --- | --- |
| CA-01 | Las pruebas HTTP de Lecturas comprueban la categoría activa tras el alta y la lectura persistida en el listado. Ángel confirmó que la pestaña basta para identificar el tipo. |
| CA-02 | La prueba HTTP comprueba que se abre la ficha guardada. El componente de información muestra los datos presentes y el origen. |
| CA-03 | Las pruebas registran fichas con datos opcionales ausentes, incluida una portada nula. Ángel comprobó visualmente las fichas con datos incompletos y la portada gris de reemplazo. El caso de una URL rota se revisó en el componente. |
| CA-04 | Las pruebas HTTP cubren el acceso ajeno y el intento de abrir desde otra estantería una ficha concreta, también con membresía en ambas. |
| CA-05 | La spec 020 registra la comprobación general en escritorio y móvil. Ángel confirmó que puede cambiar de pestaña, abrir una ficha y volver con el teclado. El indicador de foco se revisó en el código. |

Las comprobaciones manuales de cierre se confirmaron el 17 de
noviembre de 2026. La consulta se encuentra integrada en `dev` como
parte de la spec 020.
