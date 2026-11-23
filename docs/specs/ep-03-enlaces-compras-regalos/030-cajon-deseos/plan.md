---
spec: "030"
status: approved
---

# Plan técnico — 030

## Resumen

Extender el patrón actual de cajones con Deseos, manteniendo ocho huecos,
una posición por tipo y membresía verificada en servidor. Añadir únicamente
su representación y vista vacía; el alta permanece deshabilitada como se aprobó.
No crear modelos de artículos, listas, extracción ni servicios externos.

Este plan propone los contratos concretos siguientes para su aprobación.
No hay cambios de código realizados ni decisiones técnicas adicionales
necesarias fuera del patrón existente.

## Componentes afectados

- Nueva migración aditiva en `database/migrations/`, posterior a las existentes:
  posición `wish_slot` nullable en `spaces`, sin valores por defecto ni backfill.
- `app/Models/Space.php`: cast de posición, sin relación de artículos todavía.
- `app/Http/Controllers/SpaceController.php`: exponer posición en `activeSpace`,
  método de colocación y participación en exclusión mutua de todos los tipos.
- Nuevo `app/Http/Controllers/WishController.php`: únicamente `index` de la vista.
- `routes/web.php`: rutas en el grupo actual con autenticación y membresía.
- `resources/js/app.jsx`: resolver el componente lógico `Wishes/Wishes`.
- `resources/js/pages/Dashboard/Shelf/Shelf.jsx`: selector, posición y acceso.
- Nuevo `resources/js/pages/Dashboard/WishBox/WishBox.jsx` y CSS Module:
  representación, letrero y animación encapsulados, siguiendo ReadingSpines.
- Nuevo `resources/js/pages/Wishes/WishesView/WishesView.jsx`; CSS Module solo
  si necesita reglas propias no cubiertas por JournalLayout y estilos actuales.
- Recursos WebP nuevos en `resources/images/estanteria/deseos/`: caja y tarjeta
  animable como capas alineadas, sin texto incrustado en los letreros.
- Nuevo `tests/Feature/WishesDrawerTest.php` y ajuste de expectativas existentes
  que enumeren tipos/posiciones o resuelvan los componentes Inertia.
- `tests/TestCase.php`: añadir el mapping lógico `Wishes/Wishes` al archivo
  real de WishesView, conservando la comprobación de existencia del componente.

## Contratos, datos e interfaces

- `PUT /espacios/{space}/cajones/deseos`: `slot` entero entre 1 y 8. Usar
  `placeDrawer` y su actualización condicional; redirección a `spaces.show`.
- `GET /espacios/{space}/deseos`: `Wishes/Wishes`, props mínimas `spaceId` y
  `spaceSlug`, siempre del espacio autorizado. No endpoint de alta todavía.
- `wish_slot`: null significa sin colocar; conservar todas las columnas y
  posiciones actuales. Añadir restricciones equivalentes a las de Lecturas
  para impedir colisión de Deseos con los cuatro tipos en SQLite/PostgreSQL,
  tanto en inserción como al cambiar cualquiera de las cinco posiciones.
- En SQLite, triggers nuevos; en PostgreSQL, CHECK compatible con posiciones
  null y las restricciones previas. No sustituir ni debilitar controles previos.
  `down` elimina solo estos controles y la nueva columna; no se ejecuta sobre
  la base de desarrollo como parte de la verificación.
- Selector y movimientos de los cinco tipos usan la misma exclusión mutua.
  No ampliar `fillable` para permitir asignación arbitraria desde peticiones.
- `WishBox({ href })` es un único enlace: capas decorativas sin focos duplicados,
  nombre accesible Deseos, letrero renderizado con fuente y variables existentes.
- `WishesView` reutiliza JournalLayout con título Deseos y vuelta por slug.
  Mensaje «Todavía no hay deseos en esta estantería», botón nativo disabled
  «Añadir enlace» y aviso visible aprobado. Sin peticiones de alta ni rutas falsas.

## Fases de implementación

1. Revisar regresiones y preparar copia consistente de SQLite local fuera del
   repositorio. Comprobar conexión y ruta antes de cualquier migración.
2. Añadir posición y restricciones; extender modelo, colocación y payload.
   Incorporar controlador, rutas y contratos de acceso de solo lectura.
3. Crear recursos WebP coherentes con madera/luz existentes y componente de
   caja con tarjeta frontal de eslabones, apoyada sobre la balda. Animar solo
   la tarjeta mediante transform, ida y retorno; hover y focus-visible, sin
   movimiento para prefers-reduced-motion. Sin dependencias nuevas ni canvas.
4. Integrar selector y página vacía. Ajustar tamaños y límites para los ocho
   huecos, escritorio y móvil, sin modificar estilos de otros cajones.
   Seguir JSX con función default nombrada, carpetas de componente, CSS puro
   con variables globales, sin CSS vacíos ni composición no estándar.
5. Aplicar migrate en SQLite local conservando datos, comprobar migrate:status
   y comparar contenido anterior/nuevo. Ejecutar pruebas, build y revisión.
6. Registrar evidencias y validación manual en la spec/tareas. Cerrar solo
   después de pruebas y revisión final; no publicar sin autorización posterior.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Prueba HTTP de colocación en hueco libre, movimiento y liberación; verificar que sigue existiendo una posición por tipo. |
| CA-02 | Revisión visual en escritorio y móvil, varios huecos y tamaños, capas y etiqueta legibles, sin invasiones en hover/foco. |
| CA-03 | Ratón, toque, Enter, foco visible, retorno suave y movimiento reducido; comprobar que caja permanece fija y sin doble activación. |
| CA-04 | Inertia resuelve archivo real, props del espacio, estado vacío, disabled/aviso y vuelta por slug, sin endpoint de alta. |
| CA-05 | Persistencia tras nueva petición/recarga; slots fuera de rango y no enteros; ocupación por cada otro tipo en ambos sentidos; destino ocupado entre peticiones y distribución intacta. Restricciones directas en base aislada. |
| CA-06 | Invitado, miembro y no miembro; GET/PUT manipulados con otra estantería, sin lectura ni modificación ajena. |
| CA-07 | Prueba de migración sobre base aislada con datos previos: comparar valores y distribución, nueva posición null; copia/comparación de SQLite local y regresión de cuatro tipos. |

Controles derivados del proyecto: suite `vendor/bin/phpunit --display-skipped`
con PHP/pdo_sqlite y base de pruebas aislada, `npm run build`, `git diff --check`.
El CI `.github/workflows/ci-cd.yml` ejecuta además
`vendor/bin/phpunit -c phpunit.pgsql.xml` contra PostgreSQL 17 aislado y
comprueba construcción/arranque de la imagen. Nunca
ejecutar pruebas de migración sobre producción ni sobre SQLite de desarrollo.
No añadir tests que exijan `:memory:` cuando el CI use PostgreSQL aislado.

No hace falta abrir navegador para pruebas automáticas. La validación visual
queda para la comprobación local autorizada de Ángel; no atribuirla al agente
si no ha observado la interfaz. Mantener servidores locales activos.

## Documentación

Actualizar estos tres artefactos con resultados reales, controles, límites y
tiempo de cierre/GitHub Projects. No duplicar el informe de metadatos ni crear
otro documento de diseño. MEMORY solo si hay pendientes operativos reales.

## Despliegue y compatibilidad

Esta fase no despliega. La migración es aditiva, sin recrear tablas o vaciar
datos de aplicación. Conservar copia previa y verificar que los valores
anteriores siguen idénticos tras migrar localmente. PostgreSQL queda sujeto a
CI y a autorización posterior de despliegue. Antes de integrar, actualizar
la rama destino según el proceso; después, copiar solo la documentación
autorizada a documentation. Commit, push y merge requieren permiso aparte.

## Riesgos y medidas

- Colisión al mover otro tipo sobre Deseos: cubrir los cinco llamadores,
  actualización condicional y restricciones de base con pruebas bidireccionales.
- Recorte de tarjeta o apariencia flotante: capas alineadas y prueba visual
  por hueco/tamaño; caja fija y tarjeta dentro de límites durante animación.
- Regresión Inertia por reorganización: mantener mapping y comprobar archivo
  real en pruebas, sin desactivar la comprobación de existencia.
- Confundir acceso provisional con alta: disabled nativo y aviso persistente,
  sin handlers de guardado ni simulación de artículos.

Ángel aprueba el plan y expresamente `wish_slot`, restricciones
SQLite/PostgreSQL y rutas GET/PUT el 23 de noviembre de 2026; solicita después
implementar y migrar conservando datos. Resuelta la puerta de aprobación de la
revisión, sin objeciones técnicas adicionales. CA-01 a CA-07 cubiertos por
T-01 a T-07. No se autoriza publicación ni uso del navegador.
