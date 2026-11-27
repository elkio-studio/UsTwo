# Convenciones de frontend

## Responsabilidades y carpetas

- La página compone la vista y coordina el estado y las acciones que afectan
  a varias partes. Cada formulario, menú, listado u otra pieza con una
  responsabilidad clara puede tener su propio componente.
- Los layouts viven en `resources/js/layouts/NombreLayout/` con su JSX y CSS
  Module. Contienen la estructura visual compartida: cabecera, navegación,
  tema y ubicación del contenido. No gestionan formularios o movimientos.
  JournalLayout estructura las páginas privadas; AccessLayout representa el
  recibidor y recibe su modo desde la página. Son componentes envolventes,
  sin un mecanismo de layout persistente adicional.
- Un componente exclusivo vive junto a su consumidor. Si lo reutilizan
  varias vistas, muévelo al ámbito común más cercano y comparte la misma
  implementación. Por ejemplo, Películas y Series comparten
  `CollectionPoster` en `resources/js/components`.
- Una carpeta de agrupación contiene solo subcarpetas. Cada carpeta final
  contiene `Nombre.jsx` y, si hay estilos propios, `Nombre.module.css`.
  Usa nombres que describan la función de cada pieza. Por ejemplo,
  `layouts` agrupa `JournalLayout` y `AccessLayout`; los menús siguen en components.
- Retira las carpetas intermedias que solo envuelven un componente y carecen
  de un propósito concreto.

## JSX y formato

- Declara las páginas y componentes con `export default function Nombre(...)`.
  Desestructura las props en la firma cuando las recibas y escribe el
  `return` de forma explícita.
- Empieza cada archivo JSX con un comentario breve en español que indique
  qué parte de la web representa. Deja una línea en blanco después.
- Aplica a los archivos que modifiques el formato de la extensión Prettier
  de VS Code.

## Estilos

- Escribe CSS estándar en cada módulo para las reglas propias de su vista.
- No uses `!important` en CSS propio. Resuelve prioridades con selectores,
  alcance y orden de reglas; comprueba hover, foco y movimiento reducido.
  Usa `var(...)` para consumir colores, tipografías, cursores y demás
  valores de diseño definidos en `resources/css/global.css`.
- Mantén las variables, los estilos base y los temas compartidos en
  `global.css`. Impórtalo únicamente desde las páginas principales.
- Evita los colores hexadecimales y `composes` en los módulos. Corrige los
  avisos del editor en su origen. Si un módulo queda sin reglas, elimínalo.

## Hooks y utilidades

- Los hooks extraídos viven en `resources/js/hooks`; las funciones puras,
  independientes de React, en `resources/js/utils`.
- Separa coordinación y adaptadores de entrada cuando tengan responsabilidades
  reales. En el movimiento de cajones, el coordinador posee una sola operación,
  snapshot y supresión compartida; puntero/teclado leen referencias vigentes y
  la copia visual gestiona únicamente su DOM. Re-renderizar no debe reinstalar
  listeners ni cancelar temporizadores activos. Extraer no implica duplicar estado.

- Extrae un hook cuando encapsule una responsabilidad clara de React o
  permita compartir esa lógica. El estado sencillo que solo usa un
  componente puede quedarse en él.
- Extrae la lógica reutilizada que puede funcionar sin React a una utilidad.
  Por ejemplo, listado, alta y detalle comparten `collectionUrl` para
  construir la ruta de Películas o Series.
