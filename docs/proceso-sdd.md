# Proceso SDD de UsTwo

## Objetivo

Separar definición, planificación, implementación y verificación para que las
ambigüedades no se conviertan en decisiones de producto o arquitectura.

## Clasificación del cambio

Un cambio es **relevante** cuando altera comportamiento, producto, negocio,
datos, seguridad, arquitectura, dependencias, servicios externos,
almacenamiento, despliegue, UX significativa o estructura del sistema. Requiere
el flujo SDD completo.

Un cambio **ligero** es una corrección documental, de formato o interna y
evidente que no cambia comportamiento ni introduce decisiones. Usa análisis
breve, modificación mínima y verificación proporcional.

Si la clasificación no está clara, se trata como relevante hasta aclararla.

## Flujo completo

1. `$ustwo-git-flow` prepara o adopta una tarea Git con autorización.
2. `$ustwo-spec` crea `docs/specs/NNN-slug/spec.md` con `status: draft`.
3. `spec_reviewer` emite `PASS` o `BLOCKED`.
4. El desarrollador aprueba expresamente la especificación.
5. `$ustwo-plan` crea `plan.md` y `tasks.md`.
6. `plan_reviewer` emite `PASS` o `BLOCKED`.
7. El desarrollador aprueba expresamente el plan.
8. El desarrollador solicita expresamente implementar.
9. Un único agente escritor implementa las tareas aprobadas.
10. `$ustwo-verify` recopila verificaciones y evidencias reales.
11. `test_reviewer` emite `PASS` o `BLOCKED`.
12. Con todo correcto, se completan `spec.md`, `tasks.md` y `MEMORY.md`.
13. Cualquier commit requiere una autorización específica posterior.

Cada `BLOCKED` devuelve el artefacto a su fase sin autorizar reparaciones o la
fase siguiente. Las partes independientes pueden continuar si no dependen del
bloqueo.

## Diseño de interfaces dentro del flujo

Las skills de diseño se instalan en `.agents/skills/` y complementan las fases
SDD; no sustituyen sus aprobaciones ni autorizan implementar la aplicación.
Úsalas solo cuando la tarea afecte a interfaces. Para tareas sin UI, omítelas.

| Fase | Skill y resultado esperado |
| --- | --- |
| Especificación con `$ustwo-spec` | `$ui-ux-pro-max` explora alternativas de estilo, color, tipografía y UX; `$frontend-design` ayuda a formular una dirección propia para UsTwo. Recoge propuestas, referencias, flujos y criterios observables en `spec.md`, diferenciando lo aprobado de lo pendiente. |
| Planificación con `$ustwo-plan` | Traduce únicamente la dirección visual aprobada en tareas y controles trazables: composición, estados vacíos, carga y error, adaptación a pantallas y accesibilidad. Consulta solo el stack aprobado. |
| Implementación solicitada | El agente principal usa `$frontend-design` para construir las pantallas y `$ui-ux-pro-max` para consultas concretas. `$impeccable` puede ayudar a pulir dentro del alcance aprobado antes de verificar. |
| Verificación con `$ustwo-verify` | Usa `$impeccable` para crítica visual y auditoría en modo de solo lectura. Aporta evidencias de las pantallas y tamaños comprobados, teclado, foco, contraste y movimiento reducido cuando apliquen; informa de lo no comprobado. Las reparaciones vuelven a implementación. |

Las recomendaciones de las skills son propuestas, no decisiones aprobadas.
No elijas stack, librerías, fuentes externas, servicios, estilos o reglas de UX
significativas sin la aprobación correspondiente. Evita imponer patrones de
landing comercial al dashboard privado de UsTwo.

Conserva el contexto de producto en la documentación existente y las decisiones
visuales en la especificación correspondiente. No generes automáticamente
`PRODUCT.md`, `DESIGN.md`, `design-system/` ni informes paralelos; si fueran
necesarios, justifica primero su función para evitar fuentes contradictorias.
No uses `--persist` ni `--force` como efecto secundario de una consulta.

Las herramientas auxiliares se ejecutan desde la raíz del proyecto:

```powershell
python -B .agents/skills/ui-ux-pro-max/scripts/search.py "private shared dashboard" --design-system -p "UsTwo"
python -B .agents/skills/ui-ux-pro-max/scripts/search.py "keyboard focus" --domain ux
```

Son consultas sin persistencia; sus resultados deben contrastarse con el brief.
En Windows, el lanzador de Impeccable es
`.agents/skills/impeccable/scripts/impeccable.cmd`. Su motor puede requerir una
descarga adicional en el primer uso. Si no está disponible, lee directamente el
contexto del proyecto y sus referencias de revisión, e informa de la limitación.
La instalación no activa hooks, reparaciones, nuevas skills ni subagentes de
Impeccable; se conserva el contrato de un único escritor y los revisores SDD.

### Procedencia de las skills de diseño

Instalación local solicitada por el desarrollador; se conservan las licencias
originales dentro de cada skill. Referencias fijadas para poder revisar futuras
actualizaciones, sin actualización automática:

| Skill | Repositorio y carpeta de origen | Commit |
| --- | --- | --- |
| `frontend-design` | [anthropics/skills](https://github.com/anthropics/skills), `skills/frontend-design` | `2c5958ecac7cee2b583967a2c4981d0f4a75c579` |
| `ui-ux-pro-max` | [nextlevelbuilder/ui-ux-pro-max-skill](https://github.com/nextlevelbuilder/ui-ux-pro-max-skill), `.claude/skills/ui-ux-pro-max` | `4f2dbb67e64969a9a30ab00742f2d55d66ddec2b` |
| `impeccable` | [pbakaus/impeccable](https://github.com/pbakaus/impeccable), `.agents/skills/impeccable` | `924bbd194d02e5e15a227e75b5901fb0d7a4ecad` |

Adaptaciones locales: nota de integración SDD en las tres skills y rutas de
ejecución de UI UX Pro Max ajustadas a su instalación en este proyecto.

## Puertas de autorización

Las autorizaciones son independientes:

- `PASS` del revisor de especificación no aprueba `spec.md`.
- Aprobar `spec.md` solo permite planificar.
- `PASS` del revisor de plan no aprueba el plan.
- Aprobar el plan no permite implementar.
- Solicitar implementación no autoriza ramas, staging, commits o publicación.
- `PASS` del revisor final no autoriza el commit.
- Un commit no autoriza push, merge, despliegue ni integración.

## Artefactos y estados

Cada cambio relevante vive en `docs/specs/NNN-slug/`:

- `spec.md`: `draft`, `approved`, `completed` o `superseded`.
- `plan.md`: `draft` o `approved`.
- `tasks.md`: `pending`, `in_progress` o `completed`.

Los criterios se identifican como `CA-NN` y las tareas como `T-NN`. Cada tarea
debe referenciar criterios y la verificación final debe aportar evidencia por
criterio.

## Contrato de los revisores

Los tres revisores responden en español con:

```text
Resultado: PASS | BLOCKED
Bloqueos:
Riesgos no bloqueantes:
Evidencias:
```

No escriben archivos ni sustituyen la aprobación del desarrollador.

## Memoria y cierre externo

El cierre de una especificación ocurre tras completar su implementación y
verificación final. Aprobar la especificación o el plan abre la fase siguiente.

1. Crear los commits finales y publicar la rama de la especificación en
   `origin`.
2. Integrar la rama en `dev` y comprobar que el resultado está en
   `origin/dev`. `main` se reserva para despliegues.
3. Una vez confirmada la integración, borrar la rama de la especificación
   en local y en `origin` si puede hacerse de forma segura y sin forzar.
   Si queda trabajo sin integrar o el borrado requiere pasos adicionales,
   conservar la rama e indicar el motivo.

Las autorizaciones de Git se gestionan según la sección Git de
`AGENTS.md`. Si falta una comprobación o una acción de cierre, se registra
el pendiente.

`MEMORY.md` solo mantiene trabajo incompleto, decisiones pendientes, contexto
para continuar y el estado inmediato. No es backlog, historial ni autorización.

Si el cierre depende de publicación o CI externo, la verificación local deja el
pendiente en `MEMORY.md`. Una tarea posterior, tras observar el resultado real,
actualiza la memoria y retira el pendiente.

