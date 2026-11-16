---
spec: "006"
status: completed
---

# Tareas — 006

- [x] **T-01 — Aplicar la base visual y las reglas compartidas**
  - Criterios: CA-01, CA-04, CA-06.
  - Evidencia esperada: paleta, tipografía, controles y foco renderizados; módulos con enlaces a clases globales.
- [x] **T-02 — Ajustar la presentación de las pantallas existentes**
  - Criterios: CA-01, CA-02, CA-03, CA-04.
  - Evidencia esperada: acceso, panel, listado, detalle, alta y créditos revisados, incluidos lista vacía, carga del catálogo, errores, títulos largos y puntuaciones legibles.
- [x] **T-03 — Verificar alcance, accesibilidad y regresiones**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06.
  - Evidencia esperada: pruebas con SQLite, build y diff correctos; recorrido en navegador a 375 px y escritorio, foco, contraste y alcance de Sitios y Compras confirmado; auditoría final.

## Evidencia de verificación local

- **CA-01, CA-06.** El único cambio de la aplicación está en `global.css`.
  Vite compiló las clases y Edge confirmó la paleta y Georgia mediante
  estilos calculados. Se recorrieron acceso, panel, lista, alta, detalle,
  créditos y la pantalla de comprobación técnica.
- **CA-02, CA-03.** Edge a 375 y 1440 px, con dos cuentas y SQLite de
  fichero temporal, confirmó el orden de dos películas, el ajuste de un
  título largo con ambas puntuaciones y la ausencia de desbordamiento
  horizontal. A 375 px se activaron los enlaces del panel y la lista con
  teclado y se comprobó foco visible en el detalle.
- **CA-04.** Contrastes calculados de texto sobre fondo 12,88:1, acción
  principal 8,21:1, enlaces 7,45:1, errores 7,00:1 y borrado 8,33:1.
  En ambos anchos se probó alta desde lista vacía, carga de catálogo con
  retraso inducido, error de catálogo sin token, error de campo y fallo
  de guardado simulado en SQLite con reintento y datos conservados.
- **CA-05.** El panel conserva Películas como única colección. Sitios y
  Compras siguen como descripciones en la especificación. El diff no
  incluye rutas, acciones, integraciones ni datos.
- `npm.cmd run build`, PHPUnit con SQLite en memoria (9 pruebas, 185
  aserciones) y `git diff --check` pasaron.
- Auditoría final: PASS.
