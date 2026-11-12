---
spec: "011"
status: in_progress
---

# Tareas — Frontend modular

- [x] **T-01 — Registrar referencia y dependencias**
  - Criterios: CA-01, CA-02, CA-05.
  - Evidencia esperada: commit base de dev, inventario de pares, usos de selectores globales y pantallas y estados que se compararán.
- [x] **T-02 — Modularizar Journal y sus componentes compartidos**
  - Criterios: CA-01, CA-02, CA-03.
  - Evidencia esperada: menú de cuenta extraído, CSS propio de Journal, Field e Invitations y coordinación de paneles conservada.
- [x] **T-03 — Separar Dashboard y gestión de estantería**
  - Criterios: CA-01, CA-02, CA-03.
  - Evidencia esperada: selector, alta, escena y gestión con responsabilidades propias. Cajones, animación y acciones conservados.
- [x] **T-04 — Modularizar listado y portada de colecciones**
  - Criterios: CA-01, CA-02, CA-04.
  - Evidencia esperada: tarjeta junto al listado y portada en el ámbito común de colecciones. Paginación, estado vacío y portada alternativa conservados.
- [x] **T-05 — Separar búsqueda, alta y ficha de colecciones**
  - Criterios: CA-01, CA-02, CA-04.
  - Evidencia esperada: búsqueda, alta manual, confirmación, ficha y operaciones independientes. Prueba del refresco del listado al volver por historial.
- [x] **T-06 — Modularizar acceso, cuenta y créditos**
  - Criterios: CA-01, CA-02, CA-05.
  - Evidencia esperada: formularios y lista de estanterías con responsabilidades propias. Acceso, registro, credenciales y atribución conservados.
- [x] **T-07 — Revisar CSS, estructura y README**
  - Criterios: CA-01, CA-02, CA-06.
  - Evidencia esperada: cero declaraciones `composes:`, colores en variables globales, import de global.css solo en páginas principales, build correcto y README actualizado.
- [ ] **T-08 — Verificar regresiones y documentar resultados**
  - Criterios: CA-01 a CA-06.
  - Evidencia esperada: resultado de pruebas SQLite, comparación con dev en escritorio y móvil, comprobaciones de interacción y revisión final trazada a los seis criterios.

## Evidencias de implementación y verificación local

| Criterio | Resultado |
| --- | --- |
| CA-01 | Referencia dev `26b575d`. Comprobación estática de los 32 pares JSX/CSS, carpeta, exportación, retorno e imports. El resolutor de Inertia conserva las siete páginas y excluye los componentes anidados. |
| CA-02 | Cero declaraciones `composes:` y cero colores hexadecimales en módulos. `npm.cmd run build` correcto con Vite 8.3.6. |
| CA-03 | Suite SQLite y renderizado React sin navegador para selector y estantería activa. Queda la comprobación de cajones, foco y paneles con interacción real. |
| CA-04 | Suite SQLite y renderizado de listado, alta y ficha de ambas colecciones. Se conserva el listener de navegación por historial en `app.jsx`. Queda confirmar el retorno con interacción real. |
| CA-05 | Acceso, cuenta y créditos incluidos en los 11 escenarios de renderizado sin navegador. Pendiente la comparación visual con dev en escritorio y móvil. |
| CA-06 | PHPUnit 12.5.43 con PHP 8.5.12 y SQLite en memoria. 21 pruebas correctas, 576 aserciones y 2 omisiones previstas del seeder exclusivo de PostgreSQL. README actualizado y `git diff --check` correcto. |

Se utilizó PHP portátil en una carpeta temporal, con el SHA-256 de la
distribución aprobada en la spec 002. La comprobación de React usó el cargador
SSR de Vite y el renderizador de React desde Node, sin activar SSR en la app.
