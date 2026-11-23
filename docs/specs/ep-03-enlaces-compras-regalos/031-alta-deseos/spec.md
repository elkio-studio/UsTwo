---
id: "031"
title: "Registrar deseos manualmente"
status: completed
---

# 031 — Registrar deseos manualmente

Historia de la [épica EP-03, issue #25](https://github.com/elkio-studio/UsTwo/issues/25).
Referencias: [investigación 029](../029-investigacion-metadatos-enlaces/spec.md),
[informe de enlaces](../../../investigaciones/enlaces.md) y
[cajón Deseos 030](../030-cajon-deseos/spec.md).

Inicio: 23 de noviembre de 2026, 15:02 CET.
Cierre local: 23 de noviembre de 2026, 19:23 CET.
Tiempo transcurrido: 4 h 21 min. Pausa comunicada por Ángel: 2 horas.
Tiempo efectivo tras descontarla: 2 h 21 min.
**GitHub Projects: 2.5 h**, según el ajuste comunicado por Ángel.
Redondeo del proyecto: 2 h 15 min completos y 6 min restantes, al siguiente
cuarto de hora. Se mantienen las horas de inicio y cierre.
Sprint de cierre previsto: Sprint 2. Capacidad orientativa: 4 h/día,
lunes a viernes, 20 h por sprint. Estimación pendiente de validación técnica.

## Problema y objetivo

Registrar referencias para compartir en Deseos, siempre mediante alta manual.
Guardar URL y título dentro de una lista de la estantería actual, con
descripción y precio en euros opcionales, sin depender de la tienda.

## Decisiones aprobadas y revisión de alcance

Ángel aprobó la spec y el plan iniciales con autocompletado. Posteriormente,
el 23 de noviembre de 2026, decidió usar **solo euros** y confirmó retirar
el autocompletado para dejar **solo alta manual**. Esta revisión prevalece
sobre la propuesta anterior. No se afirma que ninguna página pueda leerse:
Tumerchan respondió 429, PcComponentes 403 y una página de demostración
permitió recuperar título y descripción. La escasa cobertura práctica de las
tiendas motivó la decisión; no se intentó eludir sus bloqueos.

Reglas vigentes:

- Lista obligatoria creada por el usuario, sin crear ninguna por defecto.
- Cualquier miembro puede crear listas y añadir deseos a sus listas compartidas.
- URL y título obligatorios; descripción y precio opcionales.
- Precio exclusivamente en euros, sin selector de moneda. EUR se asigna en
  servidor cuando hay importe; sin importe, precio y moneda quedan ausentes.
- Autor de sesión y estado inicial pendiente, asignados por el servidor.
- No se consulta, analiza ni descarga el contenido de ninguna URL.

## Alcance incluido y excluido

Incluye crear/elegir listas, activar Añadir enlace, formulario manual,
validación, persistencia, autoría y representación mínima en la lista elegida.

Excluye extracción/autocompletado y su endpoint, APIs, peticiones externas,
imágenes, iframes, conversión/seguimiento de precios, categorías (historia
03.6), edición/borrado, marcar comprado, tachado y visibilidad selectiva.
No se rediseña el cajón ni se añaden permisos por lista.

## Actores y permisos

Solo miembros de la estantería acceden a listas y altas. La lista debe pertenecer
a esa estantería; autor, estado y espacio no se aceptan del cliente para
suplantar datos. Usuarios ajenos o sin sesión no pueden consultar ni guardar.
La pertenencia se revalida al escribir conforme al patrón existente.

## Flujo principal

1. Abrir Deseos de la estantería y pulsar Añadir enlace.
2. Elegir una lista existente o crear una con nombre obligatorio. Sin listas,
   se explica que debe crear la primera. No hay creación automática.
3. Crear la lista es una acción independiente: no borra el deseo en preparación,
   no anida formularios y permite seleccionar la lista nueva. Puede quedar vacía.
4. Rellenar URL, título y, opcionalmente, descripción y precio en euros.
5. Guardar valida todo y persiste un deseo completo con su lista y autor real.
6. Volver a Deseos con la lista de destino identificada/seleccionada; mostrar
   título/enlace, descripción si existe, precio en EUR o ausencia y autor.
   Salir y recargar conserva los datos. No hay ficha de detalle ni CRUD todavía.

## Datos, errores y seguridad

| Dato | Regla |
| --- | --- |
| Lista | Obligatoria y propia de la estantería; nombre trim de 1–255 caracteres. |
| URL | Absoluta HTTP(S), host válido, sin credenciales, controles, barras inversas ni host ambiguo; máximo 2048. Solo se valida sintaxis, sin DNS ni conexión. |
| Título | Trim, obligatorio, 1–255 caracteres; se muestra como texto escapado. |
| Descripción | Opcional, hasta 5000 caracteres; texto escapado, sin ejecutar o incrustar HTML. |
| Precio | Opcional, entre 0 y 99999999,99, hasta dos decimales; coma o punto sin miles/exponentes. Céntimos exactos, sin float. |
| Moneda | Interna: EUR con precio, null sin precio. Una moneda distinta enviada por el cliente se rechaza. Sin conversión automática. |
| Autor y estado | Asignados en servidor al usuario real y pending. |

URL inválida, título/lista ausentes o importe inválido muestran errores por
campo, conservan el formulario y no dejan escrituras parciales. Un fallo de
almacenamiento también conserva el input; una lista creada antes puede seguir
vacía. Precio ausente no significa cero. El precio es orientativo y no se
actualiza. Cancelar crear lista no limpia el deseo; fallar la petición de lista
conserva el nombre y muestra un mensaje comprensible.

No se bloquean duplicados ni se alteran queries que identifiquen el producto.
Guardar funciona con una tienda caída o que bloquee robots: no se contacta.
Enlaces seguros y texto escapado; si se abre otra pestaña, noopener/noreferrer.
No hay consultas externas al guardar o listar, ni endpoint que actúe como proxy.

## Interfaz

Conservar JournalLayout, tipografía, tema cálido y Field existentes. Página de
alta con vuelta a Deseos, selector/creación de lista, URL, título, descripción,
Precio en euros (opcional) y Guardar. Sin selector de moneda, botón de obtener
datos o mensajes de extracción. Listado sencillo sin imágenes ni acciones CRUD.

Etiquetas visibles, opcionales identificados, foco predecible y errores accesibles.
La creación de lista no pierde datos del formulario padre. Evitar desbordamientos
con enlaces largos y mantener uso de móvil, toque y teclado, sin nuevas animaciones.

## Criterios de aceptación

- **CA-01.** Crear/elegir lista y guardar URL/título obligatorios; descripción
  y precio opcionales. Sin listas por defecto ni deseo sin lista.
- **CA-02.** Conservar lista, estantería, autor y pendiente; devolver a lista
  elegida y mantener registros tras salir y recargar.
- **CA-03.** Errores de URL/título/lista/precio o moneda manipulada conservan
  input sin escrituras parciales; creación de lista fallida conserva su nombre.
- **CA-04.** Guardar y consultar funcionan sin DNS ni peticiones externas,
  independientemente de la disponibilidad de la tienda.
- **CA-05.** No existe botón ni endpoint de autocompletado, parser, transporte
  o hook de extracción sin uso en la implementación de esta historia.
- **CA-06.** Solo miembros operan en su estantería; lista/autor/estado/space
  manipulados no permiten filtrar datos ni escribir en otra estantería.
- **CA-07.** Rechazar esquemas inseguros y URLs ambiguas; no ejecutar scripts,
  descargar imágenes ni incrustar páginas. Mostrar texto y enlaces seguros.
- **CA-08.** Solo EUR, sin selector ni conversión; exactitud de céntimos,
  precio null distinto de cero y restricciones equivalentes en la base.
- **CA-09.** Alta, listas, errores y regreso utilizables en escritorio/móvil
  y teclado, con foco visible y datos conservados.
- **CA-10.** Conservar datos/cajones existentes y deseos de prueba; sin
  categorías, CRUD, compra, visibilidad selectiva ni imágenes.

## Implementación y evidencias locales

Implementada en feat/alta-deseos: Wish/WishList, controladores de alta/listas,
regla WishUrl sin red, WishPrice en céntimos, formulario modular y listado.
Eliminados servicio/controlador/parser de extracción, endpoint y hook sin uso.
Utilidad JSON de listas y su test Node se conservan porque siguen utilizándose.

Migración inicial 013 aplicada en SQLite, lote 6, con copia consistente:
`%TEMP%/ustwo-spec031/backups/before-spec031-20261123-152306.sqlite`.
Comparación anterior idéntica y tablas nuevas inicialmente vacías.

Tras las pruebas de Ángel había una lista y un deseo EUR. Se conserva la
migración ya aplicada y se añade la 014 para restringir nuevas escrituras a
EUR. Detiene su ejecución si encuentra importes en otra moneda, sin convertir,
borrar ni renombrar valores. Copia consistente previa:
`%TEMP%/ustwo-spec031-eur/backups/before-spec031-20261123-191410.sqlite`.
Aplicada en lote 7 y confirmada por migrate:status; integridad correcta.
Todas las filas/columnas anteriores coinciden exactamente con la copia,
excepto la nueva entrada esperada del registro de migraciones. Se conservan
2 usuarios, 2 estanterías, 3 membresías, 1 película, 1 serie, 9 lecturas,
3 valoraciones de lectura, 1 lista y 1 deseo, y posiciones de cajones.

Conexión comprobada exclusivamente como SQLite local. DB_URL anulada solo
para migrar; .env intacto y sin acceso a Supabase. No se recreó ninguna base.

Controles actuales: suite SQLite 81 pruebas, 2280 aserciones, cero fallos;
dos omitidas porque requieren PostgreSQL aislado. Test Node de peticiones JSON
correcto. Build correcto, 692 módulos; Prettier, enlaces relativos y
git diff --check correctos. Web 8000 y Vite 5173 responden HTTP 200.
No se afirma haber observado la interfaz: no se abrió el navegador.

Ángel confirma haber comprobado el alta manual final en euros en escritorio,
móvil y teclado, y solicita registrar las pruebas y cerrar la spec.
La comprobación se atribuye a Ángel y completa la evidencia manual pendiente
de T-10/CA-09. El agente no ha abierto el navegador. Sin commit, publicación
ni integración.

Auditoría final de solo lectura: sin defectos adicionales identificados en
el alcance manual/EUR. Controles automáticos correctos; T-09 completada.
El bloqueo anterior de cierre correspondía únicamente a T-10 y la comprobación
manual de CA-09. Se incorpora la confirmación de Ángel a la revisión final.

## Cierre local

Revisión final de solo lectura: **PASS**, sin bloqueos para el cierre local
tras la confirmación manual de Ángel. T-01 a T-10 completadas. Se conservan
las evidencias automáticas anteriores, sin cambios de código ni repetición
innecesaria de pruebas por esta actualización documental.

Pendientes externos: autorización de commit/publicación, integración en dev,
comprobación de CI/PostgreSQL y traslado autorizado a documentation.
No se consideran realizados al cerrar la spec en local. No se ha abierto
el navegador ni subido cambios.
