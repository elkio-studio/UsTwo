---
spec: "035"
status: approved
---

# Plan de implementación — 035

El alcance inicial de UI está aprobado e implementado. El complemento
«Ampliación de gestión de listas» propone contratos/backend nuevos solicitados
después; solo ese complemento espera aprobación técnica. Sus excepciones
sustituyen los límites iniciales de no tocar controllers/routes para listas.

Ángel aprueba expresamente el plan y solicita implementarlo y verificar con
SQLite el 24/11/2026, sin navegador ni publicación. Revisión de diseño previa:
APTO, sin bloqueos. Esta aprobación sustituye los pendientes históricos del borrador.

## Resumen

Implementar la [spec aprobada](spec.md), conservando backend, datos y permisos
de Deseos. Sin nuevas dependencias, migraciones, APIs, assets ni contratos de
servidor. Plan aprobado y ejecución solicitada por Ángel; resultados de
implementación y pruebas registrados en tasks.md. No autoriza publicación.

## Componentes afectados y responsabilidades

- `resources/js/pages/Wishes/WishList/`: mantener lista/estado vacío y destino
  de foco tras borrar; delegar cada fila sin duplicar información.
- `resources/js/pages/Wishes/WishRow/`: componente hoja nuevo, JSX y CSS Module,
  para las seis zonas y estado local de descripción de cada deseo. Separarlo
  evita concentrar composición y expansión de todas las filas en WishList.
  Clave estable wish.id, sin datos persistidos ni limitación a una única fila abierta.
- `WishActions/`: transformar presentación en menú emergente anclado y
  confirmación contextual. Conservar petición, errores y onDeleted existentes.
- `WishForm/`: compactar, componer selector/Crear lista, placeholder no elegible
  y agrupar acciones, conservando estado/validación del formulario.
- `WishEdit/WishEdit.jsx`: pasar la URL actual de cancelar al grupo de acciones
  de WishForm; eliminar el Cancelar suelto, sin cambiar su destino.
- `WishListForm/`: solo ajustes locales necesarios de espaciado/retorno de foco,
  sin alterar su solicitud JSON. Si no requiere CSS propio, no crear CSS vacío.
- `resources/css/global.css` y hojas/JSX de enlaces identificados en el inventario:
  únicamente clasificación visual y eliminación de subrayados internos. No
  compactar otras pantallas ni cambiar sus URLs, navegación o permisos.
- Documentos de 035, MEMORY y regla de revisión en AGENTS/proceso SDD ya solicitada.

No tocar controllers/models/routes/migraciones/manifiestos. No crear servicios,
hooks o utilidades que no tengan responsabilidad real; estilos locales y funciones
nombradas según convenciones. Formato de Prettier de la extensión instalada.

## Contratos existentes e interfaces locales

WishRow recibe wish, spaceId y callback de borrado: usa id/title/url/description,
price/currency/author/status actuales. Renderizar texto escapado, nunca HTML
de descripción. No añadir campos al payload ni guardar expansión en la base.

WishForm añade un cancelHref opcional para editar. WishEdit conserva su back
de lista origen y pasa esa misma URL. Alta sin cancelHref mantiene el flujo
actual. El grupo está dentro del área de acciones, sin formularios anidados.
Mantener POST/PATCH/list creation/DELETE y `confirmed` exactamente como ahora.
La regla de subrayados modifica presentación, no contratos de navegación.

## Composición del listado y descripción

- WishList conserva ul/li y estado vacío. Cada WishRow usa CSS Grid con seis
  zonas compartiendo la misma plantilla y anchos coherentes a nivel del listado:
  no dimensionar columnas auto independientemente por el contenido de cada fila.
  Zonas: título, resumen, precio, autor, estado,
  acciones. Sin bordes de columna, cuadrícula visible, viñetas decorativas o
  tarjetas nuevas. Dedicar ancho flexible al título/resumen; min-width:0 y
  ajuste de palabras largas para no solapar acciones.
- Título externo conserva href/target/rel. No enlazar la fila completa ni
  repetir URL. Precio cero se distingue de null; null conserva «Sin precio indicado».
- Resumen de descripción con botón nativo y estado abierto/cerrado, nombre
  accesible por deseo y aria-expanded/controls. Mostrar el inicio con indicación
  de continuación si no cabe; nunca truncar el título.
- Texto completo condicional bajo la fila, grid-column:1/-1, white-space:pre-wrap
  y overflow-wrap. Mantener el foco en el control al desplegar/plegar; ninguna
  navegación ni mutación. Sin descripción no renderizar un control vacío.
- Móvil: título/acciones, datos precio/autor/estado y resumen si existe. Texto
  abierto ocupa ancho disponible. No scroll horizontal; filas pueden crecer
  por texto, sin convertirlas en tarjetas con fondo.
- Anchos/breakpoints/longitud del resumen se ajustan localmente a la composición
  aprobada; no requieren decidir otra UX. No introducir animaciones obligatorias.

## Menú anclado y borrado

WishActions deja display:contents y pasa a contenedor de anclaje propio en la
zona de acciones. Panel fuera del flujo de datos, con posición absoluta respecto
al botón, sin paquetes ni un sistema global de overlays. Evitar overflow:hidden
en ancestros del listado; no recortar por compactar las filas.

Medir disparador/panel al abrir y cuando cambie menú/confirmación o viewport;
elegir arriba/abajo y alineación con espacio disponible, limitar ancho/alto al
viewport y permitir scroll interno si una confirmación no cabe. Recalcular en
resize/scroll mientras esté abierto; retirar listeners al cerrar/desmontar.
No refactorizar menús de otras colecciones. Si la estructura impide cumplir
sin recorte, elevar el hallazgo antes de introducir otro sistema de overlays.

Usar botón disparador identificado por deseo, aria-expanded/controls y acciones
nativas verticales; no role=menu sin implementar su contrato de teclado.
Editar sigue Link y Eliminar abre confirmación en el panel. Llevar foco al
primer control al abrir; Cancelar al confirmar. Escape/pulsación exterior
cierran sin petición y con retorno al disparador, salvo durante procesamiento.
Cancelar retorna y no borra; error mantiene confirmación y datos visibles.
Tras éxito conservar callback/foco estable del título de lista y selección
de la 033. No quitar deseos optimistamente ni cambiar confirmed.

Eliminar y Confirmar borrado con fondo rojo oscuro, texto claro y foco visible,
usando variables del tema. Ajustar legibilidad/contraste, no solo tono estético.
Cancelar mantiene estilo secundario y su significado; el rojo no sustituye texto.

## Formularios y selector

Delta posterior solicitado por Ángel: WishListForm se presenta en dialog nativo
showModal (patrón ya usado por Shelf), con CSS Module propio y backdrop del
tema, sin dependencia ni cambios de ruta/JSON. Su formulario es hermano del
deseo, no anidado. Dialog gestiona foco inicial; quitar el enfoque anticipado
del padre al abrir. Cerrar antes de devolver foco al cancelar o seleccionar al
crear. Escape/Cancelar no cierran durante envío; error mantiene nombre/popup.
El fondo no cancela por pulsación. Mantener viewport/scroll interno si hace falta.

- Mantener WishForm y su estado montados al abrir/cerrar WishListForm. Conservar
  keys y actualización funcional de list_id; no cambiar useForm/defaults ni resetear.
- Mantener select nativo etiquetado. Estado inicial vacío con opción de placeholder
  disabled/hidden: sirve para mostrar indicación inicial pero no figura como lista
  elegible. Sin listas, indicación y Crear lista disponibles; no autoasignar una.
- Contenedor de selector y botón con grid/flex; botón a la derecha y debajo
  solo cuando no quepa. Flecha decorativa local con espacio interior suficiente
  mediante CSS, sin nuevos assets; no bloquear clics del select ni inventar combobox.
- Compactar desde CSS de WishForm, con variables existentes y overrides locales
  de márgenes/gaps de campos y textos auxiliares. No cambiar Field/globalmente
  para que el resto del proyecto se compacte de forma accidental.
- Guardar/Cancelar en grupo con separación del último campo y entre controles;
  apilable en móvil. Cancelar Link interno sin subrayado; no dispara submit.
- Aviso destacado sin lista permanece cerca de Guardar, role=alert y foco,
  con acción Crear/Elegir lista. Guardar habilitado mientras no procesa;
  guardia evita petición sin lista. Errores por campo e input permanecen.
- Crear lista abre/enfoca el campo, incluso ya abierto. Cancelar devuelve foco;
  crear añade/selecciona la nueva sin alterar URL/título/descripción/precio.

## Regla transversal de subrayados

Inventariar enlaces y reglas de text-decoration en CSS/JSX antes de editar.
Clasificar por destino real, no por la presencia del símbolo @ ni por si el
componente es Link/a. Las URLs absolutas del mismo origen siguen siendo internas.

Establecer en global.css ausencia de subrayado en texto/controles/enlaces internos,
incluido hover, manteniendo color/estilo y focus-visible. Identificar explícitamente
en los anchors que lo necesitan el destino externo o documentación mediante un
atributo de presentación local (por ejemplo data-link-kind), para conservar el
subrayado permitido sin inferencias frágiles del esquema http/https.
No crear una librería de enlaces ni cambiar componentes de navegación masivamente.

Revisar overrides de módulos que reintroduzcan subrayado interno, conservando
los que ya cumplen. Páginas candidatas a clasificar: Credits, información de
catálogos de Películas/Series/Lecturas y título del deseo; confirmar lista exacta
con el inventario. No alterar href/target/rel, contratos, bordes o separadores.
Solo modificar esos archivos si hace falta para CA-04 y registrar su motivo.

## Fases

1. Aprobación del plan/ejecución y revisión de diseño previa obligatoria;
   inventario del diff y consumidores, enlaces y estilos. Aislar impacto UI.
2. Fila responsive y descripción desplegable, sin cambiar datos.
3. Menú/confirmación anclados, color y foco, preservando borrado existente.
4. Compactación de formularios, selector y acciones, sin pérdida de input.
5. Subrayados transversales y revisión de límites para otros cajones.
6. Controles automáticos y revisión de diseño del resultado, luego comprobación
   real de Ángel escritorio/móvil/teclado antes de revisión final y cierre.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Escritorio, varias filas con títulos/autor largos y precio null/cero: seis zonas, sin elipsis de título, solapamiento ni cuadrícula. |
| CA-02 | Menú junto a bordes superiores/inferiores, resize/scroll, teclado/toque, Escape/exterior y confirmación sin recorte. |
| CA-03 | Fondo rojo oscuro/texto claro, foco y legibilidad; Cancelar no envía, fallo de borrado no retira deseo. |
| CA-04 | Inventario y revisión de CSS/anchors, navegación interna y fuente externa/documentación en otros cajones, hover y foco. |
| CA-05 | Sin listas, una y varias listas; placeholder no elegible por puntero/teclado, sin autoasignación, flecha separada. |
| CA-06 | Abrir/reabrir/cancelar/crear lista con campos ya rellenados, datos idénticos salvo list_id; selector junto a botón o apilado si falta ancho. |
| CA-07 | Comparación con capturas del problema, menor separación vertical, etiquetas/errores/aviso y controles accesibles sin hover. |
| CA-08 | Sin descripción, corta/larga/multilínea; expansión independiente del enlace, a ancho completo, teclado/estado/foco y sin petición. |
| CA-09 | Editar, Cancelar separado y retorno sin guardar, Guardar mantiene destino/lista actual; acciones móviles sin confusión. |
| CA-10 | Tres franjas móviles, zoom/textos largos, toque/teclado sin scroll horizontal y sin tarjetas añadidas. |
| CA-11 | Pruebas existentes y comprobación de errores, persistencia/recarga/historial, permisos y autoría/estado conservados. |

Antes de ejecutar PHPUnit: preflight efectivo testing, SQLite :memory:, URL
vacía y sin configuración cacheada, antes de RefreshDatabase; si falla detener
sin limpiar cachés ni tocar .env/base local. Ejecutar
`vendor/bin/phpunit --filter 'WishesTest|WishesDrawerTest' --display-skipped`
con PHP 8.5/pdo_sqlite; `node --test tests/Frontend/wishRequest.test.js`;
`npm.cmd run build`, Prettier de extensión y git diff --check.

No instalar runner ni escribir tests que dupliquen CSS/JSX. Añadir pruebas solo
si aparece lógica nueva comprobable con valor real; usar infraestructura existente,
sin pretender que tests HTTP o build prueban foco/composición. No nueva migración.
Si un fallo o cambio de alcance lo justifica, ampliar controles proporcionalmente.
PostgreSQL/CI externo quedan a Ángel; no usar Supabase para verificar UI.

Revisión del subagente de diseño antes de implementar y después del resultado,
de solo lectura. Registrar evidencia real del desarrollador y limitaciones;
no abrir navegador sin autorización ni detener Vite/PHP para compilar.

## Documentación, compatibilidad y publicación

Actualizar spec/tasks y MEMORY con evidencias por criterio. AGENTS y proceso
SDD ya recogen la obligación del revisor, sin duplicarla en un informe nuevo.
No actualizar README por este diseño. Horas GitHub Projects bajo título al
cerrar, con fechas/pausas/cálculo final y sin horas por tarea.

Conservar .env, SQLite local, servidores y registros. Sin cambios backend,
schema o deploy necesarios para esta fase. Commit/push/merge/despliegue requieren
permiso específico posterior; al integrar actualizar destino. Tras integración,
trasladar solo docs autorizados a documentation actualizada, conservando sus
reglas propias y sin copiar código. No borrar rama antes de confirmación.

## Riesgos y revisión

### Ampliación de gestión de listas — aprobada

Reglas confirmadas por Ángel en spec, CA-12–CA-15. Este complemento añade
operaciones backend al alcance inicial. Ángel aprueba el complemento y solicita
su implementación y verificación, sin navegador ni publicación.

Contratos propuestos, dentro del grupo auth/RequireSpaceMembership existente:

| Operación | Contrato |
| --- | --- |
| PATCH /espacios/{space}/deseos/listas/{listId} | WishListController::update, ID numérico; name obligatorio/trim/max255; conserva lista/deseos y redirige al listado con selección válida. |
| DELETE /espacios/{space}/deseos/listas/{listId} | WishListController::destroy; confirmed accepted y confirmed_count entero >=0, igual al recuento actual; borrar atómicamente lista y sus deseos, redirigir a selección superviviente o estado vacío. |

No nueva ruta de consulta: las props actuales contienen nombre e IDs/deseos.
Enviar selected_list_id opcional para mantener selección al operar otra lista;
aceptarlo solo si pertenece al espacio y sobrevive. Si no es válido, elegir
la primera propia superviviente o URL sin list; nunca redirigir a un ID ajeno.

Backend: reutilizar patrón de WishListController::store para transacción y
bloqueo de Space (incluida escritura inocua SQLite), revalidar miembro, resolver
lista por espacio y lockForUpdate. Update cambia solo name. Destroy compara
recuento bajo bloqueo y rechaza confirmed_count desfasado con error comprensible;
después elimina en transacción usando FK cascade ya existente, sin migraciones.
Ante QueryException devolver error conservando input/datos, sin exponer SQL.
Bloquear en el mismo orden que altas/movimientos de deseos para evitar carreras.

Frontend: un único WishListActions (JSX/CSS) para la lista seleccionada al extremo
derecho de la navegación, con key por ID para descartar estado al cambiar,
reutilizando patrón compacto/anclaje de WishActions sin introducir un sistema
global. El anclaje/foco compartido se concentra en el componente local WishMenu,
consumido por WishActions y WishListActions, evitando duplicar ese comportamiento.
WishesView agrupa enlaces flexibles y botón como hermanos y ofrece foco estable.
Renombrado en dialog nativo gestionado por WishListActions con useForm PATCH
y redirección Inertia del contrato. WishListForm conserva su alta JSON; no
mezclar ambos transportes ni estado de la ficha del deseo con renombrado.

Confirmación: usar dos pasos solo cuando list.wishes.length>0; guardar snapshot
del recuento mostrado al iniciar. Solo último paso envía confirmed/confirmed_count;
no recalcular silenciosamente el número aceptado justo al enviar. Un error de
recuento cierra la fase de confirmación y exige iniciarla de nuevo con props
actuales obtenidas del servidor, manteniendo visible la explicación.
Durante petición impedir doble envío/cierre; éxito actualiza selección/foco.
Cancelar/Escape no mutan. Mantener pequeño fondo rojo de Eliminar/Confirmar.

Ampliar WishesTest (infraestructura existente) con edición por otro miembro,
nombre vacío/largo y conservación, invitado/ajeno/revocado, ID de otra lista,
borrado vacío/con contenido/última lista, confirmación ausente/recuento distinto,
selección válida y rollback tras SQL (snapshot de lista y deseos). Probar
recuento cambiado entre confirmación y envío sin borrar contenido nuevo.
Ejecutar con guardia previa y SQLite :memory:, Node existente/build/formato/diff;
pruebas manuales de ambos pasos, cancelar, renombrado y móvil/teclado.

Limitación expresa: confirmed_count detecta cantidad desfasada, no sustitución
con idéntico recuento. No se propone snapshot de IDs ni versiones nuevas.
Revisión del complemento plan_reviewer: sin defectos técnicos adicionales;
la aprobación expresa de contratos/selección/popup ya ha sido recibida.

Dependencias: T-07/T-08 (validación/cierre) pasan a esperar T-09/T-10 nuevas.
Revisión previa de diseño realizada, propuestas de popup/navegación aquí
presentadas y revisión SDD realizada. Plan ampliado aprobado por Ángel;
implementación autorizada y registrada en tasks.md.

- Regresiones de datos al compactar: no desmontar/reinicializar formulario.
- Menú recortado o foco perdido: medir espacio, respetar procesamiento y probar
  con texto largo/bordes/scroll, sin migrar otros menús a un patrón nuevo.
- Regla de subrayados excesiva: inventario y clasificación explícita, foco
  intacto; no usar selector indiscriminado que cambie bordes o enlaces externos.
- Aspecto compacto ilegible: conservar área de toque/legibilidad y validar con
  Ángel en tamaños reales; no cerrar solo con build.

Revisión del subagente de diseño: APTO, sin bloqueos. plan_reviewer: PASS,
sin bloqueos. Aprobación expresa e implementación solicitada recibidas;
la ejecución se registra en tasks.md. Publicación no autorizada.
