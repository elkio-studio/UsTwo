---
spec: "035"
status: completed
---

# Tareas — 035

Ampliación de listas: CA-12–CA-15 y plan ampliado aprobados por Ángel;
implementación realizada, validación manual confirmada por Ángel y auditoría
final PASS. T-07/T-08/T-10 completadas; publicación pendiente.

- [x] **T-09 — Añadir edición y eliminación seguras de listas**
  - Criterios: CA-12–CA-15.
  - Tras aprobar complemento: rutas PATCH/DELETE, permisos/bloqueos, nombre,
    confirmed/recuento y transacción. Acciones de lista en menú hermano del
    enlace, popup de renombrado y confirmación única/doble según contenido.
  - Evidencia esperada: identidad/datos preservados al renombrar, sin pérdida
    parcial ni borrado ajeno, selección/foco actualizados y sin defaults.

- [x] **T-10 — Probar ampliación y revisar diseño del resultado**
  - Criterios: CA-12–CA-15.
  - WishesTest aislado: permisos, validación, renombrado, confirmación/recuento
    cambiado, borrado de lista/deseos y selección, fallo SQL/rollback; Node/build/
    formato/diff. Subagente de diseño antes/después, prueba manual de Ángel.
  - Evidencia esperada: resultados reales y validación desktop/móvil/teclado;
    no cerrar ni publicar sin revisar nuevo alcance junto al diseño anterior.

Delta local pedido por Ángel: título Editar deseo más pequeño para reducir
scroll sobrante. Revisión previa design035 APTO: clase local/hoja WishEdit,
h1 máximo 3rem con márgenes menores; sin modificar títulos globales ni ocultar
overflow. Comprobación real del ajuste pendiente, sin navegador del agente.
Prettier, build (698 módulos) y diff correctos tras el delta. Revisión posterior
design035 coherente, sin bloqueos; desaparición del sobrante pendiente de Ángel.
Ángel indica que aún queda scroll: segunda revisión previa de diseño recomienda
conservar padding inferior por Invitaciones y recuperar espacio arriba. Cabecera
solo de WishEdit con menos margen/padding, h1 margen .5rem, sin modificar controles
o esconder overflow. Prettier/build698/diff correctos. Comprobación de viewport
real pendiente; el agente no garantiza ausencia de scroll por lectura de CSS.
Ángel rechaza el aspecto del menú en la comprobación local. Se solicita revisión
de diseño de una variante más pequeña/discreta anclada al mismo botón y se
presenta alternativa al desarrollador antes de cambiar la composición.
CA-02/03 mantienen validación visual pendiente; no dar por aprobado el menú actual.
Ángel aprueba menú pequeño/discreto y elige conservar fondo rojo pequeño.
WishActions ajusta ancho al contenido, padding/huecos menores y acciones sin
estirar. Confirmación separa su ancho legible. Prettier/build698/diff correctos;
posición, confirmación y foco conservados. Pendiente comprobación renderizada.
Ángel rechaza repetición de resumen y descripción completa. Revisión previa
design035 recomienda desplegable sin duplicación y mantener botón. Aplicado
en móvil: al expandir, resumen invisible y texto completo visible una sola
vez; botón/aria siguen disponibles. Prettier/build698/diff correctos.
Elección de escritorio (desplegable sin duplicación o hover/foco) preguntada;
no cambiar a hover sin decisión explícita ni usar title como sustituto accesible.
Ángel elige desplegable sin duplicación también en escritorio. Abierto el
botón muestra Ocultar descripción y el texto completo se ve una sola vez;
en móvil solo se oculta esa etiqueta visual, conservando flecha/aria/foco.
No hover añadido ni backend cambiado. Validación renderizada pendiente.
Ángel solicita Crear lista en popup. Revisión previa design035 APTO con guardas
de foco/conservación. WishListForm usa dialog nativo con título asociado y
autofocus; Cancelar/Escape cierran sin guardar y retornan al botón estable,
éxito cierra antes de onCreated para enfocar selector y conservar resto de datos.
Error mantiene nombre/popup; loading bloquea doble envío/cierre. CSS propio
usa tema/backdrop y límites de viewport, sin paquetes nuevos. Padre conserva
estado y deja de competir por foco inicial. Prettier, Node1, build699 y diff
correctos; backend intacto y validación real del popup pendiente.
Revisión posterior design035: coherente, sin defectos técnicos bloqueantes
por lectura. Pendientes foco/Escape/error/éxito y teclado móvil en local.

Auditoría test_reviewer: BLOCKED solo para cierre por validación manual
pendiente T-07/T-08, sin defectos técnicos bloqueantes adicionales.
Preflight temporal retirado tras controles; no se borraron bases o directorios.

[Spec aprobada](spec.md), [plan aprobado](plan.md). Implementación solicitada;
en curso, sin navegador ni publicación. Revisión previa de diseño APTO.

- [x] **T-01 — Revisar alcance y precondiciones**
  - Criterios: CA-01–CA-11.
  - Confirmar aprobación del plan/ejecución, rama/registro, consumidores y diff.
    Revisión obligatoria del subagente de diseño antes de tocar UI; inventariar
    enlaces/estilos y limitar cambios fuera de Deseos a subrayados.
  - Evidencia esperada: lista justificada de archivos y revisión sin decisiones nuevas.

- [x] **T-02 — Construir fila y descripción responsive**
  - Criterios: CA-01, CA-07, CA-08, CA-10, CA-11.
  - WishRow hoja con seis zonas y tres franjas; WishList conserva vacíos/foco.
    Resumen/expansión de descripción por fila, sin navegación ni escritura.
  - Evidencia esperada: títulos sin elipsis, opcionales correctos, expansión
    a ancho completo y ausencia de solapamientos/scroll horizontal.

- [x] **T-03 — Anclar acciones y confirmar borrado**
  - Criterios: CA-02, CA-03, CA-10, CA-11.
  - Menú vertical anclado con ajuste a viewport, cierre Escape/exterior, foco,
    confirmación y estados de error/procesamiento; rojo oscuro/texto claro.
  - Evidencia esperada: no recorte, Cancelar inicial/retorno, doble envío impedido,
    petición unchanged y lista/foco estables al borrar. Listeners retirados al cerrar.

- [x] **T-04 — Compactar formulario, selector y acciones**
  - Criterios: CA-05, CA-06, CA-07, CA-09, CA-10, CA-11.
  - Columna compacta, placeholder no elegible, flecha con margen y Crear lista
    a la derecha/apilado si no cabe. cancelHref y grupo Guardar/Cancelar.
  - Evidencia esperada: abrir/cancelar/crear sin pérdida de campos, error claro
    sin lista, select etiquetado, regreso sin PATCH y foco coherente.

- [x] **T-05 — Aplicar regla transversal de subrayados**
  - Criterios: CA-04, CA-11.
  - Regla visual global e identificación explícita de externos/documentación;
    revisar overrides solo necesarios y conservar destinos/seguridad/foco.
  - Evidencia esperada: internos sin subrayado normal/hover, excepciones externas
    intactas, ningún rediseño de otras colecciones ni cambio de contratos.

- [x] **T-06 — Ejecutar controles automáticos y revisar diff**
  - Criterios: CA-01–CA-11, con límites de evidencia visual.
  - Preflight SQLite memoria antes de tests; filtro PHPUnit Deseos, Node
    existente, build, Prettier y diff. Revisar rastreados/no rastreados.
  - Evidencia esperada: resultados reales sin conexión a datos locales/producción,
    dependencias nuevas, migraciones o tests que solo reflejen la implementación.

- [x] **T-07 — Revisar diseño final y validar interacción**
  - Criterios: CA-01–CA-11.
  - Subagente de diseño de solo lectura después de implementar; Ángel confirma
    escritorio/móvil/teclado, estados, textos largos, bordes/scroll, conservación
    del deseo, Cancelar, subrayados y movimiento reducido si hay transiciones.
  - Evidencia esperada: observaciones atribuidas y limitaciones, sin inventar
    mediciones ni abrir navegador sin autorización; reparar en fase correspondiente.

- [x] **T-08 — Auditar evidencias y cerrar documentación**
  - Criterios: CA-01–CA-11.
  - ustwo-verify y test_reviewer de solo lectura, matriz final; cerrar solo con
    todos los criterios acreditados. Actualizar spec/tasks/MEMORY y horas bajo título.
  - Evidencia esperada: PASS y horas GitHub Projects con fechas/pausas; publicación,
    integración/CI y traslado a documentation pendientes de permiso específico.

## Evidencias de implementación y controles — 24/11/2026

### Ampliación aprobada: gestión de listas

- Ajuste solicitado: Editar lista adopta el aspecto textual de Editar deseo,
  mismo color/padding/altura y sin recuadro; conserva semántica de botón para
  abrir popup y foco visible. Eliminar y acciones del popup no cambian.

- Corrección de Ángel: un único menú al extremo derecho de la navegación,
  para la lista seleccionada. Grupo de enlaces flexible y botón separado;
  key por ID descarta estado al cambiar de lista. Sin cambios backend.
  Revisión previa design035 coherente y posterior APTO por lectura;
  build703 y diff correctos. Comprobación renderizada pendiente.

- T-09 implementada: WishListController update/destroy y rutas protegidas;
  nombre validado, selección solo propia/superviviente, transacción y bloqueo
  común con altas. DELETE exige confirmación/recuento vigente; FK existente
  elimina deseos de la lista, sin migración ni cambios en datos locales.
- WishListActions ofrece renombrado modal y confirmación única/doble. Solo el
  último paso envía DELETE; Cancelar inicial, procesamiento protegido y error
  de recuento obliga a reiniciar con props actualizadas. WishMenu concentra
  anclaje/foco compartido con WishActions; navegación y acciones son hermanos.
- T-10 automática: guardia previa confirma testing, SQLite :memory:, URL vacía
  y sin configuración cacheada. Deseos: **22 pruebas/546 aserciones**, sin
  fallos ni omisiones. Suite completa: **91 pruebas/2559 aserciones**, sin
  fallos; **2 omitidas** porque los seeders requieren PostgreSQL aislado.
  Pruebas nuevas cubren miembro/ajeno/revocado, nombre, conservación,
  confirmación/recuento cambiado, cascada/selección y rollback tras SQL.
- Node wishRequest: **1 prueba correcta**. Build: **703 módulos**, correcto.
  Prettier --check de componentes de la ampliación y git diff --check correctos.
- Diseño posterior design035: **APTO por lectura**, sin bloqueos concretos;
  no observación renderizada. Pendientes manuales: renombrar/cancelar/error,
  borrar lista vacía, ambos pasos con deseos, cancelar en cada paso, última
  lista, selección/foco y menús en bordes en escritorio/móvil/teclado.
- HTTP por terminal: PHP8000/acceso y Vite5173/@vite/client responden 200.
  No reinicios, navegador, commits ni publicación. T-10/T-07/T-08 siguen
  pendientes de validación manual; no cerrar todavía. Auditoría test_reviewer:
  BLOCKED únicamente para cierre por esa validación, sin defectos técnicos
  bloqueantes adicionales. Evidencias automáticas actuales incorporadas.

- T-01: spec/plan aprobados y solicitud explícita de implementación. Rama y
  registro feat/rediseno-deseos, base 97b356e, índice vacío. Subagente design035
  previo: APTO, sin bloqueos; principal único escritor. No hay cambios backend,
  rutas, modelos, schema, dependencias ni datos locales.
- T-02: WishRow mantiene clave por deseo, seis zonas en plantilla compartida
  definida por WishList y tres franjas mediante media query. Títulos completos,
  resumen y contenido completo separados; controles con estado accesible,
  texto escapado/pre-wrap y expansión local sin peticiones. Cero/null diferenciados.
- T-03: WishActions ya no usa display:contents; menú anclado absoluto, medida
  de espacio/ajuste al viewport, altura máxima y scroll interno. ResizeObserver
  y listeners de resize/scroll retirados al cerrar; Escape/exterior cierran.
  Foco tras posición visible, Cancelar inicial al confirmar y retorno al
  disparador. Confirmed/petición/callback/error existentes conservados.
  Eliminar/confirmar fondo --color-danger y texto --color-warm-linen.
- T-04: CSS local reduce márgenes de Field sin cambiar el componente compartido;
  select nativo con placeholder disabled/hidden y flecha decorativa separada.
  Crear lista a la derecha/apilado cuando no cabe; formulario estable, selección
  funcional de list_id e input conservado. Cancelar pasa al grupo de acciones
  de WishForm mediante cancelHref; mismo destino y sin submit.
- T-05: global.css elimina subrayado de navegación interna/botones, conserva
  focus-visible y permite externos/documentación identificados por data-link-kind.
  Marcados títulos WishRow, Credits, MovieInformation, ReadingInformation,
  ReadingSearch y atribución Geoapify/OSM en PlacesMap. Fuera de Deseos solo
  atributos de presentación y formato Prettier, sin cambios de lógica o destinos.
- T-06: preflight temporal antes de PHPUnit, configuración efectiva testing /
  SQLite :memory: / URL vacía / sin config cache. Filtro WishesTest|WishesDrawerTest:
  **17 pruebas, 443 aserciones, sin fallos ni omisiones**, salida 0.
- `node --test tests/Frontend/wishRequest.test.js`: **1 prueba correcta**.
  `npm.cmd run build`: correcto, **697 módulos**. Prettier de extensión aplicado
  a JSX/CSS modificados; git diff --check correcto. No nuevo runner ni tests
  de UI que simplemente reflejen la implementación; interacción aún no observada.
- Contraste calculado de colores declarados #8e2828/#f2e4cf: **6.75:1**.
  Es cálculo de tokens, no medición de píxeles renderizados, ni prueba de todos
  los estados/contrastes de la interfaz.
- T-07 pendiente: revisión posterior de diseño solicitada; primer intento
  interrumpido por límite de uso del subagente, reintentado tras «sigue» de Ángel.
  Sigue pendiente confirmación real de escritorio/móvil/teclado, menús en bordes,
  selector/crear lista sin pérdida de datos, descripción, acciones y subrayados.
- Reintento de design035 completado: sin defectos técnicos bloqueantes por
  lectura de código. Foco condicionado a posición visible, listeners/observadores
  retirados, formulario estable y contraste de tokens confirmados. No abrió
  navegador ni verificó composición renderizada. T-07 sigue pendiente solo de
  comprobación real de Ángel; no sustituirla por esta revisión.
- Comprobación HTTP por terminal: acceso local 8000, cliente Vite 5173 y módulo
  WishRow responden 200. Mismos procesos PHP/Vite conservados, sin reiniciar.
  Prettier --check y diff correctos tras la revisión. Test_reviewer solicitado.
- T-08 pendiente: no cerrar, publicar ni registrar horas finales hasta completar
  revisión final y evidencia manual. No se abrió navegador ni se detuvieron servicios.

## Cierre y matriz final — 24/11/2026, 21:29 CET

Los pendientes descritos anteriormente son evidencia histórica de las iteraciones;
quedan resueltos por la confirmación final de Ángel de escritorio, móvil y teclado.
T-07/T-08/T-10 completadas. Test_reviewer reaudita y emite PASS, sin bloqueos.

| Criterios | Evidencia final |
| --- | --- |
| CA-01, CA-07, CA-08 | Filas compactas, descripción sin duplicación y adaptación móvil: validación humana de Ángel. |
| CA-02, CA-03, CA-10 | Menús, confirmaciones, rojo y foco/teclado: revisión de diseño y validación humana. |
| CA-04 | Subrayados internos eliminados y excepciones externas/documentación conservadas: diff y validación humana. |
| CA-05, CA-06, CA-09 | Selector, popup Crear lista sin perder datos y acciones de formulario: revisión y validación humana. |
| CA-11 | Regresión Deseos 22 pruebas/546 aserciones y suite 91/2559 sin fallos; 2 omisiones por PostgreSQL aislado. |
| CA-12 | Renombrado validado por miembros, identidad/deseos preservados: pruebas backend y validación humana. |
| CA-13 | Confirmación única/doble y cancelación: revisión del flujo, validación backend y humana. |
| CA-14 | Aislamiento, membresía/recuento vigente y rollback: pruebas SQLite en memoria. |
| CA-15 | Menú único para seleccionada, actualización/selección/foco, Editar uniforme: revisión de diseño y validación humana. |

Node1, build703, Prettier y diff correctos. Sin navegador del agente, commits ni
publicación. Horas y cálculo registrados en spec.md; sin tiempos por tarea.
