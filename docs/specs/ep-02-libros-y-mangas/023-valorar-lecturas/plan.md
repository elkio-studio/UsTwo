---
spec: "023"
status: approved
---

# Plan técnico — Valorar las lecturas compartidas

## 1. Persistencia

- Añadir una migración reversible de valoraciones de Lecturas y la
  relación desde `Reading`, siguiendo los modelos de Películas y Series.
  Cada registro tiene lectura y autor, puntuación y nota anulables, y
  unicidad por lectura y usuario en SQLite y PostgreSQL.
- Aplicar el borrado en cascada desde la lectura y la cuenta según el
  patrón existente. Las lecturas que ya existen empiezan sin aportaciones.

## 2. Guardado y consulta

- Añadir `PUT /espacios/{space}/lecturas/{readingId}/valoracion` al grupo
  autenticado y protegido por membresía. Buscar la lectura mediante
  `$space->readings()` y comprobar la pertenencia antes de escribir.
- Aplicar las reglas de Películas y Series. Puntuación y nota se envían
  aunque sus valores sean nulos. La puntuación admite enteros de 0 a 100
  y la nota, texto de hasta 5.000 caracteres. Rechazar `user_id` enviado
  por el cliente y obtener el autor de la sesión.
- Guardar ambas propiedades mediante una única operación de inserción o
  actualización por lectura y autor. Ante un fallo de escritura, devolver
  un error al formulario y mantener la última aportación guardada.
  Tras un guardado correcto, volver al listado con la categoría de
  Lecturas activa. Aplicar el mismo regreso al listado en Películas
  y Series.
- Cargar las aportaciones de miembros actuales en el detalle y el ID del
  usuario actual para identificar su aportación. En el listado, precargar
  las puntuaciones por ficha y miembro en una consulta de relación, sin
  incluir las notas ni consultar una vez por tarjeta.

## 3. Integración en la interfaz

- Activar `CollectionRatingForm` en el detalle de Lecturas con la
  aportación propia, la URL de la ficha y el manejo de errores existente.
  Pasar las aportaciones visibles a `CollectionReviews`.
- Usar las puntuaciones de cada lectura en `CollectionCard`. Mantener
  la pestaña activa y aprovechar la recarga del listado que ya existe
  al volver desde una ficha. Comprobar que se muestra cero como valor.
- Comprobar con toque y teclado la apertura del formulario, la edición,
  el guardado y los mensajes de error, con foco visible.

## 4. Pruebas, documentación y despliegue

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Crear y actualizar la aportación propia. Verificar el detalle después de guardar y que sigue habiendo una sola fila para ese miembro y lectura. |
| CA-02 | Dos miembros valoran una lectura. Comprobar que ambos ven las aportaciones en el detalle y las puntuaciones en el listado, sin notas en los datos de las tarjetas. |
| CA-03 | Verificar autor por sesión, rechazo de suplantación, acceso sin sesión y sin membresía, y uso de una estantería distinta incluso con membresía en ambas. Confirmar que se conserva la aportación ajena. |
| CA-04 | Tras recargar detalle y listado, comprobar valores actualizados, aislamiento respecto a otra ficha y conservación de la pestaña. Verificar la limpieza al eliminar una lectura. |
| CA-05 | Probar 0, 100, valores nulos, decimales, valores fuera de rango y el límite de la nota. Simular un fallo de escritura y comprobar que se conservan la aportación previa y el borrador del formulario. Revisar toque y teclado. |

Ampliar las pruebas de Lecturas, ejecutar PHPUnit en SQLite, compilar
con Vite y comprobar el diff. Aplicar la migración a la base SQLite
local y comprobar su estado. La migración se aplica en el flujo
habitual de despliegue y su compatibilidad con PostgreSQL se verifica
en el CI del proyecto. Registrar evidencias y horas en la spec al cierre.
