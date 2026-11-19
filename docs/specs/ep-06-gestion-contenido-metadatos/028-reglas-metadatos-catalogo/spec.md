---
id: "028"
title: "Aplicar las reglas de metadatos de catálogo"
status: completed
---

# 028 — Aplicar las reglas de metadatos de catálogo

Implementación pendiente de la política aprobada en la
[spec 025](../025-edicion-metadatos/spec.md) y en
[DT-007](../../../decisiones-tecnicas.md#dt-007--edición-según-procedencia-de-los-metadatos),
para la [épica EP-06](https://github.com/elkio-studio/UsTwo/issues/28).
Conserva la edición manual de la [spec 026](../026-editar-fichas-manuales/spec.md)
y la protección personal de la [spec 027](../027-proteger-aportaciones-personales/spec.md).

Inicio: 19 de noviembre de 2026, 20:59 CET.
Cierre local: 19 de noviembre de 2026, 21:21 CET.
Tiempo transcurrido: 22 min, sin pausas comunicadas.
GitHub Projects: **0.5 h** (estimación basada en el tiempo transcurrido,
redondeada según el proceso SDD).
No se asigna una estimación o sprint nuevo sin validación del desarrollador.

## Problema y objetivo

La política ya está aprobada, pero el código no la aplica completamente.
Películas y Series permiten cambiar título y año importados y no completar
la sinopsis de catálogo ausente. Lecturas permite completar campos vacíos,
pero después los bloquea sin distinguir quién aportó el dato.

Aplicar la protección según la procedencia de cada campo en Películas,
Series, Libros, Manga y Cómics, conservando fichas y aportaciones existentes.

## Alcance incluido

- Registrar de forma persistente qué valores proceden del catálogo y
  cuáles son aportaciones manuales, además del origen general de la ficha.
- Proteger en servidor e interfaz los valores importados; permitir completar
  campos ausentes y corregir o vaciar después los valores manuales opcionales.
- Aplicar las reglas en altas de catálogo, edición y vinculación actual a TMDB.
- Adaptar registros existentes según la confirmación de Ángel en la spec 025.
- Mantener la pertenencia por estantería y la protección de valoraciones.

## Alcance excluido

- Corregir a mano un valor que realmente devolvió el catálogo, aunque
  parezca incorrecto; requiere una excepción de producto posterior.
- Sincronización masiva, refresco automático, historial de metadatos,
  nuevos proveedores o nuevos flujos de vinculación para Lecturas.
- Añadir portadas manuales o cambiar la identidad externa desde el
  formulario de metadatos. Se conserva el reemplazo de portada existente.
- Rediseñar las fichas, alterar valoraciones personales o reglas de borrado.

## Campos y reglas aprobadas

| Campo | Regla |
| --- | --- |
| Título | Siempre obligatorio. Editable en manuales; protegido cuando lo aporta el catálogo. |
| Año y sinopsis de Películas y Series | Protegidos si proceden de TMDB. Si faltan, cualquier miembro puede completarlos y después corregir los valores manuales. |
| Autoría, publicación y sinopsis de Lecturas | Misma regla: valor importado protegido; aportación manual corregible. |
| Fecha de visionado | Dato propio compartido, editable conforme a sus validaciones actuales. |
| Portada | Protegida si viene del catálogo. Si falta, imagen de reemplazo, sin alta manual de portada. |
| Proveedor, tipo e ID externo | Identidad conservada, modificable solo mediante el flujo de vinculación ya admitido. |
| Puntuación y nota personal | Cada autor mantiene el control de su aportación; editar metadatos no la modifica. |

La procedencia manual no implica que solo quien escribió ese metadato pueda
corregirlo: son datos compartidos editables por los miembros de la estantería.
Una Lectura con proveedor y sin ID sigue siendo de catálogo según la
[spec 024](../024-origen-fichas/spec.md).

## Flujos

1. **Alta desde catálogo.** Guardar los metadatos y su procedencia juntos.
   Un valor ausente no pasa a importado por pertenecer a una ficha de API.
2. **Consulta y edición.** El detalle muestra qué campos se pueden editar
   y cuáles están protegidos. Un miembro completa un dato ausente, guarda
   y puede corregirlo en una visita posterior, también otro miembro.
3. **Validación.** Se mantienen los límites actuales de título, fechas,
   autoría, sinopsis y duplicados. Se envían solo cambios permitidos.
   El servidor vuelve a comprobar permisos y procedencia, sin confiar
   en que el formulario haya deshabilitado campos.
4. **Vinculación a TMDB.** Cuando el catálogo devuelve un valor, este
   sustituye el manual y queda protegido, incluidos título y año. Si no
   lo devuelve, se conserva el valor manual y su posibilidad de corrección.
   Se conserva el ID local, el visionado y todas las valoraciones.

## Registros existentes y persistencia

Ángel confirmó que las fichas de catálogo de producción no contienen
metadatos manuales. Sus valores presentes se pueden clasificar como
importados; los ausentes siguen siendo completables. Las fichas manuales
conservan sus valores editables. Los datos locales son pruebas y no
requieren reconstrucción individual de procedencia.

La adaptación conserva fichas, IDs, asociaciones y valoraciones, sin borrar
ni reiniciar bases. No debe reclasificar como importado un campo completado
manualmente después de la adaptación. El mecanismo y la migración, si es
necesaria, se concretarán en el plan y requerirán su aprobación antes de
ejecutarse. Si se crea migración, se aplicará y comprobará en SQLite local.

## Errores y concurrencia

- Un cambio de campo protegido se rechaza completo: no guardar la parte
  editable de una petición que también intenta modificar un valor externo.
- Errores de validación, proveedor o guardado conservan el estado anterior
  y permiten corregir el formulario sin perder lo escrito.
- Guardar el valor y su procedencia es una operación indivisible.
- Si una edición parte de un campo editable que pasa a importado antes
  de guardarla, no puede sobrescribir el dato externo. Se comunica el
  conflicto y se conserva el registro vigente.
- Se rechazan rutas de otra estantería, incluso con pertenencia a ambas.
  Solo miembros actuales pueden editar; los campos personales no permiten
  alterar aportaciones mediante este flujo.

## Interfaz y accesibilidad

Reutilizar los formularios de detalle actuales: campos importados visibles
como no editables y campos manuales o ausentes editables, con una explicación
breve de la protección. Conservar etiquetas, errores, foco y retorno actual,
incluida la categoría de Lecturas. Sin nuevos paneles o rediseño.
Comprobar teclado, móvil y ausencia de desbordamiento horizontal.

## Criterios de aceptación

- **CA-01.** Altas y consultas de las cinco colecciones conservan la
  procedencia por campo tras recarga; proveedor sin ID no implica ficha manual.
- **CA-02.** Un miembro no modifica valores importados, ni mediante
  petición manipulada. Una petición mixta se rechaza sin cambios parciales.
- **CA-03.** Campos ausentes se completan y sus valores manuales pueden
  corregirse en otra visita por cualquier miembro. Opcionales vaciados
  siguen editables y campos omitidos no cambian. Manuales mantienen la spec 026.
- **CA-04.** Vincular a TMDB aplica los valores que devuelve el catálogo,
  protege esos campos y conserva los manuales que no devuelve. No cambia
  ID local, visionado ni aportaciones personales.
- **CA-05.** La adaptación de datos existentes respeta la clasificación
  aprobada, conserva todos los registros y no reclasifica aportaciones
  manuales posteriores como importadas.
- **CA-06.** Errores y conflictos entre edición y vinculación no dejan
  valores o procedencias parciales. No se permite editar otra estantería
  ni sobrescribir notas o puntuaciones de ningún miembro.
- **CA-07.** Los campos editables y protegidos se distinguen en los
  detalles de las tres colecciones; edición y errores funcionan en móvil
  y teclado y mantienen la navegación existente. Portadas ausentes conservan
  el reemplazo sin ofrecer incorporación manual.

## Validación prevista y decisiones

Pruebas SQLite con datos incompletos de catálogo, aportaciones manuales,
corrección posterior, migración si corresponde, proveedor sin ID, peticiones
manipuladas, dos miembros, aislamiento y conflictos con vinculación TMDB.
Reutilizar regresiones de altas, metadatos y valoraciones. Comprobar UI y
registrar evidencia manual sin inferirla de la compilación.

Las reglas funcionales proceden de la spec 025. El modelo técnico de
procedencia se propondrá y aprobará con el plan; no se elige aquí una
columna, tabla, servicio ni dependencia. Spec aprobada expresamente por Ángel.

## Ejecución y evidencias locales

Ángel aprobó el plan, su elección técnica y la ejecución con migración local.
Se incorporan catalog_fields y metadata_version a las tres tablas y un trait
de política por campo a los modelos. Altas, edición y vinculación usan la
procedencia persistida; las ediciones exigen versión y guardan de forma
condicional. Los formularios reciben los campos editables del servidor.

| Criterio | Evidencia disponible |
| --- | --- |
| CA-01 | Altas existentes y nuevas pruebas de las cinco colecciones, lectura de permisos y versión; proveedor sin ID mantiene origen de catálogo. |
| CA-02 | Pruebas de campos externos completos, petición mixta y manipulación de identidad, portada, procedencia y puntuación. Sin escritura parcial ni incremento de versión. |
| CA-03 | Dos miembros completan, corrigen, vacían y vuelven a completar un campo ausente; persisten permisos y procedencia. Se actualizan las pruebas previas de edición al contrato de versión. |
| CA-04 | TMDB sustituye título y conserva año/sinopsis manuales cuando faltan en el resultado; se mantienen ID, visionado y dos aportaciones. Regresión de campos presentes de catálogo y vinculación existente. |
| CA-05 | Prueba aislada de migración compara columnas originales, IDs, timestamps y valoraciones, clasifica manuales y catálogos incompletos y comprueba que repetir migrate no reclasifica una aportación manual posterior. Copia y comparación de la base local descritas abajo. |
| CA-06 | Pruebas de versión ausente/antigua y carreras durante consulta TMDB o entre comprobación y escritura; regresión de errores SQL, aislamiento y aportaciones personales. Operaciones sin cambios no incrementan versión. |
| CA-07 | Interfaz compila y mantiene Field, etiquetas, estado de errores y navegación. Ángel confirma haber comprobado los campos protegidos y editables en escritorio, móvil y teclado. Corrección posterior y conflictos cuentan además con pruebas HTTP y de persistencia; no se atribuyen al agente comprobaciones visuales. |

### Migración local conservando datos

Conexión comprobada explícitamente como SQLite sobre
`database/database.sqlite`, anulando DB_URL solo en el proceso de migración.
No se modifica `.env` ni se utiliza Supabase.

Copia consistente previa mediante SQLite `VACUUM INTO`:
`%TEMP%/UsTwo-backups/before-spec028-20261119-201400.sqlite`.
La marca del nombre corresponde al reloj del proceso PHP, no al horario
de trabajo de la spec. No se reinicia ni borra la base original.

`migrate` y `migrate:status` confirman aplicada
`2026_11_19_000011_add_catalog_metadata_provenance` en el lote 4.
Se compararon todas las columnas anteriores y el número de filas de todas
las tablas de aplicación antes y después (excepto el registro de migraciones):
contenido idéntico. Se conservan 2 usuarios, 2 estanterías, 3 membresías,
1 película, 1 serie, 9 lecturas y 3 valoraciones de lectura; las demás
tablas estaban vacías y siguen intactas. La comparación usa huellas de
contenido sin imprimir datos personales.

### Controles

- PHP 8.5 con pdo_sqlite, `vendor/bin/phpunit --display-skipped`:
  67 pruebas, 1 989 aserciones, 0 fallos. Dos pruebas de seeder de producción
  omitidas porque requieren PostgreSQL aislado. SQLite en memoria para tests.
- `npm run build`: correcto, 679 módulos.
- `git diff --check`: correcto.

No se ha abierto el navegador ni publicado cambios. La verificación
PostgreSQL y el despliegue quedan fuera de la validación local, con la
ventana sin escrituras pendiente de autorización. La comprobación manual
ha sido confirmada por Ángel y se solicita revisión final del cierre.

La revisión detectó un fallo SQL de alta/vinculación TMDB sin respuesta de
formulario; se corrigió en ejecución y se añadió regresión que verifica
error `save`, input retenido y conservación de valores, procedencia, versión
y valoración previa. Suite completa repetida correctamente tras la corrección.

Reauditoría de código: sin defectos adicionales identificados. La confirmación
manual de Ángel se incorpora para resolver el bloqueo anterior de CA-07.
Revisión final: `PASS`, sin bloqueos.
Laravel en 8000 y Vite en 5173 siguen respondiendo
HTTP 200; no se han reiniciado para esta ejecución.

Pendientes externos: commit, publicación, integración en dev y traslado de
la documentación a documentation. No se autoriza por este cierre tocar
producción ni dar por verificado PostgreSQL.
