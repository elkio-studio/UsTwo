---
spec: "026"
status: approved
---

# Plan — Editar fichas manuales

## Enfoque y componentes

Completar la edición existente sin introducir servicios, dependencias ni
formularios paralelos. Reutilizar `CollectionController` para Películas y
Series, `MovieMetadataForm` para su interfaz y `Field` con `multiline`.
Lecturas conserva su controlador y formulario actuales: modificar solo
si la verificación encuentra un incumplimiento de la spec aprobada.

Archivos previstos:

- `app/Http/Controllers/CollectionController.php`.
- `resources/js/pages/MovieDetail/MovieMetadataForm/MovieMetadataForm.jsx`.
- Pruebas existentes en `tests/Feature/MvpTest.php`, `SeriesTest.php`
  y `ReadingsTest.php`.
- Artefactos de la spec para evidencias y cierre; `MEMORY.md` únicamente
  para pendientes operativos.

## Contratos y persistencia

- Mantener rutas PATCH y retorno al detalle actuales.
- Aceptar `overview` opcional, texto hasta 20 000 caracteres, únicamente
  en fichas de Películas y Series con `tmdb_id` nulo. Rechazar un intento
  de modificar esta nueva propiedad mediante la edición de una ficha de
  catálogo, conservando el registro completo.
- No ampliar ni cambiar en esta historia la edición actual de los otros
  campos de catálogo: aplicar toda la política 025 queda fuera del alcance.
- Usar las columnas `overview` existentes y los modelos actuales;
  no se requiere migración ni cambio de esquema en la base local.
- Validar todos los cambios antes de la actualización única. Mantener
  campos omitidos, permitir vaciar los opcionales y conservar reglas de
  título, año, visionado y duplicados. Presentar error de guardado sin
  escritura parcial ni pérdida de datos del formulario.
- Conservar la búsqueda de ficha mediante la relación de su estantería
  y la protección de pertenencia actual; no aceptar IDs, origen ni
  aportaciones personales como metadatos editables.

## Fases

1. Contrastar pruebas actuales y cubrir los casos de la matriz antes de
   ampliar la edición. Reutilizar fixtures y mecanismos de fallo existentes.
2. Incorporar validación y guardado de `overview` manual en el controlador
   compartido. Si el manejo actual de errores de guardado no conserva
   formulario y mensaje, ajustar ese flujo dentro del mismo controlador.
3. Añadir al formulario compartido la sinopsis solo cuando la ficha sea
   manual. Integrarla en los valores iniciales y en el envío de campos
   modificados; preservar el estado tras guardar y ante error. Reutilizar
   `Field`, etiquetas, estilos, foco y distribución del detalle existentes,
   sin CSS vacío ni nuevos componentes innecesarios.
4. Ejecutar verificaciones locales y registrar evidencias. Corregir solo
   incumplimientos dentro de esta historia; cambios de alcance requieren
   decisión del desarrollador.

## Pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Edición válida en Películas y Series y en las tres categorías de Lecturas; recarga del detalle y retorno al listado con categoría correcta. |
| CA-02 | Título vacío, límites de longitud, año y fechas inválidos; sinopsis de 20 000 aceptada y 20 001 rechazada; campos opcionales vaciados y campos omitidos intactos; duplicado sin año conserva la ficha. |
| CA-03 | Cuenta ajena y ruta de otra estantería, incluida una cuenta miembro de ambas, en las colecciones afectadas. |
| CA-04 | Petición con cambios válidos e inválidos y fallo de guardado simulado: registro intacto y errores comunicados; ediciones válidas conservan IDs, origen y valoraciones de todos los miembros. Verificación manual del texto retenido ante error. |
| CA-05 | Fichas manuales existentes y creadas por el flujo de alta, sin cambios de esquema; regresión de catálogo, incluida Lectura con proveedor sin ID y rechazo de `overview` en película o serie vinculada. |
| CA-06 | Formulario y errores en móvil, escritorio y teclado, etiqueta asociada, foco visible, guardado y ausencia de desbordamiento horizontal. |

Ejecutar PHPUnit con PHP 8.5 y SQLite en memoria según `phpunit.xml`,
primero las pruebas afectadas y después la suite completa. Ejecutar
`npm run build` y `git diff --check`. No añadir herramientas ni comandos
de lint inexistentes. Registrar las verificaciones manuales efectivamente
observadas; no abrir navegador sin solicitud ni dar por probada la interfaz
solo por compilar. Mantener los servidores locales activos si ya lo están.

## Documentación y cierre

Registrar resultados por criterio en esta spec y tareas, usando la revisión
final del proyecto. Al cerrar, anotar horario, pausas conocidas y horas
para GitHub Projects. No duplicar la política de DT-007 ni cambiar las
specs históricas. Mantener pendientes externos en memoria operativa.

## Compatibilidad, despliegue y riesgos

No hay migraciones, nuevas APIs, cambios de rutas ni dependencias. No ejecutar
comandos de reinicio o borrado de bases. La publicación e integración y el
traslado documental a `documentation` requieren autorización posterior.

El riesgo principal es ampliar accidentalmente la edición de catálogo al
usar controlador y formulario compartidos: condicionar `overview` por
origen tanto en servidor como en interfaz y cubrirlo con regresión.
Otro riesgo es perder cambios ante errores: verificar atomicidad y estado
del formulario sin alterar las valoraciones personales.
