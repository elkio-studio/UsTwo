# Revisión de código sin uso y restos de funcionalidades

Fecha: 25/11/2026. Rama: `feat/arrastrar-cajones`, base `0923c66` y cambios
locales de la spec 036. Revisión solicitada por Ángel; no autoriza eliminar
en bloque lo encontrado ni publicar cambios.

En esta copia de documentation, los enlaces al código apuntan al commit
integrado 23a3655, porque esta rama contiene únicamente docs.

## Alcance y método

Se revisó el código propio de frontend y backend, sus entradas, contratos,
configuración, migraciones, pruebas, assets y despliegue. Se excluyeron código
de terceros (`vendor`, `node_modules`), bundles generados de `public/build`,
cachés y registros. No se leyó ni modificó `.env` ni se consultaron datos reales.

Inventario contrastado:

| Área | Archivos/resultado |
| --- | --- |
| Frontend | 63 JSX y 5 JS, incluidos hook y utilidades. |
| Estilos | 49 CSS propios: 48 módulos y global.css. |
| Backend | 35 PHP en app: controladores, modelos/trait, middleware, servicios, reglas, soporte, seeders y provider. |
| Configuración | 11 PHP de config, bootstrap/providers, entradas HTTP y consola, vista Blade y configuración Vite/Composer/npm. |
| Datos | 17 migraciones; no ejecutadas ni eliminadas por esta revisión. |
| Pruebas y operaciones | 14 PHP de pruebas, 2 archivos Node, 3 scripts deploy, workflow CI/CD y Dockerfile. |
| Recursos | 11 imágenes en resources/images, todas con referencia textual; entradas estáticas de public y .htaccess. |

Método: búsqueda de referencias y consumidores con `rg`, grafo estático de
imports relativos JS/JSX desde `app.jsx`, resolución de las acciones de rutas
contra controladores y herencia, cruce de CSS Modules y clases usadas, búsqueda
de declaraciones/consumos de variables CSS, revisión manual de ramas y de
entradas indirectas Laravel/Artisan. Revisión de UI por subagente de diseño de
solo lectura, sin navegador ni modificaciones.

El grafo y las búsquedas son heurísticas estáticas, no un analizador formal de
flujo ni cobertura de ejecución. Una rama sin consumidor actual se distingue
de una ruta accesible, una protección de errores y una capacidad intencional.
No se garantiza ausencia absoluta de código muerto por compilar o por contar
referencias. No se han probado accesos de clientes externos ni se ha ejecutado
una revisión visual en navegador.

## Hallazgos confirmados de ausencia de consumo o efecto

### F-01 — Modo readOnly del formulario de valoración sin consumidores

Archivo: [CollectionRatingForm.jsx](https://github.com/elkio-studio/UsTwo/blob/23a3655/resources/js/components/CollectionRatingForm/CollectionRatingForm.jsx),
líneas 6, 18, 27, 35 y 42 en la revisión.

`readOnly = false` condiciona envío, campos y botón. Los únicos consumidores,
[ReadingDetail](https://github.com/elkio-studio/UsTwo/blob/23a3655/resources/js/pages/Readings/ReadingDetail/ReadingDetail.jsx)
y [CollectionEntryDetail](https://github.com/elkio-studio/UsTwo/blob/23a3655/resources/js/pages/MovieDetail/CollectionEntryDetail/CollectionEntryDetail.jsx),
no pasan esa prop. El modo true no se activa desde la aplicación actual.

Acción sugerida: retirar prop/condicionales de ese modo si no se desea conservar
esa capacidad. Mantener el bloqueo real `processing`. No es prueba de que una
valoración actual esté deshabilitada. **No modificado en este repaso.**

### F-02 — Variante pública de JournalLayout sin consumidores

Archivo: [JournalLayout.jsx](https://github.com/elkio-studio/UsTwo/blob/23a3655/resources/js/components/Journal/JournalLayout/JournalLayout.jsx),
líneas 14, 21, 44, 47 y 62.

Ningún consumidor pasa `privatePage`; siempre vale true. La variante que
enlaza a `/acceso` y omite menús/invitaciones no se usa. El acceso tiene su
composición propia. El layout privado sí se utiliza ampliamente.

Acción sugerida: simplificar únicamente la variante pública si se confirma
que no se quiere reutilizar. **No eliminado el layout ni alterada autenticación.**

### F-03 — Variable title extraída y no utilizada

Archivo: [ReadingsView.jsx](https://github.com/elkio-studio/UsTwo/blob/23a3655/resources/js/pages/Readings/ReadingsView/ReadingsView.jsx),
línea 67: segundo `tabs.map(({ category, title, empty }, index) => ...)`.

Dentro de ese callback se usan category/empty/index, pero no title. El primer
map sí necesita title para las pestañas. Basta retirar la segunda extracción;
no borrar los títulos de tabs. **Pendiente de limpieza, sin cambio funcional.**

### F-04 — Siete tokens de color sin consumidores

Archivo: [global.css](https://github.com/elkio-studio/UsTwo/blob/23a3655/resources/css/global.css), líneas 57–66.

Las siguientes propiedades solo aparecen en su declaración: no hay consumo
`var(...)` ni acceso JS por nombre en los recursos propios revisados:

- `--palette-drawer-grain-light`
- `--palette-drawer-wood`
- `--palette-wood-shadow`
- `--palette-drawer-handle-light`
- `--palette-wood-handle-shadow`
- `--palette-wood-light`
- `--palette-drawer-ink`

Son candidatos a restos del dibujo anterior de cajones; la ausencia de consumo
es comprobable, pero el origen histórico no se demuestra solo por el nombre.
Retirarlos no equivale a retirar otros tokens de nombre parecido usados por
libros, caja, controles o escena. **No eliminados en esta auditoría.**

### B-01 — Método register vacío del provider

Archivo: [AppServiceProvider.php](https://github.com/elkio-studio/UsTwo/blob/23a3655/app/Providers/AppServiceProvider.php), línea 17.

El override register no contiene instrucciones, solo un comentario. No aporta
comportamiento frente al método base vacío. Puede retirarse como limpieza menor.
El provider y su boot sí están registrados: prohíben operaciones destructivas
de base de datos en producción. **No borrar el provider ni esa protección.**

## Contratos antiguos o mantenimiento: revisar antes de retirar

### B-02 — PUT /espacios/{space}/activo sin consumidor en la UI actual

Referencias: [routes/web.php](https://github.com/elkio-studio/UsTwo/blob/23a3655/routes/web.php), línea 34, y
[SpaceController::select](https://github.com/elkio-studio/UsTwo/blob/23a3655/app/Http/Controllers/SpaceController.php), línea 60.

El selector actual navega mediante Link a `/{slug}`; show ya guarda active_space.
No se encontró una petición frontend a `/activo`. La ruta sigue registrada y
`SharedSpacesTest` la invoca: no es una función inalcanzable ni se puede afirmar
que ningún cliente la use. Es un contrato anterior que requiere decidir si
mantener compatibilidad o retirar ruta/método/pruebas de forma coordinada.
**Conservado.**

### B-03 — Completar metadatos por consola sigue activo, pero necesita revisión

Archivo: [routes/console.php](https://github.com/elkio-studio/UsTwo/blob/23a3655/routes/console.php), comando
`ustwo:movie-metadata`, líneas 10–40. Documentado en README.

Es invocable desde Artisan y usa TMDB; no es código muerto. Escribe poster_path
y overview vacíos directamente, sin actualizar catalog_fields/metadata_version
incorporados después por la spec 028. Si un campo no estaba marcado como
importado, completarlo así puede dejarlo editable y no reflejar la escritura
en la versión usada para detectar conflictos.

Acción sugerida: revisar este escritor antiguo contra las reglas actuales antes
de utilizarlo, o decidir su retirada expresa. Es una revisión funcional, **no
una eliminación automática justificada por ausencia de uso frontend**.
No ejecutado ni modificado, no se consultó TMDB durante este repaso.

## La excepción de hueco ocupado no es código muerto

[SpaceController::placeDrawer](https://github.com/elkio-studio/UsTwo/blob/23a3655/app/Http/Controllers/SpaceController.php),
líneas 116–131, es el contrato de colocación usado por el diálogo + y por las
cinco rutas de tipo. La UI filtra tipos ya colocados; el cliente envía solo slot.

[moveDrawer](https://github.com/elkio-studio/UsTwo/blob/23a3655/app/Http/Controllers/SpaceController.php), desde línea 133,
es el contrato de arrastre/intercambio, que recibe tipo y snapshot completo.
**Este segundo contrato sí intercambia destinos ocupados.**

La excepción puede ocurrir si otro miembro ocupa el hueco mientras el diálogo
está abierto o si se envía una petición manipulada al endpoint de alta. Quitar
la guardia no convierte el alta en intercambio y permitiría intentar una
colisión, que las restricciones de base de datos también rechazan. Se conserva.
`DrawerMovementTest::test_existing_placement_contract_still_rejects_occupied_slots`
acredita que la rama sigue siendo alcanzable, no una condición imposible.

## Comprobaciones sin restos confirmados y falsos positivos descartados

- Los **68 módulos JS/JSX son alcanzables** desde app.jsx; no hay módulos
  huérfanos ni imports relativos sin resolver en el grafo revisado.
- Las **66 referencias explícitas controlador/acción en rutas** resuelven,
  incluidas las acciones heredadas de CollectionController. No se encontraron
  clases de servicio/controlador propias completas que se puedan borrar por
  estar desconectadas de las entradas actuales.
- Los **48 CSS Modules están importados**; no se encontraron clases locales
  declaradas sin consumo en sus componentes. `.leaflet-top` es una clase global
  que genera Leaflet, no una clase local perdida.
- Las **11 imágenes** de resources/images tienen referencias. Lomos modernos y
  paletas sepia son variantes conservadas expresamente para comparar, no restos.
- `initializeHasCatalogMetadata` lo llama Eloquent por convención, middleware
  handle y provider boot los llama Laravel. No necesitan una llamada textual
  directa del código de aplicación para estar vivos.
- `LocalUsersSeeder` tiene entrada CLI documentada. Los seeders de producción,
  migraciones y scripts de despliegue también tienen entradas indirectas.
  No eliminar migraciones aplicadas por carecer de imports desde PHP.
- Rutas legacy de Películas conservan enlaces antiguos y autorización: sin
  enlace actual en JSX no equivale a ruta muerta. Mantener hasta decidir su retiro.
- Notas/errores/estados ocultos y pestañas no activas sí tienen consumidores.
  La región role=status del movimiento anuncia resultados y se conserva.
- `WishRow` presenta Pendiente; el estado de compra pertenece a otra historia.
  No se encontró un controlador de extracción de URLs abandonado: el alta
  actual es manual y WishUrl valida seguridad de la URL.
- Configuración de mail/queue/filesystems y archivos de arranque pueden ser
  consumidos por el framework o herramientas. No se clasifican como muertos
  por no tener llamadas desde un controlador.
- La telemetría de ReadingCatalog se conserva por decisión explícita, aunque
  Open Library esté funcionando; registra fallos reales y no es un resto a borrar.
- `public/favicon.ico` no está enlazado en la vista actual, que usa favicon.svg;
  no se declara eliminable porque clientes pueden solicitar la ruta convencional.

## Correcciones expresamente solicitadas en este repaso

- Eliminados los dos `!important` propios: ReadingSpines y WishBox. La pausa
  usa especificidad equivalente a hover/foco y aparece después en la cascada.
  Se conservan los estados normales y movimiento reducido.
- Prohibición de `!important` documentada en AGENTS y convenciones frontend.
- Hook movido de Shelf a `resources/js/hooks/useShelfMovement.js`; sus cálculos
  puros siguen en utils. Imports actualizados; no se ha copiado otra implementación.
- Retirado el párrafo shelf-movement-help y su aria-describedby, por petición
  explícita. No se ha confundido con código inalcanzable: antes tenía consumidor
  accesible. Se mantienen anuncios dinámicos y errores.

El resto de hallazgos se entrega para decidir la limpieza. No se han borrado
rutas, capacidades, assets, clases ni tokens adicionales como efecto secundario.

## Verificación y límites

- `node --test tests/Frontend/shelfMovement.test.js`: 3 pruebas correctas.
- `npm.cmd run build`: correcto, 705 módulos; nuevo import del hook resuelto.
- Prettier de los archivos modificados y `git diff --check`: correctos.
- Búsqueda en CSS propio: cero apariciones de `!important`.
- Enlaces locales de este informe: cero destinos inexistentes.
- Hook anterior: no existe; no hay dos implementaciones.
- Revisión posterior de diseño: APTO por lectura. Especificidad de los dos
  selectores finales 0,3,0, igual que hover/foco y con orden posterior.
- Auditoría final test_reviewer: PASS. Contrasta inventario, grafo, hallazgos,
  imports, CSS y contratos conservados; sin bloqueos ni modificaciones.

No se repitió la suite PHP para cambios solo de CSS/imports/documentación:
no se ha modificado backend desde el resultado previo de la spec 036,
96 pruebas/2736 aserciones sin fallos y dos omisiones por PostgreSQL aislado.
No equivale a probar de nuevo esos escenarios ni a acreditar los cambios
funcionales de limpieza que todavía no se han ejecutado.

Sin navegador, consultas externas, commits ni publicación. No se ha ejecutado
el comando antiguo de metadatos ni modificado registros para esta auditoría.
