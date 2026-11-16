---
spec: "006"
status: approved
---

# Plan técnico — 006

## Resumen

Aplicar «Una casa compartida» al acceso, panel y recorrido existente de
Películas. Conservar los flujos y el alcance de la especificación 005.

## Componentes afectados

- `resources/css/global.css` como fuente de las reglas y variables de estilo.
- `Journal` y `Field` para cabecera, jerarquía visual, controles y foco.
- `Login`, `Dashboard`, `Movies`, `MovieCreate`, `MovieDetail` y `Credits`.
  Ajustar solo la presentación y las clases necesarias. Mantener la pareja
  JSX y CSS Module en cada carpeta, con módulos que enlazan clases globales.

## Contratos, datos e interfaces

- Reutilizar las rutas, acciones y propiedades Inertia del MVP. Conservar
  etiquetas, validación, estados vacíos y de error y alternativa manual.
- Actualizar las variables existentes con piedra clara `#F4F2ED`, blanco
  cálido `#FFFDFA`, tinta `#252B2A` y verde petróleo `#24564F`. Usar Georgia
  para encabezados, la fuente del sistema para texto y formularios, y
  cifras tabulares para puntuaciones.
- Reutilizar la cabecera actual y la acción de alta. Mantener el título y
  ambas puntuaciones en las filas y el resto en el detalle. Permitir que
  la fila se reorganice cuando el título y las puntuaciones no quepan.
- Mantener Sitios y Compras como descripciones en `spec.md`.

## Fases de implementación

1. Aplicar la base visual a la cabecera, títulos, enlaces y controles.
2. Aplicar la composición a las pantallas existentes y revisar los estados
   vacío, carga del catálogo («Buscando…»), error, lista y detalle.
3. Ejecutar las comprobaciones y auditar la implementación contra 005 y 006.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
|---|---|
| CA-01 | Revisar en navegador acceso, panel, listado, alta, detalle y créditos. Comparar colores y fuentes calculados con la especificación. |
| CA-02 | Usar dos películas con fechas distintas y puntuaciones de las dos cuentas. Verificar el orden, el contenido de las filas y el detalle. |
| CA-03 | A 375 px, recorrer panel, lista y detalle con teclado. Probar un título largo y ambas puntuaciones, y comprobar foco visible y ausencia de desbordamiento horizontal. |
| CA-04 | Medir el contraste de las combinaciones renderizadas de texto y etiquetas de botones. Revisar «Buscando…», el error junto al campo, el error de catálogo, el fallo de guardado y el acceso al alta desde la lista vacía. |
| CA-05 | Comparar las descripciones de Sitios y Compras con la identidad aprobada. Comprobar el panel y el diff de rutas, acciones y datos. |
| CA-06 | Inspeccionar `global.css`, los módulos afectados y sus importaciones. Verificar que Vite resuelve las clases. |

Ejecutar `npm.cmd run build`, PHPUnit con SQLite según el README y
`git diff --check`. Registrar los tamaños, estados y resultados observados.

## Documentación

- Mantener la especificación como referencia visual y registrar la evidencia
  de cada criterio en las tareas de esta misma carpeta.

## Despliegue y compatibilidad

- Verificación local con el entorno instalado. Mantener Laravel, Inertia,
  React, Vite y SQLite existentes.

## Riesgos y medidas

- Las variables globales afectan a varias pantallas. Comprobar contraste,
  foco y estados en las pantallas existentes, incluida la comprobación técnica.
- Los títulos largos y las puntuaciones de ambas personas pueden forzar
  el ancho de la fila. Comprobar el ajuste a 375 px y en escritorio.
