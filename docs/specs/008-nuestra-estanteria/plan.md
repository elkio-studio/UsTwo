---
spec: "008"
status: approved
---

# Plan técnico — 008

## Resumen

Integrar la estantería en el panel privado con JSX y CSS puro. Preparar dos
composiciones del mismo mueble, con un cajón navegable superpuesto y una
base visual legible aunque no se carguen los recursos de la escena.

## Componentes afectados

- `resources/js/pages/Dashboard/Dashboard.jsx` y `Dashboard.module.css`
  para el título, la escena y el enlace de Películas. Reutilizar `Journal`
  para conservar la navegación y el cierre de sesión.
- `resources/css/global.css` para las proporciones, coordenadas del cajón,
  colores, iluminación, estados de foco y movimiento. El módulo solo
  compone las clases globales necesarias.
- `resources/js/pages/Movies/Movies.jsx` para actualizar el texto de
  retorno a «Volver a la estantería».
- Recursos gráficos locales bajo `resources/images/estanteria/`, fuera de
  las carpetas finales de páginas y componentes. Vite los incorpora al build.

## Contratos, datos e interfaces

- Se reutilizan el panel autenticado `/` y el listado `/peliculas`, con
  `Link` de Inertia y un único destino para Películas. La etiqueta es texto
  y los huecos de la escena son decorativos.
- El clic principal, el toque y Enter reproducen una apertura breve antes
  de navegar. Los clics modificados conservan el comportamiento del enlace.
  Con movimiento reducido se navega directamente. Evitar dobles activaciones
  y restablecer la posición del cajón al volver.
- El cajón se muestra tanto con películas como con el listado vacío. No se
  necesitan props, rutas, peticiones ni cambios en la base de datos.

## Fases de implementación

1. Usar la maqueta aprobada como referencia de madera, latón y luz. Preparar
   dos escenas optimizadas, una baja y ancha y otra alta y estrecha, con
   huecos abiertos y luz estable de lámpara. Separar el frente de Películas
   de la escena para que se pueda mover al abrirlo.
2. Integrar las escenas en `Dashboard` con CSS responsive, mantener el
   encuadre y el área táctil en anchos intermedios y colocar el enlace
   sobre el hueco correspondiente de cada composición. Dar al enlace una
   superficie y un foco visibles también con las imágenes bloqueadas.
3. Añadir llamas y reflejos localizados con CSS. Limitar la animación de
   iluminación a las zonas próximas a las velas y aplicar
   `prefers-reduced-motion` al ambiente y a la apertura.
4. Conectar la apertura con el listado y ajustar el texto de retorno.
   Revisar ambos encuadres y recorrer los flujos existentes.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Comprobar proporciones y distribución a 1280 y 375 px. Revisar 320 px y los anchos a ambos lados del cambio de composición para detectar recortes o desbordamiento. |
| CA-02 | Confirmar un cajón en ambas vistas y que los demás huecos quedan fuera del orden de tabulación. Repetir con el listado vacío. |
| CA-03 | Medir el objetivo táctil, comprobar foco, clic, toque y Enter, y verificar el retorno y una segunda apertura. |
| CA-04 | Comprobar en ambas vistas que la lámpara permanece estable y las velas iluminan zonas localizadas. Medir el contraste de la etiqueta en los extremos de la animación. |
| CA-05 | Activar movimiento reducido y comprobar navegación inmediata. Bloquear las imágenes y probar la etiqueta, el foco y la apertura en ambas vistas. |
| CA-06 | Recorrer alta, consulta y valoración en SQLite local con datos de prueba. Confirmar que solo Películas está habilitado y ejecutar `npm run build` y las pruebas PHP existentes. |

## Documentación

- Registrar en `tasks.md` los tamaños verificados, los estados comprobados
  y el resultado de build y pruebas. Mantener la evidencia visual con la
  verificación del cambio.

## Despliegue y compatibilidad

- Mantener el stack actual y el flujo `dev` → `main`. Los recursos entran
  con el build de Vite. La verificación local precede a cualquier integración
  o publicación autorizada.

## Riesgos y medidas

- El enlace puede desalinearse si se recorta el fondo. Escalar la escena y
  sus coordenadas dentro del mismo contenedor de proporciones conocidas.
- Si falla un recurso, el enlace debe conservar su tamaño, texto y foco.
- Mantener el texto fuera de las imágenes, limitar el peso de las escenas y
  comprobar que los reflejos no cambian el contraste de las etiquetas.
