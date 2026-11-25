---
spec: "036"
status: approved
---

# Plan técnico — 036

## Resumen y autorizaciones

Implementar la [spec aprobada](spec.md) en feat/arrastrar-cajones. Este documento
propone contratos técnicos que requieren aprobación expresa junto al plan.
No se modifica código durante su preparación; [tareas](tasks.md) pending.

## Análisis y componentes afectados

- Shelf.jsx/CSS: integra recogida, arrastre, destinos, teclado, anuncios y
  alta solo de tipos ausentes. No cambiar los ocho huecos ni las imágenes.
- ShelfDashboard: identidad de Shelf por ID de estantería para desmontar y
  limpiar todo estado de gesto al cambiar de espacio, sin heredarlo entre ambos.
- Hook useShelfMovement en resources/js/hooks: aislar ciclo de gesto, referencias,
  timers, listeners y limpieza para no convertir Shelf en un manejador monolítico.
  Sin dependencias ni sistema global de drag and drop.
- ReadingSpines y WishBox: conservar enlaces reales; solo exponer lo mínimo
  necesario para inhibir hover durante recogida, sin recrear sus ilustraciones.
  Usar captura en el contenedor de hueco para uniformar los cinco tipos.
- Utilidad pura en resources/js/utils para validar destinos/transiciones y
  calcular distribución esperada; solo extraer lógica que necesite pruebas reales.
- SpaceController, rutas y pruebas Feature de colocación: movimiento atómico y
  conflictos. No cambia contenido, modelos de colecciones ni schema.

## Propuestas técnicas que requieren aprobación

### Contrato de movimiento

Nuevo PUT `/espacios/{space}/cajones/mover`, en auth/RequireSpaceMembership,
antes de cualquier ruta dinámica coincidente. Mantener rutas actuales de alta.

Payload: `drawer` enum peliculas/series/sitios/lecturas/deseos, `slot` entero
1–8 y `expected_positions` con las cinco claves de posición, todas presentes,
cada una null o entero1–8, sin posiciones duplicadas. Claves aceptadas mediante
lista cerrada; nunca permitir nombres de columnas arbitrarios del cliente.

`expected_positions` representa la distribución que el usuario vio al recoger.
Comparar las cinco posiciones con el estado actual bajo bloqueo. Detecta cambios
de posición vigentes, no historia intermedia con vuelta a idéntica distribución.
No introducir versión ni migración. Cajón ausente: rechazar movimiento, no darlo
de alta por esta ruta. Destino igual al origen: no guardar; frontend evita envío.

Usar validación Laravel/Inertia y redirección a spaces.show; errores asociados
a movimiento, sin SQL ni datos de otros espacios. Ante discrepancia, devolver
error e incorporar distribución actual con recarga parcial si el retorno Inertia
conserva props anteriores. Cancelar la recogida; nunca reintentar automáticamente
con el nuevo snapshot. No almacenar el snapshot en sesión ni base de datos.

### Atomicidad y concurrencia

Transacción con lock de Space y revalidación de membresía, siguiendo el patrón
existente para escrituras de Deseos: en SQLite adquirir escritura inocua antes
de lectura; en PostgreSQL lockForUpdate. Comparar snapshot después del bloqueo.
Resolver destino en las cinco columnas permitidas y aplicar movimiento o
intercambio en **una sola sentencia UPDATE** de la fila; los triggers SQLite y
CHECK PostgreSQL existentes no permiten estados intermedios con colisión.
Capturar fallos de BD con mensaje comprensible y rollback.

Hacer participar las altas existentes en el mismo bloqueo/transacción y
revalidación para no competir con movimientos. Mantener su payload slot y
rechazo de ocupado. No cambiar por sorpresa clientes existentes a intercambio;
la nueva ruta separa expresamente alta/colocación anterior de movimiento.

### Interacción propuesta

- Ratón: botón principal, arrastre tras 8px; clic corto conserva navegación.
- Táctil: mantener 450ms con tolerancia8px recoge; desplazarse antes cancela el
  timer y deja scroll. Valores propuestos, ajustables solo con evidencia local.
- Usar eventos nativos sin paquetes: Pointer Events para ratón y Touch Events
  para táctil con listeners locales no pasivos cuando sea necesario. No declarar
  touch-action:none permanentemente sobre cajones; bloquear scroll solo después
  de recogida y preventDefault de touchmove cancelable. Probar realmente si el
  navegador conserva el gesto; no asumir que cambiar touch-action tarde resuelve
  la captura. Si falla, volver al desarrollador, no añadir asas o modo distinto.
  Registrar listeners locales antes del gesto; seguir solo el dedo activo.
  Un segundo dedo/pinch cancela recogida sin bloquear zoom general.
- Estados explícitos: reposo, pulsación pendiente, recogido, arrastrando,
  esperando toque de destino, guardando. Un longpress sin desplazamiento pasa
  a esperar destino al levantar; un drag finalizado se coloca o cancela.
- Ignorar únicamente el clic residual del gesto, sin impedir el siguiente clic
  físico ni clics modificados de apertura fuera de recogida. Impedir drag nativo
  de imágenes/enlaces y menú contextual solo durante el gesto pertinente.
- Captura de handlers sobre huecos, no botones anidados en links. Ghost local
  no interactivo del cajón con representación actual, sin reconstruir canvas
  ni duplicar IDs/controles accesibles; no alterar distribución hasta éxito.
- Resaltar origen/destino con borde y texto «Mover a hueco N» o «Intercambiar
  con X». Sin botón de cancelar; Escape/origen cancelan. Region status polite
  y errores alert; sin anuncio de controles inexistentes.
- Espacio en cajón enfocado recoge; flechas buscan siguiente hueco en dirección
  visual con su geometría actual; en borde no saltan a otra fila arbitraria.
  Enter confirma y Escape cancela; prevenir teclas solo en ese contexto. Destinos
  vacíos con foco programático aunque no tengan +. Enter normal abre enlace.
- Guardar una sola petición, impedir otros gestos mientras procesa, reponer
  foco al mismo tipo movido tras éxito; al cancelar al origen; tras conflicto
  a su ubicación vigente. Si no existe por navegación, no forzar foco obsoleto.
- Limpiar timer, listeners y representación al desmontar, cambiar estantería,
  cancelar sistema/puntero o perder contexto. No generar escrituras en cleanup.
- Suspender animaciones de hover mientras se recoge/arrastra; respetar reduced
  motion en ghost/retorno. No cambiar las animaciones normales fuera del gesto.
- El diálogo + ofrece solo tipos no colocados. Si no queda ninguno, conservar
  + y mostrar aviso de que todos están colocados y Cancelar; sin opciones de
  movimiento ni duplicados. Mientras se mueve, el hueco funciona como destino.
- Estados informativos visualmente ocultos mediante clase accesible local;
  mantener región live y errores visibles, sin botón Cancelar movimiento.
  Guía estática y aria-describedby retirados por petición posterior de Ángel.

## Fases de implementación

1. Confirmar aprobación de contratos y plan, autorización de implementación,
   registro de rama y revisión previa de diseño. Inventariar restricciones/tests.
2. Implementar backend/validación/atomicidad y alta coordinada, pruebas aisladas.
3. Implementar estado y gestos uniformes, cancelación y guardado; después teclado
   y alternativa de toque. Conservar aperturas, imágenes y contenido.
4. Feedback, foco, movimiento reducido, retirada de Mover aquí y + persistentes.
5. Verificación automática y revisión posterior de diseño; validación manual
   humana ratón/táctil/teclado. Auditar con test_reviewer y cerrar solo con evidencias.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Manual cinco tipos, clic vs drag, modificadores y clic residual; helper solo si prueba comportamiento significativo. |
| CA-02 | Feature vacío, posiciones antes/después y GET posterior; recarga manual. |
| CA-03 | Feature intercambio de pares, triggers activos, contenido/valoraciones intactos y rollback tras fallo. |
| CA-04 | Node transiciones sin envío; manual Escape/origen/fuera/cancelación del sistema/desmontaje. |
| CA-05 | Táctil real scroll antes de450ms, longpress, drag, levantar y tocar destino, cancelar; sin navegar ni bloquear scroll en reposo. |
| CA-06 | Alta conserva contrato y rechaza ocupado; manual tipos ausentes y + presentes con cinco tipos, aviso sin duplicados y huecos accesibles. |
| CA-07 | Feature invitado, ajeno, miembro revocado, manipulación de enums/IDs/posiciones y conservación de tablas de contenido. |
| CA-08 | Feature snapshot desfasado, errorSQL y rollback; Node guardado único; manual errores/foco y no reintento automático. |
| CA-09 | Manual Space/flechas/Enter/Escape, geometría desktop/móvil, foco destinos vacíos y retorno; sin secuestrar teclado en reposo. |
| CA-10 | Diseño antes/después y validación humana de anuncios, contraste, borde/texto, animaciones y reduced motion. |

Pruebas backend en SQLite :memory: con guardia efectiva previa a PHPUnit:
testing, URL vacía, sin caché de configuración y nombre de BD :memory:. No tocar
SQLite local ni Supabase. Derivar filtro de tests existentes de cajones y añadir
Feature específico si separa comportamiento; Node --test para lógica pura con
runner existente, no test que copie CSS/JSX. Build, Prettier y git diff --check.
No instalar runner UI ni abrir navegador sin permiso. Pruebas PostgreSQL aisladas
corresponden al CI del desarrollador; no fingir que SQLite acredita concurrencia
PostgreSQL real. Caso snapshot desfasado se simula en pruebas de contrato.

## Documentación, compatibilidad y despliegue

Actualizar spec/tasks/MEMORY con evidencias reales, limitaciones y horas bajo
título al cerrar. No nuevos informes ni cambios generales de arquitectura.
Sin migración propuesta: conservar datos locales y .env, servidores activos.
Build frontend y backend se publican juntos para disponer de ruta/gestos.
Publicación, integración dev/documentation, CI y despliegue requieren permisos
específicos posteriores; no cambiar main durante esta implementación.

## Riesgos y estado

Riesgos principales: arbitraje de scroll táctil, clic residual, cambios de layout,
colisiones de concurrencia y pérdida de foco al cambiar posiciones. Medidas
descritas arriba; si una prueba exige otro comportamiento, volver a decidir.

Plan y tareas aprobados expresamente por Ángel, incluidos los contratos técnicos,
y solicitud explícita de implementación recibida. Implementación autorizada
en feat/arrastrar-cajones, sin publicación.
Revisión plan_reviewer: BLOCKED únicamente por aprobación técnica pendiente;
sin defectos técnicos adicionales. Los contratos anteriores no se implementan
hasta recibir esa aprobación y una solicitud explícita de implementación,
ambas recibidas posteriormente de Ángel.
Revisión previa design035: enfoque coherente, sin bloqueos de composición;
los controles táctiles, foco y captura requieren la comprobación real prevista.
