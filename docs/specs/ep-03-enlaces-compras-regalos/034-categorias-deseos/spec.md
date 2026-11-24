---
id: "034"
title: "Verificar las listas de Deseos como categorías"
status: completed
---

# 034 — Verificar las listas de Deseos como categorías

**GitHub Projects: `0.5` h estimadas.**

Historia de la [épica EP-03, issue #25](https://github.com/elkio-studio/UsTwo/issues/25).
Referencias: [alta 031](../031-alta-deseos/spec.md),
[consulta 032](../032-consulta-deseos/spec.md) y
[edición y borrado 033](../033-editar-borrar-deseos/spec.md).

Inicio: 24 de noviembre de 2026, 11:33 CET.
Sprint de cierre previsto: Sprint 2. Capacidad orientativa: 4 h/día,
lunes a viernes, 20 h por sprint; estimación pendiente de validación técnica.

## Problema y objetivo

Distinguir la finalidad de un enlace sin introducir una clasificación que
duplique las listas existentes. Ángel confirma que las categorías son las
propias listas: en cada lista reúne los deseos con la finalidad que quiera.

Esta entrega verifica y documenta el comportamiento ya existente de las
specs 031–033. No crea categorías predefinidas, otro selector, etiquetas ni
un campo category. La interfaz continúa usando el término «Lista».

Excepción posterior aprobada por Ángel durante la comprobación local:
corregir en esta misma rama el botón Guardar deseo. Debe poder pulsarse sin
listas y mostrar que hay que crear una, conservando los datos. Si hay listas
pero falta seleccionar una, indicar que debe elegirla. Solo bloquear el botón
mientras se envía; no enviar una petición si falta la lista. El resto de la
spec mantiene su naturaleza de verificación y sus exclusiones.

Ángel solicita además retirar la URL visible del previsualizado del deseo:
el título conserva su href y apertura segura; la URL sigue almacenada y
editable. Ajuste local autorizado de WishList y limpieza del estilo sin uso,
sin cambiar contratos ni datos.

## Decisiones aprobadas

- Usar la pertenencia a una lista como clasificación del deseo.
- Cada deseo requiere una lista de su estantería; no crear una por defecto.
- La lista se puede elegir al añadir y cambiar al editar el deseo.
- Conservar las listas actuales de los deseos existentes. El criterio de
  categoría válida por defecto se satisface conservando esa clasificación,
  sin reasignar registros a una categoría nueva ni crear una lista automática.
- Visibilidad y permisos actuales de la estantería, sin privacidad adicional.
- Rama docs/categorias-deseos desde dev y spec de verificación documental;
  sin implementar ni ejecutar verificaciones en esta fase de preparación.

## Alcance incluido

- Revisar la asignación obligatoria a listas propias al crear y editar.
- Verificar que la consulta identifica la lista seleccionada por su nombre y
  agrupa sus deseos; no se exige repetir el nombre en cada entrada.
- Verificar movimiento entre listas propias, persistencia y conservación de
  identidad, autoría, datos y estado del deseo, según la 033.
- Comprobar que los registros existentes conservan una lista válida y que
  no aparecen deseos de otro espacio, incluso si alguien pertenece a ambos.
- Documentar la equivalencia lista/categoría y el límite de regalos sorpresa.
- Recoger comprobación de uso existente en escritorio, móvil y teclado.

## Alcance excluido

- Funcionalidad nueva, rutas, esquema, migraciones o tests persistentes.
  Las correcciones autorizadas posteriormente se limitan a WishForm y su
  CSS Module (aviso y conservación del deseo al crear lista) y WishList y su
  CSS Module (retirar URL duplicada), descritas en el objetivo; sin rediseño.
- Catálogo fijo de categorías, múltiples categorías por deseo o listas por defecto.
- Renombrar/eliminar listas, rediseño del cajón o de sus pantallas.
- Estado comprado/pendiente editable, tachado, elección de destinatarios o
  exclusión de participantes; privacidad especial de listas o regalos.
- Acceso a tiendas, extracción, imágenes, seguimiento de precios y APIs.
- Datos de producción, siembra/modificación de datos en la base de la web,
  commits, publicación, integración o despliegue durante esta preparación.

## Actores

- Miembro: crea listas explícitamente, guarda deseos en ellas y mueve deseos
  de su estantería mediante la edición existente, incluidos los de otro autor.
- Usuario sin sesión o ajeno: no consulta ni altera datos de esa estantería.
- Ángel: aprueba la spec y plan posterior; valida la interacción y el cierre.
- Agente: inspecciona/documenta; ejecutará solo la verificación autorizada en
  una fase posterior, sin cambiar comportamiento ni datos reales.

## Flujo principal que se verificará

1. Un miembro entra en Deseos y consulta las listas de su estantería.
2. Al añadir un deseo, selecciona una lista existente o crea explícitamente
   una en el flujo de alta; el nombre lo decide el usuario, sin categorías fijas.
3. Guarda el deseo. La consulta muestra seleccionada esa lista y su nombre;
   el deseo aparece dentro de ella, con sus datos actuales.
4. Desde Editar puede elegir otra lista existente del mismo espacio.
5. Tras guardar se selecciona el destino y el deseo deja de figurar en la
   anterior sin duplicarse. Las listas vacías se conservan.
6. Al recargar o volver a consultar, el deseo mantiene su lista guardada.
   El comportamiento del historial existente de la 033 se revisa sin ampliarlo.

## Flujos alternativos, errores y casos límite

- Sin listas: se conserva el estado vacío y la creación explícita en alta;
  no asignar una lista automáticamente. Una lista puede existir vacía.
- Sin lista seleccionada al guardar o con destino inexistente/ajeno: rechazo
  con error, input conservado y sin creación/movimiento parcial.
- Cancelar edición no mueve el deseo. URL, título, descripción y precio
  mantienen la validación existente; esta historia no la cambia.
- Lista o deseo de otro espacio: no se permite consultarlo ni usarlo como
  destino, aunque el usuario sea miembro de ambos espacios.
- Pérdida de membresía: se aplican las denegaciones existentes; nadie obtiene
  acceso adicional por conocer el ID de una lista.
- Error de guardado: conservar clasificación y datos previos. Una tienda
  inaccesible no afecta a esta clasificación porque no se consulta la URL.
- Registros existentes: preservar su wish_list_id y la lista referenciada;
  no backfill, reasignación, limpieza o migración. Si se encuentra un deseo
  huérfano o un defecto, registrar el hallazgo y elevar la decisión antes de
  reparar; no inventar una categoría de respaldo.

## Privacidad de regalos: decisión y trabajo pendientes

En esta historia todos los miembros de la estantería conservan el acceso
actual. Nombrar una lista «Regalos» no la convierte en privada ni oculta
deseos, autoría o cambios de estado. No ofrecer una garantía de sorpresa.

En conversaciones previas se planteó como finalidad futura permitir que un
cambio comprado/pendiente se muestre a todos o excluya participantes elegidos:
la persona excluida seguiría viendo el artículo como pendiente, sin ocultarle
el artículo. Esta intención no autoriza implementar ese comportamiento aquí.

Su contrato de visibilidad, selección de participantes, permisos, persistencia
y pruebas se definirá y aprobará en una historia/spec posterior antes de
implementar privacidad especial. No asumirla por el nombre de la lista ni
introducir estados o exclusiones por inferencia.

## Criterios de aceptación

- **CA-01.** La lista es la categoría: alta exige elegir una lista propia o
  crearla explícitamente. Sin lista válida no se guarda el deseo; no hay
  categoría paralela ni lista automática.
- **CA-02.** La consulta identifica la lista seleccionada y sus deseos;
  cambiar la lista desde la edición existente actualiza la agrupación y la
  selección sin duplicar el deseo ni modificar su autoría/estado.
- **CA-03.** Los deseos existentes conservan su lista válida tras consulta,
  recarga y verificación. No se modifica ningún dato previo, se crea una
  categoría de respaldo ni se ejecuta una migración para esta historia.
- **CA-04.** Miembros, invitados y usuarios ajenos mantienen los permisos
  actuales. Usar en la ruta de una estantería una lista o deseo de otra se
  rechaza, también perteneciendo a ambas. Se puede consultar cada espacio
  autorizado desde su propia ruta; error/cancelación conserva datos previos.
- **CA-05.** La documentación deja explícitos el acceso compartido actual y
  la privacidad especial de regalos fuera de alcance y pendiente de definición
  y aprobación posterior. No se incorpora código de exclusión ni estado nuevo.
- **CA-06.** La selección/consulta de lista y su cambio al editar son utilizables
  en escritorio, móvil y teclado con la interfaz actual, sin rediseño.
- **CA-07.** Guardar deseo permanece habilitado sin listas. Al pulsarlo, indica
  que hay que crear una; si hay listas y no se eligió ninguna, pide seleccionarla.
  Por ajuste solicitado por Ángel, el aviso se resalta junto al botón de guardar,
  recibe foco y ofrece Crear lista/Elegir lista para ir al control correspondiente.
  No envía la petición
  ni pierde input; al crear/seleccionar se retira el error de lista. Durante
  una petición real se mantiene la protección contra doble envío.

El alcance inicial sin cambios de código se conserva como contexto de las
pruebas ya ejecutadas; esta excepción explícita sustituye esa prohibición
únicamente para WishForm y requiere validación manual del aviso antes de cerrar.

## Verificación prevista y límites de evidencia

Tras aprobar spec y plan de verificación: inspección de WishList/Wish,
WishController/WishListController, rutas protegidas, WishForm y WishesView;
reutilizar pruebas existentes de WishesTest y WishesDrawerTest donde apliquen.
Comprobar que hay evidencia para asignación obligatoria, conservación de
datos, movimiento, permisos y errores; documentar cualquier carencia.

Ejecutar pruebas solo con SQLite :memory: y configuración efectiva comprobada
antes de RefreshDatabase, sin URL externa ni caché de configuración. Si hiciera
falta una comprobación adicional, será efímera y aislada, sin modificar tests
del repositorio ni la base local. Ninguna prueba ejecutada durante la redacción
de esta spec se da por realizada: son controles futuros, sujetos a autorización.

Para registros existentes, revisar las restricciones/relaciones y comprobar
fixtures previos en la base aislada antes/después de consultar; no usar ni
modificar datos de producción. Registrar con precisión qué acredita ese ensayo.
La validación manual de Ángel cubrirá selección y movimiento en los tres
contextos; no atribuir al agente observación visual o de foco que no haya hecho.

No abrir navegador, detener PHP/Vite, tocar .env ni migrar. Si aparece una
carencia funcional, informar del hallazgo y pedir decisión de alcance antes
de implementar; no cerrar como verificación satisfactoria mientras siga abierta.

## Decisiones pendientes y estado

Sin decisiones pendientes dentro de esta verificación: lista equivale a
categoría, datos actuales se conservan y privacidad especial queda excluida.
Los detalles de regalos sorpresa pertenecen a una historia posterior.

Revisión spec_reviewer: PASS, sin bloqueos. Enlaces relativos comprobados.
Ángel aprueba expresamente la spec el 24/11/2026 y solicita el plan de
verificación en docs/categorias-deseos. Después aprueba el plan y solicita
ejecutarlo con SQLite en memoria, sin código, navegador ni publicación.
Estado completed tras verificación automática, ajustes expresamente autorizados,
validación manual de Ángel y reauditoría final PASS.

## Estado de verificación — 24/11/2026

Resultados y matriz en [tasks.md](tasks.md): 17 pruebas existentes/443
aserciones y 2 efímeras/159 aserciones; **19 pruebas, 602 aserciones, sin fallos
ni omisiones**, exclusivamente SQLite :memory: con guardias previas.
Esas pruebas se ejecutaron antes del ajuste autorizado de WishForm; no se
modificaron tests persistentes, esquema, .env o datos locales. El cambio
posterior de código comprende CA-07 y los ajustes visuales aprobados del
objetivo, sin backend nuevo ni cambios de clasificación.
La preservación comprobada corresponde a fixtures previos, no a producción.

Ángel confirma el 24/11/2026 que todo funciona en escritorio, móvil y teclado,
incluida la conservación de los datos al crear una lista, y solicita el cierre.
La confirmación resuelve los hallazgos anteriores de apertura/aviso y pérdida
de input; se atribuye al desarrollador, no al agente. No se abrió navegador.
Reauditoría final test_reviewer: PASS, sin bloqueos. Cierre local completado;
commit, publicación, integración y CI externos pendientes de autorización.

## Cierre y horas para GitHub Projects

- Inicio: 24/11/2026, 11:33 CET.
- Cierre local: 24/11/2026, 12:05 CET, al registrar la confirmación de Ángel.
- Tiempo transcurrido: 32 minutos; sin pausas comunicadas para esta spec.
- Cálculo del valor indicado al principio: dos cuartos completos y dos minutos
  restantes redondeados hacia abajo según la regla acordada. No equivale a
  una medición de dedicación efectiva.

Las listas satisfacen la clasificación sin campos/categorías paralelos ni
migración. Regalos sorpresa sigue fuera. Se incluyen los ajustes de UI
autorizados durante las comprobaciones; no se afirma entrega solo documental.
No se creó commit, subieron cambios ni abrió navegador para este cierre.
