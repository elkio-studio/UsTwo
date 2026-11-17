---
spec: "020"
status: approved
---

# Plan técnico — Registrar lecturas desde catálogo o manualmente

Implementar el alta y listado en el cajón existente, con una ficha por
libro, serie de manga o cómic individual. Cada recopilatorio se guarda
como una ficha de Cómics.

## 1. Persistencia y acceso

- Crear una migración reversible y un modelo de lectura relacionado con
  `Space`. Guardar la estantería, categoría y título obligatorio. Guardar
  descripción, autoría, fecha de publicación y URL de portada cuando
  existan. Conservar la precisión que da el catálogo (año, mes o día)
  sin inventar fechas completas.
- Conservar proveedor, tipo de registro e ID externo como identidad de
  catálogo. Aplicar un índice único sobre esos tres campos y la estantería
  en SQLite y PostgreSQL. Los tres campos pueden quedar vacíos en el
  alta manual. Convertir una colisión concurrente en un error de alta.
- Sustituir el render directo de Lecturas por un controlador y mantener
  el componente Inertia, la ruta de entrada y el enlace de vuelta.
  Añadir las rutas de búsqueda, selección y guardado bajo el grupo
  autenticado y protegido por membresía de la estantería. Consultar
  y guardar a través de la relación de la estantería de la ruta.
- Validar título tras quitar espacios exteriores y categoría admitida.
  Si coincide el título con una ficha local, devolver un aviso que
  permita confirmar el alta. Si coincide la identidad de catálogo,
  devolver el bloqueo incluso si se confirmó un aviso de título.

## 2. Catálogos

- Seguir el patrón de servicios HTTP existente. Separar la consulta y
  normalización por proveedor y ofrecer a la página el mismo conjunto
  de campos de ficha. Validar la categoría y la selección en el servidor.
- Open Library busca obras individuales y conserva su ID de obra.
  MangaUpdates busca series y conserva el ID de serie. Comic Vine
  permite distinguir colecciones por año o editorial y seleccionar
  un número o recopilatorio con su ID de ficha individual.
- Configurar `COMIC_VINE_API_KEY` en el servidor y añadir solo su nombre
  a `.env.example`. Mantener la clave fuera de las respuestas y
  mensajes de error. Usar tiempos de espera acotados y distinguir
  respuestas vacías de errores externos o límites de uso.
- Usar la URL original de portada de Comic Vine. Mantener el alta
  manual disponible con proveedor caído o sin clave configurada.

## 3. Interfaz

- Mantener las tres pestañas y sus estados vacíos. Mostrar las fichas de
  la categoría activa y ofrecer «Añadir» desde esa pestaña.
- Separar búsqueda, resultados, formulario y portada en componentes
  de Lecturas según sus responsabilidades. Reutilizar controles
  compartidos existentes si encajan. Mostrar los datos de catálogo
  disponibles antes de guardar y conservar el borrador ante errores,
  incluido el aviso por título. Tras guardar, mostrar la ficha en su
  pestaña.
- Usar texto negro diagonal sobre fondo gris claro para portadas
  ausentes o fallidas. Comprobar foco visible, navegación por teclado,
  toque y mensajes asociados a sus campos.

## 4. Pruebas, documentación y despliegue

| Criterio | Verificación |
| --- | --- |
| CA-01 | Simular los tres proveedores. Buscar libro y serie de manga. Distinguir volúmenes de Batman por año y seleccionar el número 4. Buscar un Mortadelo en español y guardar un recopilatorio como una sola ficha. |
| CA-02 | Crear manualmente en las tres pestañas con proveedores indisponibles y consultar las fichas tras recargar. |
| CA-03 | Verificar proveedor, tipo de registro e ID guardados, y que el alta manual admite la ausencia de identidad externa. |
| CA-04 | Probar colisión de ID, aviso por título con continuación, restricción única, guardado del mismo ID en otra estantería y acceso ajeno o sin sesión. |
| CA-05 | Probar sin resultados, límites, fallo externo y error de guardado. Revisar conservación de datos, foco, teclado y toque. |
| CA-06 | Rechazar título vacío, conservar metadatos disponibles y comprobar portada original, imagen fallida y sustitución visual. |

Aplicar la migración en SQLite local y comprobar que figura como aplicada.
Ejecutar PHPUnit, el build de Vite y `git diff --check`. Cubrir la
compatibilidad con PostgreSQL en el flujo de pruebas del proyecto.
El despliegue usa la migración habitual y requiere configurar la clave
de Comic Vine en el entorno de ejecución para habilitar ese catálogo.
Registrar las evidencias y los límites en estos artefactos, y anotar las
horas para GitHub Projects al cerrar.
