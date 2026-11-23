---
id: "030"
title: "Añadir y abrir el cajón de Deseos"
status: completed
---

# 030 — Añadir y abrir el cajón de Deseos

Historia de la [épica EP-03, issue #25](https://github.com/elkio-studio/UsTwo/issues/25).
Referencias: [spec 029](../029-investigacion-metadatos-enlaces/spec.md)
y [comprobación de metadatos](../../../investigaciones/enlaces.md).

Inicio: 23 de noviembre de 2026, 14:14 CET.
Cierre local: 23 de noviembre de 2026, 14:53 CET.
Tiempo transcurrido estimado: 39 minutos, sin pausas comunicadas.
GitHub Projects: **0.75 h estimadas**, aplicando el redondeo en cuartos de hora
del proyecto (30 minutos completos y 9 restantes, al siguiente cuarto).
Sprint de cierre previsto: Sprint 2. Capacidad orientativa: 4 h/día de
lunes a viernes, 20 h por sprint; estimación pendiente de validación técnica.

## Problema y objetivo

Permitir que un miembro añada y abra una colección de enlaces desde la
estantería. Su nombre visual será «Deseos» y se reconocerá por una caja
de madera con tarjetas asomando, integrada en la escena existente.

## Alcance incluido

- Incorporar Deseos al selector de cajones y a las reglas actuales de
  colocación, movimiento, persistencia y acceso por estantería.
- Representación de caja con tarjetas y letrero «Deseos».
- Animación de una tarjeta al pasar el cursor, con alternativa de teclado,
  apertura táctil directa y respeto al movimiento reducido.
- Vista inicial «Deseos», estado vacío útil y regreso a su estantería.
- Acceso visible a añadir enlace, deshabilitado y con el aviso aprobado abajo.

## Alcance excluido

- Crear, editar, borrar o guardar enlaces y listas; formularios de alta,
  extracción desde URL, precios, estados de compra y visibilidad selectiva.
- Consultar tiendas, integrar APIs o incorporar imágenes de productos.
- Rediseñar los cajones configurables o la estantería, cambiar su capacidad
  o alterar las reglas actuales de los otros tipos.
- Implementar durante la preparación de esta especificación.

## Diseño aprobado y criterios visuales

Ángel ha elegido el nombre «Deseos», una caja de madera con varias tarjetas
asomando y la animación de una tarjeta que sube ligeramente al pasar el cursor.
La tarjeta delantera incorpora un símbolo de eslabones para comunicar enlaces.
El conjunto conserva los materiales, iluminación cálida y proporciones de la
estantería. La caja descansa sobre la balda y cabe dentro del hueco también
durante la animación, sin invadir cajones vecinos ni recortar la tarjeta.

El letrero conserva el estilo y la tipografía actuales, como texto renderizado
separado de la imagen; no se incluye un editor ni renombrado por el usuario.
Los recursos de caja y tarjetas
seguirán el formato WebP de los cajones existentes; no se generan en esta fase.
La solución de capas para animar la tarjeta se concretará en el plan.

Solo la tarjeta se desplaza: la caja permanece apoyada. El movimiento de
salida y retorno es suave, sin saltos ni animación continua. El foco de
teclado ofrece el mismo gesto y un indicador visible. La animación no es
necesaria para descubrir o abrir el cajón. Un toque lo abre directamente;
con movimiento reducido, permanece estático y conserva el foco visible.

## Actores

- Miembro de la estantería: coloca, mueve y abre Deseos según los permisos
  existentes; no se introducen roles o permisos adicionales.
- Usuario ajeno o sin sesión: no obtiene acceso a la estantería ni puede
  modificar su distribución.
- Ángel: valida la especificación y las decisiones de diseño y técnicas.

## Flujo principal

1. Un miembro abre el selector de un hueco libre y elige «Añadir Deseos».
2. La caja aparece en ese hueco, con un único acceso accesible «Deseos».
3. Al activarla, se abre la vista Deseos de esa misma estantería.
4. La vista muestra un mensaje de colección vacía, acceso a añadir enlace
   y enlace para volver a la estantería de origen.
5. Si Deseos ya está colocado, elegirlo desde otro hueco libre lo mueve
   allí y libera el anterior, siguiendo el comportamiento actual.

## Flujos alternativos, reglas y errores

- Hay un cajón Deseos por estantería, igual que para los tipos actuales;
  la distribución conserva los ocho huecos existentes.
- Un hueco inválido, ocupado o que otro miembro acaba de ocupar se rechaza
  sin cambios parciales. La distribución guardada permanece intacta.
- Todos los tipos respetan la ocupación de Deseos y viceversa. Mover la
  representación no crea otra colección ni cambia su estantería.
- El servidor comprueba membresía al abrir, colocar o mover. Cambiar el ID
  de estantería en una petición no concede acceso a otra colección.
- Salir y volver o recargar conserva la ubicación de cada cajón. Las
  estanterías existentes mantienen sus datos y distribución; no se añade
  Deseos automáticamente ni se reinicia ninguna base de datos.
- Los errores usan el patrón actual y permiten reintentar sin perder la
  distribución. No se implementa gestión de errores de tiendas en esta historia.

## Decisión aprobada: acceso a añadir enlace

La historia pide acceso a añadir enlace, pero el alta corresponde a otra
historia y todavía no existe. Ángel aprueba mostrar «Añadir enlace» como botón
deshabilitado, acompañado del texto visible «Podrás añadir enlaces en una
próxima actualización», sin navegar a una ruta inexistente ni simular guardado.
El botón permanece deshabilitado hasta la siguiente historia de alta de
enlaces. Esta decisión no incorpora el formulario ni autoriza implementar
todavía la spec 030.

## Criterios de aceptación

- **CA-01.** Deseos aparece en el selector y puede colocarse en un hueco
  libre. Si ya existe, se mueve sin duplicarse y libera el hueco anterior.
- **CA-02.** La caja con tarjetas, símbolo de enlaces y letrero Deseos se
  adapta al hueco en escritorio y móvil; se mantiene apoyada y sin solapamientos
  en reposo y durante la animación.
- **CA-03.** Hover y foco elevan ligeramente una tarjeta y la devuelven
  suavemente; la caja no se desplaza. Toque y teclado abren el cajón sin
  depender del hover, con foco visible y alternativa sin movimiento.
- **CA-04.** Abrir muestra la vista Deseos de la estantería actual, estado
  vacío útil y vuelta a su estantería. «Añadir enlace» aparece deshabilitado,
  con el aviso visible «Podrás añadir enlaces en una próxima actualización»;
  no navega ni guarda datos.
- **CA-05.** Colocación y movimiento persisten tras recarga. Destinos
  inválidos, ocupados o conflictos de ocupación no producen cambios parciales;
  se conserva la compatibilidad con Películas, Series, Sitios y Lecturas.
- **CA-06.** Un miembro puede operar según el patrón actual; usuarios ajenos
  no pueden abrir ni modificar Deseos de otra estantería, incluso con
  peticiones manipuladas. No se filtran datos entre estanterías.
- **CA-07.** La incorporación conserva los datos y posiciones existentes,
  no crea Deseos automáticamente y no amplía ni rediseña cajones configurables.

## Validación prevista

En la futura implementación: pruebas de colocación, movimiento, colisiones,
permisos, aislamiento y persistencia; regresión de los tipos existentes.
Aplicar cualquier migración con conservación de datos en SQLite local y
comprobar su estado antes de verificar la web. Nunca recrear ni vaciar la base.

Comprobar manualmente escritorio y móvil, ajuste al hueco, animación de
entrada/retorno, toque, teclado, foco y movimiento reducido, apertura y regreso.
Las comprobaciones automáticas y la validación manual de Ángel se registran
en las evidencias siguientes; el agente no ha abierto el navegador.

## Decisiones pendientes y estado

- No quedan decisiones funcionales pendientes en esta especificación.
- Plan y propuesta técnica aprobados; implementación solicitada por Ángel.
  Recursos WebP y componentes creados. Comprobación visual confirmada por Ángel.
- No se implementa extracción ni se amplía el alcance a la wishlist completa
  descrita en la investigación 029.

Estado: aprobada expresamente por Ángel el 23 de noviembre de 2026.
Ángel aprobó después el plan y solicitó implementar con migración local,
sin abrir el navegador ni publicar. Validación final completada y cierre local
solicitado por Ángel; estado `completed`.

La decisión de «Añadir enlace» queda validada por Ángel el 23 de noviembre
de 2026. Revisión documental de la actualización: `PASS`, sin bloqueos.
Ángel ha aprobado después la especificación completa y solicitado su plan.

## Implementación y evidencias locales

Implementado en `feat/cajon-deseos`, sin commit ni publicación:

- Posición nullable `wish_slot`, cast y payload de estantería. Restricciones
  aditivas para SQLite/PostgreSQL y colocación condicional de los cinco tipos.
- Rutas de apertura y colocación protegidas por el grupo actual de membresía,
  controlador index y mapping real de Inertia también en `tests/TestCase.php`.
- WishBox con caja y tarjeta WebP independientes, letrero renderizado,
  transformación solo de tarjeta en hover/foco y sin movimiento cuando se pide
  movimiento reducido. Un enlace abre directamente también con toque.
- WishesView con JournalLayout, vuelta por slug, estado vacío, botón disabled
  y aviso visible. No hay alta, listas ni extracción de URLs implementadas.

### Migración SQLite conservando datos

Conexión comprobada como SQLite local sobre `database/database.sqlite`,
sin URL externa. Se anuló la URL de conexión solo en el proceso de migración;
no se modificó `.env` ni se accedió a Supabase.

Copia consistente mediante la API de backup de SQLite, con origen de solo
lectura y comprobación de integridad antes/después:
`%TEMP%/ustwo-spec030/backups/before-spec030-20261123-144713.sqlite`.
El nombre corresponde al reloj local del proceso de copia.

`migrate` y `migrate:status` confirman aplicada
`2026_11_23_000012_add_wish_slot_to_spaces`, lote 5.
Todas las filas y columnas anteriores de las tablas de aplicación coinciden
con la copia. Se excluye únicamente el registro de migraciones, que incorpora
la nueva entrada. Las dos estanterías tienen `wish_slot` null inicialmente.
Integridad de SQLite correcta; no se recrea ni vacía la base.

Se conservan 2 usuarios, 2 estanterías, 3 membresías, 1 película, 1 serie,
9 lecturas y 3 valoraciones de lectura; las demás tablas estaban vacías y
siguen intactas. No se imprimieron datos personales durante la comparación.

### Controles y cobertura

- PHP 8.5.12 con pdo_sqlite, `vendor/bin/phpunit --display-skipped`:
  **73 pruebas, 2 160 aserciones, 0 fallos**. Dos omitidas por requerir
  PostgreSQL aislado. Los tests usan SQLite en memoria, no la base de la web.
- `npm run build`: correcto, 685 módulos.
- Formato de JSX/CSS con el Prettier instalado por la extensión de VS Code.
- `git diff --check`: correcto.

| Criterio | Evidencia y límites |
| --- | --- |
| CA-01 | WishesDrawerTest comprueba colocación por propietario, movimiento por miembro, hueco liberado y payload persistido. |
| CA-02 | Recursos transparentes y medidas relativas al hueco; Ángel confirma la comprobación del cajón en escritorio y móvil. |
| CA-03 | Enlace único, CSS de hover/foco/retorno y prefers-reduced-motion; Ángel confirma la validación local, con teclado y movimiento reducido. |
| CA-04 | Test del componente Inertia real, props y ausencia de POST; vista con botón nativo disabled, aviso y vuelta. Ángel confirma la comprobación local del cajón. |
| CA-05 | Rechazos de slots inválidos y ocupados, colisiones bidireccionales con los otros cuatro tipos, inserciones y actualizaciones SQL protegidas. Regresión de tipos existentes correcta. |
| CA-06 | Invitado, miembro, otra estantería y retirada de membresía; peticiones ajenas rechazadas sin modificar posiciones. |
| CA-07 | Prueba de migración aislada conserva datos, revierte solo Deseos y mantiene restricciones anteriores; comparación de SQLite local idéntica. |

Limitación del entorno: el primer intento aislado de PHP no pudo leer
`vendor/autoload.php`; se ejecutaron los controles con el mismo PHP local
fuera de ese aislamiento. Un fallo inicial del test de comparación por nombres
de tabla cualificados (`main.spaces`) se corrigió usando nombres sin esquema;
la suite completa posterior pasó. No fue un cambio de datos locales.

Ángel confirma haber comprobado el cajón en escritorio y móvil, con teclado
y movimiento reducido, y solicita registrar las pruebas y cerrar la spec.
Esta confirmación aporta la validación manual pendiente de T-07; se atribuye
a Ángel, no al agente. No se abrió ningún navegador por parte del agente.
PostgreSQL y CI no se han ejecutado en esta validación local.

Revisión final de solo lectura: sin defectos adicionales identificados;
controles automáticos correctos. El bloqueo anterior correspondía únicamente
a T-07 y a las evidencias visuales de CA-02, CA-03 y CA-04. La confirmación
manual de Ángel se aporta a la revisión final de cierre.

Revisión final de cierre local: **PASS**, sin bloqueos, tras la confirmación
de Ángel. T-01 a T-07 completadas. No hay cambios de código posteriores a la
suite y la auditoría anteriores; no se repiten pruebas por el cierre documental.
Pendientes externos: commit, publicación de la rama, integración en dev,
CI/PostgreSQL y traslado autorizado de documentación a documentation.
No se consideran realizados por cerrar esta especificación en local.
