---
spec: "010"
status: completed
---

# Tareas — Cajón de Series

- [x] **T-01 — Preparar almacenamiento, rutas y permisos** (CA-01, CA-03, CA-06).
  Verificar migraciones y aislamiento en ambas bases.
- [x] **T-02 — Conectar TMDB y los flujos de Series** (CA-02, CA-03, CA-05, CA-06, CA-07).
  Probar alta directa y manual, duplicados, edición, valoraciones y borrado.
- [x] **T-03 — Integrar el cajón y las pantallas** (CA-01, CA-04, CA-05).
  Comprobar coexistencia de cajones, portadas, navegación y estado vacío.
- [x] **T-04 — Compactar la búsqueda de Películas** (CA-08).
  Comprobar las columnas por tamaño y conservar el alta directa.
- [x] **T-05 — Verificar el conjunto y registrar evidencias** (CA-01 a CA-08).
  Ejecutar suites, compilación y revisión de las pantallas y sus estados.
  Actualizar el README y ensayar el solapamiento entre revisiones y los
  permisos de las nuevas tablas en PostgreSQL aislado.

## Evidencia local

- SQLite: 21 pruebas aprobadas, 576 aserciones y dos omisiones exclusivas
  de PostgreSQL. PostgreSQL 17 aislado: 23 pruebas y 595 aserciones.
- Migración aplicada al SQLite local tras guardar una copia. Se compararon
  películas, valoraciones y posiciones de los cajones de Películas.
- La base rechazó una escritura que solo actualizaba `movie_slot`
  e intentaba ocupar el hueco de Series, como haría la revisión anterior.
- En PostgreSQL aislado el rol de aplicación usó tablas y secuencias
  de Series sin permiso para crear objetos en el esquema.
- Consulta real a TMDB TV con búsqueda, portada y sinopsis. Compilación
  y comprobación de espacios en el diff correctas.
- El desarrollador confirmó que la prueba visual local del cajón de
  Series y la cuadrícula de Películas salió bien (CA-01, CA-04 y CA-08).
  El agente hizo las comprobaciones automatizadas sin abrir el navegador.
