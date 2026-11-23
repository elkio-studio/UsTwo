---
id: "032"
title: "Verificar la consulta de Deseos compartidos"
status: completed
---

# 032 — Verificar la consulta de Deseos compartidos

Historia de la [épica EP-03, issue #25](https://github.com/elkio-studio/UsTwo/issues/25).
Base funcional: [spec 031, alta manual](../031-alta-deseos/spec.md).
Contexto: [spec 030, cajón Deseos](../030-cajon-deseos/spec.md) e
[investigación de enlaces](../../../investigaciones/enlaces.md).

Inicio: 23 de noviembre de 2026, 19:36 CET.
Cierre local: 23 de noviembre de 2026, 19:58 CET.
Tiempo transcurrido estimado: 22 minutos, sin pausas comunicadas para esta spec.
**GitHub Projects: 0.5 h estimadas**. Redondeo del proyecto: 15 minutos
completos y 7 minutos restantes, al siguiente cuarto de hora.
Sprint de cierre previsto: Sprint 2. Capacidad orientativa: 4 h/día,
lunes a viernes, 20 h por sprint; estimación pendiente de validación técnica.

## Problema y objetivo

Como miembro de una estantería, revisar las referencias guardadas en sus
listas de Deseos y abrir sus destinos con seguridad, incluso sin vista previa
o cuando una tienda no responda.

La 031 ya implementó un listado mínimo para comprobar el alta. Esta historia
verifica expresamente sus criterios de consulta y documenta evidencias;
no presupone que deba reimplementarse la funcionalidad ni que pueda darse
por cerrada solo por existir el código.

## Decisiones y alcance confirmado

Ángel autoriza crear esta spec en docs/consulta-deseos desde dev, centrada en
verificar la consulta existente y sin implementar todavía.

- Conservar la organización actual por listas de la estantería y el listado
  de título, destino y descripción cuando exista.
- Conservar la decisión vigente de alta manual, sin extracción ni imágenes.
  «Sin vista previa» significa que el enlace sigue siendo usable sin imagen,
  tarjeta remota o metadatos obtenidos de la tienda; no añadir esas funciones.
- Mantener precio EUR o su ausencia, autor y estado pendiente que ya muestra
  la 031. Esta historia no añade gestión del precio ni del estado.
- Mantener la apertura existente en otra pestaña con protección de la ventana
  original y sin incrustar contenido externo.

## Alcance incluido

- Revisar la respuesta de consulta, presentación y navegación entre listas.
- Verificar título y destino, descripción opcional y legibilidad de datos largos.
- Verificar enlaces seguros y colección independiente del estado de las tiendas.
- Comprobar permisos y aislamiento entre estanterías y listas manipuladas.
- Comprobar persistencia tras recarga y uso en escritorio, móvil y teclado.
- Registrar la cobertura existente, sus lagunas y evidencias de validación.

## Alcance excluido

- Reimplementar el alta, añadir una ficha de detalle o rediseñar el cajón.
- Editar/borrar deseos o listas, categorías, búsqueda/filtros nuevos,
  seguimiento de precios, marcar comprado o visibilidad selectiva del estado.
- Recuperar vistas previas, comprobar automáticamente enlaces caídos,
  extraer metadatos, consultar tiendas o introducir APIs/dependencias.
- Cambiar el modelo de datos, migrar o modificar registros locales por
  preparar la spec. No crear datos de prueba en la base de la web todavía.
- Código, pruebas nuevas o modificadas, commits y publicación durante la
  preparación documental. Si aparece un defecto o falta cobertura, se documenta
  y su corrección se planifica y autoriza antes de ejecutarla.

## Actores y flujo principal

- Miembro: consulta únicamente las listas y deseos de su estantería.
- Usuario sin sesión o ajeno: no puede acceder a la colección.
- Ángel: valida la spec y aporta la comprobación manual cuando se realice.

1. El miembro abre Deseos desde su estantería.
2. La vista muestra las listas disponibles y cuál se está consultando.
3. Selecciona una lista y revisa los deseos guardados: título enlazado,
   destino visible y descripción solo si se rellenó.
4. Activa el título y abre el destino en otra pestaña. La colección no
   descarga ni ejecuta el contenido de la página de destino.
5. Si el destino falla o está caído, ese fallo ocurre fuera de UsTwo;
   la colección y sus otros enlaces siguen disponibles.
6. Recarga o vuelve a abrir la colección y encuentra los datos persistidos.
   Puede regresar a la estantería de origen mediante el enlace existente.

## Flujos alternativos, errores y límites

- Sin listas o con una lista vacía: conservar los mensajes existentes y el
  acceso de alta; no crear listas o deseos automáticamente para rellenar la vista.
- Descripción ausente: no impedir la apertura ni mostrar contenido inventado.
- Sin imagen o vista previa: título y URL bastan para usar el enlace.
- URL larga o descripción extensa: mantener legibilidad sin desbordamiento
  horizontal; el contenido se muestra como texto, sin ejecutar HTML.
- Lista ajena o inexistente en el parámetro de consulta: rechazo según el
  patrón actual, sin devolver nombres, registros ni otros datos de esa lista.
- Otra estantería en la ruta: validar sesión/membresía en servidor, sin
  confiar en la lista seleccionada o en ocultar enlaces en el frontend.
- Una tienda inaccesible o que bloquee peticiones no causa error de consulta:
  cargar el listado no hace DNS ni peticiones a esas URLs.
- No introducir un indicador automático «enlace caído», pues comprobarlo
  supondría nueva extracción y acceso externo fuera del alcance aprobado.

## Criterios de aceptación

- **CA-01.** La lista seleccionada muestra título y destino guardados y
  descripción cuando exista; conserva los datos actuales de EUR/autor sin
  introducir nuevos campos obligatorios o consultas externas.
- **CA-02.** El título abre la URL HTTP(S) guardada en otra pestaña con
  noopener/noreferrer. La página externa no recibe acceso a la ventana original
  y no se incrusta ni ejecuta dentro de la colección.
- **CA-03.** Un deseo sin descripción ni vista previa sigue mostrando título
  y URL utilizables; los estados vacíos son los actuales y no inventan datos.
- **CA-04.** Solo se devuelven listas y deseos de la estantería autorizada.
  Invitados, no miembros y peticiones con otra estantería o lista ajena no
  permiten consultar registros de otros espacios.
- **CA-05.** Consultar una colección con un destino inaccesible sigue
  funcionando sin consultar esa URL. No se incorporan comprobación de enlaces,
  seguimiento de precios o extracción.
- **CA-06.** La selección de lista y los datos guardados se recuperan mediante
  la navegación/recarga existente; la vuelta a la estantería apunta a su origen.
  El listado es utilizable en escritorio/móvil y con teclado/foco visible,
  incluidos enlaces largos, sin depender de hover o de una vista previa.

## Estado observado y evidencias por completar

Inspección de solo lectura, no validación final:

| Criterio | Base observada en la 031 | Evidencia a completar |
| --- | --- | --- |
| CA-01 | WishController::index entrega title, url y description; WishList los muestra. | Comprobar datos completos/opcionales y representación real. |
| CA-02 | WishList usa ancla con target blank y rel noopener noreferrer; WishUrl valida HTTP(S) al guardar. | Verificar activación con ratón/teclado y atributos del enlace, sin ejecutar contenido incrustado. |
| CA-03 | Descripción condicional, sin imágenes; WishesView y WishList tienen estado vacío. | Revisar enlace sin descripción/vista previa y lista vacía. |
| CA-04 | Consulta a través de Space::wishLists y middleware de membresía; query list validada contra las listas propias. | Revisar cobertura de sesión, miembro, ajeno y manipulación de lista/ruta sin fugas de datos. |
| CA-05 | El listado no contiene transporte de extracción; WishesTest ya bloquea peticiones externas en flujos de consulta. | Evidencia explícita con un destino inaccesible y sin peticiones a la tienda; no es necesario visitar ese destino. |
| CA-06 | Listas con enlace ?list=ID y aria-current, destino de vuelta por spaceSlug y CSS overflow-wrap. | Comprobar recarga y navegación, móvil, teclado/foco y contenido largo. |

La existencia de estos archivos no demuestra por sí sola todos los criterios.
No se trasladan automáticamente las pruebas manuales de alta de la 031 a la
consulta de esta historia. No se ejecutan pruebas ni se afirman resultados
nuevos durante la preparación de esta spec.

## Validación prevista y decisiones pendientes

Tras aprobación, preparar un plan de verificación proporcional, reutilizando
pruebas/evidencias válidas. Identificar lagunas concretas antes de proponer
tests adicionales o cambios de código. Las pruebas de datos/permisos se harán
en bases aisladas; no borrar ni recrear SQLite local ni tocar Supabase.

La comprobación de apertura segura puede realizarse con un destino controlado;
para demostrar independencia de un destino caído basta impedir peticiones
externas y verificar el listado, sin sondear páginas reales. El agente no
abrirá el navegador sin autorización; la validación manual puede aportarla Ángel.

Sin decisiones funcionales nuevas pendientes: se verifica el comportamiento
actual aprobado. Si se detecta una necesidad que cambie ese comportamiento,
se eleva antes de ampliar el alcance. Ángel aprueba expresamente la spec
el 23 de noviembre de 2026 y solicita preparar el plan de verificación.
Sin implementación, commit ni publicación.

Revisión de especificación: **PASS**, sin bloqueos para presentarla a Ángel.
Enlaces relativos y comprobación de whitespace correctos. La aprobación de
la spec fue concedida por Ángel; el plan y la verificación posterior siguen
pendientes de sus fases. No se cierra la historia.

Plan de verificación y tareas preparados; revisión del plan PASS. Ángel
aprueba el plan y solicita ejecutarlo con SQLite en memoria, sin modificar
código, abrir el navegador ni publicar cambios.

## Ejecución del plan y evidencias

Verificación realizada en docs/consulta-deseos, base 0986c35. No se han
modificado aplicación, pruebas persistentes, configuración ni bases de la web.

### Aislamiento previo

Antes de ejecutar, se leyó phpunit.xml y se comprobó la configuración efectiva
tras bootstrap: entorno testing, database.default sqlite, database :memory:,
URL de conexión vacía/null y sin caché de configuración. No se abrió conexión
de escritura para comprobar esos valores. APP_ENV se fijó solo en el proceso
de terminal; no se editó .env ni se cambió el entorno del servidor local.

El archivo temporal de consulta incluyó su propia guardia en
beforeRefreshingDatabase(), antes de migrar, transaccionar o crear fixtures.
RefreshDatabase solo actuó sobre SQLite en memoria; no se ejecutó migrate
contra el archivo local ni se accedió a Supabase.

### Pruebas realizadas

- PHP 8.5.12 con pdo_sqlite:
  `vendor/bin/phpunit --filter 'WishesTest|WishesDrawerTest' --display-skipped`:
  **12 pruebas, 267 aserciones, cero fallos y cero omisiones**.
- Comprobación efímera fuera del repositorio:
  `vendor/bin/phpunit -c phpunit.xml %TEMP%/ustwo-spec032/ConsultWishesProbeTest.php --display-skipped`:
  **3 pruebas, 133 aserciones, cero fallos y cero omisiones**.
- En total: **15 pruebas y 400 aserciones** de esta ejecución, con SQLite
  en memoria. No se atribuyen nuevas pruebas PostgreSQL o CI a esta spec.

El test temporal creó únicamente fixtures en memoria: dos estanterías con
listas/deseos diferentes, propietario y miembro con acceso a ambas, usuario
ajeno, lista vacía, registro completo, registro sin descripción/precio y
destino .invalid. Comprobó también una estantería sin listas, selección y
peticiones repetidas como recarga. Las URLs de prueba no se visitaron.

| Criterio | Evidencia actual y límites |
| --- | --- |
| CA-01 | Test efímero afirma título, URL exacta con query, descripción multilínea, EUR/precio, autor y pendiente en el payload real. Ángel confirma la consulta en escritorio, móvil y teclado. |
| CA-02 | Inspección de WishList.jsx: enlace con target blank y rel noopener noreferrer; texto JSX y ausencia de iframe/HTML remoto. WishUrl valida HTTP(S) al guardar. Ángel confirma la apertura en otra pestaña. |
| CA-03 | Prueba efímera de descripción/precio null, ausencia de image, lista vacía y espacio sin listas; revisión de los condicionales del frontend y consulta confirmada por Ángel. |
| CA-04 | Miembro con acceso a A/B recibe solo listas y deseos de A; consulta B por separado no incluye A. Invitado, ajeno, retirada de membresía, lista extranjera/inexistente/no numérica y query de tipo array rechazados sin nombres/títulos/descripciones ajenos. |
| CA-05 | Destino .invalid incluido en listado HTTP 200 junto a otros deseos; Http::preventStrayRequests y assertNothingSent. Inspección del camino index y frontend sin otros transportes ni consultas a tiendas. |
| CA-06 | Peticiones repetidas y selección de lista mantienen selectedListId y datos. Inspección de vuelta por spaceSlug, aria-current, overflow-wrap/white-space y foco global. Ángel confirma consulta en escritorio/móvil/teclado y recarga. |

### Integridad y límites

`git diff --exit-code HEAD -- app resources routes database tests config bootstrap composer.json composer.lock package.json package-lock.json`:
sin cambios. El único trabajo en curso está en los artefactos documentales de
la 032. `git diff --check` correcto. No se repiten build, suite completa ni
Node del alta porque no ha cambiado el código y no hay hallazgo que lo justifique.

Ángel confirma la consulta de Deseos en escritorio, móvil y teclado, la apertura
en otra pestaña y la recarga, y solicita registrar las pruebas y cerrar.
La confirmación corresponde expresamente a esta historia, no a la prueba
anterior de alta. El agente no abrió navegador ni visitó destinos externos.

Auditoría final de solo lectura: no se identifican defectos adicionales ni
cambios de aplicación. Controles automáticos correctos; el bloqueo anterior
era únicamente por T-05 y las evidencias manuales de CA-01–CA-03 y CA-06.
Se aporta la confirmación de Ángel a la revisión final de cierre, sin atribuir
al agente haber observado esas interacciones.

Tras la auditoría se retiraron únicamente ConsultWishesProbeTest.php y
preflight.php del directorio temporal. No se eliminaron carpetas ni bases
de datos. Los resultados y casos ejecutados quedan registrados aquí.

## Cierre local

Revisión final de solo lectura: **PASS**, sin bloqueos para el cierre local
tras la confirmación específica de Ángel. T-01 a T-06 completadas.
15 pruebas/400 aserciones correctas y cobertura documental/manual de todos
los criterios. Sin cambios de código, pruebas persistentes o datos de la web.

No se repiten pruebas por este cierre documental. Quedan pendientes de
autorización el commit, publicación, integración en dev y traslado a
documentation actualizada. No se ha abierto el navegador ni subido cambios.
