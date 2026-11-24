---
spec: "034"
status: approved
---

# Plan de verificación — 034

## Resumen

Verificar la [spec aprobada](spec.md) sobre la clasificación por listas ya
implementada en 031–033. El resultado será evidencia trazable y documentación;
no se añaden categorías, código, tests persistentes ni comportamientos.
Ángel aprueba el plan y solicita su ejecución con SQLite en memoria, sin
modificar código, abrir navegador ni publicar, el 24/11/2026.

## Componentes revisados sin modificación

Complemento posterior autorizado por Ángel: corregir en esta misma rama
WishForm para permitir pulsar Guardar sin listas y mostrar error/foco sin
enviar ni perder datos. Añadir guardia local de list_id al intento de guardar,
mantener required y validación servidor, quitar únicamente el bloqueo por
ausencia de listas y retirar el error al elegir/crear. No nuevos contratos,
dependencias ni migraciones. Verificar Prettier, build y diff, más confirmación
manual del aviso (CA-07). Las indicaciones de diff solo documental o no build
del plan inicial no aplican a esta corrección explícita posterior.

Ángel pide después que el aviso destaque: situarlo junto a Guardar con título,
estilo de error del tema y foco, más acción Crear/Elegir lista. Cambios locales
en WishForm y su CSS Module; no nuevos estilos globales. Este ajuste sustituye
el foco directo inicial en los controles por foco en el aviso y acción explícita.

- WishController index/create/store/edit/update y WishListController store.
- Relaciones Space::wishLists, WishList::wishes y Wish; FK wish_list_id vigente.
- Rutas de Deseos y RequireSpaceMembership.
- WishForm/WishListForm, WishesView/WishList/WishActions/WishEdit y sus estilos;
  listener de historial de app.jsx y mappings de tests/TestCase.php.
- WishesTest/WishesDrawerTest, phpunit.xml y evidencias 031–033.

## Contratos y datos existentes

La pertenencia a wish_lists es la categoría; wish_lists pertenece a un espacio.
No hay lista automática ni catálogo fijo. Alta exige list_id propio; edición
permite seleccionar otra lista propia, conservando ID/autor/estado. Consulta
identifica la clasificación por el nombre de la lista seleccionada y ?list=ID.

GET `/espacios/{space}/deseos`, alta, creación de lista y edición permanecen
protegidos como ahora. Ninguna ruta, prop o campo nuevo se propone. Rechazar
lista/deseo ajenos en la ruta A aunque el miembro también pueda consultar B
desde su propia ruta. No confundir aislamiento con prohibir ese acceso legítimo.

No modificar registros, esquema, configuración o base local para esta historia.
No se ejecutan migraciones locales ni seeders. Las migraciones de los tests
solo podrán ejecutarse dentro de SQLite en memoria previamente comprobada.

## Fases

1. Confirmar autorización de ejecución, rama/registro y diff documental;
   revisar el código vigente y mapear CA-01–CA-06 sin suponer resultados.
2. Verificar configuración efectiva antes de PHPUnit/RefreshDatabase:
   APP_ENV=testing, database.default=sqlite, database=:memory:, DB_URL y URL
   efectiva de conexión vacías/null, configuración sin caché. Aplicar valores
   de phpunit.xml únicamente al proceso. Si no se acredita, detener sin limpiar
   caché, editar .env o conectar a otra base.
3. Ejecutar con PHP 8.5/pdo_sqlite las pruebas existentes:
   `vendor/bin/phpunit --filter 'WishesTest|WishesDrawerTest' --display-skipped`.
   Registrar código de salida, pruebas, aserciones y omisiones reales.
4. Cubrir solo huecos de evidencia mediante comprobaciones efímeras aisladas
   fuera del repositorio, siguiendo la verificación de la 032 descrita abajo.
   No alterar la aplicación ni tests persistentes para obtener un resultado.
5. Inspeccionar UI existente sin abrir navegador y recoger confirmación manual
   de Ángel. Documentar la privacidad fuera de alcance y los límites de evidencia.
6. Aplicar ustwo-verify y revisión test_reviewer de solo lectura. Si todo tiene
   evidencia, cerrar spec/tasks y actualizar MEMORY con horas GitHub Projects.
   Cualquier hallazgo funcional se eleva antes de reparar y bloquea el criterio.

## Comprobaciones adicionales, solo si falta evidencia

Crear un archivo temporal de prueba fuera del repositorio con Tests/TestCase,
phpunit.xml y RefreshDatabase. La guardia beforeRefreshingDatabase verifica
testing/sqlite/:memory:/URL vacía y ausencia de config cache antes de migrar
o insertar fixtures; no basta comprobarlo dentro del test, después de setUp.
No instalar herramientas. Al terminar eliminar únicamente el archivo temporal
creado, nunca bases ni directorios mediante limpieza recursiva.

- Preparar dos espacios y dos listas por espacio; un miembro de ambos y un
  ajeno. Crear fixtures previos de deseos y listas, incluida una lista vacía.
  Bloquear solicitudes externas; la clasificación no necesita visitar URLs.
- Capturar IDs, wish_list_id y todos los valores almacenados antes de consultar.
  Pedir A, seleccionar cada lista y repetir GET como recarga. Comprobar nombre,
  selectedListId, agrupación correcta y ausencia de datos de B en la respuesta.
  Comparar las filas anteriores y los recuentos: ninguna escritura/default.
- Verificar que B se consulta separadamente desde su ruta autorizada, sin
  agregarse a A. ID ajeno, inexistente, parámetro inválido/array y usuario ajeno
  no revelan otras listas. Reutilizar pruebas existentes para invitados/revocación.
- Si la suite no cubre el ciclo completo necesario, ejecutar en memoria alta
  con lista obligatoria y cambio a otra lista propia; comprobar redirección,
  nueva agrupación y conservación ID/autor/estado/datos no editados, sin duplicar.
  Omitir list_id o enviar destino ajeno debe conservar input y filas previas.
- No repetir tests de precio, borrado o migraciones si la evidencia existente
  basta para los criterios. No introducir clasificación por defecto para lograr
  que pase un caso ni reparar fixtures inválidos como si fueran datos reales.

Estas comprobaciones acreditan fixtures previos y restricciones del modelo,
no una auditoría de todos los datos de producción. Si aparece una carencia
de evidencia, registrarla; no afirmar preservación de una base no inspeccionada.

## Matriz de criterios y evidencia prevista

| Criterio | Evidencia |
| --- | --- |
| CA-01 | Alta/lista obligatoria en WishesTest, ausencia de defaults, creación explícita y revisión de select/validación/FK. |
| CA-02 | Prueba existente de edición/movimiento y GET de agrupación/nombre/lista seleccionada; confirmación manual del flujo. |
| CA-03 | Comparación de fixtures previos antes/después de consultas y recarga; no escrituras ni nuevas listas y ninguna migración local. |
| CA-04 | Tests de aislamiento/membresía y ambos espacios; errores/input/rollback existentes, revisión de cancelación y confirmación manual. |
| CA-05 | Spec y revisión del diff: no categorías paralelas, privacidad, nuevos estados ni exclusiones; intención futura distinguida del acceso actual. |
| CA-06 | Revisión de semántica/estilos y confirmación real de Ángel en escritorio, móvil y teclado. |

## Interfaz y evidencia manual

Conservar la dirección visual y controles existentes: nombres de listas,
selección visible/aria-current, select etiquetado en alta/edición, errores y
foco visibles. Inspeccionar sin cambiar CSS/JSX ni crear diseño paralelo.

Ángel comprueba elegir lista al añadir, consultarla, mover el deseo a otra,
cancelar sin mover, volver/recargar y operar con teclado en escritorio/móvil.
No presentar build o pruebas HTTP como evidencia de adaptación visual/foco.
No abrir navegador ni detener PHP/Vite para esta tarea; si falta validación
manual, mantener el criterio y cierre pendientes.

## Controles, documentación y compatibilidad

Ejecutar git diff --check y revisar archivos rastreados/no rastreados para
acreditar diff solo documental. No build, instalación ni suite completa por
defecto: no hay cambios de aplicación. Si falla un test, distinguir problema
de entorno de defecto real sin reparaciones implícitas. PostgreSQL/CI quedan
fuera de esta verificación local; no abrir conexión a Supabase.

Actualizar spec/tasks con resultados, matriz y limitaciones; MEMORY con estado
inmediato. No nuevo informe ni duplicación de reglas en README/decisiones.
Registrar inicio/cierre y horas GitHub Projects al cerrar, sin horas por T-NN.

No hay despliegue ni cambios de compatibilidad en esta entrega. Commit, push,
integración y copia a documentation requieren autorización posterior; no vienen
incluidos en aprobar o ejecutar el plan. Al autorizarse integración, actualizar
primero la rama destino según el flujo SDD.

## Riesgos y revisión

- Confundir listas con categorías fijas: verificar el comportamiento libre
  actual, sin cambiar nombres ni clasificaciones de registros existentes.
- Exagerar preservación: distinguir fixtures aislados, revisión de restricciones
  y datos reales que no se consultan.
- Privacidad implícita: una lista llamada Regalos mantiene permisos actuales;
  ocultar cambios de estado requiere su propia definición/aprobación.
- Si falla el aislamiento previo, detener antes de ejecutar tests o fixtures.

Revisión plan_reviewer: PASS, sin bloqueos; aprobación humana y solicitud de
ejecución recibidas. Los resultados se registran en tasks.md, sin alterar
este contrato de verificación.
