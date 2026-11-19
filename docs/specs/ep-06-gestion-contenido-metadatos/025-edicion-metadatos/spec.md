---
id: "025"
title: "Definir la edición de metadatos externos"
status: completed
---

# 025 — Definir la edición de metadatos externos

[Historia #57](https://github.com/elkio-studio/UsTwo/issues/57) de la
[épica EP-06](https://github.com/elkio-studio/UsTwo/issues/28).
Continúa la identificación del origen de la [spec 024](../024-origen-fichas/spec.md).

Inicio: 19 de noviembre de 2026, 20:00 CET.
Cierre documental local: 19 de noviembre de 2026, 20:14 CET.
Tiempo transcurrido: 14 min. Sin pausas comunicadas.
GitHub Projects: **0.25 h** (estimación basada en el tiempo transcurrido).
Pendiente de commit, publicación, integración en dev y traslado de la
documentación a documentation, con las autorizaciones correspondientes.
Cierre previsto en Sprint 1. Capacidad de referencia de 4 h/día y
20 h por sprint. Esfuerzo y asignación pendientes de validar.

## Objetivo y entregable

Acordar las reglas de edición por campo para Películas, Series y
Lecturas. Esta historia entrega la política documentada y validada
por Ángel. Su aplicación al código se prepara después mediante las
historias correspondientes.

## Reglas confirmadas

- Los valores que aporta el catálogo quedan protegidos.
- Un miembro puede completar los metadatos ausentes y corregir después
  los valores que se hayan completado manualmente.
- La procedencia se distingue por campo. Que una ficha sea de catálogo
  no convierte en externos los datos que aporte posteriormente un miembro.
- Las reglas se aplican igual a Películas, Series y Lecturas.
- Las fichas manuales mantienen la edición de sus campos. La identidad
  externa queda vinculada al catálogo actual según la spec 024.
- Las puntuaciones y las notas personales pertenecen a cada miembro
  y conservan las reglas de autoría existentes.

## Inventario de campos

| Campos | Procedencia y regla |
| --- | --- |
| Título | Obligatorio. Editable en una ficha manual. Protegido cuando procede del catálogo. |
| Año de estreno de Películas y Series | Protegido si lo aporta TMDB. Se puede completar y corregir si lo aportan los miembros. |
| Sinopsis de Películas y Series | Protegida si la aporta TMDB. Una sinopsis ausente puede completarse y corregirse manualmente. |
| Autoría, fecha de publicación y sinopsis de Lecturas | Protegidas cuando vienen del catálogo. Los valores completados manualmente siguen siendo corregibles. |
| Fecha de visionado | Dato propio y compartido de la estantería. Sigue siendo editable por sus miembros. |
| Puntuación y nota personal | Datos propios del miembro. Cada persona modifica su aportación. |
| Proveedor, tipo de registro e identificador externo | Identidad de catálogo. Su modificación se realiza mediante el flujo de vinculación admitido. |
| URL o ruta de portada | Dato externo cuando lo devuelve el catálogo. La portada de reemplazo sigue disponible si falta la imagen. |

Las portadas ausentes conservan la imagen de reemplazo. Añadir una
portada manual queda fuera de esta historia y se definirá en otra.
La regla de completar campos ausentes no incorpora ahora edición de
portadas.

## Correcciones, errores y concurrencia

Si un dato importado es incorrecto, la regla aprobada lo mantiene
protegido. Las correcciones manuales se permiten para los datos que
aportaron los miembros. Cualquier excepción para corregir datos
externos requiere una decisión de producto posterior.

Un intento de modificar un campo protegido se rechaza y conserva la
ficha anterior. Las peticiones se limitan a miembros de la estantería
y se validan completas antes de guardar. Un cambio de metadatos
compartidos conserva las notas y puntuaciones de cada persona.

## Registros existentes y vinculación

Ángel confirma que en las fichas de catálogo de la base de datos de
producción no hay metadatos introducidos manualmente. Al aplicar la
política, sus valores existentes se podrán clasificar como importados
y proteger. Los campos vacíos seguirán admitiendo aportaciones manuales
que podrán corregirse después. Esta confirmación procede del desarrollador.

Los datos locales son pruebas y no requieren una revisión individual
de procedencia. Esta decisión no autoriza borrar ni reiniciar bases
de datos. Las fichas, sus identificadores y las aportaciones personales
se conservan al aplicar la política.

Cuando una ficha manual se vincula posteriormente a un catálogo, se
usa el valor que devuelve el proveedor y queda protegido. Si el
proveedor no devuelve un dato y el miembro ya lo había escrito, se
conserva como dato manual y se permite corregirlo después.

## Criterios de aceptación

- **CA-01.** El inventario distingue metadatos externos, datos compartidos
  y aportaciones personales en las tres colecciones.
- **CA-02.** La política recoge campos protegidos, campos completables y
  corrección posterior de datos manuales. Las portadas ausentes mantienen
  el reemplazo; añadir portadas manuales queda para otra historia.
- **CA-03.** Se documenta cómo tratar datos incompletos, errores de
  catálogo, registros existentes y vinculación posterior, conforme a las
  decisiones confirmadas por Ángel.
- **CA-04.** Ángel valida la política antes de su implementación.
  No quedan decisiones pendientes dentro del alcance documental.
  Las historias que la apliquen comprueban
  persistencia, permisos por estantería y conservación de aportaciones
  personales.

## Evidencias de ejecución documental

Ángel aprobó expresamente la spec y el plan y solicitó ejecutar las
tareas sin código ni publicación. La política estable queda registrada
en [DT-007](../../../decisiones-tecnicas.md#dt-007--edición-según-procedencia-de-los-metadatos).

| Criterio | Resultado de la revisión |
| --- | --- |
| CA-01 | Inventario revisado para las tres colecciones, distinguiendo identidad externa, metadatos, visionado y aportaciones personales. |
| CA-02 | Valor importado protegido; campo vacío completado y posteriormente corregible; portadas manuales excluidas y reemplazo conservado. |
| CA-03 | Error del catálogo no habilita editar un valor importado. Valores existentes de producción clasificados según confirmación de Ángel. Al vincular, prevalece catálogo; si falta el dato, se conserva el manual editable. Sin borrado de registros, IDs ni aportaciones personales. |
| CA-04 | Aprobaciones registradas y aplicación al código remitida a historias posteriores con pruebas de persistencia, permisos y conservación de valoraciones. |

Contraste con las specs 022 y 024 y lectura de los controladores actuales:
Lecturas todavía protege los campos completados tras guardarlos; Películas
y Series todavía permiten editar título y año importados. Esta entrega
define la política futura, sin modificar esos comportamientos ni reabrir
las specs anteriores.

Controles: `git diff --check` correcto y destinos de enlaces relativos
comprobados en decisiones técnicas y los tres artefactos de la spec.
No se ejecutan PHPUnit, build ni migraciones porque solo cambia documentación.
No se ha abierto el navegador ni modificado la base de datos.

Revisión final documental: `PASS`, sin bloqueos. Publicación e integración
siguen pendientes y no se consideran realizadas con esta verificación.
