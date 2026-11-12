---
id: "011"
title: "Frontend modular"
status: approved
---

# 011 — Frontend modular

## Problema y objetivo

Varias páginas combinan presentación, navegación y operaciones independientes.
Los 11 módulos CSS actuales delegan sus estilos mediante 68 declaraciones
`composes`. Esto concentra las reglas locales en `global.css` y genera avisos
de propiedad desconocida en el editor. El refactor acerca cada responsabilidad
a su página o componente y conserva el comportamiento y el aspecto actuales.

## Alcance incluido

- Revisar las siete páginas y los cuatro componentes de `resources/js`, junto
  con el punto de entrada. Refactorizar los archivos del inventario siguiente.
- Separar componentes que representan una responsabilidad concreta, como
  editar una valoración, gestionar miembros o mostrar resultados de búsqueda.
- Trasladar las reglas locales a sus módulos CSS. Mantener las variables de
  diseño y las reglas realmente globales en `resources/css/global.css`.
- Actualizar la sección de convenciones del README cuando se implemente el
  refactor, sustituyendo la indicación de usar `composes`.

## Alcance excluido

- Nuevas funcionalidades, cambios visuales deliberados y cambios de permisos,
  rutas, datos, contratos de Inertia, dependencias o despliegue.
- Backend, base de datos y automatizaciones. Los contratos actuales de Inertia
  y las rutas se consultan como referencia para verificar la compatibilidad.

## Actores

- Quien mantiene el frontend y necesita localizar dónde se presenta una vista,
  quién es dueño de su estado y dónde se envía cada petición.
- Quien usa la aplicación y espera conservar los recorridos ya disponibles.

## Inventario y responsabilidades

Los pares de la tabla están bajo `resources/js`. Cada nombre de la primera
columna identifica tanto el archivo `.jsx` como su `.module.css`. Los nombres
de componentes que se proponen describen responsabilidades y podrán ajustarse
durante la planificación.

| Par existente | Responsabilidades propuestas |
| --- | --- |
| `pages/Dashboard/Dashboard` | La página compone el selector y la estantería activa. Extraer `ShelfList` y el alta `AddShelf`, la escena `Shelf` con su cuadrícula, animación y diálogo de colocación, y `SpaceMenu` para selección y acciones sobre la estantería. En el menú, separar cambio de nombre, invitación y gestión de miembros según sus formularios y acciones. |
| `pages/MovieCreate/MovieCreate` | La página coordina el alta manual, el resultado seleccionado y la confirmación que devuelve el servidor. Un componente de búsqueda controla su consulta, petición, estado de carga y error y muestra una lista de resultados que comunica la selección. Separar el formulario de alta y la confirmación de coincidencias en unidades que se puedan leer y comprobar de forma independiente. |
| `pages/MovieDetail/MovieDetail` | La página compone la presentación de la ficha, la lista de valoraciones y las acciones. Separar información de la ficha, valoraciones visibles, formulario de valoración personal, formulario de datos comunes y borrado con confirmación. Cada operación conserva su estado y envío. |
| `pages/Movies/Movies` | El listado conserva el encabezado, estado vacío y navegación de página. Extraer la tarjeta con su portada, título y puntuaciones. Mantener la paginación junto al listado mientras siga siendo exclusiva de esa pantalla. |
| `pages/Account/Account` | La página organiza el formulario de credenciales y la lista de estanterías. El formulario controla su propio envío. La lista presenta el rol y se encarga de las acciones de entrar, editar o salir. |
| `pages/Login/Login` | La página controla la elección entre iniciar sesión y registrarse. Separar el formulario de acceso y registro del marco del recibidor. El control de visibilidad de contraseña pertenece al campo o al formulario que lo usa. |
| `pages/Credits/Credits` | Mantener la atribución y el regreso dentro de la página. Trasladar su estilo local al módulo. |
| `components/Journal/Journal` | Mantener la composición del layout, navegación de regreso y coordinación del cierre de paneles. Extraer el menú de cuenta dentro de `Journal`; su acceso al usuario de sesión, enlace de cuenta y cierre de sesión pertenecen al menú. |
| `components/Field/Field` | Mantener la etiqueta, la relación accesible con el control, el error y la acción opcional. Trasladar el estilo propio al módulo. |
| `components/Invitations/Invitations` | Mantener la bandeja, el contador, la respuesta y el error como una misma responsabilidad. Trasladar el estilo propio al módulo. |
| `components/MoviePoster/MoviePoster` | Mantener la imagen y su alternativa ante ausencia o error. Acercar la portada a las pantallas de colecciones, que son las que hoy la comparten, y usar un nombre válido tanto para películas como para series. |

`resources/js/app.jsx` se revisa como punto de arranque. Conserva el registro
del refresco del listado al volver desde el historial y el arranque de
Inertia. La regla de exportación de componentes no aplica a este archivo.

## Convenciones

- Las páginas componen la vista y coordinan únicamente el estado y las
  acciones que afectan a varias de sus partes. Un componente conserva su
  estado, efectos y petición cuando son propios de su responsabilidad.
- Un componente exclusivo vive junto a su consumidor. Si aparecen varios
  consumidores, se traslada al ámbito común más cercano. Las pantallas ya
  compartidas de Películas y Series conservan una sola implementación.
- Las carpetas de agrupación contienen solo subcarpetas. Cada carpeta final
  contiene el `.jsx` de una página o componente y un `.module.css` si tiene
  estilos propios. Los
  componentes exclusivos se agrupan junto a su consumidor.
- Cada página o componente usa `export default function Nombre(...)`,
  desestructura los props en la firma cuando los recibe y devuelve el JSX
  con un `return` explícito.
- Cada módulo contiene CSS estándar. Los colores, tipografías, espaciados y
  demás valores compartidos se consumen con `var(...)` desde
  `global.css`. Las reglas base y compartidas por toda la aplicación
  permanecen en el CSS global, que solo se importa en las páginas principales. Eliminar `composes` de los módulos sin
  cambiar la configuración del editor para ocultar sus avisos.

## Compatibilidad y casos límite

- Conservar los nombres de página que usa Inertia, incluido
  `Movies/Movies`, y los props que reciben las pantallas.
- Conservar la animación y posición de los cajones, el cierre de paneles
  al pulsar fuera y el refresco del listado al regresar con el historial.
- Los estados vacío, carga, error, coincidencias, confirmación de borrado y
  portada alternativa siguen disponibles donde corresponda.
- Mantener foco visible, acceso por teclado, tacto, movimiento reducido
  y comportamiento en escritorio y móvil.

## Pruebas al cambiar código

- Al modificar código existente, revisa las pruebas que cubren el
  comportamiento afectado y ejecútalas. Si el cambio mueve o renombra
  archivos, comprueba también las rutas y los nombres que usan las pruebas.
  Por ejemplo, las aserciones de Inertia deben seguir verificando que el JSX
  existe en su ubicación actual.
- Al añadir código nuevo, añade pruebas que cubran el comportamiento que
  introduce. Incluye los casos de error o los límites cuando apliquen.
- Antes de subir cambios de código, ejecuta las pruebas afectadas y la
  suite local aplicable. Registra el resultado y las comprobaciones
  pendientes en las evidencias de la especificación.

## Criterios de aceptación

- **CA-01.** Los once pares del inventario y los componentes nuevos se ajustan a la convención de carpeta y
  exportación. Las pantallas compartidas mantienen un único origen y cada
  componente extraído vive en el ámbito común más cercano a sus consumidores.
- **CA-02.** Los módulos usan CSS estándar y valores compartidos mediante
  variables. La búsqueda de `composes:` en `resources/js` devuelve cero
  resultados y el build sigue pasando.
- **CA-03.** Dashboard conserva alta, selección, gestión, colocación y
  apertura de cajones. Journal conserva menús, invitaciones y navegación.
- **CA-04.** Películas y Series conservan búsqueda, alta, coincidencias,
  listado, paginación, ficha, valoración, edición, borrado y retorno con
  historial actualizado.
- **CA-05.** Acceso, registro, credenciales de cuenta y créditos siguen
  funcionando. Se compara el aspecto y la interacción con dev en escritorio y móvil.
- **CA-06.** Las pruebas existentes siguen pasando. El README refleja el
  uso de módulos locales y variables globales.

## Decisiones aprobadas

- Modularizar páginas, extraer por responsabilidad y subir un componente
  al ámbito compartido más cercano cuando exista reutilización real.
- Usar funciones con exportación por defecto y CSS estándar en los
  módulos, con variables de diseño procedentes de `global.css`.
- Preparar la especificación en `refactor/frontend-modular`, creada desde `dev`. La rama se trasladó después a la copia habitual a petición del desarrollador.

## Decisiones pendientes

- Ninguna para la planificación. El desarrollador aprobó la spec 011 en la conversación.
