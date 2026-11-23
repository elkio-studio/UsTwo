---
spec: "032"
status: approved
---

# Plan de verificación — 032

## Resumen

Verificar y documentar la consulta de Deseos implementada en la 031, sin
reimplementarla. El resultado será una matriz de evidencias y validación
manual, no una funcionalidad nueva. Ángel aprueba el plan y solicita ejecutarlo
con SQLite en memoria, sin modificar código, abrir navegador ni publicar.
No se modifica código, pruebas del repositorio o configuración.

## Componentes que se revisan, sin modificarlos

- WishController::index, Space::wishLists y relaciones WishList/Wish.
- Rutas de consulta y middleware RequireSpaceMembership.
- WishesView/WishList y sus CSS Modules, JournalLayout y estilos globales.
- Mapping Wishes/Wishes en resources/js/app.jsx y tests/TestCase.php.
- Pruebas existentes WishesTest y WishesDrawerTest y evidencias de la 031.

## Contratos y datos existentes

- GET `/espacios/{space}/deseos`, protegido por sesión y membresía.
- `?list=ID` pertenece a las listas de ese espacio; lista ajena o inválida
  no revela información. Sin parámetro se sigue la selección actual.
- Props de consulta: spaceId, spaceSlug, selectedListId y listas con deseos.
  Estos incluyen título, URL, descripción opcional, EUR/precio o ausencia,
  autor y pendiente. No se añaden campos, rutas ni reglas de negocio.
- El enlace externo abre otra pestaña con noopener/noreferrer. No descarga
  contenido remoto dentro de la colección ni obtiene vistas previas.

No se cambian datos ni esquema. La base SQLite de la web y Supabase quedan
intactas; no hacen falta migraciones, seeds o reinicios.

## Fases de verificación

1. Confirmar rama/tarea, spec aprobada, base integrada y ausencia de cambios
   de aplicación. Revisar los componentes y vincular cada criterio a evidencia
   existente o pendiente, sin presentar inspección como prueba de ejecución.
2. Comprobar antes la configuración efectiva de testing: conexión sqlite,
   database :memory: y DB_URL/config URL vacía, con los valores forzados del
   phpunit.xml actual. Si existe caché de configuración o no puede confirmarse
   el aislamiento, detener sin limpiar cachés ni ejecutar tests/migraciones.
   Ejecutar las pruebas existentes de Deseos con PHP/pdo_sqlite y SQLite en
   memoria. Reutilizar sus evidencias de datos, recarga, aislamiento y acceso;
   no volver a probar migraciones de la base local.
3. Completar huecos de consulta con comprobaciones puntuales **efímeras**:
   un archivo de prueba PHPUnit fuera del repositorio, usando Tests/TestCase,
   RefreshDatabase y phpunit.xml. Solo tras autorizar ejecutar este plan.
   Ningún test del repositorio se modifica ni se instala un runner.
4. Revisar semántica y estilos del frontend, y pedir a Ángel la comprobación
   real del listado en escritorio/móvil/teclado. El agente no abre navegador.
5. Registrar resultados y límites en spec/tareas, auditar con test_reviewer
   de solo lectura y cerrar únicamente cuando todos los criterios tengan
   evidencia y Ángel confirme la comprobación manual.

### Comprobaciones efímeras propuestas

Los tests actuales no afirman todos los datos de consulta, por ejemplo
descripción/destino o la exclusión de registros de otra estantería que el
mismo usuario también puede abrir. Completar la evidencia con fixtures en
memoria; no guardar artículos de prueba en la base de desarrollo:

- Dos estanterías con listas y deseos distintos, un propietario, un miembro
  y un usuario ajeno. Incluir un miembro con acceso a ambas para comprobar
  que consultar A no agrega registros de B aunque sean accesibles por separado.
- Un registro completo, otro sin descripción ni precio y una lista vacía.
  Título/URL no se inventan; comprobar props reales y ausencia de imágenes.
- Un registro con URL sintácticamente válida de dominio `.invalid`, destinado
  solo a fixture. Bloquear peticiones externas y pedir el listado sin visitar
  el destino; comprobar HTTP 200, sus datos y el resto de referencias.
- Selección explícita de otra lista propia y petición repetida como recarga:
  los valores persistidos se mantienen y selectedListId corresponde a la URL.
- Sin sesión, no miembro, estantería alterada y list ajena/inexistente:
  denegación/rechazo sin datos de nombres, URLs o descripciones ajenos.

El test efímero aplica su guardia en `beforeRefreshingDatabase()`, antes de
que RefreshDatabase migre durante setUp, no en el cuerpo del test. Exige
entorno testing, configuración efectiva database.default sqlite, database
exactamente :memory: y URL de esa conexión vacía/null. Comprobar estos valores
sin abrir una conexión de escritura. Si falla, lanzar excepción antes de
migraciones, transacciones o fixtures. No permite conexiones externas ni usa
`.env` para elegir la base. Se elimina solo el archivo temporal al terminar,
nunca una base. La comprobación previa del paso 2 protege también la ejecución
de los tests existentes, que no se modifican para esta historia.

Esta propuesta es verificación aislada, no una suite nueva persistida ni
cambio de aplicación. Si revela un defecto, se informa y se vuelve a
planificación antes de reparar; si requiere una regresión permanente, se
presentan ruta y alcance para aprobación, sin incorporarla automáticamente.

## Estrategia de pruebas y criterios

| Criterio | Evidencia prevista |
| --- | --- |
| CA-01 | Tests existentes más comprobación efímera de título, URL y descripción; Ángel confirma su presentación real y datos opcionales. |
| CA-02 | Inspección del ancla/validación HTTP(S) y ausencia de incrustación; Ángel confirma que ratón/Enter abren el destino controlado en otra pestaña. Los atributos se revisan aparte de la observación. |
| CA-03 | Fixture sin descripción/preview, lista vacía y estado sin listas; revisión de render condicional y confirmación manual de que el enlace sigue siendo accesible. |
| CA-04 | Fixtures de dos espacios y roles; revisar payload íntegro, no solo nombre de página, y respuestas ante ruta/list manipulados. |
| CA-05 | Fixture .invalid consultado sin red, Http::preventStrayRequests y revisión del camino de consulta sin transportes alternativos. No se visita una tienda real ni se comprueba su disponibilidad. |
| CA-06 | Cambiar lista y repetir GET/recarga, comprobar selectedListId/datos y vuelta por slug; confirmación de Ángel sobre móvil, teclado, foco y enlaces largos. |

### Comandos proporcionales

- `vendor/bin/phpunit --filter 'WishesTest|WishesDrawerTest' --display-skipped`
  con el PHP 8.5/pdo_sqlite independiente ya instalado.
- `vendor/bin/phpunit -c phpunit.xml <archivo-temporal-de-consulta>` para
  las comprobaciones aisladas descritas; ruta literal fuera del repositorio.
- `git diff --check` y comprobación de enlaces documentales.

No hace falta repetir build, suite completa o pruebas Node del alta por
añadir únicamente documentos y comprobar código idéntico al integrado.
Solo ampliar controles si un cambio o hallazgo lo justifica y se autoriza.
PG/CI de la 031 fue confirmado verde por Ángel; no atribuirlo a esta ejecución
ni afirmar PostgreSQL nuevo si se ha comprobado solo SQLite.

## Lista manual para Ángel

1. Abrir Deseos y cambiar entre listas; revisar título, destino, descripción
   y un deseo sin descripción/vista previa. Revisar lista vacía si existe.
2. Activar un título con ratón y con teclado: nueva pestaña, colección intacta;
   basta un destino público/controlado, sin visitar el fixture .invalid.
3. Recargar la colección y comprobar lista/datos; usar la vuelta a su estantería.
4. En móvil y teclado, comprobar foco visible, controles accesibles y que
   enlaces/descripciones largos no provoquen desbordamiento horizontal.

La inspección de fuentes no sustituye esta comprobación. Si los datos locales
no cubren un caso, Ángel puede comprobarlo mediante el alta existente; el
agente no inserta ni borra registros de desarrollo sin autorización específica.

## Documentación y cierre

Solo se editan spec.md, plan.md y tasks.md de la 032; MEMORY cuando haya
estado operativo pendiente o cierre registrado. La spec 031 y el informe de
investigación no se duplican ni se reescriben para esta consulta.

Anotar comandos/resultados, fuentes de inspección, evidencia por CA y autor de
la validación manual. Registrar inicio/cierre, pausas comunicadas y horas para
GitHub Projects al cerrar. No cerrar por un PASS parcial o por existir código.

## Compatibilidad, publicación y riesgos

- Sin despliegue, migraciones, dependencias ni cambios de datos de la web.
  Mantener PHP 8000 y Vite 5173 si siguen activos; no usar Brave ni abrir navegador.
- Un test existente puede pasar sin cubrir todos los campos o roles: se
  complementa con los casos efímeros, no se infiere cobertura completa.
- Un enlace externo caído pertenece al destino; no crear un chequeador remoto
  para verificar independencia de la colección.
- Cualquier defecto o prueba persistente pendiente se eleva para aprobación.
  Commit, publicación, integración y traslado a documentation son posteriores
  y requieren autorización, actualizando antes la rama destino.

Plan aprobado expresamente por Ángel el 23 de noviembre de 2026. La ejecución
se solicita después de la aprobación, sin autorización de publicación.

Revisión del plan: **PASS**, sin bloqueos tras concretar la salvaguarda previa
a RefreshDatabase. Aprobación de Ángel y solicitud de ejecución recibidas.
