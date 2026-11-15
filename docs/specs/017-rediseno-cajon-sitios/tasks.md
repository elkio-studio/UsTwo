---
spec: "017"
status: completed
---

# Tareas — Rediseño del cajón de Sitios

- [x] **T-01 — Ampliar el mapa y adaptar el panel**
  - Criterios: CA-01, CA-02, CA-04.
  - Evidencia: panel inicial según dispositivo y mapa utilizable en ambos estados.
- [x] **T-02 — Trasladar la gestión de sitios al panel**
  - Criterios: CA-03, CA-04, CA-05.
  - Evidencia: ficha desde el marcador, formularios, confirmación y errores.
- [x] **T-03 — Implementar edición y borrado de listas en el panel**
  - Criterios: CA-05, CA-06, CA-07.
  - Evidencia: permisos, validación, confirmación simple o doble, cancelación
    y borrado conjunto de lista y sitios con actualización de marcadores.
- [x] **T-04 — Verificar y documentar**
  - Criterios: CA-01 a CA-08.
  - Evidencia: suite SQLite, build de Vite, diff y pruebas de interacción.
- [x] **T-05 — Permitir mover el botón de invitaciones**
  - Criterios: CA-08.
  - Evidencia: arrastre libre, ajuste animado al lateral y movimiento con teclado.

## Evidencias locales

- PHPUnit con PHP 8.5.12 y SQLite. Pasaron 34 pruebas con 854 aserciones.
  Se omitieron dos casos exclusivos de PostgreSQL.
- La suite comprobó validación, permisos de miembros, aislamiento,
  borrado en cascada y rechazo de un recuento desactualizado.
- Pasaron el build de Vite y `git diff --check`.
- Ángel confirmó el funcionamiento en local en escritorio y móvil tras
  compactar el bloque de navegación bajo la cabecera en móvil. La
  confirmación fue general. No se registró evidencia individual de
  teclado, bloqueo de teselas ni de cada paso de la lista.

## Guía de comprobaciones manuales

1. En escritorio, comprobar la cabecera, el reparto 40/60 y el mapa a
   toda altura. Plegar y reabrir el panel y comprobar las transiciones,
   el botón a la izquierda y la conservación de un formulario a medio hacer.
2. En móvil, comprobar que el panel empieza plegado, abre desde abajo y
   permite llegar a todos los campos. Con teclado, comprobar el foco,
   la selección de marcadores con Enter o Espacio y las confirmaciones.
   Comprobar que los controles y la atribución del mapa siguen accesibles.
3. Buscar, añadir, consultar, editar y eliminar un sitio. Abrir una lista
   y pulsar un sitio para comprobar el zoom. Recargar para verificar
   que el mapa refleja los cambios. Bloquear temporalmente las teselas
   y comprobar que se puede continuar introduciendo coordenadas.
4. Crear desde «+», editar desde «⋯» y comprobar el emoji del marcador.
   Borrar una lista vacía y otra con sitios. En la segunda, probar cancelar
   en ambos pasos y confirmar después con una lista de prueba.
5. Arrastrar el botón de invitaciones por el centro hacia ambos lados,
   con ratón y toque. Comprobar el deslizamiento, la altura, el ajuste
   al borde, la apertura por pulsación y el movimiento con flechas.
