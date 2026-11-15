---
id: "016"
title: "Editar y eliminar sitios visitados"
status: completed
---

# 016 — Editar y eliminar sitios visitados

Historia propuesta para v0.2 de la [épica EP-01](https://github.com/elkio-studio/UsTwo/issues/23).
Depende del [alta de sitios](../014-guardar-sitio-visitado/spec.md) y de la
[consulta desde el mapa](../015-consultar-sitios/spec.md).

Inicio: 15 de noviembre de 2026, 21:25 CET.
Cierre local: 15 de noviembre de 2026, 22:26 CET.
Tiempo efectivo estimado: 21 minutos de los 61 transcurridos, tras
descontar 40 minutos de cena.
Valor para GitHub Projects: `0.50` h.
Sprint 1 previsto, pendiente de asignación de sprint y responsable.
Capacidad de referencia: 4 h/día de lunes a viernes, 20 h por sprint.
Estimación pendiente de validación técnica. El cierre requiere integración
y pruebas.

## Problema y objetivo

Un miembro necesita corregir los datos de un sitio visitado o retirarlo de
la estantería desde el mapa.

## Alcance incluido

- Consultar el nombre, la ubicación y la nota opcional del sitio.
- Editar el nombre, la ubicación y la nota desde el popup del marcador.
- Eliminar el sitio desde el popup después de confirmar.
- Reflejar las altas, los cambios y las bajas en el mapa.

## Alcance excluido

- Historial de cambios, gestión de listas y cambio de lista del sitio.
- Rediseño del cajón, panel lateral y ampliación del mapa.

## Actores

- Miembros autenticados de la estantería, incluido su propietario.

## Flujo principal

1. El miembro abre el cajón Sitios y selecciona un marcador. El popup
   permite consultar el nombre, la ubicación y la nota si existe.
2. Con «Editar sitio», ve los valores actuales y puede corregirlos.
   Al guardar, el marcador muestra los datos actualizados y se mueve si
   cambió la ubicación.
3. Con «Eliminar sitio», se le pide confirmar el borrado. Al confirmar,
   el marcador desaparece. Al cancelar, el sitio permanece intacto.

## Flujos alternativos y errores

- Si la edición es inválida o el guardado falla, se explica el problema
  y se conservan los datos introducidos para reintentar.
- Si falla el borrado, se informa y el marcador sigue en el mapa.
- Una cuenta sin acceso a la estantería o una solicitud que use el
  identificador de un sitio de otra estantería no puede consultar ni
  modificar el registro.

## Reglas de negocio

- El nombre y la ubicación son obligatorios. La nota sigue siendo opcional
  y se puede vaciar al editar.
- Se aplican las reglas del alta para nombre, nota y coordenadas. La latitud
  y la longitud deben ser finitas y estar en sus rangos válidos.
- Editar conserva la lista y el estado «Visitado». Eliminar un sitio no
  elimina su lista, aunque quede vacía.

## Criterios de aceptación

- **CA-01.** Al seleccionar un marcador se pueden consultar nombre,
  ubicación y nota cuando exista.
- **CA-02.** Se pueden guardar cambios válidos en nombre, ubicación y
  nota. Un valor inválido no modifica el sitio y el formulario conserva
  lo escrito.
- **CA-03.** Eliminar requiere confirmación. Cancelar conserva el sitio.
  Confirmar elimina el sitio y conserva su lista.
- **CA-04.** Al dar de alta un sitio aparece su marcador, al editarlo se
  actualiza y al eliminarlo desaparece. El resultado persiste al recargar.
- **CA-05.** Los miembros pueden actuar sobre sitios de su estantería.
  El acceso directo sin membresía y el uso de un sitio ajeno desde una
  estantería autorizada se rechazan sin modificar datos.

## Validación

Comprobar la consulta con y sin nota, la edición válida e inválida, la
confirmación y cancelación de borrado, los cambios del mapa tras recargar
y el aislamiento con dos estanterías. Verificar la selección, la edición
y la confirmación con toque en móvil y con teclado y foco visible.

## Decisiones aprobadas

- Las acciones de edición y eliminación se abren desde el popup.
- Cualquier miembro puede editar y eliminar sitios de su estantería.
- El borrado requiere confirmación.
- El popup muestra la latitud y la longitud guardadas.
- La ubicación se corrige mediante búsqueda, selección en el mapa o
  entrada directa de coordenadas. Cambiar el punto conserva el nombre
  que figure en el formulario hasta que el miembro lo edite.

## Decisiones pendientes

- Ninguna.
