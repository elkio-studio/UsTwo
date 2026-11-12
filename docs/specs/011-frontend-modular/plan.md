---
spec: "011"
status: approved
---

# Plan técnico — Frontend modular

## Resumen

Refactorizar por pantalla en la rama `refactor/frontend-modular`. Cada
extracción tendrá su JSX y su módulo CSS. Antes de editar, registrar una
referencia de la estructura, estilos y recorridos en el commit base de `dev`
para poder comparar el resultado con la misma configuración local.

## Componentes afectados

| Área | Reparto de responsabilidades |
| --- | --- |
| Dashboard | La página elige entre selector y estantería activa. El selector presenta la lista y el alta tiene su formulario. La escena conserva cuadrícula, animación y diálogo de cajones. El menú de estantería conserva selección y coordina unidades de nombre, invitación y miembros. |
| Journal, Invitations y Field | Journal conserva el layout, navegación de regreso y coordinación de paneles. Su menú de cuenta presenta la sesión y contiene edición de cuenta y cierre de sesión. Invitations conserva bandeja, contador y acciones. Field conserva etiqueta, control, acción opcional y error accesible. |
| Movies y MoviePoster | La página conserva encabezado, estado vacío, lista y paginación. La tarjeta presenta título y puntuaciones. La portada se mueve al ámbito de colecciones compartido por listado, búsqueda y ficha, con nombre neutro y su alternativa actual. |
| MovieCreate | La página coordina el modo manual, selección y confirmación devuelta por el servidor. La búsqueda posee consulta, petición, carga y error, y sus resultados comunican la selección. El formulario manual y la confirmación de coincidencias conservan sus operaciones. |
| MovieDetail | La página compone ficha y valoraciones visibles. La valoración personal, los datos comunes y la confirmación de borrado conservan estados, errores y envíos independientes. |
| Login, Account y Credits | Login controla el modo acceso o registro, con formulario y visibilidad de contraseña fuera del marco del recibidor. Account separa credenciales de la lista de estanterías con sus roles y acciones. Credits conserva atribución y regreso. |

## Contratos, datos e interfaces

- Conservar nombres y props de Inertia, rutas, métodos, payloads,
  redirecciones y permisos. Películas y Series conservan las mismas
  pantallas parametrizadas. Revisar los imports al mover cada componente.
- Los componentes exclusivos viven junto a su consumidor. Promover solo los
  reutilizados al ámbito común más cercano. Las carpetas de agrupación solo
  contienen subcarpetas y cada carpeta final contiene su JSX y un módulo CSS
  cuando tiene estilos propios.
- Usar `export default function Nombre`, props desestructurados cuando
  proceda y `return` explícito. Revisar `app.jsx` como arranque y preservar
  el refresco al navegar por historial.
- Trasladar reglas locales a CSS Modules por componente. Mantener base y
  variables en `global.css`, importado solo en páginas principales. Usar
  `var(...)` para valores compartidos y colores, retirar `composes` y
  eliminar cada selector global sustituido tras comprobar sus consumidores.

## Fases de implementación

1. Registrar la referencia en dev y mapear consumidores de estilos y props.
2. Extraer Journal y Dashboard, trasladando sus estilos y los de Field e Invitations.
3. Extraer listado, portada, alta y detalle de las colecciones compartidas.
4. Completar acceso, cuenta y créditos. Revisar los once pares originales,
   sus reemplazos y los nuevos componentes.
5. Actualizar la convención en README y ejecutar la verificación final.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Revisar carpetas, imports, firmas y propietarios de estado. Confirmar un solo origen para pantallas de Películas y Series. |
| CA-02 | Buscar `composes:` en resources/js y esperar cero resultados. Revisar colores y valores compartidos, imports de global.css y ejecutar `npm run build`. |
| CA-03 | Verificar alta y gestión de estanterías, invitaciones, cajones y cierre de paneles al pulsar fuera. Comprobar foco, teclado, toque y movimiento reducido. |
| CA-04 | Probar ambos cajones, incluidos catálogo, alta manual, coincidencias, carga y error, portada alternativa, paginación, valoración, edición, borrado y regreso con el botón Atrás del navegador. |
| CA-05 | Verificar acceso, registro, credenciales y créditos. Comparar las pantallas afectadas con la referencia en dev en escritorio y móvil, con los mismos estados y tamaños. |
| CA-06 | Ejecutar `composer test` con SQLite de pruebas, comprobar la convención en README y registrar evidencias por criterio. |

La revisión visual se hará mediante comprobación manual del usuario si no
autoriza abrir el navegador. Registrar los controles manuales pendientes
hasta disponer de su resultado.

## Documentación

Actualizar la sección frontend del README durante la implementación. Reflejar
componentes junto a su consumidor, promoción al ámbito compartido,
exportación JSX y CSS Modules con variables globales.

## Despliegue y compatibilidad

El alcance se verifica en local con SQLite. Se conservan el pipeline,
dependencias, configuración de despliegue y esquema actuales.

## Riesgos y medidas

- Al pasar de reglas globales a módulos puede cambiar la cascada. Comparar
  orden, especificidad, media queries, foco y movimiento reducido antes de
  retirar cada selector antiguo.
- Al mover formularios pueden cambiar su ciclo de vida o perderse errores.
  Comprobar que cada formulario conserva sus datos y comportamiento en
  cambios de modo, navegación y respuestas de validación.
