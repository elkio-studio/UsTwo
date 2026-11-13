---
spec: "014"
status: completed
---

# Tareas — Guardar un sitio visitado

- [x] **T-01 — Crear persistencia y relaciones**
  - Criterios: CA-01, CA-02, CA-04, CA-07, CA-08.
  - Evidencia esperada: migraciones reversibles y relaciones que conservan
    la estantería de cada sitio a través de su lista.
- [x] **T-02 — Implementar guardado y permisos**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-07, CA-08.
  - Evidencia esperada: pruebas de validación, pertenencia, aislamiento,
    transacción del primer guardado y creación de listas vacías posteriores.
- [x] **T-03 — Integrar la búsqueda de Geoapify**
  - Criterios: CA-03, CA-05.
  - Evidencia esperada: pruebas con respuestas simuladas de búsqueda directa
    e inversa, ausencia de coincidencias, fallo y cuota.
- [x] **T-04 — Construir el mapa y los formularios**
  - Criterios: CA-01 a CA-08.
  - Evidencia esperada: selección y guardado por las tres vías, marcadores,
    gestión de listas del alta, errores y atribución.
- [x] **T-05 — Verificar y documentar**
  - Criterios: CA-01 a CA-08.
  - Evidencia esperada: suite SQLite, build de Vite, pruebas de flujo con
    Geoapify Free, consumo observado, recarga, móvil y teclado. Registrar
    las comprobaciones pendientes y los pasos de configuración.

## Evidencias locales

- PHPUnit con PHP 8.5.12 y SQLite en memoria. Pasaron 28 pruebas con
  721 aserciones. Dos pruebas exclusivas de PostgreSQL se omitieron.
- Las pruebas verifican permisos, aislamiento, listas vacías posteriores,
  validación y reversión del primer guardado. Geoapify se probó con
  respuestas simuladas de búsqueda directa, inversa, cuota y error.
- El build de Vite y la comprobación de formato del frontend pasaron.
- Ángel confirmó en local que Geoapify devuelve direcciones, que pulsar
  el mapa propone nombres de calles y que puede crear listas y guardar sitios.
- Ángel confirmó que el marcador permanece tras recargar y que en móvil
  se puede elegir un punto tocando el mapa con la atribución visible.
- Ángel confirmó que el alta se puede completar con teclado y que el foco
  es visible.
- El panel de Geoapify registró 303 peticiones y 95 créditos consumidos
  entre el 13 de octubre y el 13 de noviembre de 2026 (UTC), considerando
  todas las claves del proyecto. El panel mostró un límite de 3.000
  créditos diarios y un consumo del 0,10 % de la cuota del periodo.
- El CI queda pendiente de publicación.
