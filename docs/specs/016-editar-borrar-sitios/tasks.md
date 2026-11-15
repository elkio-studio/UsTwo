---
spec: "016"
status: completed
---

# Tareas — Editar y eliminar sitios visitados

- [x] **T-01 — Proteger y validar actualización y borrado**
  - Criterios: CA-02, CA-03, CA-05.
  - Evidencia: pruebas de mutaciones válidas, validación, lista conservada
    y rechazo de operaciones sobre sitios ajenos.
- [x] **T-02 — Mostrar ubicación y permitir editar desde el popup**
  - Criterios: CA-01, CA-02, CA-04.
  - Evidencia: coordenadas visibles y editables por las tres vías, nombre
    conservado al cambiar el punto y marcador actualizado al guardar.
- [x] **T-03 — Confirmar el borrado y actualizar los marcadores**
  - Criterios: CA-03, CA-04.
  - Evidencia: cancelar conserva el sitio, confirmar elimina el marcador
    y un fallo se comunica sin retirar el sitio.
- [x] **T-04 — Verificar y documentar el recorrido**
  - Criterios: CA-01 a CA-05.
  - Evidencia: suite SQLite, build de Vite, diff y comprobaciones de
    persistencia, móvil, teclado, foco y aislamiento.

## Evidencias locales

- PHPUnit con PHP 8.5.12 y SQLite en memoria. Pasaron 32 pruebas con
  814 aserciones. Se omitieron dos pruebas exclusivas de PostgreSQL.
- Las pruebas cubren actualización, validación, conservación de la lista,
  fallo de borrado y rechazo por falta de sesión, membresía o pertenencia
  del sitio a la estantería de la ruta.
- El build de Vite pasó tras el ajuste de contraste de los botones del
  popup. `git diff --check` pasó.
- Ángel confirmó que la edición y el borrado funcionan en local. La
  confirmación fue general. No se registró evidencia individual de
  cada método de selección de ubicación, cancelación, móvil ni teclado.
