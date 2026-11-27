---
spec: "037"
status: completed
---

# Tareas — 037

Spec y plan aprobados; implementación solicitada expresamente por Ángel.
Sin autorización para navegador, commits ni publicación.

- [x] **T-01 — Confirmar precondiciones y diseño aprobado**
  - Criterios: CA-01–CA-06.
  - Revisar registro/árbol, aprobación del plan y petición de implementar,
    estados originales y revisión de diseño previa de solo lectura.
  - Evidencia: alcance/archivos y permisos claros, sin cambios de ocupación.
- [x] **T-02 — Separar origen y destino y limpiar indicadores del ghost**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-06.
  - Derivar data-target excluyendo origin; retirar data-origin/data-target del
    clon. Conservar estado interno, señales originales y copia inerte.
  - Evidencia: origen sin borde continuo, destino distinto único, ghost sin
    señal de hueco real; ningún cambio de gestos/servidor/assets.
- [x] **T-03 — Ejecutar controles proporcionales y revisar regresión**
  - Criterios: CA-01–CA-06, con límites de prueba visual.
  - Node existente, build, formato y diff; revisar cinco tipos, vacíos y
    cancelación por lectura, sin tests espejo de JSX/CSS ni conexiones reales.
  - Evidencia: comandos/resultados y limitaciones; sin cambios ajenos/backend.
- [x] **T-04 — Validar visualmente y resolver la puerta de oclusión**
  - Criterios: CA-01–CA-05.
  - Ángel comprueba escritorio/móvil/teclado, origen/vacío/ocupado/fuera,
    ghost sobre cinco tipos, bordes/nombres, foco y reduced motion.
  - Evidencia: confirmación atribuida o caso reproducible de oclusión. Si falla,
    presentar corrección mínima y esperar aprobación significativa antes de
    reparar; no cerrar mientras CA-04 no esté acreditado.
- [x] **T-05 — Auditar y cerrar documentación**
  - Criterios: CA-01–CA-06.
  - Revisión de diseño posterior, ustwo-verify y test_reviewer solo lectura;
    actualizar spec/tasks/MEMORY y horas bajo título solo tras PASS.
  - Evidencia: matriz final y límites honestos; publicación/CI pendientes de
    permisos, sin navegador o datos locales tocados por iniciativa propia.

## Evidencias de implementación — 27/11/2026

- T-01: rama y registro feat/indicadores-destino, base23a3655; spec/plan aprobados
  y ejecución solicitada expresamente. Diseño previo APTO, solo lectura.
- T-02: Shelf deriva data-target solo para target distinto de origin; no cambia
  esos valores internos. El clon pierde data-origin/data-target; el nodo real
  conserva su origen. Sin cambios en CSS, backend, gestos, snapshot o datos.
- T-03: `node --test tests/Frontend/shelfMovement.test.js`, 3 pruebas correctas;
  `npm.cmd run build`, correcto, 705 módulos. Prettier --check inicialmente
  señaló formato; aplicado --write en los dos archivos y comprobación posterior
  correcta. `git diff --check` correcto; diff de código limitado a ambos archivos.
  Sin suite PHP repetida ni migraciones: no hay cambios de servidor.
- T-04 pendiente: validación visual de Ángel de origen discontinuo, un único
  destino continuo distinto, fuera sin destino, ghost sin señales falsas y
  borde/colección del destino reconocibles en cinco tipos. Cubrir escritorio,
  móvil, teclado, toque de destino, cancelación y movimiento reducido.
- T-05 en revisión: diseño posterior APTO por lectura, sin nuevos bloqueos
  técnicos; test_reviewer BLOCKED solo para cierre por validación visual pendiente,
  sin otros defectos técnicos bloqueantes;
  no cerrar sin evidencia de legibilidad/oclusión real. No navegador, commit
  ni publicación; base local, .env y servicios conservados.

## Validación final y cierre — 27/11/2026, 12:38 CET

T-04/T-05 completadas: Ángel confirma origen distinguible y destino no oculto
en escritorio, móvil y teclado. Los pendientes anteriores son históricos;
resueltos mediante esta confirmación humana. Reauditoría test_reviewer PASS,
sin bloqueos; diseño previo/posterior APTO. Sin observación visual del agente.

| Criterios | Evidencia final |
| --- | --- |
| CA-01 | data-target excluye origin; origen distinguible confirmado por Ángel. |
| CA-02 | Destino distinto conserva señal; confirmación visual humana. |
| CA-03 | Estados internos y limpieza intactos; regresión Node de fuera/origen y lectura del diff. |
| CA-04 | Ghost sin atributos de estado; Ángel confirma que los indicadores no tapan el destino en escritorio/móvil/teclado. |
| CA-05 | Gestos, foco, anuncios y preferencias sin cambios; revisión técnica y validación humana de las entradas. |
| CA-06 | Sin cambios de backend/contratos/ocupación/contenido; Node correcto y diff limitado a presentación. |

Controles vigentes: Node3, build705, Prettier y diff correctos. No se repiten
como nuevas pruebas los resultados PHP de036. Sin base local alterada,
commits ni publicación; horas y cálculo registrados en spec.md.
