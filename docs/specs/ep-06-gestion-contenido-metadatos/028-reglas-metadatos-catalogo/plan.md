---
spec: "028"
status: approved
---

# Plan — Aplicar reglas de metadatos de catálogo

## Enfoque y decisión técnica propuesta

Conservar los modelos y controladores existentes. Proponer en cada tabla
`movies`, `series` y `readings` dos columnas:

- `catalog_fields`: JSON con los nombres de campos cuyo valor procede del
  catálogo; nunca contiene valores, IDs de usuarios ni historial.
- `metadata_version`: entero no negativo con valor inicial 0 para controlar
  escrituras simultáneas de metadatos y vinculación.

Un campo de metadatos no incluido en la lista es completado a mano o está
ausente, y sigue editable. Vaciarlo no lo convierte en externo. La identidad
de catálogo sigue determinada por tmdb_id o provider, no por esta lista.
La lista y versión son controladas por servidor, nunca por el formulario.

Ángel ha aprobado expresamente esta elección técnica. Evita
una tabla de procedencias y permite guardar valor y protección en una sola
actualización, tanto en SQLite como en PostgreSQL.

## Componentes afectados

- Nueva migración aditiva en `database/migrations`, con fecha y secuencia
  posteriores a las existentes, sin editar migraciones ya aplicadas.
- `Movie`, `Series`, `Reading` y un trait pequeño
  `app/Models/Concerns/HasCatalogMetadata.php`: centralizar cálculo de campos
  importados, cast de lista y obtención de campos editables. Cada modelo
  declara sus campos; el trait no consulta proveedores ni guarda registros.
  Se justifica para que la misma política no se replique en tres modelos.
- `CollectionController`: altas, vinculación TMDB, edición y datos del detalle.
- `ReadingController`: alta desde selección validada, edición y detalle.
- `MovieMetadataForm` y `ReadingMetadataForm`: usar permisos por campo
  enviados por servidor en lugar de deducirlos del valor nulo o del proveedor.
- Pruebas existentes `MvpTest`, `SeriesTest`, `ReadingsTest`; nueva prueba
  de migración si no hay lugar apropiado en las existentes.
- Artefactos de esta spec y memoria para evidencias y pendientes.

## Persistencia y adaptación

1. Añadir catalog_fields nullable y metadata_version con valor 0. Rellenar
   la lista por lotes con Query Builder, sin llamar catálogos ni usar modelos
   que puedan cambiar después. Fichas manuales: lista vacía. Fichas con
   tmdb_id o provider: nombres de campos conocidos cuyo valor esté presente,
   conforme a la confirmación de producción de la spec 025.
2. Para el cálculo, null y cadenas vacías o solo espacios son ausencia;
   no alterar el contenido histórico ni tratar un valor numérico 0 como vacío.
   Incluir portada cuando existe. Preservar todas las demás columnas,
   IDs, timestamps y relaciones. El título sigue sujeto a la validación
   obligatoria existente, sin crear nuevas rutas de alta sin título.
3. Nuevas altas guardan lista explícita junto con los valores normalizados
   por el proveedor. Altas manuales usan lista vacía. La selección de Lecturas
   se valida y la procedencia se calcula en servidor; no confiar en datos
   de procedencia incluidos por el cliente.
4. La adaptación solo se ejecuta como parte de la nueva migración una vez,
   no al iniciar la aplicación ni en cada guardado. No reclasificar valores
   manuales posteriores. Lista nula en una ficha de catálogo se trata de
   forma conservadora como valores presentes importados, hasta su adaptación;
   no usar un error de procedencia para abrir campos protegidos.
5. El down solo retira las columnas nuevas; no restaura ni elimina fichas
   o valoraciones. Probarlo exclusivamente en una base temporal aislada,
   nunca sobre la base local o de producción.

## Contratos de edición y concurrencia

El detalle entrega una lista de campos editables y metadata_version.
PATCH envía esa versión y únicamente los campos modificados. Conservar
las rutas y respuestas actuales, incluido el retorno al detalle.

Validar pertenencia y campos permitidos: título, año, sinopsis y visionado
en Películas/Series; título, autoría, publicación y sinopsis en Lecturas.
Respetar protección por catalog_fields. Identidad, portada, campos personales
y catalog_fields no se aceptan como metadatos editables. Versión ausente o
inválida produce error de formulario, sin escritura; actualizar pruebas
existentes que hagan PATCH para enviar la versión correspondiente.

La escritura condicional exige mismo ID, estantería y versión leída e
incrementa metadata_version junto con los valores. Si falla la condición,
devolver conflicto de formulario con indicación de recargar, conservando
input y registro vigente. Si no hay cambios, no incrementar la versión.
El guardado del visionado también participa en esta versión para evitar
escrituras incoherentes. Las valoraciones siguen su flujo separado.

Cuando la petición de vinculación incluye candidate_id, leer primero la
ficha desde la relación de su estantería y capturar metadata_version antes
de consultar TMDB. Esta lectura no reserva ni modifica la ficha. Tras la
consulta externa, volver a comprobar que cumple los criterios de candidato
del flujo actual y, dentro de la transacción existente, que sigue siendo
manual y tiene aquella versión capturada. La escritura condicional usa
esa referencia anterior a la consulta externa. Aplica cada valor presente del proveedor,
incluidos título y año; cuando falta, conserva el valor manual. Actualiza
lista y versión en la misma operación. No altera ID local, visionado ni
ratings. Si hubo otra edición mientras se consultaba TMDB, se comunica
conflicto y no se guarda un estado combinado parcial.

## Fases de implementación

1. Ampliar pruebas de la matriz y verificar el esquema existente.
2. Implementar migración, casts y helpers de política por campo.
3. Integrar procedencia en altas, vinculación y ediciones con validación
   completa y control de versión. Mantener reglas de duplicados y fechas.
4. Adaptar formularios: mostrar valores externos no editables con texto
   breve de explicación; habilitar los ausentes y manuales, incluida sinopsis
   de catálogo ausente. Enviar solo cambios permitidos y versión. Reusar
   Field, estilos y componentes existentes, sin nuevos CSS vacíos, hooks o
   utilidades si no hay una responsabilidad independiente que extraer.
5. Aplicar la migración en SQLite local tras comprobar la conexión y hacer
   copia de seguridad del archivo existente. Verificar migrate:status,
   pruebas, compilación, diff e interfaz. Mantener los servidores activos;
   reiniciar solo si hace falta cargar configuración, preservando Vite.

## Pruebas y trazabilidad

| Criterio | Evidencia prevista |
| --- | --- |
| CA-01 | Alta manual y catálogo de las cinco colecciones; lectura de lista y permisos tras recarga; Lectura con proveedor e ID nulo. |
| CA-02 | Modificar cada campo externo mediante PATCH manipulado, incluida petición mixta; rechazar cambios de lista, identidad y portada. Todos los valores y la versión previa quedan intactos. |
| CA-03 | Completar año/sinopsis/autoría/fecha ausentes, corregir por otro miembro, vaciar opcionales y volver a completar; campos omitidos intactos y regresión de manuales. |
| CA-04 | TMDB con y sin título/año/sinopsis/portada cuando la respuesta sea admisible por el contrato actual; prevalencia externa o conservación manual; ID, visionado y dos valoraciones intactos. Un resultado sin título que el adaptador rechaza conserva su error actual. |
| CA-05 | Fixture previo a migración con manuales y catálogos incompletos, relaciones y valores presentes; comparar contenido e IDs después y comprobar que futuras ediciones manuales no se reclasifican al volver a ejecutar migrate. |
| CA-06 | Dos ediciones y carrera entre PATCH y vinculación con versión anterior; fallo SQL y petición inválida; conservar estado y procedencia de forma indivisible. Probar rutas ajenas, miembro de ambas y campos personales inyectados. |
| CA-07 | Móvil y teclado: diferencia visible de editables/protegidos, errores, input retenido, guardado y retorno. Portada ausente mantiene reemplazo. |

Ejecutar PHPUnit con PHP 8.5 y SQLite en memoria según phpunit.xml, primero
pruebas afectadas y después suite completa; `npm run build` y
`git diff --check`. Las pruebas de migración usan una base temporal propia,
con tablas previas creadas mediante migraciones, sin borrar datos de desarrollo.
No abrir navegador sin solicitud; registrar comprobaciones manuales reales
de Ángel por separado. No dar por verificado PostgreSQL con pruebas SQLite.

## Documentación, despliegue y compatibilidad

Actualizar evidencias en esta spec y tareas; enlazar DT-007 sin duplicar
su política. Registrar cierre, pausas y horas GitHub Projects tras revisión
final satisfactoria y dejar pendientes externos en MEMORY.md.

Para producción, la migración aditiva se ejecutará por el flujo CI/CD
existente antes del despliegue autorizado. Requiere una ventana sin escrituras
de metadatos durante adaptación y cambio de versión: el cliente anterior no
envía metadata_version y las escrituras antiguas no registran procedencia.
No iniciar ese despliegue ni tocar producción durante la ejecución local.
Tras desplegar, recargar clientes antiguos ante el error de versión.

No añadir sincronización externa, historial, servicios ni dependencias.
Riesgos: clasificar datos históricos según la confirmación recibida, proteger
un campo rellenado a mano por una inferencia incorrecta y carreras con TMDB.
Mitigar con adaptación única, permisos persistidos explícitos, versión
condicional y pruebas de conflicto. Si se descubre otra situación histórica
en producción, elevarla antes de adaptar esos registros.

## Aprobaciones

Ángel confirma catalog_fields, metadata_version, el trait compartido y la
versión obligatoria en PATCH. Ángel aprueba el plan completo y solicita
expresamente su implementación y migración local conservando los datos.
El despliegue y su ventana sin escrituras se autorizarán por separado.
