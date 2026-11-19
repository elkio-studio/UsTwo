---
spec: "027"
status: approved
---

# Plan — Proteger aportaciones personales

## Enfoque

Conservar la arquitectura y las reglas existentes. Los controladores ya
obtienen el autor de la sesión, buscan la ficha mediante su estantería y
actualizan por la pareja ficha/autor. Reutilizar esas garantías, sus
restricciones de unicidad y los componentes compartidos de valoración.
Añadir cobertura donde falta y corregir únicamente incumplimientos de la
[spec aprobada](spec.md), sin introducir otra capa de autorización.

## Componentes y contratos

- `app/Http/Controllers/CollectionController.php`: conservar el contrato
  de valoración de Películas y Series; añadir manejo de fallo SQL con
  error de formulario, como ya hace ReadingController.
- `app/Http/Controllers/ReadingController.php`: reutilizar su contrato,
  validación y manejo de errores actuales; comprobar regresión sin
  modificarlo si cumple.
- `tests/Feature/MvpTest.php`, `SeriesTest.php` y `ReadingsTest.php`:
  completar casos usando fixtures, HTTP simulado y fallos SQL existentes.
- Revisar `CollectionRatingForm`, `CollectionReviews` y las páginas de
  detalle que eligen la aportación del usuario. Mantenerlos si cumplen;
  cualquier corrección se limita a autoría, errores y comportamiento aprobado.
- Artefactos de esta spec y `MEMORY.md` para evidencias y pendientes.

Mantener rutas PUT de valoración, escala 0–100, nota hasta 5 000
caracteres y valores nulos. Rechazar el autor indicado por el cliente.
La actualización persiste solo score y note de la pareja ficha/usuario
autenticado. No añadir endpoints que permitan seleccionar una valoración
ajena, ni aceptar IDs o campos personales desde la edición compartida.
Conservar los retornos a listados y categoría de Lecturas.

## Datos y compatibilidad

Comprobar las claves foráneas y unicidad existentes de las tablas de
valoraciones. No se prevé migración ni actualización de datos históricos:
mantener asociaciones, valores y reglas de borrado actuales. Si la revisión
descubre que requiere un cambio de esquema no previsto, detener esa parte
y presentar la necesidad al desarrollador antes de ampliar el plan.

## Fases

1. Contrastar la cobertura actual con la matriz siguiente. Añadir pruebas
   de dos miembros sobre la misma ficha y de conservación de todas las
   aportaciones antes de cambiar lógica.
2. En CollectionController, envolver únicamente el guardado de valoración
   en el manejo de QueryException existente en Lecturas: registrar error
   y devolver error `save`, manteniendo el input y el estado anterior.
   Conservar validación previa y actualización única.
3. Verificar los flujos compartidos y de vinculación actual a TMDB: las
   peticiones con score, note, user_id o identificadores de aportaciones
   no deben permitir editar relaciones personales desde metadatos.
   Mantener la conducta actual de rechazo o exclusión de campos ajenos
   cuando ya sea segura, sin imponer una política nueva de metadatos.
4. Ejecutar controles, recopilar evidencias reales y solicitar auditoría
   final. Comprobaciones manuales pendientes impiden cerrar la spec.

## Pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Dos autores crean y corrigen aportaciones en Películas, Series y las tres categorías de Lecturas, en fichas manuales y de catálogo. Verificar pareja de IDs, valores tras consulta y ausencia de duplicados. |
| CA-02 | Intentos de enviar user_id e IDs de valoración ajena; conservar ambas aportaciones. Revisar selección de ownRating por viewerId y consulta de valores ajenos no editables. |
| CA-03 | Capturar las aportaciones de ambos autores antes y después de editar metadatos manuales o de catálogo mediante flujos permitidos; incluir campos personales manipulados. Reutilizar pruebas de vinculación TMDB y ampliarlas para dos autores. |
| CA-04 | Sin sesión, cuenta ajena, rutas cruzadas de miembro de una o ambas estanterías; comprobar que no cambia ninguna aportación ni ficha distinta. |
| CA-05 | 0, 100, nulos y nota de 5 000 aceptados; decimales, fuera de rango y 5 001 rechazados. Simular fallo SQL y comprobar estado anterior e input conservados. Las valoraciones existentes siguen intactas. |
| CA-06 | Retorno a los listados correctos y categorías books/manga/comics; comprobar manualmente edición propia, consulta ajena, errores, foco, teclado y móvil en los componentes reutilizados. |

Ejecutar PHPUnit con PHP 8.5 y SQLite en memoria según `phpunit.xml`:
primero pruebas afectadas y después suite completa. Ejecutar `npm run build`
y `git diff --check`. No instalar herramientas ni crear comandos de calidad
inexistentes. No abrir navegador sin petición; registrar por separado
las comprobaciones que realice Ángel, sin inferirlas de las pruebas HTTP.

## Documentación y cierre

Actualizar evidencias por criterio en la spec y tareas, sin duplicar DT-007
ni reescribir historias cerradas. Tras verificación final satisfactoria,
registrar inicio, cierre, pausas conocidas y horas para GitHub Projects.
Mantener en MEMORY.md los pendientes operativos; commits, publicación,
integración y traslado de la spec a documentation necesitan autorización
posterior y actualización previa de las ramas de destino.

## Despliegue y riesgos

No se introducen dependencias, APIs, cambios de rutas ni despliegue especial.
No reiniciar ni borrar bases y mantener los servidores locales activos.

El principal riesgo es confundir datos de ficha compartida y personales:
conservar listas de campos permitidos y verificar todas las aportaciones
ante peticiones manipuladas. Otro riesgo es que un fallo SQL muestre un
error genérico sin conservar el formulario: probarlo en servidor y comprobar
la presentación con el componente actual.
