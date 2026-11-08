# Gobernanza de agentes de UsTwo

## Contexto obligatorio

Antes de una tarea relevante, lee `README.md`, `MEMORY.md`,
`docs/proceso-sdd.md`, `docs/decisiones-tecnicas.md` y los artefactos SDD que
correspondan. Respeta la estructura y las convenciones reales del repositorio.

La documentación aprobada del proyecto prevalece sobre recomendaciones
genéricas. `MEMORY.md` describe estado operativo, pero no concede autorización
ni sustituye una decisión aprobada.

UsTwo está en definición de producto. No presupongas stack, arquitectura,
modelo de datos ni autorización para implementar la aplicación.

## Autoridad y decisiones

Los agentes ofrecen análisis y ejecución técnica; el desarrollador conserva
las decisiones de producto y técnicas significativas. Antes de depender de una
decisión no aprobada:

1. identifica la decisión y por qué es necesaria;
2. presenta alternativas razonables con consecuencias;
3. recomienda una opción solo cuando exista una preferencia clara;
4. espera la decisión del desarrollador para la parte dependiente.

Requieren aprobación explícita, entre otras, las decisiones sobre alcance,
reglas de negocio, UX significativa, arquitectura, datos, autenticación,
autorización, seguridad, contratos, dependencias, servicios externos,
almacenamiento, infraestructura, despliegue y refactors estructurales.

Se permiten decisiones locales evidentes como imports, formato, nombres
internos coherentes, organización trivial y eliminación segura de código muerto
dentro de una tarea ya aprobada. No uses estos permisos para introducir capas,
patrones, dependencias o convenciones nuevas.

## Flujo SDD

Considera relevante un cambio que afecte comportamiento, producto, negocio,
datos, seguridad, arquitectura, dependencias, integraciones, almacenamiento,
despliegue, UX significativa o estructura del sistema. Usa, en orden:

1. `$ustwo-spec` para crear y revisar la especificación;
2. aprobación humana de la especificación;
3. `$ustwo-plan` para crear y revisar plan y tareas;
4. aprobación humana del plan;
5. solicitud humana explícita de implementación;
6. implementación por un único agente escritor;
7. `$ustwo-verify` y revisión final basada en evidencias;
8. permiso específico para cualquier commit.

Los cambios triviales sin variación de comportamiento pueden seguir un flujo
ligero: análisis breve, cambio mínimo y verificación proporcional.

Las autorizaciones no se heredan entre fases. Un `PASS` de un revisor no es una
aprobación humana; aprobar una fase no autoriza la siguiente; implementar no
autoriza commits, push, merge ni despliegue.

## Colaboración entre agentes

Para trabajo de interfaz, aplica las skills locales `ui-ux-pro-max`,
`frontend-design` e `impeccable` según la sección «Diseño de interfaces dentro
del flujo» de `docs/proceso-sdd.md`. Esa sección delimita su uso por fase y
prevalece sobre sus automatismos y recomendaciones genéricas.

El agente principal es el único escritor. `spec_reviewer`, `plan_reviewer` y
`test_reviewer` son revisores lógicos de solo lectura: no modifican archivos,
no toman decisiones y no reparan hallazgos. Sus permisos técnicos se heredan de
la sesión padre, por lo que esta restricción también debe imponerse en la tarea
que se les delegue.

## Git

Para futuros cambios realizados por un agente, aplica `$ustwo-git-flow` antes
de modificar archivos. La rama base es `main`. Crear o adoptar ramas, preparar
archivos y crear commits requiere autorización concreta e inmediata.

El desarrollador se reserva push, merge, integración, resolución de conflictos,
cambio a `main` y borrado de ramas. No uses `fetch`, `pull`, `rebase`, `reset`,
`stash` ni operaciones forzadas como reparación automática.

Los commits autorizados se escriben en español con el formato
`<tipo>: <descripción>`, usando tipos como `feat`, `fix`, `refactor`, `docs`,
`test` o `chore`.

## Calidad, documentación y seguridad

Busca la solución correcta más simple. Aplica SOLID, DRY y Clean Code cuando
reduzcan el coste de mantenimiento; evita abstracciones prematuras. Revisa
dependencias e impactos antes de refactorizar y añade pruebas cuando exista una
infraestructura de pruebas aplicable.

Actualiza documentación existente antes de crear documentos nuevos. No
dupliques información entre `AGENTS.md`, skills, decisiones y especificaciones.

No guardes ni muestres secretos en código, terminal, documentación, pruebas,
parches o commits. No instales herramientas ni inventes comandos de calidad:
derívalos de los archivos reales del repositorio.

