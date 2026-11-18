---
spec: "024"
status: completed
---

# Tareas — Identificar el origen de las fichas

- [x] **T-01 — Verificar el origen con los datos existentes**
  - Criterios: CA-01, CA-02, CA-03.
  - Evidencia: pruebas de altas manuales y de catálogo y de vinculación
    posterior a TMDB sin pérdida de la ficha ni de sus valoraciones.
- [x] **T-02 — Mostrar el proveedor de una Lectura sin ID externo**
  - Criterios: CA-01, CA-02.
  - Evidencia: nombre del proveedor visible sin enlace cuando falta
    el ID, enlaces conservados para fichas con ID y etiqueta manual
    para fichas sin proveedor.
- [x] **T-03 — Verificar las reglas de edición y el aislamiento**
  - Criterios: CA-04.
  - Evidencia: una ficha con proveedor e ID nulo se trata como ficha
    de catálogo. Las pruebas de acceso entre estanterías pasan.
- [x] **T-04 — Ejecutar controles y registrar resultados**
  - Criterios: CA-01 a CA-04.
  - Evidencia: PHPUnit en SQLite, Vite, comprobación del diff y revisión
    del detalle en móvil y con teclado. Registrar las horas al cerrar.

## Verificación local

- PHPUnit en SQLite en memoria con PHP 8.5.12: 52 pruebas superadas,
  2 exclusivas de PostgreSQL omitidas y 1.243 aserciones. Se comprobó
  que una Lectura con proveedor e ID nulo conserva sus datos importados,
  permite completar un campo vacío y mantiene el aislamiento por estantería.
- El renderizado estático de React comprobó los tres proveedores con
  y sin ID externo, y el texto «Añadida a mano» en una ficha manual.
- En el archivo SQLite local se insertó temporalmente una Lectura de
  Open Library sin ID externo. La ruta autenticada devolvió HTTP 200.
  Con los datos de esa respuesta, React mostró el proveedor como texto
  sin enlace. Se revirtió la inserción y se comprobó que la ficha de
  prueba ya no existía.
- La compilación de Vite y la comprobación del diff pasaron.
- El 18 de noviembre de 2026, Ángel confirmó la comprobación manual
  del detalle en móvil y con teclado.
