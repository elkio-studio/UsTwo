---
id: "026"
title: "Editar fichas manuales"
status: completed
---

# 026 — Editar fichas manuales

[Historia #58](https://github.com/elkio-studio/UsTwo/issues/58) de la
[épica EP-06](https://github.com/elkio-studio/UsTwo/issues/28).
Aplica a las fichas manuales la política de la
[spec 025](../025-edicion-metadatos/spec.md).

Inicio: 19 de noviembre de 2026, 20:24 CET.
Cierre local: 19 de noviembre de 2026, 20:39 CET.
Tiempo transcurrido: 15 min, sin pausas comunicadas.
GitHub Projects: **0.25 h** (estimación basada en el tiempo transcurrido).
Cierre previsto en Sprint 1. Capacidad orientativa de 4 h/día y
20 h por sprint; estimación y asignación pendientes de validación.

## Problema y objetivo

Permitir que cualquier miembro corrija los datos compartidos de una
ficha manual de su estantería. La edición ya existe parcialmente:
Películas y Series permiten título, año y fecha de visionado;
Lecturas permite título, autoría, fecha de publicación y sinopsis.
Falta incluir la sinopsis manual en Películas y Series y verificar
el comportamiento de las tres colecciones de forma coherente.

## Alcance incluido

- Películas y Series sin vínculo a TMDB: título, año de estreno,
  sinopsis y fecha de visionado.
- Lecturas sin proveedor: título, autoría, fecha de publicación y
  sinopsis, tanto Libros como Manga y Cómics.
- Validación en servidor, persistencia y aislamiento por estantería,
  conservando identificadores locales y aportaciones personales.
- Compatibilidad con fichas existentes y nuevas de estos tipos.

## Alcance excluido

- Aplicar las restricciones de campos importados o la procedencia por
  campo de la spec 025 a las fichas de catálogo. Su implementación
  requiere otra historia; esta no se presenta como cumplimiento completo
  de esa política.
- Portadas manuales, cambio de categoría, vinculación a catálogos,
  nuevos tipos de colección, borrado, historial y sincronización externa.
- Modificar puntuaciones o notas personales desde la edición compartida.

## Actores y origen

Cualquier miembro de la estantería puede corregir sus fichas manuales,
independientemente de quién las creó. Una cuenta ajena no puede hacerlo.
El origen sigue la [spec 024](../024-origen-fichas/spec.md): una película
o serie vinculada a TMDB deja de ser manual; una lectura con proveedor
es de catálogo aunque carezca de identificador externo.

## Flujo principal

1. El miembro abre el detalle de una ficha manual en su estantería.
2. Abre la edición de datos compartidos, revisa los valores guardados
   y cambia uno o varios campos permitidos.
3. Guarda. El servidor comprueba pertenencia, origen y campos antes de
   escribir. Los valores válidos sustituyen los anteriores de esa ficha.
4. El detalle muestra los cambios; persisten al recargar y al volver
   al listado. En Lecturas se conserva la categoría correspondiente.

## Validación y errores

- Título obligatorio y máximo de 255 caracteres; no se acepta vacío.
- Se conservan las reglas actuales de año (1000–9999, opcional),
  visionado (fecha obligatoria, válida y no futura) y duplicados de
  Películas y Series, sin introducir otra política de duplicados. Si el
  título coincide con otro de la colección y no se indica año, se pide
  el año para distinguirlos, conservando la ficha anterior.
- Lecturas mantiene autoría opcional hasta 255 caracteres, fecha de
  publicación opcional válida y sinopsis opcional hasta 20 000 caracteres.
- La nueva sinopsis manual de Películas y Series es
  opcional y hasta 20 000 caracteres, igual que Lecturas.
- Los campos opcionales pueden vaciarse. Los campos no enviados se
  conservan; guardar no cambia el origen ni los IDs de la ficha.
- Un error de validación o guardado conserva la versión persistida
  anterior y explica el problema sin perder lo escrito en el formulario.
- Una ruta que combine una estantería con una ficha de otra se rechaza,
  incluso cuando la cuenta pertenezca a ambas. No hay cambios parciales.
- Editar metadatos conserva todas las valoraciones y notas personales.

## Interfaz y accesibilidad

Conservar la edición desde el detalle y los formularios actuales, sin
rediseño. Añadir la sinopsis de Películas y Series como campo multilínea
con etiqueta visible, usando los componentes y estilos existentes.
Mantener errores junto al campo, estado de guardado, foco visible y uso
con teclado y móvil, sin desplazamiento horizontal.

## Criterios de aceptación

- **CA-01.** Un miembro edita y guarda los campos manuales incluidos en
  Películas, Series, Libros, Manga y Cómics. Los valores persisten tras
  recarga y regreso al listado, conservando la pestaña de Lecturas.
- **CA-02.** Se rechaza título vacío, fechas y años inválidos y valores
  que excedan los límites acordados. Se pueden vaciar campos opcionales
  y se conservan los no enviados.
- **CA-03.** Una cuenta ajena o una ruta de otra estantería no permite
  editar la ficha; también se prueba una cuenta miembro de ambas.
- **CA-04.** Un fallo de validación o guardado conserva los datos
  persistidos previos y los escritos para corregirlos. Los cambios válidos
  conservan IDs, origen, notas y puntuaciones personales.
- **CA-05.** La edición funciona en fichas manuales previas y nuevas de
  las colecciones incluidas. No se amplía la edición de catálogo; una
  Lectura con proveedor sin ID no se trata como manual.
- **CA-06.** El formulario y sus errores pueden utilizarse con teclado,
  foco visible y en móvil, manteniendo el patrón del detalle existente.

## Validación prevista

Pruebas con SQLite para ediciones válidas, invalidación, campos opcionales,
persistencia, conservación de aportaciones y permisos por estantería.
Verificar compatibilidad de los flujos de catálogo existentes y comprobar
el formulario en escritorio, móvil y teclado. No borrar ni reiniciar bases
de datos para realizar la validación.

## Decisiones aprobadas

La política de la spec 025, el origen actual de la spec 024 y la exclusión
de portadas manuales están aprobados. Ángel confirma incorporar la
sinopsis manual de Películas y Series como campo multilínea opcional,
con un máximo de 20 000 caracteres y dentro del formulario existente.

## Decisiones pendientes

Ninguna dentro del alcance. Spec aprobada expresamente por Ángel.

## Ejecución y evidencias locales

Plan aprobado y ejecución solicitada por Ángel. Añadido `overview` manual
al controlador y formulario compartidos de Películas y Series; reutiliza
Field multilínea y las columnas existentes. Las fichas de catálogo rechazan
esta nueva edición. Un fallo de persistencia devuelve error de formulario.
Lecturas conserva su implementación y amplía cobertura de regresión.

| Criterio | Evidencia disponible |
| --- | --- |
| CA-01 | Nuevas pruebas de edición en Películas y Series y las tres categorías de Lecturas; consultas de colección con categoría y pruebas existentes de detalle y retorno. |
| CA-02 | Sinopsis de 20 000 aceptada y de 20 001 rechazada; título vacío, año y fecha inválidos; vaciado de opcionales y campos omitidos conservados. |
| CA-03 | Pruebas de rutas cruzadas para miembros de una o ambas estanterías y pruebas existentes de acceso ajeno. |
| CA-04 | Cambios mixtos inválidos no guardados; fallo SQL simulado devuelve error y retiene input; conservación de origen y valoraciones comprobada. |
| CA-05 | Pruebas de alta existentes, edición de fichas manuales persistidas y regresión de catálogo, incluida Lectura con proveedor sin ID. Sin cambio de esquema. |
| CA-06 | Campo con etiqueta y error asociado mediante Field; compilación correcta. Ángel confirma que la edición funciona en escritorio, móvil y teclado. Retención del input ante error respaldada por pruebas HTTP y el estado del formulario existente. |

Comandos ejecutados con PHP 8.5 y `pdo_sqlite`:

- `vendor/bin/phpunit --display-skipped`: 57 pruebas, 1 387 aserciones,
  0 fallos; 2 omitidas por requerir PostgreSQL aislado para el seeder
  de producción. SQLite en memoria forzado por `phpunit.xml`.
- `npm run build`: correcto, 679 módulos compilados.
- `git diff --check`: correcto.

El agente no ha abierto el navegador ni ejecutado migraciones, reinicios o
borrados de la base de desarrollo. No se han publicado cambios.
Se corrigió únicamente el arranque del servidor local para utilizar SQLite
explícitamente, sin modificar `.env`; el acceso por HTTP devuelve validación
normal (422 con credenciales ficticias) y Vite responde 200. Ambos quedan activos.

Auditoría final de código: sin defectos adicionales identificados dentro del
alcance. El bloqueo anterior de CA-06 se revisa tras la confirmación manual
de Ángel. Revisión final: `PASS`, sin bloqueos. Pendientes externos: commit, publicación, integración en dev y
traslado de la documentación a documentation, con autorización específica.
