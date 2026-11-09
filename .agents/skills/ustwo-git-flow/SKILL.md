---
name: ustwo-git-flow
description: "Gestiona de forma supervisada la rama, el registro local de tarea, el staging y los commits de cambios realizados por agentes en UsTwo. No se usa para inspecciones de solo lectura ni autoriza push, merge o despliegue."
---

# Flujo Git local de UsTwo

Mantén exactamente una tarea Git activa y exige autorización concreta antes de
cada mutación. Las ramas de trabajo parten de `dev` y se integran en `dev`.
`main` se reserva para despliegues.

## Registro local

Usa estas claves de configuración local del repositorio:

```text
ustwo.active-branch
ustwo.base-commit
```

Cada clave debe tener exactamente un valor o ambas deben estar ausentes. Si el
estado está incompleto, duplicado o no coincide con la rama actual, detente e
informa; no lo repares automáticamente. `MEMORY.md` no sustituye este registro.

## Inspección previa

Antes de proponer una acción, consulta al menos rama actual, `git status`, diff
de trabajo y staged, valores completos de ambas claves y referencias locales
relevantes. Las comprobaciones de solo lectura no necesitan permiso.

## Autorizaciones

Cada acción que cambie Git requiere primero una propuesta concreta con rama,
rutas o mensaje exactos y después permiso explícito, inmediato y de un solo
uso. Una solicitud general de trabajo, una aprobación SDD, un `PASS` o un
permiso anterior no sirven. Si la acción falla o cambia la propuesta, solicita
otra autorización.

## Fases

### `start`

Exige ausencia de tarea activa, índice y árbol limpios, rama actual `dev` y,
si existe `origin/dev`, ausencia de divergencia local conocida. Propón una rama
`<tipo>/<slug>` y, tras autorización, créala y registra su nombre y el commit
base. Tipos permitidos: `feat`, `fix`, `refactor`, `docs`, `test` y `chore`; el
slug debe ser ASCII en kebab-case.

No ejecutes `fetch` o `pull` para forzar la sincronización. Si no puede
comprobarse, informa de la limitación.

### `adopt`

Permite registrar, con autorización, una rama local de trabajo existente
cuando no hay tarea activa. Inspecciona primero sus cambios y propone el
commit base, normalmente el `merge-base` con `dev`. No cambies ni limpies el
trabajo existente.

### `commit`

Exige que la rama actual coincida con el registro y que el índice esté vacío al
iniciar. Propón rutas literales y el mensaje
`<tipo>: <descripción breve en español>`. Tras autorización, prepara solo esas
rutas, revisa el diff staged y crea un único commit. No uses globs amplios ni
incluyas cambios ajenos.

### `final-commit`

Sigue las mismas reglas que `commit`. Solo después de que el commit final tenga
éxito elimina ambas claves del registro activo. Permanece en la rama de trabajo.

### `cancel`

Con autorización específica, elimina únicamente las dos claves locales. Deja
intactos rama, commits, índice y archivos de trabajo.

## Límites

El desarrollador se reserva push, integración, merge, resolución de conflictos,
cambio a `main` y borrado de ramas. No uses `fetch`, `pull`, `rebase`, `reset`,
`stash`, force push ni operaciones forzadas como reparación automática. Nunca
crees commits de merge.

