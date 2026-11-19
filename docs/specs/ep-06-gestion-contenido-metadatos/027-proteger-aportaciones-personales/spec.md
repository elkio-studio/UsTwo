---
id: "027"
title: "Proteger las notas y puntuaciones personales"
status: completed
---

# 027 — Proteger las notas y puntuaciones personales

[Historia #59](https://github.com/elkio-studio/UsTwo/issues/59) de la
[épica EP-06](https://github.com/elkio-studio/UsTwo/issues/28).
Continúa la política de la [spec 025](../025-edicion-metadatos/spec.md)
y la edición manual de la [spec 026](../026-editar-fichas-manuales/spec.md).
Reutiliza las valoraciones existentes y las reglas de la
[spec 023](../../ep-02-libros-y-mangas/023-valorar-lecturas/spec.md).

Inicio: 19 de noviembre de 2026, 20:45 CET.
Cierre local: 19 de noviembre de 2026, 20:54 CET.
Tiempo transcurrido: 9 min, sin pausas comunicadas.
GitHub Projects: **0.25 h** (estimación basada en el tiempo transcurrido,
redondeada según el proceso SDD).
Cierre previsto en Sprint 1. Capacidad orientativa de 4 h/día y
20 h por sprint. Estimación y asignación pendientes de validación técnica.

## Problema y objetivo

Garantizar que cada miembro conserva la autoría y el control de su
puntuación y nota, independientemente de quién creó o edite la ficha
compartida. La base ya existe en Películas, Series y Lecturas; esta
historia revisa sus garantías y cubre los huecos que se identifiquen,
sin sustituirlas por una valoración global.

## Alcance incluido

- Notas y puntuaciones de Películas, Series, Libros, Manga y Cómics,
  tanto de fichas manuales como de catálogo.
- Asociación persistente de cada aportación con su autor y su ficha.
- Edición exclusivamente por el autor autenticado y miembro actual de
  la estantería, con protección en el servidor.
- Conservación de aportaciones al editar metadatos compartidos mediante
  los flujos existentes, incluida la vinculación actual a TMDB.
- Regresión de permisos, errores, persistencia y presentación existentes.

## Alcance excluido

- Nuevos tipos de colección, valoraciones globales, historial y moderación.
- Cambiar la escala, la visibilidad actual o las reglas de abandono de
  una estantería y borrado de fichas. Esta historia no cambia los efectos
  actuales de esos flujos sobre sus relaciones.
- Aplicar el resto de restricciones de metadatos externos de la spec 025.
- Rediseñar formularios o introducir otro sistema de valoraciones.

## Actores y flujo principal

1. Un miembro abre una ficha de su estantería y consulta las aportaciones
   de los miembros actuales según el patrón de Películas y Series.
2. «Tu valoración» muestra únicamente sus valores editables. Las
   aportaciones ajenas se muestran para consulta, no como campos editables.
3. Guarda o corrige su nota y puntuación. El servidor obtiene el autor
   de la sesión y la ficha de la estantería de la ruta.
4. Se crea o actualiza una única aportación por autor y ficha sin tocar
   las demás. El resultado persiste al recargar.
5. Se vuelve al listado de Películas o Series; en Lecturas, a la pestaña
   correspondiente a Libro, Manga o Cómic, conforme al comportamiento actual.

## Reglas y errores

- Puntuación opcional, entera entre 0 y 100 inclusive; cero es válido.
  Nota opcional hasta 5 000 caracteres. Ambos valores pueden vaciarse.
- No se acepta que el cliente elija otro autor. Incluir un `user_id`
  no permitido se rechaza; tampoco puede utilizarse otro identificador
  de aportación para modificar valores ajenos.
- La cuenta debe pertenecer a la estantería de la ficha. Se rechaza
  usar una ruta de otra estantería incluso si pertenece a ambas.
- Una actualización de metadatos no reasigna autores ni cambia notas
  o puntuaciones de ningún miembro. Campos personales introducidos en
  esa petición no deben permitir alterar valoraciones.
- Los fallos de validación o persistencia muestran un error, conservan
  la aportación guardada y permiten corregir los datos escritos sin perderlos.
- Guardar varias veces no crea duplicados ni cambia la ficha asociada.
  Los registros existentes conservan sus autores, valores y asociaciones.

## Interfaz y accesibilidad

Mantener los formularios y componentes compartidos actuales de valoración,
sus etiquetas, la consulta de aportaciones y los listados. Verificar uso
con teclado y toque, foco visible y errores asociados a sus campos.
No introducir cambios de diseño ni nuevas interacciones.

## Criterios de aceptación

- **CA-01.** Dos miembros valoran la misma ficha por separado en las
  colecciones incluidas. Cada aportación queda asociada al autor y ficha
  correctos y persiste al recargar; actualizarla no crea duplicados.
- **CA-02.** Cada miembro modifica solo su aportación. Una petición que
  intenta indicar otro autor se rechaza y conserva ambas valoraciones.
  La interfaz permite consultar las ajenas sin editarlas.
- **CA-03.** Editar los metadatos compartidos de una ficha manual o de
  catálogo, o vincular una ficha a TMDB con el flujo actual, conserva
  todas las notas, puntuaciones y autores. Una petición de metadatos
  con campos personales no puede sobrescribir esas aportaciones.
- **CA-04.** Sin sesión o sin pertenencia se impide modificar una
  aportación. Una ruta de otra estantería tampoco lo permite, incluso
  con pertenencia a ambas. No se alteran otras fichas ni colecciones.
- **CA-05.** Se mantienen los límites y valores opcionales actuales;
  errores de validación y guardado conservan el estado anterior y el
  contenido introducido para corregirlo. Las aportaciones previas no
  pierden información.
- **CA-06.** Guardar vuelve al listado y categoría correctos. El flujo
  de consulta y edición propia es utilizable en móvil y con teclado,
  con foco visible y errores comprensibles, reutilizando el patrón actual.

## Validación prevista

Revisar pruebas existentes antes de añadir cobertura. Comprobar con
SQLite los casos de dos miembros, autor manipulado, rutas cruzadas,
campos personales en la edición compartida, fallos, límites y conservación
tras editar o vincular. Mantener regresión de las tres categorías de
Lecturas y de Películas y Series. Registrar las comprobaciones manuales
que se realicen; no inferirlas de la compilación.

No se presupone una migración: revisar primero asociaciones y restricciones
existentes. Cualquier cambio de datos o esquema necesario debe justificarse
en la planificación antes de implementar. No borrar ni reiniciar bases.

## Decisiones aprobadas y pendientes

La autoría personal, la escala y la navegación proceden de las historias
aprobadas y de DT-007. No quedan decisiones nuevas de producto dentro
del alcance. Spec aprobada expresamente por Ángel.

## Ejecución y evidencias locales

Plan aprobado y ejecución solicitada por Ángel. Se mantiene la asociación
por autor y ficha y el autor obtenido de la sesión. Añadido manejo de
QueryException al guardado de valoración de CollectionController, siguiendo
el patrón de Lecturas: error `save`, input retenido y estado anterior intacto.
No se modifican interfaz, rutas, escala ni asociaciones existentes.

| Criterio | Evidencia disponible |
| --- | --- |
| CA-01 | Nuevas pruebas de dos autores en Películas y Series y en Libros, Manga y Cómics manuales y de catálogo. Actualización sin duplicados y consulta de ambas aportaciones. Restricciones únicas por ficha/autor ya presentes en las tres tablas. |
| CA-02 | Peticiones con user_id ajeno rechazadas; un ID de valoración ajena enviado no cambia la selección del autor. Detalles calculan ownRating mediante viewerId y CollectionReviews solo presenta las aportaciones ajenas. |
| CA-03 | Edición compartida con campos personales y vinculación TMDB de Películas y Series conservan ambas aportaciones. Lecturas rechaza petición mezclada y permite edición de autoría sin alterar valoraciones, con y sin proveedor. |
| CA-04 | Pruebas nuevas de rutas cruzadas y pérdida de pertenencia, complementadas por cobertura existente de falta de sesión, cuenta ajena y miembro de ambas estanterías. |
| CA-05 | Límites, opcionales y errores validados. Fallo SQL simulado en Películas y Series conserva ambas aportaciones e input; Lecturas mantiene su cobertura de fallos existente. |
| CA-06 | Retornos correctos comprobados por HTTP, incluidas las tres categorías de Lecturas. Ángel confirma haber comprobado edición propia, consulta ajena y errores en móvil y teclado. Componentes compartidos sin cambios. |

Controles ejecutados:

- PHP 8.5 con `pdo_sqlite`, `vendor/bin/phpunit --display-skipped`:
  60 pruebas, 1 683 aserciones, sin fallos; 2 omitidas porque los casos
  del seeder de producción requieren PostgreSQL aislado. SQLite en memoria
  forzado por `phpunit.xml`; no se modifica la base de desarrollo.
- `npm run build`: correcto, 679 módulos.
- `git diff --check`: correcto.

Revisadas las claves foráneas y unicidad existentes de movie_ratings,
series_ratings y reading_ratings; no se requiere migración. Revisión de
solo lectura de los componentes y selección de autor, sin auditoría visual
en navegador por el agente. Las comprobaciones manuales anteriores las
realizó y confirmó Ángel. No se han publicado cambios.

Auditoría de código: sin defectos adicionales identificados. La confirmación
manual resuelve la evidencia pendiente de CA-06. Revisión final: `PASS`,
sin bloqueos.
Pendientes externos: commit, publicación, integración en dev y traslado de
la documentación a documentation, con autorización específica.
