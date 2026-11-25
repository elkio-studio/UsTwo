---
id: "035"
title: "Rediseñar la interfaz de Deseos"
status: completed
---

# 035 — Rediseñar la interfaz de Deseos

**GitHub Projects: `9` h estimadas.**

Inicio: 24/11/2026, 12:28 CET.
Rediseño vinculado al cajón de Deseos de EP-03, no una nueva historia funcional
asumida. Referencias: [alta 031](../031-alta-deseos/spec.md),
[consulta 032](../032-consulta-deseos/spec.md),
[edición/borrado 033](../033-editar-borrar-deseos/spec.md) y
[listas y ajustes 034](../034-categorias-deseos/spec.md).

## Problema y objetivo

Rediseñar Deseos con una presentación compacta, datos alineados, acciones
contextuales y formularios claros, conservando las reglas de 031–034.
Las indicaciones, propuestas de diseño y spec completa de esta versión están
aprobadas expresamente por Ángel; el rediseño y su ampliación están implementados y verificados.
Cierre local autorizado por Ángel tras su validación manual; publicación pendiente.

## Decisiones confirmadas

- Crear feat/rediseno-deseos desde dev y preparar esta spec.
- Recoger las indicaciones de Ángel en un único documento, distinguiendo
  decisiones confirmadas, propuestas y cuestiones pendientes.
- Preparar plan/tareas tras la aprobación global recibida; no implementar,
  hacer commit o publicar durante esta planificación.
- Al cerrar, colocar las horas GitHub Projects bajo el título, según el proceso SDD.
- Ángel aprueba expresamente las propuestas del subagente de diseño el
  24/11/2026: seis zonas de escritorio, descripción desplegada a ancho completo,
  tres franjas móviles, menú anclado, formulario vertical compacto, acciones
  separadas, fondo rojo oscuro/texto claro y títulos largos sin elipsis.

## Base funcional que debe respetar el rediseño

La implementación vigente permite listas explícitas sin lista por defecto,
alta manual de deseos con URL/título, descripción/precio EUR opcionales,
autor original y consulta por lista. Las listas son la clasificación.
Los miembros del espacio pueden editar/mover/borrar sus deseos compartidos,
con confirmación de borrado y aislamiento por estantería.

El título abre la URL, que no se repite como texto en la consulta. La edición
conserva la URL. Guardar sin lista muestra aviso destacado y permite crearla
sin perder los campos del deseo; se mantienen errores y accesibilidad.

Estas reglas describen la base existente; cualquier variación que Ángel
solicite se documentará expresamente antes de aprobarla. No se presume que
rediseñar autorice nuevos datos, permisos o funcionalidades.

## Cambios de diseño solicitados

### Dirección general: compacto

Ángel pide reducir los grandes huecos y mantener todo compacto. Conservar
legibilidad, errores visibles y controles utilizables por toque/teclado.
No se han fijado medidas concretas ni aprobado reducir arbitrariamente las
zonas de interacción. Las capturas muestran el estado que se quiere cambiar.

### Listado — primera captura

- Cada deseo en una fila compacta, con título, resumen de descripción, precio, autor, estado y acciones
  alineados entre filas como columnas imaginarias de Excel, sin cuadrícula ni
  bordes de columnas visibles. No apilar esos datos en párrafos separados.
- Mantener el título como enlace y no repetir la URL en texto.
- Descripción, cuando exista: mostrar inicialmente solo el principio en un
  resumen compacto y permitir desplegar el texto completo y volver a plegarlo.
  El control expone su estado y funciona por toque/teclado; no depende de hover.
  Conservar el contenido íntegro, sin modificar lo almacenado ni usar el
  despliegue de la descripción para abrir el enlace externo del título.
- La descripción completa se despliega debajo de la fila a todo su ancho y
  conserva saltos de línea. El resumen plegado ocupa su zona alineada.
- Ajuste posterior pedido por Ángel: al desplegar en móvil, ocultar el resumen
  repetido manteniendo el botón de plegar y una sola copia visible del texto.
  Ángel elige también en escritorio desplegable sin duplicación: abierto,
  el control muestra Ocultar descripción en vez del resumen. No se añade hover.
- Títulos largos permiten saltos de línea sin elipsis; la fila puede crecer.
  Autor y otros textos se ajustan sin desbordar ni ocultar información.
- Editar/Eliminar se despliegan desde el botón de tres puntos, anclados a él,
  en lugar de aparecer como botones sueltos dentro del contenido de la fila.
- Ajuste posterior validado por Ángel en local: menú pequeño y discreto,
  ajustado al ancho de las dos opciones, menos relleno y sin un gran botón
  rojo extendido por todo el panel. La confirmación conserva ancho propio
  legible; no obliga a ensanchar el menú de acciones.
  Ángel elige mantener Eliminar con fondo rojo pequeño, no solo texto rojo.
- Botones Eliminar de Deseos, incluida confirmación destructiva, con fondo
  rojo oscuro, texto claro y foco visible. El tono definitivo se
  probará y validará después, no se fija todavía un color concreto.
  No cambia la exigencia de confirmar antes de borrar.

### Listas — segunda y tercera capturas

- «Elige una lista» debe ser una indicación inicial, no una opción seleccionable
  del menú. Las opciones son las listas reales. No seleccionar automáticamente
  la primera ni crear una lista por defecto para resolver el estado inicial.
- La flecha del selector debe quedar más separada del borde derecho.
- Crear lista debe estar a la derecha del selector, en su misma fila con ancho
  suficiente. Mantener la creación explícita y conservar todos los datos del
  deseo al abrir, cancelar o terminar de crear la lista.
- Ajuste posterior solicitado por Ángel: Crear lista abre un popup modal en
  lugar del formulario inline. Nombre enfocado al abrir, Cancelar/Escape sin
  mutación y retorno al botón estable; al crear, cerrar y seleccionar la lista
  nueva sin perder el deseo. Error mantiene popup/nombre y mensaje; durante
  envío impedir doble petición y cierre ambiguo. Sin nuevas dependencias.

### Formulario — cuarta captura

- Compactar espacios entre Lista, URL, Título, Descripción, Precio, texto
  orientativo y Guardar. Acercar etiquetas a sus controles y reducir huecos
  verticales, manteniendo campos, validación, opcionales y errores visibles.
- Conservar una columna vertical de campos, sin rejilla nueva. En móvil,
  Crear lista pasa debajo del selector solo cuando no caben juntos.
- En edición, separar más Cancelar de Guardar cambios: no dejarlo pegado al
  borde inferior del botón. Mantenerlo como acción secundaria claramente
  distinguible, con espacio para activarlo sin confundirlo con guardar.
  Cancelar conserva su navegación sin guardar; sin subrayado por ser interna.
- Ajuste posterior solicitado por Ángel en la prueba local: reducir ligeramente
  el título Editar deseo y su espacio para evitar el pequeño scroll sobrante
  de esa página. Solo edición; no ocultar overflow ni impedir scroll necesario
  por errores, zoom, textos largos o pantallas bajas.
- Guardar/Cancelar forman un grupo de acciones separado de los campos, con
  espacio claro entre ellos, y pueden apilarse cuando falte ancho en móvil.

### Adaptación móvil aprobada

Cada deseo se organiza en tres franjas compactas: título y tres puntos;
precio/autor/estado; resumen desplegable de descripción si existe. No crear
tarjetas con fondo propio ni necesitar desplazamiento horizontal para operar.
La descripción abierta ocupa el ancho disponible bajo esas franjas. Textos
largos pueden aumentar su altura sin ocultar controles o perder información.

### Subrayados — alcance transversal solicitado

Ángel precisa la regla anterior: reservar el subrayado para enlaces a destinos
externos a la aplicación o a la documentación de la propia web. No subrayar
texto normal, botones ni enlaces internos de navegación, tampoco en hover.
El título de un deseo apunta a una tienda externa, por lo que pertenece a la
excepción y puede llevar subrayado; esta precisión sustituye la petición
inicial de quitárselo indiscriminadamente.

Mantener enlaces operables y reconocibles y el foco visible de teclado. No
quitar bordes, separadores o indicadores de foco por confundirlos con un
subrayado. La regla afecta a toda la aplicación, no solo a Deseos, tal como
Ángel pidió al decir «en general en la web». Fuera de Deseos solo se revisa
ese tratamiento de subrayados, no se compactan ni rediseñan otras pantallas.

## Alcance de la entrega definida

- Rediseñar el listado, alta y edición de Deseos y la creación contextual de
  listas, manteniendo el diseño aprobado y la identidad existente de UsTwo.
- Preservar estados vacíos, procesamiento, errores, confirmaciones e input.
- Adaptación de escritorio/móvil, teclado, foco y movimiento reducido.
- Aplicar la regla de subrayados a toda la web; fuera de Deseos no hay rediseño.
- Incorporar la revisión de diseño obligatoria documentada en AGENTS.md y
  proceso-sdd.md. La preparación actual sigue siendo solo documental.

## Alcance excluido sin aprobación adicional

- Implementar durante la preparación de spec, assets/dependencias nuevos,
  refactors estructurales, cambios de esquema o de contratos de servidor.
- Estados comprado/pendiente editables, tachado o privacidad especial de regalos.
- Extracción de URLs, imágenes, seguimiento de precios o servicios externos.
- Cambios implícitos de reglas, permisos o datos por motivos de presentación.
- Acceder/modificar bases locales o de producción, abrir navegador o parar
  servidores durante la preparación documental.

## Actores y flujos

Ángel aprueba el diseño y valida la spec/plan; el principal es el único escritor
y el subagente de diseño revisa sin modificar archivos. Actores de aplicación:
miembros de la estantería; usuarios ajenos siguen sin acceso.

1. El miembro consulta una lista: deseos alineados en escritorio o tres franjas
   en móvil, nombre de lista y estado vacío actuales cuando corresponda.
2. El título abre su URL externa con protección actual. El resumen de
   descripción es un control distinto; desplegar/plegar no abre esa URL.
3. Tres puntos abre un menú anclado hacia el espacio disponible, con Editar
   y Eliminar en vertical. Escape o pulsación exterior lo cierran sin acción.
   Restaurar foco al disparador si se cierra/cancela; no recortar el menú.
4. Editar mantiene datos precargados y reglas actuales. El grupo Guardar/
   Cancelar distingue las acciones; cancelar navega sin enviar cambios.
5. Eliminar abre la confirmación en el mismo contexto; enfocar Cancelar al
   abrir, bloquear doble envío y conservar error si falla. Tras éxito mantener
   lista/estado vacío y foco estable conforme a la 033, sin borrar la lista.
6. En alta se elige una lista real o se crea explícitamente. Crear/abrir/
   cancelar no reinicia el deseo; al crear, seleccionar la nueva lista sin
   alterar otros campos. Guardar sin lista muestra el aviso destacado vigente.

## Estados, errores y accesibilidad

- Sin descripción: no hay control vacío. Sin precio: «Sin precio indicado».
  Sin deseos/listas: conservar estados vacíos y accesos actuales, sin defaults.
- Labels y opcionales siguen visibles, errores asociados al campo y aviso de
  lista cerca de Guardar. Conservar input y clasificación ante validación o fallo.
- Cambios de ficha no alteran autor/estado. Aislamiento y membresía actuales,
  sin nuevas capacidades por mostrar o esconder controles.
- Menú y descripción operables con toque/teclado, nombre accesible y estado
  abierto/cerrado, foco visible. Abrir/cerrar/cancelar no envía mutaciones.
- Controles siguen utilizables sin hover; menú/confirmación ajustados al
  viewport y textos largos sin desbordamiento horizontal.
- No se prescribe animación nueva; cualquier transición de implementación
  respeta movimiento reducido y no condiciona el acceso a acciones.

## Criterios de aceptación

### Ampliación funcional de listas solicitada durante la validación

Ángel pide editar/eliminar listas desde un único botón de tres puntos al
extremo derecho de toda la navegación, aplicado a la lista seleccionada,
y confirma expresamente: cualquier miembro de la estantería puede hacerlo;
lista con deseos exige doble confirmación y se elimina con todos sus deseos.
La ampliación sustituye para este caso el límite inicial de no añadir operaciones
de servidor, sin autorizar cambios de esquema, permisos ajenos ni privacidad.
Ángel aprueba expresamente el plan ampliado y solicita implementarlo, con
SQLite, revisión de diseño, sin navegador ni publicación.

- Grupo de enlaces y único botón de acciones son controles hermanos: abrir
  menú no navega ni selecciona accidentalmente otra lista. Menú compacto anclado
  con Editar/Eliminar, mismo estilo y accesibilidad del menú de deseos.
- Editar renombra en popup coherente con Crear lista: nombre actual precargado,
  obligatorio tras trim y máximo 255 como el alta. Cancelar/Escape no guarda;
  éxito conserva ID, deseos, autorías y selección. No añadir emojis/campos.
- Eliminar lista vacía: confirmación única identificando nombre; Cancelar inicial.
  Con deseos: primer paso identifica lista y número de deseos, segundo advierte
  explícitamente que se borran todos definitivamente. Solo el segundo confirmar
  envía borrado. Cancelar/cerrar en cualquier paso no modifica datos.
- Antes de borrar, el servidor revalida membresía, lista del espacio y número
  de deseos confirmado. Si cambia el recuento, rechazar y renovar confirmación,
  nunca ampliar silenciosamente el alcance aprobado. Fallo revierte lista/deseos.
- Al borrar la seleccionada, volver a otra lista propia existente o al estado
  vacío sin crear una por defecto ni mantener ?list de una lista inexistente.
  Al borrar otra, conservar selección válida actual. Foco a un control estable
  de la navegación si desaparece el disparador. Editar/borrar no cruza espacios.

- **CA-12.** Un único botón de tres puntos al extremo derecho gestiona la lista
  seleccionada, independiente de los enlaces; cualquier
  miembro puede renombrarla en popup con validación y conservación de ID/deseos.
- **CA-13.** Lista vacía requiere una confirmación; con contenido, dos pasos y
  aviso del número/borrado de todos sus deseos. Cancelar/cerrar no envía DELETE.
- **CA-14.** Revalidación de permisos, lista y recuento confirmado en servidor;
  lista ajena/membresía perdida o recuento cambiado no permite borrado. Error
  conserva datos previos sin cambios parciales ni afectación a otras listas.
- **CA-15.** Listado/nombre/selección se actualizan tras editar/borrar; sin lista
  eliminada seleccionada ni defaults. Menú/popup/confirmaciones accesibles por
  toque/teclado, foco/error/procesamiento coherentes con las acciones de deseos.

La guardia de recuento detecta cambios de cantidad, no sustituciones de deseos
que mantengan la misma cantidad. No se introduce historial ni control de
versiones de la lista. Revisión spec_reviewer del alcance ampliado: PASS;
complemento técnico aprobado expresamente por Ángel e implementado. La
validación manual de las nuevas interacciones sigue pendiente.

Criterios de la versión aprobada:

- **CA-01.** En escritorio cada deseo presenta seis zonas alineadas: título,
  resumen, precio, autor, estado y acciones, sin cuadrícula ni columnas dibujadas.
  Títulos largos se ajustan sin elipsis ni pérdida de información.
- **CA-02.** Editar/Eliminar se despliegan anclados al botón de tres puntos,
  sin recorte y con acceso por teclado/toque y confirmación de borrado actual.
- **CA-03.** Eliminar/confirmar borrado usan fondo rojo oscuro, texto claro
  legible y foco visible; el tono se valida visualmente. Cancelar no modifica registros.
- **CA-04.** Solo enlaces externos o a documentación de la propia web pueden
  llevar subrayado. Textos normales, botones y navegación interna no lo muestran
  en reposo ni hover; conservar reconocimiento y foco de teclado. Los títulos
  de deseos con destino externo siguen la excepción.
- **CA-05.** «Elige una lista» no es una lista seleccionable; no hay selección
  automática. La flecha mantiene separación respecto al borde derecho.
- **CA-06.** Crear lista a la derecha del selector con ancho suficiente;
  pasa debajo solo cuando no cabe. Abrir/cancelar/crear no borra input ni
  bloquea volver al formulario; no se genera una lista por defecto. Apertura
  en popup modal con título accesible y foco al nombre, cancelación/Escape,
  error persistente y cierre/selección de nueva lista tras éxito.
- **CA-07.** Formulario y listado reducen huecos verticales actuales sin perder
  etiquetas, errores, campos o acceso a acciones.

- **CA-08.** Si hay descripción, se ve su inicio como resumen y se puede
  desplegar/plegar el texto completo con toque o teclado y estado reconocible.
  Sin descripción no se muestra un desplegable vacío. No altera el título
  enlazado ni guarda cambios en el deseo. Texto completo bajo la fila a todo
  su ancho, con saltos conservados; plegar recupera el resumen compacto.
- **CA-09.** En edición, Guardar cambios y Cancelar tienen separación visible
  y áreas de interacción distintas, también en móvil y teclado. Cancelar no
  envía cambios ni muestra subrayado como navegación interna.

- **CA-10.** En móvil, tres franjas: título/acciones, precio/autor/estado y
  descripción si existe. Sin tarjetas con fondo propio ni scroll horizontal;
  textos largos no ocultan acciones y todos los flujos admiten toque/teclado.
- **CA-11.** Vacíos, errores, procesamiento, confirmación, permisos, autoría,
  estado, datos al crear listas y navegación/recarga mantienen el comportamiento
  de 031–034. Ninguna interacción meramente visual modifica registros.

## Decisiones pendientes y estado

Sin decisiones funcionales o de composición pendientes en esta versión tras
la aprobación de las propuestas. Espaciado, anchos, extensión del resumen,
breakpoints y tono exacto de rojo son detalles locales a ajustar en el plan/
implementación autorizados y validar por legibilidad, sin cambiar lo aprobado.
Revisión documental y aprobación global de la spec completadas; plan e
implementación mantienen sus propias autorizaciones posteriores.

### Revisión de diseño y usabilidad — propuestas aprobadas

Ángel solicita esta revisión mediante subagente. Encargo ejecutado por
design035, con contrato de solo lectura: spec y componentes vigentes, sin
navegador, pruebas ni modificaciones de archivos. El agente principal incorpora
sus propuestas aquí. Ángel las aprueba expresamente el 24/11/2026; se incorporan
como decisiones de esta versión. La revisión no constituye verificación visual.

| Prioridad | Diseño aprobado por Ángel | Motivo y límite |
| --- | --- | --- |
| Alta | Escritorio: seis zonas alineadas, título, resumen de descripción, precio, autor, estado y tres puntos; más ancho para título/resumen, sin cuadrícula. | Mantener una fila compacta en reposo. No convertir toda la fila en enlace: desplegar o abrir acciones no debe abrir la tienda. |
| Alta | Resumen de descripción en su zona; al desplegar, texto completo debajo abarcando el ancho del deseo. | Evitar leer una descripción larga en una columna estrecha. Sin descripción no hay control vacío; al plegar se recupera la fila compacta. |
| Alta | Móvil: título/tres puntos en primera franja, precio/autor/estado en segunda y resumen desplegable en tercera cuando exista. | Sin scroll horizontal ni tarjetas con fondo propio. Sustituye la opción previa de dos líneas. |
| Alta | Menú anclado que abre hacia el espacio disponible, Editar/Eliminar en vertical y confirmación en ese contexto. | No romper alineación ni recortar acciones. Cancelar inicial, Escape/click exterior, foco de retorno y protección durante envío; cierre no borra. |
| Alta | Formulario vertical con menor separación; selector/Crear lista juntos y paso del botón debajo solo si no caben. | Sin rejilla nueva de campos. Conservar input al abrir/cancelar/crear y el aviso junto a Guardar. |
| Media | Guardar/Cancelar como grupo separado de los campos y con espacio claro entre acciones; apilable en móvil. | Guardar principal, Cancelar secundario sin subrayado, sin convertirlo en acción destructiva. |
| Media | Rojo oscuro de fondo en acciones destructivas con texto claro y foco visible. | Evitar texto rojo oscuro sobre marrón; ajustar tono legible en fase autorizada. Identificar Eliminar también por texto. |

Composición aprobada, todavía no implementada:

```text
Escritorio, plegado:
Título | Inicio de descripción… ▾ | Precio | @autor | Pendiente | ⋯
         (las barras explican zonas; no se dibujan en la interfaz)

Al desplegar:
Título | Inicio de descripción… ▴ | Precio | @autor | Pendiente | ⋯
Descripción completa con ancho de lectura y saltos de línea conservados…

Móvil:
Título del deseo                                      ⋯
Precio · @autor · Pendiente
Inicio de descripción… ▾
```

Decisiones significativas resueltas por la aprobación de Ángel:

1. Tres franjas móviles y paso de Crear lista debajo
   cuando no quepa junto al selector.
2. El texto completo de la descripción se abre bajo toda la fila,
   mientras su resumen plegado ocupa una de las zonas alineadas.
3. Títulos largos: saltos de línea sin elipsis para no ocultar el
   nombre; la fila puede crecer. No se aplica truncado por inferencia.
4. La composición y spec quedan aprobadas en esta versión y Ángel solicita
   planificar. La implementación requiere aprobación del plan y solicitud propia.

El flujo obligatorio de revisión de diseño solicitado por Ángel se documenta
en docs/proceso-sdd.md y se enlaza desde AGENTS.md: revisión al recibir
indicaciones, antes de implementar y después para auditar el resultado. El
subagente no modifica ni aprueba; Ángel decide y el principal escribe.

Precio ausente mantiene el comportamiento actual («Sin precio indicado») y
descripción ausente no añade texto, mientras no se pida cambiarlo. No es
necesario decidir medidas CSS ni un hexadecimal de rojo para aprobar el alcance;
el rojo oscuro puede validarse visualmente en la implementación autorizada.

## Validación prevista

En el plan posterior: pruebas de regresión existentes de Deseos con base
aislada, build y controles de formato/diff derivados del repositorio; revisión
transversal de subrayados sin rediseñar otros cajones. Comprobar textos largos,
con/sin descripción/precio, listado vacío y errores. Las pruebas HTTP no
acreditan la composición visual ni teclado/foco.

Subagente de diseño obligatorio antes de implementar y tras el resultado.
Validación real de Ángel en escritorio/móvil/teclado: columnas/franjas,
desplegables, menú/confirmación, formulario, Crear lista sin perder input,
Guardar/Cancelar y subrayados externos/internos. No se ha ejecutado ninguna
de estas verificaciones ni se ha abierto navegador en la preparación.

Ángel aprueba expresamente la spec completa y solicita preparar el plan en
feat/rediseno-deseos el 24/11/2026. Estado approved, sin implementación.
Ángel aprueba después el plan y solicita implementación/verificación SQLite,
sin navegador ni publicación. Implementación realizada y controles registrados
en tasks.md; revisión final PASS y validación manual confirmada por Ángel. Cerrada localmente.

La revisión anterior quedó BLOCKED por composición pendiente; esas decisiones
han sido resueltas por la aprobación posterior de Ángel. Reauditoría final
spec_reviewer: PASS, sin bloqueos. La revisión de diseño confirma coherencia.
Ningún PASS sustituye la aprobación humana de la spec completa.

## Cierre — 24/11/2026

Inicio: 24/11/2026, 12:28 CET. Cierre: 24/11/2026, 21:29 CET.
Tiempo transcurrido: 9 h 1 min; no se han comunicado pausas para esta spec.
Estimación por tiempo transcurrido, no medición de trabajo efectivo. Redondeo:
36 cuartos completos y 1 minuto restante, que se descarta según la regla SDD.
El valor para GitHub Projects figura al principio del documento.

Ángel confirma que todo funciona en escritorio, móvil y teclado, incluidos
los últimos ajustes del menú único de listas y Editar. Se registra como
validación humana, sin observación de navegador por el agente. Revisión de
diseño previa/posterior APTO y auditoría final test_reviewer PASS.

Controles: SQLite en memoria con guardia previa, Deseos 22 pruebas/546
aserciones; suite completa 91 pruebas/2559 aserciones, sin fallos y 2 omisiones
que requieren PostgreSQL aislado. Node: 1 prueba correcta; build: 703 módulos;
formato y diff correctos. Matriz y evidencias en tasks.md.

Cierre de implementación y validación local. Commit, publicación, integración
en dev, CI y traslado a documentation siguen pendientes de autorización.
