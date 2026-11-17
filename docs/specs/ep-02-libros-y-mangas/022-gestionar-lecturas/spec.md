---
id: "022"
title: "Gestionar las fichas compartidas de Lecturas"
status: completed
---

# 022 — Gestionar las fichas compartidas de Lecturas

Historia propuesta para v0.2 de la [épica EP-02](https://github.com/elkio-studio/UsTwo/issues/24).
Continúa el [alta](../020-alta-lecturas/spec.md) y la
[consulta](../021-consulta-lecturas/spec.md).

Inicio: 17 de noviembre de 2026, 19:38 CET.
Cierre local: 17 de noviembre de 2026, 19:59 CET.
Tiempo transcurrido: 21 min.
GitHub Projects: **0.5 h** (estimación basada en el tiempo transcurrido).

Cierre previsto en Sprint 2. Capacidad de referencia de 4 h/día y 20 h
por sprint. Esfuerzo y asignación pendientes de validar.

## Alcance

Un miembro puede corregir o retirar una ficha de Libros, Manga o Cómics
desde su detalle en la estantería actual. Las valoraciones, notas y
progreso de lectura quedan fuera de esta historia.

En una ficha manual se pueden editar el título, la autoría, la fecha de
publicación y la sinopsis. El título sigue siendo obligatorio.

En una ficha de catálogo se pueden completar la autoría, la fecha de
publicación y la sinopsis cuando faltaban en el catálogo. Los datos
importados se conservan tal como llegaron. El identificador y el
origen de catálogo se mantienen al guardar. Esta es la regla para
esta historia mientras EP-06 define la política general de metadatos.

## Flujo y errores

- **Edición.** Desde la ficha, el miembro cambia los campos permitidos.
  Si una validación falla, se indica el motivo, se mantienen los datos
  escritos en el formulario y la ficha guardada conserva los anteriores.
  Al volver al listado, se recargan las lecturas y se conserva la
  pestaña activa, también al regresar con Atrás del navegador.
- **Borrado.** La ficha pide confirmación. Cancelar conserva el registro.
  Confirmar lo retira de la colección y vuelve a su pestaña. Si falla,
  la ficha sigue disponible y se muestra el error.
- **Permisos.** Las operaciones se limitan a la estantería de la ficha.
  Una URL de otra estantería no permite editar ni borrar el registro.

## Criterios de aceptación

- **CA-01.** Un miembro puede editar los campos de una ficha manual. Los
  cambios válidos se mantienen al recargar. Un título vacío se rechaza.
- **CA-02.** Un miembro puede completar un campo que faltaba en el
  catálogo y ver el valor al recargar. Si intenta modificar un dato
  importado, se rechaza el cambio y se conserva la ficha anterior.
- **CA-03.** Eliminar requiere confirmación. Cancelar conserva la ficha.
  Tras confirmar, la ficha desaparece también al recargar la colección.
- **CA-04.** Se rechazan las operaciones de cuentas ajenas y las que
  usen una ruta de una estantería distinta de la ficha, incluso si la
  cuenta pertenece a ambas.
- **CA-05.** Un fallo de validación o guardado deja intacta la ficha
  anterior. Un fallo de borrado conserva el registro en la colección.
  Los formularios y la confirmación se pueden usar en móvil y con
  teclado, con foco visible.
