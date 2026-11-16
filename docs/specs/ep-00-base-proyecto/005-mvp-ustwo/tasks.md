---
spec: "005"
status: completed
---

# Tareas — 005

- [x] **T-01 — Preparar persistencia SQLite y migraciones**
  - Criterios: CA-07, CA-09, CA-11, CA-15, CA-18.
  - Evidencia esperada: PDO SQLite disponible en el PHP independiente, configuración local de ejemplo y PHPUnit actualizadas, pruebas aisladas del fichero local, fichero y auxiliares excluidos de Git, y migraciones reproducibles con pruebas de unicidad y borrado de aportaciones.
- [x] **T-02 — Habilitar las dos cuentas y proteger las rutas**
  - Criterios: CA-01, CA-04, CA-08, CA-16.
  - Evidencia esperada: inicio y cierre de sesión, dos cuentas independientes, rechazo de acceso directo y procedimiento local de recuperación.
- [x] **T-03 — Construir panel, lista, detalle y alta manual**
  - Criterios: CA-01, CA-02, CA-05–CA-07, CA-12–CA-14, CA-19, CA-23, CA-24.
  - Evidencia esperada: lista vacía y con datos, alta válida e inválida, reintento, confirmación de coincidencias manuales, orden y detalle.
- [x] **T-04 — Implementar edición, aportaciones y borrado**
  - Criterios: CA-03, CA-09–CA-11, CA-14, CA-15, CA-17, CA-26.
  - Evidencia esperada: pruebas de autoría, límites, edición simultánea, confirmación y cancelación del borrado, y edición posterior a un borrado.
- [x] **T-05 — Integrar catálogo y vinculación con TMDB**
  - Criterios: CA-18–CA-22, CA-25.
  - Evidencia esperada: pruebas de búsqueda, fallo, ausencia de resultados, alta manual, identificador repetido, selección entre candidatos, créditos y comprobación real local.
- [x] **T-06 — Verificar el MVP y actualizar README**
  - Criterios: CA-01–CA-26 y requisitos de interfaz.
  - Evidencia esperada: pruebas Laravel y compilación Vite, recorrido de ambas cuentas en navegador, verificación de teclado, foco y móvil, instrucciones locales y auditoría final.

## Evidencia de implementación local

- SQLite de fichero migrado con PHP 8.5.12 y `-d extension=pdo_sqlite`.
  `artisan migrate:status` confirma la migración del MVP. El archivo y sus
  auxiliares se ignoran en Git.
- `vendor/bin/phpunit` pasó 9 pruebas y 185 aserciones con SQLite en
  memoria. `npm.cmd run build` y `git diff --check` pasaron.
- Edge a 375 px con una base temporal de fichero verificó dos sesiones,
  alta manual ante indisponibilidad del catálogo, persistencia entre
  sesiones, puntuaciones 0 y 100, foco visible por teclado, ausencia de
  desplazamiento horizontal, cancelación y confirmación del borrado y créditos.
- TMDB real respondió HTTP 200 con 20 resultados. En Edge a 375 px se
  seleccionó y guardó La llegada (2016) y se verificó su persistencia
  entre cuentas en SQLite. El PHP independiente usa un archivo de
  certificados raíz de Windows para cURL y mantiene la verificación TLS.
- Auditoría final: PASS. T-05 y T-06 completadas.
