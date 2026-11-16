---
spec: "010"
status: approved
---

# Plan técnico — Cajón de Series

El desarrollador aprobó el plan, incluido el buscador de Películas en
tres columnas en escritorio.

## Datos y permisos

- Añadir almacenamiento para series y valoraciones vinculado a la
  estantería. Mantener separados los identificadores de Películas y
  Series. La base debe impedir duplicados de un ID de TMDB dentro de
  la misma estantería y de una valoración para la misma serie y cuenta.
- Conservar el cajón de Películas y añadir una posición opcional para
  Series. Verificar el hueco libre al escribir, también al mover
  Películas. Las escrituras simultáneas deben impedir que ambos
  cajones acaben en el mismo hueco. La base debe garantizar esa regla
  incluso si la revisión anterior escribe `movie_slot` sin conocer
  Series. Aplicar cambios aditivos al esquema.
- Usar rutas con la estantería explícita y comprobar la pertenencia
  en el servidor antes de leer, buscar o escribir. Resolver la ficha
  dentro de esa estantería y obtener el autor de la valoración de la
  sesión. El borrado confirmado elimina las aportaciones de la ficha.

## Catálogo y recorrido

- Ampliar el servicio TMDB para usar búsqueda y detalle de televisión
  con el token existente. Normalizar el título, año, portada y sinopsis.
  Distinguir ficha no disponible de coincidencia manual sin perder el
  formulario. Mantener la atribución de TMDB.
- Aplicar las reglas de Películas al alta, vinculación, edición,
  borrado y paginación. Acotar las coincidencias y sus confirmaciones
  a Series de la estantería activa. En la vinculación, guardar la
  referencia externa sin reemplazar fecha ni aportaciones.
- Integrar Series en el selector de cajones y reutilizar la dirección
  visual y componentes de Películas. Conservar la navegación de vuelta
  y refrescar el listado al restaurarlo desde el historial.
- Compactar los resultados de `MovieCreate` en CSS. Tres columnas en
  escritorio, dos en tablet y una en móvil. Conservar el área de
  pulsación, el foco y el envío directo.

## Verificación

| Criterios | Evidencia prevista |
| --- | --- |
| CA-01 | Migración de una estantería existente, colocación y movimiento de ambos cajones, colisión y persistencia tras recargar. |
| CA-02, CA-07 | Respuestas simuladas de TMDB TV, alta directa y manual, duplicados, confirmación, fallo de catálogo y borrado en cascada. |
| CA-03, CA-06 | Acceso anónimo y cruzado, autor de valoraciones, valores límite, edición compartida y preservación al vincular. |
| CA-04, CA-05 | Paginación con más de 12 fichas, listado a 375 px, foco, puntero, tacto e historial. |
| CA-08 | Revisar las tres composiciones del buscador de Películas y probar que mantiene el alta directa. |

Ejecutar las suites SQLite y PostgreSQL aislado y `npm run build`.
Registrar las comprobaciones visuales y separar cualquier pendiente
manual. Actualizar el README con el acceso a Series y la colocación de
cajones. Antes de publicar, verificar las migraciones sobre una copia
aislada y que Películas sigue funcionando con sus datos actuales.
Ensayar una escritura de la revisión anterior con el esquema actualizado
y un hueco de Series ya ocupado. Comprobar también en PostgreSQL que
el rol de aplicación puede usar las nuevas tablas y secuencias sin
adquirir permisos de administración del esquema.
