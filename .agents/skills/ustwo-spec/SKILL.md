---
name: ustwo-spec
description: "Crea o revisa especificaciones SDD para cambios relevantes de UsTwo antes de planificar o implementar. No se usa para erratas, formato ni ajustes internos evidentes sin cambio de comportamiento."
---

# Especificación SDD de UsTwo

Convierte una necesidad relevante en un contrato funcional verificable sin
decidir producto, arquitectura o implementación por el desarrollador.

## Contexto obligatorio

Lee `AGENTS.md`, `README.md`, `MEMORY.md`, `docs/proceso-sdd.md` y
`docs/decisiones-tecnicas.md`. Revisa también las especificaciones relacionadas
si existen.

Si el alcance incluye UI, aplica la sección «Diseño de interfaces dentro del
flujo» de `docs/proceso-sdd.md`: usa `ui-ux-pro-max` y `frontend-design` para
formular propuestas y criterios visuales, sin implementar ni aprobarlos.

## Flujo

1. Clasifica el cambio según `docs/proceso-sdd.md`. Si es ligero, indícalo y no
   crees artefactos SDD salvo petición explícita.
2. Identifica decisiones pendientes capaces de cambiar el comportamiento. No
   las completes por inferencia; solicita la decisión necesaria y continúa solo
   con las partes independientes.
3. Obtén el siguiente identificador de tres dígitos inspeccionando
   `docs/specs/`. No renumeres ni reutilices identificadores existentes.
4. Crea `docs/specs/NNN-slug/spec.md` a partir de
   [assets/spec-template.md](assets/spec-template.md). Usa un slug ASCII en
   kebab-case y `status: draft`.
5. Define problema, objetivo, alcance incluido y excluido, actores, flujos,
   errores, reglas y casos límite. Distingue requisitos confirmados, propuestas
   y decisiones pendientes.
6. Numera criterios observables como `CA-01`, `CA-02`, etc. Cada criterio debe
   poder verificarse sin depender de expresiones subjetivas.
7. Documenta como aprobadas solo decisiones respaldadas por el desarrollador o
   por documentación vigente. No crees `plan.md`, `tasks.md` ni código.
8. Delega la revisión en `spec_reviewer`, proporcionándole la ruta exacta y
   recordando que no puede escribir. Si devuelve `BLOCKED`, corrige únicamente
   lo autorizado o pide la decisión pendiente y repite la revisión.
9. Tras `PASS`, informa del resultado y espera aprobación humana. `PASS` no
   permite cambiar el estado ni planificar.

Solo cambia `status` a `approved` cuando el desarrollador apruebe expresamente
esa versión de la especificación. Esa aprobación permite usar `$ustwo-plan`, no
implementar.

