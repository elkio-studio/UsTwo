---
spec: "038"
status: completed
---

# Tareas — 038

Refactor técnico sin US asociada. Plan aprobado y ejecución solicitada por
Ángel. Sin autorización de navegador, commits ni publicación.

- [x] **T-01 — Confirmar precondiciones y diseño**
  - Criterios: CA-01–CA-07.
  - Aprobar plan y obtener petición explícita de implementar; revisar rama/registro,
    diff y consumidores. Revisión previa de diseño solo lectura.
  - Evidencia: alcance/autoridad claros, único escritor, sin cambios funcionales.
- [x] **T-02 — Reubicar JournalLayout y extraer AccessLayout**
  - Criterios: CA-01, CA-02, CA-07.
  - Mover JSX/CSS y consumidores; extraer main/Head/bienvenida/estilos del acceso,
    conservar state/AccessForm montado y variables. Retirar solo copia sustituida.
  - Evidencia: contrato/DOM iguales, imports resueltos, build y sin CSS abandonado.
- [x] **T-03 — Extraer ghost conservando su ciclo de vida**
  - Criterios: CA-03, CA-05, CA-06.
  - useShelfGhost con show/remove estables; clon inerte, canvas estático,
    atributos y limpieza sin operación/red. Coordinador controla cuándo usarlo.
  - Evidencia: no renderer adicional ni cambios de apariencia o señales037.
- [x] **T-04 — Separar puntero/teclado y centralizar lógica común**
  - Criterios: CA-03, CA-04, CA-05, CA-07.
  - Adaptadores con bindings vigentes, listeners/flags conservados y operación
    única; helpers de distancia/candidato. Mantener salida pública y cancelación.
  - Evidencia: sin ciclos de render que limpien timers, refs duplicadas o
    supresión inaccesible; ratón/táctil mantienen finales distintos.
- [x] **T-05 — Probar y documentar convenciones**
  - Criterios: CA-01–CA-07.
  - Node existente y casos de helpers, build, formato/diff y revisión de imports,
    responsabilidades, estilos y archivos sustituidos. Actualizar convenciones.
  - Evidencia: controles reales sin dependencias/backend/migración ni tests espejo.
- [x] **T-06 — Validar equivalencia y cerrar**
  - Criterios: CA-01–CA-07.
  - Diseño posterior, prueba humana acceso/registro/temas/paneles y cinco tipos
    con ratón/táctil/teclado/cancelación/foco; ustwo-verify y test_reviewer.
  - Evidencia: PASS y matriz con límites de Node/build honestos; spec/tasks/MEMORY
    completos y horas bajo título. Sin commit/publicación por esta autorización.

## Evidencias de implementación — 27/11/2026

- T-01: spec/plan aprobados y ejecución solicitada, registro de rama coherente
  con refactor/layouts-movimiento-cajones, basec6a9751. Diseño previo APTO.
- T-02: JournalLayout/CSS trasladados y consumidores actualizados; AccessLayout
  recoge el mismo main/Head/bienvenida y CSS del recibidor. AccessForm y su key
  interna intactos; la página conserva useState y el mismo hijo sin key de modo.
  Ambos CSS trasladados comparados con git HEAD normalizando CRLF: idénticos.
  Build intermedio de layouts correcto, 706 módulos.
- T-03/T-04: cuatro hooks, coordinador único de operación/timer/supresión/red,
  entradas con bindings.current en cada evento y en callback diferido de450ms,
  13 listeners de Pointer y1 de Keyboard con sus flags, ghost estable e inerte
  y cleanup DOM. Coordinador limpia refs/timer; las entradas solo listeners.
  Helpers gestureDistance/createShelfGesture utilizados en ratón/touch, con
  copia del punto inicial e identificadores intactos. Origen/indicadores de037
  conservados. useShelfMovement pasa de457 a191 líneas; el total de los cuatro
  es555: no se presenta como reducción global ni se comprime por apariencia.
- T-05: Node shelfMovement5 pruebas correctas (2 nuevas sobre umbral/dirección,
  candidato e independencia de puntos); ejecución conjunta con wishRequest:
  **6 pruebas correctas**, sin fallos u omisiones. Build final **709 módulos**,
  correcto. Prettier --check de hooks/layouts JSX/pageLogin/util/test y
  git diff --check correctos. Imports de páginas restantes limitados al cambio
  de ruta; CSS trasladado sin cambios de contenido.
- Grafo estático: **72 módulos alcanzables**, cero imports relativos rotos,
  cero referencias al layout antiguo y cero !important en CSS propio. Carpeta
  antigua del layout vacía retirada; sin copias de implementación anteriores.
  Convenciones actualizadas; enlace del informe de auditoría al layout movido
  actualizado, sin limpiar sus capacidades o candidatos.
- Diseño posterior **APTO por lectura**, sin regresiones técnicas concretas;
  no acredita interfaz renderizada. Sin cambios en app/rutas/config/migraciones,
  app.jsx o AccessForm, ni dependencias. No suite PHP repetida, sin motivo por
  este refactor frontend. Sin navegador, datos locales tocados, commit o subida.
- Estado previo al cierre, T-06 pendiente: equivalencia visual, campos/errores al alternar acceso/registro,
  temas/paneles/invitaciones, cinco tipos con ratón/móvil/teclado, cancelación y
  clic residual, foco, cambio de estantería/conflictos y reduced motion. Auditoría
  final test_reviewer realizada: **BLOCKED para cierre** por falta de evidencia
  humana. Sin regresiones técnicas concretas por lectura. El revisor repitió
  Node (6 correctas) y diff --check. Sus observaciones sobre frases obsoletas
  de aprobación en el plan se corrigieron sin cambiar el alcance aprobado.
  No cerrar sin la evidencia humana pendiente.

## Cierre — 27/11/2026, 13:16 CET

Ángel confirma «lo veo todo bien» y solicita cerrar la spec y subir la rama.
Se registra su aceptación de la equivalencia visual y de interacción pendiente
en T-06; no se atribuye al agente una prueba de navegador. Los controles
automáticos anteriores siguen vigentes, sin cambios de código posteriores.
GitHub Projects: **0.5 h**, estimación transcurrida de 24 minutos (12:52–13:16),
redondeada al cuarto de hora. Sin US asociada ni pausas comunicadas.
Auditoría final test_reviewer: **PASS**, sin bloqueos tras esta aceptación.
