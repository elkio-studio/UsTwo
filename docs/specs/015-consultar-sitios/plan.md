---
spec: "015"
status: approved
---

# Plan técnico — Consultar los sitios guardados en el mapa

## Resumen

Ampliar el popup de Leaflet y verificar el recorrido de consulta. El
servidor ya entrega nombre, nota y coordenadas por estantería.

## Componentes, datos y fases

1. Reutilizar en `PlacesMap.jsx` el marcador por registro, su emoji y el
   popup existente. Mostrar el nombre y, cuando exista, la nota en un
   segundo bloque. Insertar ambos valores como texto para que se lean
   literalmente, incluidos los caracteres que puedan parecer HTML.
2. Ajustar `PlacesMap.module.css` donde haga falta para conservar la
   estética de Sitios, el foco visible y la lectura de notas largas o
   con saltos de línea en móvil. Mantener la interacción de Leaflet
   por clic, toque y teclado y el aviso de mapa base no disponible.
3. Ampliar `VisitedPlacesTest.php` para verificar el contrato de lectura
   con varios sitios, con y sin nota, y dos estanterías con datos.
   Mantener las rutas, los permisos de membresía y la persistencia actuales.

## Estrategia de pruebas

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Comparar los registros leídos con los marcadores y sus coordenadas. |
| CA-02 | Abrir marcadores por clic, toque y teclado. Comprobar foco visible, nombre solo cuando no hay nota y lectura de una nota larga en móvil. Probar que el texto con apariencia de HTML se muestra literalmente. |
| CA-03 | Salir y volver al cajón y recargar. Comprobar marcadores, nombres y notas. |
| CA-04 | Comprobar con SQLite que cada estantería devuelve sus registros y que una cuenta ajena recibe 404 por acceso directo. |
| CA-05 | Bloquear las teselas, comprobar el aviso y restaurar la carga. Volver a Sitios y comprobar que los registros siguen disponibles. |

Ejecutar la suite de PHPUnit con SQLite, el build de Vite y
`git diff --check`. Ángel puede realizar las comprobaciones interactivas
si no autoriza abrir el navegador. Registrar los resultados observados.

## Documentación y compatibilidad

Anotar las evidencias en las tareas de esta spec y publicar su documentación
en `documentation` al cerrar. Se mantienen SQLite, PostgreSQL y las
claves actuales de Geoapify. El cambio utiliza el despliegue existente.

## Casos límite

Probar también marcadores muy próximos o coincidentes para detectar
solapamientos. Si impiden la consulta, registrar el caso para decidir
cómo resolverlo dentro de la historia.
