---
id: "033"
title: "Editar y eliminar deseos compartidos"
status: completed
---

# 033 — Editar y eliminar deseos compartidos

Historia de la [épica EP-03, issue #25](https://github.com/elkio-studio/UsTwo/issues/25).
Referencias: [alta manual 031](../031-alta-deseos/spec.md) y
[consulta 032](../032-consulta-deseos/spec.md).

Inicio: 23 de noviembre de 2026, 20:07 CET.
Sprint de cierre previsto: Sprint 2. Capacidad orientativa: 4 h/día,
lunes a viernes, 20 h por sprint; estimación pendiente de validación técnica.

## Problema y objetivo

Como miembro de una estantería, corregir o retirar referencias de sus listas
de Deseos. Mantener URL, título y descripción editables, añadir la edición
del precio en euros y permitir cambiar el deseo de lista sin duplicarlo.
Eliminar exige confirmación y cancelar no modifica datos.

## Decisiones aprobadas e interacción

Ángel autoriza la rama feat/editar-borrar-deseos desde dev y esta spec, con:

- Menú de tres puntos junto a cada deseo con «Editar» y «Eliminar».
- Edición de URL, título, descripción, precio exclusivamente en euros y lista.
- Preparación documental solamente, sin implementar todavía.

Ángel confirma además expresamente:

- Conservar el título como enlace externo seguro; el menú se usa para las
  acciones y no sustituye ni intercepta la apertura del destino.
- «Editar» abre una página de edición coherente con el alta, con sus datos
  actuales y vuelta a la lista de origen; sin crear una ficha de detalle nueva.
- «Eliminar» abre una confirmación contextual en el listado, identificando
  el deseo y ofreciendo «Cancelar» y «Confirmar borrado». Seguir el patrón
  existente de confirmación, con foco inicial en Cancelar y retorno al disparador.
- Cualquier miembro puede editar/eliminar los deseos compartidos de su
  estantería, aunque los publicara otro miembro. La autoría original se conserva
  al editar: no se convierte en el usuario que acaba de corregir la ficha.

Estas decisiones completan el contrato de la spec para su aprobación global;
no se presentan como implementadas ni autorizan ejecutar la implementación.

## Alcance incluido

- Menú accesible de acciones de cada deseo en el listado existente.
- Formulario precargado con los cinco datos editables y validación al guardar.
- Cambio entre listas existentes de la misma estantería.
- Borrado definitivo individual con confirmación explícita en interfaz y servidor.
- Actualización del listado después de guardar, mover o borrar y persistencia
  tras recarga. Mantener el contexto de la lista correspondiente.
- Permisos, aislamiento, errores sin cambios parciales y uso móvil/teclado.

## Alcance excluido

- Papelera, recuperación, borrado masivo o historial de cambios.
- Editar/eliminar listas, categorías o gestionar participantes de listas.
  La creación de listas mantiene su flujo actual de alta; la edición solo
  selecciona una lista de destino existente.
- Cambiar comprado/pendiente, tachado o visibilidad selectiva del estado.
- Cambiar el autor original, moneda distinta de EUR, conversión o seguimiento
  de precios. No introducir valoraciones personales o campos nuevos.
- Autocompletado/extracción, peticiones a la URL, imágenes, iframes o APIs.
- Rediseñar la colección o el cajón. Código, pruebas, datos locales, plan/tareas,
  commits o publicación durante esta preparación de la spec.

## Actores y permisos

- Miembro de la estantería: consulta y opera sobre deseos de ese espacio,
  conforme a la regla de contenido compartido confirmada por Ángel.
- Usuario sin sesión o ajeno: no accede a edición ni puede modificar/borrar.
- Servidor: comprueba membresía y obtiene el deseo dentro de las listas de
  la estantería de la ruta, sin confiar en IDs, autor o espacio del cliente.
- Ángel: valida producto, interacción y decisiones técnicas de la spec/plan.

## Flujo de edición

1. El miembro abre el menú de tres puntos de un deseo y elige Editar.
2. Se muestran URL, título, descripción, precio EUR y lista actuales. Los
   opcionales ausentes se muestran vacíos, sin inventar datos ni un precio cero.
3. Corrige los campos y, si quiere, elige otra lista existente del mismo espacio.
4. Guardar valida todo en servidor y aplica el cambio completo al mismo deseo.
   Se conservan su identidad, autor original y estado guardado.
5. Vuelve al listado actualizado. Si cambió de lista, se muestra seleccionada
   la lista de destino, donde aparece el deseo; ya no aparece en la anterior.
6. Cancelar o volver sin guardar no envía una modificación ni altera el deseo.

## Flujo de eliminación

1. En el menú del deseo, el miembro elige Eliminar.
2. Se identifica el deseo y se avisa del borrado definitivo. Abrir el menú o
   la confirmación no borra ni envía todavía una petición de eliminación.
3. Cancelar, incluido cerrar la confirmación antes de enviarla, no modifica
   datos y devuelve el foco a un control coherente del mismo deseo.
4. Confirmar envía el borrado con aceptación explícita; el servidor vuelve a
   verificar permiso y pertenencia. Una petición sin confirmar se rechaza.
5. El listado se actualiza y mantiene la lista del deseo borrado; si queda
   vacía, muestra su estado vacío. No se elimina la lista ni otro deseo.

## Validación, reglas y errores

Aplicar las reglas vigentes de la 031 también al editar:

- URL y título obligatorios. URL HTTP(S) absoluta, host válido, sin credenciales,
  controles, barras inversas o host ambiguo; máximo 2048. Sin DNS ni conexión.
- Título trim 1–255, descripción opcional hasta 5000, mostrados como texto.
- Precio opcional 0–99999999,99, hasta dos decimales con coma o punto, sin
  miles/exponentes. Céntimos exactos, sin float; solo EUR y sin selector.
  Borrar el precio deja importe/moneda ausentes; cero sigue siendo un precio válido.
- Lista obligatoria, existente y de la estantería actual. No mover a otro
  espacio ni elegir silenciosamente otra lista si el destino resulta inválido.
- Mover un deseo conserva su ID y autor y no crea duplicados. Si deja vacía la
  lista anterior, esta se conserva. No cambiar estados u otros campos protegidos
  aunque se envíen en una petición manipulada.
- Error de validación o almacenamiento: mensaje comprensible, input conservado
  para reintentar y ningún cambio parcial de campos/lista ni borrado accidental.
- Si el deseo ya no existe o es ajeno, rechazar sin revelar datos de otra
  estantería ni modificar otro registro. Si se perdió membresía desde que se
  abrió el formulario, la operación también se deniega.
- Impedir envíos simultáneos desde la interfaz mientras guarda/borra. No añadir
  bloqueo optimista, versiones o un historial sin una decisión posterior.
- Un fallo de borrado conserva el registro y permite reintentar con un mensaje;
  no quitarlo definitivamente del listado antes de confirmar éxito del servidor.

Después de guardar o borrar, obtener datos actuales del servidor y no mostrar
una copia antigua de la colección. Comprobar también la vuelta con el historial
del navegador, sin que reaparezcan deseos eliminados o datos anteriores.

## Interfaz y accesibilidad

Conservar JournalLayout, colores cálidos, tipografía y controles existentes,
sin nuevos estilos globales, imágenes o paquetes. Formulario con etiquetas y
errores visibles, opcionales identificados y vuelta a la lista de origen.

El control de tres puntos tiene nombre accesible que identifica el deseo,
es operable por toque y teclado, expone su estado abierto/cerrado y conserva
foco visible. Editar/Eliminar son acciones separadas del enlace al destino.
Cerrar el menú o cancelar la confirmación no pierde el foco ni ejecuta acciones.
Tras borrar, el foco pasa a un control existente coherente del listado.
No se necesitan animaciones nuevas; respetar el movimiento reducido actual.

## Criterios de aceptación

- **CA-01.** Cada deseo muestra menú de tres puntos con Editar/Eliminar,
  sin cambiar la apertura externa del título ni permitir borrado al abrirlo.
- **CA-02.** Editar precarga y permite corregir URL, título, descripción,
  precio en euros y lista, con las reglas vigentes. Cancelar no guarda.
- **CA-03.** Cambiar de lista solo admite un destino del mismo espacio,
  conserva ID/autor/estado y no duplica registros ni borra la lista anterior.
- **CA-04.** Eliminar requiere confirmación explícita; cancelar no envía
  cambios. Confirmar elimina únicamente el deseo autorizado y mantiene su lista.
- **CA-05.** Guardar/mover/borrar actualiza el listado y la selección de lista
  correspondiente. Recarga y vuelta por historial muestran datos vigentes.
- **CA-06.** Solo miembros pueden editar/borrar deseos de su estantería,
  incluidos los de otros autores según la regla aprobada. IDs, ruta o lista manipulados,
  falta de sesión y membresía retirada no permiten consultar/cambiar datos ajenos.
- **CA-07.** Validación o fallo de guardado conserva input y datos previos,
  sin escrituras parciales. Un fallo de borrado conserva el deseo. URL inválida
  no se guarda y una tienda caída no bloquea editar porque no se consulta.
- **CA-08.** Menú, edición, confirmación, cancelación y regreso funcionan en
  escritorio/móvil/teclado, con foco visible y restaurado, sin nuevas dependencias
  ni acciones accesibles exclusivamente por hover.

## Validación prevista y estado

En la implementación futura: pruebas aisladas de edición completa/opcional,
precio null/cero/EUR y URL inválida, movimiento entre listas propias y rechazo
de lista/registro ajenos, autor/estado manipulados, ausencia de confirmación,
cancelación sin petición, borrado del último deseo sin borrar lista, membresía
retirada, errores SQL sin cambios parciales y ausencia de consultas externas.

Confirmación manual de Ángel sobre menú y enlace externo, edición/guardado,
borrado/cancelación, selección/recarga/historial y foco en escritorio/móvil/teclado.
Las pruebas automáticas usarán bases aisladas. No insertar ni borrar registros
de la base de la web para preparar esta spec; conservar .env y servidores activos.

Los contratos de rutas, componentes y persistencia quedan concretados en el
plan aprobado. El modelo ya tiene los campos necesarios; no se ha requerido
migración ni cambio de arquitectura.
Ángel aprueba expresamente la spec completa el 23 de noviembre de 2026 y
solicita preparar el plan en feat/editar-borrar-deseos. Posteriormente aprueba
plan/contratos y solicita implementar. Sin decisiones funcionales pendientes.

El bloqueo anterior por permiso sobre deseos de otros autores, página de
edición y confirmación contextual queda resuelto por las respuestas explícitas
de Ángel el 23 de noviembre de 2026. Reauditoría de spec_reviewer: PASS,
sin bloqueos. Este resultado no sustituye la aprobación global de Ángel.
Enlaces relativos comprobados.

## Estado de implementación — 23/11/2026

Plan y contratos aprobados expresamente; implementación solicitada y realizada
en feat/editar-borrar-deseos. Edición/borrado y pruebas automáticas registrados
en [tasks.md](tasks.md). Suite SQLite en memoria: 86 pruebas, 2456 aserciones,
sin fallos y 2 omisiones de PostgreSQL; Node y build correctos.
Sin migraciones nuevas ni modificaciones de datos locales. Web/Vite activos.

Ángel confirma que editar, mover y borrar deseos funciona en escritorio,
móvil y teclado. Validación y matriz de evidencias registradas en tasks.md;
la observación manual es del desarrollador, sin navegador del agente ni
mediciones independientes de contraste o trazas de foco/historial.

Reauditoría final test_reviewer: PASS, sin bloqueos, tras esa confirmación.
Spec completada localmente. No se ha creado commit ni publicado cambios.
Integración y CI externo pendientes de autorización y comprobación posteriores.

## Cierre y horas para GitHub Projects

- Inicio: 23/11/2026, 20:07 CET.
- Cierre local: 23/11/2026, 20:26 CET, al registrar la confirmación de Ángel.
- Tiempo transcurrido: 19 minutos; sin pausas comunicadas para esta spec.
- GitHub Projects: **0.25 h estimadas**, calculadas por tiempo transcurrido:
  un cuarto completo y cuatro minutos restantes, redondeados hacia abajo
  según la regla acordada. No es una medición de dedicación efectiva.

Se incluye la limpieza solicitada: utilidad wishRequest en resources/js/utils
y eliminación de las carpetas vacías Wishes/utils y Readings/ReadingCard,
sin referencias pendientes. Node y build repetidos correctamente tras el cambio.
