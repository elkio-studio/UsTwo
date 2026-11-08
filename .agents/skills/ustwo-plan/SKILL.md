---
name: ustwo-plan
description: "Convierte una especificación SDD aprobada de UsTwo en un plan técnico y tareas trazables. No se usa con especificaciones draft, bloqueadas o con decisiones técnicas pendientes."
---

# Plan SDD de UsTwo

Produce un plan ejecutable que implemente exactamente una especificación
aprobada, sin ampliar alcance ni introducir decisiones nuevas.

## Precondiciones

Lee `AGENTS.md`, `README.md`, `MEMORY.md`, `docs/proceso-sdd.md`,
`docs/decisiones-tecnicas.md` y la especificación indicada. Detente si
`spec.md` no tiene `status: approved`, contiene decisiones pendientes que
afectan al plan o contradice la documentación vigente.

Para UI, aplica «Diseño de interfaces dentro del flujo» de
`docs/proceso-sdd.md`. Planifica la dirección visual aprobada y su verificación;
no conviertas recomendaciones de las skills de diseño en decisiones nuevas.

## Flujo

1. Crea `plan.md` desde [assets/plan-template.md](assets/plan-template.md) con
   `status: draft` en la misma carpeta de la especificación.
2. Crea `tasks.md` desde [assets/tasks-template.md](assets/tasks-template.md)
   con `status: pending`.
3. Describe solo los componentes, contratos, datos, fases, pruebas,
   documentación, compatibilidad, despliegue y riesgos que realmente apliquen.
   Si una elección técnica no está aprobada, trátala como bloqueo.
4. Divide el trabajo en acciones pequeñas, ordenadas y verificables. Identifica
   cada tarea como `T-NN` y relaciónala con uno o más criterios `CA-NN`.
5. Comprueba que todos los criterios estén cubiertos y que ninguna tarea añada
   comportamiento fuera del alcance. No modifiques código ni configuración de
   la aplicación.
6. Delega la revisión en `plan_reviewer`, proporcionando las tres rutas y
   recordando que no puede escribir. Ante `BLOCKED`, corrige solo lo autorizado
   o vuelve al desarrollador para la decisión necesaria.
7. Tras `PASS`, informa y espera aprobación humana. `PASS` no aprueba el plan.

Solo cambia `plan.md` a `status: approved` cuando el desarrollador apruebe
expresamente esa versión. Mantén `tasks.md` en `pending`; aprobar el plan no
autoriza la implementación.

