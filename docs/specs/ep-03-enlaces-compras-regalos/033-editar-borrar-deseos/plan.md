---
spec: "033"
status: approved
---

# Plan técnico — 033

## Resumen y estado

Implementar únicamente la [spec aprobada](spec.md): editar y borrar deseos
desde el menú de tres puntos, conservando autoría y aislamiento. Cualquier
miembro del espacio puede operar sobre sus deseos compartidos.

Ángel aprueba expresamente el plan y los contratos técnicos propuestos y
solicita la implementación el 23 de noviembre de 2026. Reauditoría del plan:
PASS, sin bloqueos. Se siguen rutas, transacciones y componentes existentes,
sin nueva arquitectura o dependencias.

## Componentes afectados

- `app/Http/Controllers/WishController.php`: añadir edit/update/destroy;
  compartir las reglas de campos con store dentro del mismo controlador para
  evitar divergencias. Reutilizar WishUrl y WishPrice sin relajar validación.
- `routes/web.php`: añadir tres rutas al grupo existente con autenticación
  y RequireSpaceMembership. No cambiar las rutas de alta/listas ni otros cajones.
- `resources/js/pages/Wishes/WishForm/`: reutilizar el formulario para alta y
  edición con valores iniciales, destino de guardado y texto del botón según
  el modo. La edición no ofrece crear listas; el alta conserva ese flujo.
- `resources/js/pages/Wishes/WishEdit/WishEdit.jsx`: página de composición
  con JournalLayout y WishForm; CSS solo si hay estilos propios reales.
- `resources/js/pages/Wishes/WishActions/`: menú contextual y confirmación
  del deseo, en un componente hoja con su CSS Module cuando sea necesario.
  Seguir el comportamiento de foco de DeleteReading sin refactorizar Lecturas.
- `resources/js/pages/Wishes/WishList/` y `WishesView/`: pasar spaceId,
  montar las acciones y mantener un destino de foco estable tras borrar.
- `resources/js/app.jsx` y `tests/TestCase.php`: resolver Wishes/Edit al
  archivo real y añadir recarga por historial para Wishes/Wishes.
- `tests/Feature/WishesTest.php`: ampliar las pruebas existentes.

No modificar modelos, migraciones, assets, dependencias, estilos globales
ni utilidades de solicitudes de listas salvo que una evidencia obligue a
revisar el plan. No crear un hook o servicio sin una responsabilidad real.

## Contratos, datos e interfaces

### Rutas e Inertia

Base: `/espacios/{space}/deseos`, dentro del grupo de permisos actual.

| Método y sufijo | Contrato |
| --- | --- |
| GET `/{wish}/editar` | wishes.edit; devuelve Wishes/Edit con spaceId, listas propias id/name y wish editable. |
| PATCH `/{wish}` | wishes.update; valida y actualiza atómicamente; redirige a wishes.index con list=destino. |
| DELETE `/{wish}` | wishes.destroy; requiere confirmed accepted; borra solo ese deseo y redirige con list=origen. |

Registrar sin interceptar `/nuevo` ni `/listas`. Recibir el ID del deseo y
resolverlo mediante las listas de ese espacio, sin binding global que pueda
exponer datos ajenos. Un ID inexistente o de otro espacio devuelve 404 aunque
el usuario pertenezca a ambos espacios. Mantener el comportamiento de invitados
y miembros ajenos del middleware existente.

Wishes/Edit incluye wish con id, list_id, url, title, description y price
decimal o null. No necesita datos de otra estantería ni identidad editable.
La lista de vuelta se deriva del deseo resuelto, no de una URL arbitraria
enviada por el cliente. Cancelar y el enlace de cabecera vuelven a esa lista.

PATCH usa list_id, url, title, description y price. Moneda EUR derivada del
precio, como en alta; rechazar otra moneda enviada y una moneda sin precio.
URL/título obligatorios, límites 2048/255, descripción nullable hasta 5000;
aplicar trim y normalización vacía actuales. WishUrl no resuelve ni consulta
la URL. WishPrice conserva céntimos exactos, rango, coma/punto y cero/null.
La lista debe existir dentro del espacio. Autor, estado e identidad no son
asignables desde el payload; no cambiar su política actual de protección.

DELETE requiere `confirmed` con regla accepted. Su lista de origen se obtiene
del registro autorizado antes del borrado. No borrar listas aunque queden vacías.

### Atomicidad y errores

Seguir la transacción de store: en SQLite adquirir el bloqueo mediante la
actualización inocua existente de spaces; después lockForUpdate del espacio,
revalidar membresía y resolver/bloquear deseo y lista de destino bajo el mismo
espacio. Conservar este orden para no introducir una vía alternativa sin bloqueo.

Actualizar únicamente los cinco campos editables y la FK de lista; conservar
id, author_id y status. En eliminación revalidar también membresía/pertenencia
dentro de la transacción. Las rutas GET no escriben. No hacer llamadas externas.

Un destino ajeno/inexistente produce error list_id sin modificar el deseo;
un deseo ajeno/inexistente o membresía perdida produce 404. QueryException
revierte toda la operación y se traduce en error save o delete comprensible.
Inertia conserva input en edición y mantiene registro/confirmación al fallar
el borrado. Los detalles SQL no se muestran. No retirar optimistamente el deseo.
Sin control de versiones nuevo: no se resuelven ediciones concurrentes entre
miembros; sí se impiden dobles envíos mientras la petición está en curso.

## Interfaz y dirección visual aprobada

Mantener JournalLayout, tema cálido, controles y espaciado del alta. No añadir
una ficha de detalle. WishForm inicializa campos de edición, convierte null
en vacío y usa PATCH; alta sigue usando POST y su creación explícita de listas.
No reinicializar input en un error ni confundir precio cero con ausencia.

WishActions presenta un botón de tres puntos con nombre accesible por deseo,
aria-expanded/aria-controls y un desplegable de acciones nativas, sin imponer
role=menu si no se implementa su interacción completa. Editar es Link a la
página; Eliminar abre la confirmación en ese mismo elemento del listado.
No envolver ni sustituir el título externo: mantener target/rel seguros.

Al abrir la confirmación enfocar Cancelar; cerrar/cancelar devuelve foco al
botón de tres puntos. Escape cierra el desplegable o cancela la confirmación
antes del envío. El formulario de borrado usa useForm({ confirmed: true });
solo Confirmar envía DELETE. Deshabilitar controles durante su petición y
mostrar errores sin desmontar la confirmación. Tras éxito enfocar el título
de la lista seleccionada mediante una referencia estable y tabIndex=-1.
No anidar formularios. Controles utilizables por toque y teclado, foco visible,
sin depender de hover, sin nuevos subrayados de botones ni animación obligatoria.
Mantener textos largos dentro del ancho en móvil y los estados vacíos actuales.

## Navegación y datos vigentes

Update redirige al listado con la lista destino; destroy con la lista origen.
La respuesta nueva del servidor actualiza listas y selectedListId, conservando
listas vacías y el resto de deseos. Comprobar que no queda selección antigua.

Ampliar el listener navigate existente de app.jsx solo en type=history y
component Wishes/Wishes: recargar lists y selectedListId desde el servidor.
No disparar otra recarga en visitas normales ni crear un bucle. Si el parámetro
list ya no es válido, respetar el 404 vigente; no seleccionar datos ajenos.
Comprobar botón lateral/atrás/adelante y recarga tras editar, mover y borrar.

## Fases de implementación

1. Confirmar aprobación del plan, estado Git y consumidores del formulario.
2. Backend: validación compartida, resolución por espacio, rutas, edición y
   borrado transaccionales; cubrirlos con pruebas aisladas.
3. Frontend: reutilización del formulario, página y mappings, acciones y foco,
   selección de lista y actualización por historial.
4. Verificación automática, formato y revisión del diff; comprobación manual
   de Ángel sobre escritorio/móvil/teclado antes de cerrar.
5. Registrar evidencias por criterio, revisión test_reviewer y cierre con
   horas GitHub Projects descontando solo pausas comunicadas.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Inspección y prueba manual del menú por deseo, título externo intacto, abrir no envía DELETE. |
| CA-02 | GET prefijado, PATCH completo/opcionales, límites, precio coma/punto/cero/null; cancelación manual sin guardar. |
| CA-03 | Movimiento entre dos listas propias, mismo ID/autor/estado, conteo estable y lista anterior vacía conservada. |
| CA-04 | DELETE sin confirmación rechazado, confirmado elimina solo uno, conserva lista; cancelar no envía petición. |
| CA-05 | Redirecciones a lista destino/origen, GET repetido actualizado y comprobación manual de recarga/atrás/adelante. |
| CA-06 | Miembro distinto del autor permitido; invitado/ajeno/revocado denegados; deseo/lista ajenos incluso perteneciendo a ambos espacios; payload autor/estado manipulado sin efecto. |
| CA-07 | URL inválida, título vacío/largo, descripción/rango/moneda inválidos; fallo SQL de actualización y borrado, comparar todos los valores anteriores y input; Http sin llamadas. |
| CA-08 | Escritorio/móvil, teclado, foco inicial/restaurado/tras borrar, Escape, errores legibles y controles durante procesamiento. |

Extender WishesTest con fixtures de dos espacios, dos miembros y un ajeno.
Para errores SQL usar eventos temporales del modelo y restaurar listeners en
finally, como la prueba actual, sin dejar fallos inyectados en la aplicación.
Demostrar ausencia de cambios parciales en campos/lista y de borrado adicional.

Antes de PHPUnit, confirmar configuración efectiva de pruebas sin caché,
APP_ENV=testing, SQLite :memory: y DB_URL vacía, antes de RefreshDatabase.
No usar la SQLite de desarrollo ni Supabase. Ejecutar la suite existente con
`vendor/bin/phpunit --display-skipped`, usando PHP 8.5/pdo_sqlite del proyecto;
`node --test tests/Frontend/wishRequest.test.js` para el consumidor conservado;
`npm.cmd run build`, Prettier local sobre JSX/CSS modificados y git diff --check.
PostgreSQL solo si está disponible el entorno aislado definido por
phpunit.pgsql.xml; si no, registrar limitación y dejar CI a Ángel.
No instalar un runner de UI; distinguir controles automáticos de interacción
manual. Build no acredita foco ni historial. Mantener PHP y Vite activos.

## Documentación, compatibilidad y despliegue

Actualizar spec/tasks con evidencias reales y MEMORY.md con estado inmediato.
No crear informe adicional ni cambiar README. No hay cambios de esquema ni
migración que ejecutar: no tocar .env ni datos locales para preparar el plan.
Mantener las rutas de creación, las restricciones EUR y demás colecciones.

No commit, push, integración ni despliegue en esta fase. Requieren permiso
posterior. Al publicar el cierre, actualizar dev antes de integrar; actualizar
documentation antes de trasladar únicamente los artefactos autorizados de esta
spec, conforme al proceso SDD. CI y despliegue son comprobaciones posteriores.

## Riesgos y revisión

- Duplicar reglas de alta/edición: compartir la validación dentro del controlador.
- Pérdida de autoría o salida del espacio: payload limitado y pertenencia
  comprobada de nuevo bajo transacción, también en movimiento y borrado.
- Caché de historial y foco desmontado: recarga acotada y destino estable
  verificables manualmente; no afirmar su corrección solo por tests HTTP.
- Sin versiones: una edición válida posterior puede reemplazar otra; exclusión
  de concurrencia ya declarada, sin añadir mecanismos no aprobados.

Revisión plan_reviewer: PASS tras la aprobación expresa de Ángel del plan y
los contratos GET /{wish}/editar, PATCH/DELETE /{wish}, página Wishes/Edit y
sus props. Cobertura de CA-01–CA-08 y coherencia con patrones existentes.
Commit, publicación e integración no autorizados por esta aprobación.
