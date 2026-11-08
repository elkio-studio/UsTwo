---
name: ustwo-verify
description: "Verifica una implementación terminada o un cambio ligero de UsTwo mediante diff, criterios, controles reales y evidencias. No implementa reparaciones ni autoriza commits o publicación."
---

# Verificación SDD de UsTwo

Determina si un cambio puede cerrarse basándose en el repositorio y en
resultados observables, no en afirmaciones del agente escritor.

## Preparación

Lee `AGENTS.md`, `README.md`, `MEMORY.md`, `docs/proceso-sdd.md`,
`docs/decisiones-tecnicas.md`, el estado Git y el diff completo. Para un cambio
SDD, lee `spec.md`, `plan.md` y `tasks.md`; exige especificación y plan aprobados
y una solicitud humana explícita de implementación. Para un cambio ligero,
documenta por qué no necesitó artefactos SDD.

Cuando exista UI, aplica «Diseño de interfaces dentro del flujo» de
`docs/proceso-sdd.md` y consulta `impeccable` para crítica y auditoría de solo
lectura. No ejecutes comandos de pulido o reparación durante la verificación.

## Flujo

1. Revisa todos los cambios rastreados y no rastreados para detectar alcance
   accidental, secretos, archivos generados o documentación incoherente.
2. Relaciona cada `CA-NN` con implementación, prueba o evidencia observable y
   cada `T-NN` con su resultado. No des por cumplido un criterio solo porque una
   tarea esté marcada.
3. Obtén los comandos aplicables de scripts, manifiestos, configuración de CI y
   documentación del repositorio. Ejecuta siempre `git diff --check` cuando
   exista un diff. No inventes controles ni instales herramientas ausentes.
4. Ejecuta los controles razonables para el alcance y conserva comando,
   resultado y limitaciones. Una herramienta no disponible se informa; no se
   sustituye silenciosamente por una comprobación más débil.
5. Revisa pruebas, formato, lint, análisis estático, build, migraciones,
   compatibilidad, seguridad y documentación solo cuando sean aplicables.
6. Delega la auditoría final en `test_reviewer`, entregando rutas, diff,
   comandos y resultados. Recuérdale que no puede modificar archivos.
7. Si el resultado es `BLOCKED`, informa y detente. Reparar requiere volver a
   la fase de implementación; no arregles durante esta ejecución de la skill.
8. Con `PASS` y todos los controles correctos, marca `tasks.md` como
   `completed`, `spec.md` como `completed` y limpia de `MEMORY.md` solo el
   estado ya resuelto. No alteres `plan.md`.

La verificación no autoriza staging, commit, push, merge, despliegue ni cierre
de pendientes externos que todavía no se hayan observado.

