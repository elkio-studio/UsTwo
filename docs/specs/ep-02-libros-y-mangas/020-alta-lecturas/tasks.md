---
spec: "020"
status: completed
---

# Tareas — Registrar lecturas desde catálogo o manualmente

- [x] **T-01 — Persistencia y rutas protegidas**
  - Criterios: CA-02, CA-03, CA-04, CA-06.
  - Evidencia: migración reversible aplicada en SQLite local, relación
    con `Space`, identidad única por estantería y rutas con membresía.
- [x] **T-02 — Consulta y selección en los tres catálogos**
  - Criterios: CA-01, CA-03, CA-05, CA-06.
  - Evidencia: pruebas HTTP simuladas de Open Library, MangaUpdates y
    Comic Vine con fichas válidas, sin resultado y con fallos.
- [x] **T-03 — Alta manual y desde catálogo con prevención de duplicados**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06.
  - Evidencia: pruebas de guardado y recarga, aviso por título, rechazo
    por ID, aislamiento y un único registro por recopilatorio.
- [x] **T-04 — Integrar el alta y las fichas en las pestañas**
  - Criterios: CA-01, CA-02, CA-04, CA-05, CA-06.
  - Evidencia: cuadrícula compartida, alta en página propia, ficha con
    sinopsis y vuelta a la categoría de origen. Ángel confirmó el
    funcionamiento en escritorio y móvil.
- [x] **T-05 — Verificar y documentar el cierre**
  - Criterios: CA-01 a CA-06.
  - Evidencia: suite, build, diff, controles manuales, requisitos de
    despliegue y horas registradas en la spec.

## Verificación local

- SQLite local: `2026_11_17_000009_create_readings_table` aplicada y
  confirmada como `Ran` en `database/database.sqlite`.
- SQLite en memoria con PHP 8.5.12: PHPUnit ejecutó 45 casos, 43
  superados y 2 exclusivos de PostgreSQL omitidos, con 1.034 aserciones.
  Las pruebas de Lecturas cubren los tres catálogos con respuestas
  simuladas, altas manuales, persistencia, permisos, duplicados,
  recopilatorios y recuperación ante un fallo de guardado.
  También verifican la página de alta, el detalle y el aislamiento del
  detalle entre estanterías.
- Comic Vine real: la búsqueda por «Batman» encontró la colección de
  2016 en la cuarta página de 12 resultados. El filtro por colección y
  número devolvió Batman #4, ID `542596`. Las pruebas de fichas y
  portadas de Batman y Mortadelo están en la especificación.
- Open Library, MangaUpdates y Comic Vine respondieron desde el servicio
  Laravel local con búsquedas de «The Outsiders», «Naruto» y «Mortadelo
  y Filemón», respectivamente, tras corregir la ruta del certificado
  HTTPS de PHP.
- Las comprobaciones de limpieza reproducen el bloque de enlaces
  oficiales de Naruto y las referencias Markdown de The Outsiders.
- El 17 de noviembre de 2026, Ángel confirmó que el flujo funciona en
  escritorio y móvil. La confirmación es general. Los pasos de foco,
  teclado y portada rota carecen de un resultado manual individual.
- Vite y comprobación de espacios en Git superados. El cierre local
  usa SQLite. Los casos exclusivos de PostgreSQL quedan para CI.
