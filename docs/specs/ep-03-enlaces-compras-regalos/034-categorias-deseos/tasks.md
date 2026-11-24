---
spec: "034"
status: completed
---

# Tareas de verificación — 034

Referencia: [spec.md](spec.md) y [plan.md](plan.md). Plan aprobado y ejecución
solicitada con SQLite en memoria, sin cambios de aplicación ni publicación.

- [x] **T-01 — Revisar precondiciones y comportamiento existente**
  - Criterios: CA-01–CA-06.
  - Confirmar plan aprobado/ejecución solicitada, rama y diff; revisar modelo,
    controladores, rutas, interfaz y pruebas existentes sin modificarlos.
  - Evidencia esperada: mapa por criterio y ausencia de campos/categorías nuevas.

- [x] **T-02 — Acreditar aislamiento y ejecutar pruebas existentes**
  - Criterios: CA-01, CA-02, CA-04.
  - Preflight efectivo testing/SQLite :memory:/URL vacía/sin config cache antes
    de RefreshDatabase; ejecutar filtro WishesTest|WishesDrawerTest del plan.
  - Evidencia esperada: guardia y resultados reales, sin uso de datos locales
    o Supabase; detener si no se acredita seguridad, sin limpiar configuración.

- [x] **T-03 — Completar evidencia de clasificación y conservación**
  - Criterios: CA-01–CA-04.
  - Solo si hay huecos: prueba efímera fuera del repositorio con guardia
    beforeRefreshingDatabase, fixtures de ambos espacios y snapshots previos.
    Verificar agrupación/selección/recarga sin escritura y movimiento aislado.
  - Evidencia esperada: comparación exacta de datos previos, permisos y ausencia
    de defaults/duplicados, sin extrapolar a producción. Eliminar solo el test
    temporal creado; ningún test persistente ni base se borra o modifica.

- [x] **T-04 — Revisar privacidad e interacción sin rediseño**
  - Criterios: CA-02, CA-04, CA-05, CA-06.
  - Revisar nombre/lista seleccionada, semántica, validación y controles;
    confirmar documentalmente privacidad especial fuera del alcance.
    Recoger validación real de Ángel sobre selección, movimiento/cancelación,
    recarga y teclado en escritorio/móvil. No abrir navegador.
  - Evidencia esperada: confirmación manual atribuida y límites observados,
    sin promesas de sorpresa ni mediciones visuales inventadas.

- [x] **T-05 — Auditar y cerrar con evidencias y horas**
  - Criterios: CA-01–CA-06.
  - Revisar diff solo documental y git diff --check; aplicar ustwo-verify y
    test_reviewer de solo lectura. Resolver hallazgos mediante decisión antes
    de cualquier reparación; no cerrar con criterio pendiente.
  - Evidencia esperada: matriz final y PASS; spec/tasks completed, MEMORY
    actualizado, inicio/cierre y horas GitHub Projects. Publicación/integración
    siguen pendientes de permiso, sin cambios de código ni datos locales.

## Evidencias de ejecución — 24/11/2026

Esta sección recoge primero la verificación sin cambios; el ajuste de código
autorizado después se registra al final, sin atribuirle pruebas anteriores.

- T-01: rama y registro docs/categorias-deseos, base 4681a0c; spec y plan
  aprobados, ejecución solicitada. Diff solo MEMORY y documentos 034, índice
  vacío. Revisión de WishController/WishListController, modelos, rutas,
  RequireSpaceMembership, WishForm, WishEdit, WishesView y WishList.
- T-02: preflight fuera del repositorio carga valores de phpunit.xml solo en
  el proceso y comprueba configuración efectiva antes de PHPUnit: testing,
  sqlite, :memory:, URL vacía, sin caché de configuración. Resultado correcto.
  PHP 8.5.12 con pdo_sqlite; filtro WishesTest|WishesDrawerTest:
  **17 pruebas, 443 aserciones, sin fallos ni omisiones**, salida 0.
- T-03: CategoryListsProbeTest.php efímero en el directorio temporal
  ustwo-spec034, fuera del repositorio, con guardia beforeRefreshingDatabase
  previa a migrar/fixtures. Segunda ejecución tras preflight correcto:
  **2 pruebas, 159 aserciones, sin fallos ni omisiones**, salida 0.
  Total de esta verificación: **19 pruebas y 602 aserciones**.
- Prueba adicional 1: dos espacios, dos listas por espacio (incluidas vacías),
  miembro de ambos y ajeno. Snapshots completos de wish_lists/wishes antes y
  después de consultar A y repetir selección/recarga, consultar B por separado
  y rechazar lista ajena/inexistente/valor inválido/array. Nombres, IDs,
  agrupación, selección, autor y estado correctos; ninguna fila/default creado
  ni dato previo modificado por consultas. Ajeno denegado.
- Prueba adicional 2: sin listas, alta GET no crea defaults; POST sin list_id
  rechaza y conserva input. Creación explícita de dos listas vacías y alta;
  edición sin list_id rechazada conservando todos los atributos. Movimiento
  válido cambia solo wish_list_id/updated_at, conserva los demás atributos y
  cuenta de deseos, selecciona destino y mantiene vacía la lista origen.
- Http::preventStrayRequests y assertNothingSent: sin solicitudes externas.
  Ninguna de estas pruebas consulta datos locales/producción; acreditan fixtures
  previos en memoria y comportamiento existente, no auditoría de Supabase.
- T-04 parcial: lista seleccionada identificada por nombre y aria-current;
  selector etiquetado y obligatorio, errores por campo; Cancelar en WishEdit
  navega sin PATCH. CSS existente usa variables/ancho adaptable; no se cambió.
  Privacidad fuera de alcance: Regalos sigue siendo una lista compartida, sin
  campos nuevos ni exclusiones. No se midieron contraste/foco renderizados.
- git diff --check correcto. No build ni suite completa: código intacto.
  No migraciones locales, seeds, cambios de .env, navegador ni publicación.
  No se han detenido ni reiniciado PHP/Vite.
- Pendiente T-04: confirmación de Ángel de selección/consulta, cambio de lista,
  cancelar sin mover, recarga y uso de escritorio/móvil/teclado para esta spec.
  T-05 y cierre/horas finales pendientes de esa evidencia y revisión final.
- test_reviewer: BLOCKED solo para cierre por confirmación manual pendiente
  (T-04/CA-06); sin bloqueos adicionales de la verificación automática.
  Tras su inspección se eliminaron únicamente preflight.php y
  CategoryListsProbeTest.php creados en TEMP/ustwo-spec034, comprobando su
  ausencia. No se borraron directorios ni bases; no quedan tests nuevos en repo.

## Ajuste posterior autorizado durante la prueba local

- Ángel detectó Guardar deshabilitado sin listas y pidió que se pueda pulsar
  para ver el aviso de crear lista. Autorizó hacerlo en docs/categorias-deseos.
- [x] **T-06 — Corregir aviso de lista obligatoria (CA-01, CA-06, CA-07):** código
  implementado en WishForm y confirmación manual final recibida.
  Guardar solo se deshabilita durante processing. Click/submit comprueban lista
  y evitan envío si falta; error visible y foco Crear lista/selector. Cambiar o
  crear lista borra su error. No se reinicia el resto de input.
- Prettier aplicado; npm.cmd run build correcto (695 módulos) y diff --check
  correcto tras el cambio. No migración ni cambios backend; las 19 pruebas
  anteriores acreditan el comportamiento del servidor sin cambios, no la
  interacción nueva. No se añadió un test que replique el manejador de UI.
- Continúa pendiente validación real escritorio/móvil/teclado y CA-07.
  Sin navegador, commit ni subida; PHP/Vite conservados activos.
- Reauditoría test_reviewer del delta: sin defectos técnicos bloqueantes
  adicionales. Cierre BLOCKED solo por validación manual pendiente, incluido
  CA-07. No se atribuye al build comprobación real de foco o teclado.
- Ajuste visual posterior pedido por Ángel: aviso destacado junto a Guardar,
  título «No se ha guardado el deseo», borde y texto con token de error,
  role=alert y foco sobre el aviso. Acción Crear lista/Elegir lista permite
  continuar. Se mantienen datos y error asociado al selector. WishForm y su
  CSS Module son los únicos archivos de aplicación cambiados. Prettier,
  build695 y diff correctos de nuevo; validación visual real pendiente.
- Ángel comunica pérdida de campos al usar Crear lista desde el aviso.
  Se refuerza identidad estable de ambos formularios mediante keys y se cambia
  selección de lista creada con actualización funcional del estado actual,
  conservando URL/título/descripción/precio. Prettier/build695/diff correctos.
  No se ha reproducido el gesto en navegador ni confirmado la resolución;
  pendiente concretar si sucede al abrir o al confirmar y validarlo con Ángel.
  No considerar CA-07 satisfecho ni cerrar mientras persista ese hallazgo.
- Ángel precisa que ocurre al abrir Crear lista. El subformulario se sitúa
  después del formulario principal, manteniendo su posición e identidad estable
  al abrir/cancelar. Guardado de lista continúa actualizando solo list_id.
  Prettier/build695/diff correctos tras esta corrección. Pendiente que Ángel
  confirme conservación de los campos; no se afirma reproducción en navegador.
- Ángel muestra Crear lista deshabilitado al estar ya abierto el formulario.
  Se retira ese bloqueo: el mismo botón abre o lleva al campo Nombre de la
  lista ya abierto, con aria-expanded/controls. El aviso usa la misma acción;
  se limpia el error de lista para evitar que el aviso recupere el foco.
  El formulario del deseo sigue montado y conserva input. Prettier/build695/
  diff correctos. Sigue pendiente comprobación manual del ciclo completo.
- Ajuste posterior solicitado por Ángel: retirar de WishList la línea con
  URL en texto y su clase CSS sin uso. El título mantiene href/target/rel y
  la edición conserva URL. Prettier/build695/diff correctos de nuevo;
  sin backend ni cambios de datos. Pendiente validación visual de Ángel.

## Confirmación final de Ángel — 24/11/2026

Ángel comunica: «ya he comprobado que todo funciona en escritorio, móvil y
teclado y que no se pierden los datos al crear una lista». Solicita registrar
pruebas y cerrar sin publicar. Los pendientes/hallazgos descritos arriba son
el historial de la comprobación; esta confirmación resuelve la validación manual.
No se inventan tamaños, pulsaciones, medidas de contraste ni trazas adicionales.

| Criterio | Evidencia disponible para cierre |
| --- | --- |
| CA-01 | Tests de lista obligatoria/sin defaults y comprobación manual del flujo. |
| CA-02 | Movimiento/agrupación/selección probados en memoria y confirmados en local. |
| CA-03 | Snapshots exactos de fixtures previos en consultas/recarga, sin migración o reasignación. No auditoría de producción. |
| CA-04 | Permisos y aislamiento de ambos espacios, invalidación y rollback en pruebas existentes; confirmación manual general. |
| CA-05 | Spec limita regalos a permisos compartidos actuales; diff sin privacidad o estados nuevos. |
| CA-06 | Confirmación explícita del desarrollador en escritorio, móvil y teclado. |
| CA-07 | Aviso destacado, apertura/reapertura y conservación del deseo revisados en código; confirmación explícita de no perder datos al crear lista; build y formato correctos. |

La retirada de URL duplicada conserva href/target/rel del título y URL editable;
incluida en la confirmación general del desarrollador. Sin observación visual
independiente del agente. Reauditoría final test_reviewer: PASS, sin bloqueos.

## Cierre local

T-01–T-06 completadas, incluidas las correcciones autorizadas durante la
validación. Las notas previas de pendientes describen el historial y quedan
resueltas por la confirmación final y la reauditoría PASS.
Inicio 11:33 y cierre 12:05 CET del 24/11/2026: 32 minutos, sin pausas comunicadas.
GitHub Projects: **0.5 h estimadas** conforme al redondeo acordado.
Spec/tasks completed y MEMORY actualizado. Sin commit, push, merge ni navegador;
publicación/integración/CI y copia a documentation requieren permiso posterior.
