---
id: "015"
title: "Consultar los sitios guardados en el mapa"
status: completed
---

# 015 — Consultar los sitios guardados en el mapa

Historia US-01.4 de la [épica EP-01](https://github.com/elkio-studio/UsTwo/issues/23).
Depende de US-01.1 ([investigación de mapas](../../investigaciones/mapas.md))
y US-01.3 ([spec 014](../014-guardar-sitio-visitado/spec.md)).

Inicio: 13 de noviembre de 2026, 21:47 CET, al crear la rama de trabajo.
Cierre local: 13 de noviembre de 2026, 22:04 CET. Tiempo transcurrido:
17 minutos. Valor estimado para GitHub Projects: `0.25` h.
Sprint previsto: Sprint 1. Capacidad de referencia: 20 h por sprint,
4 h/día de lunes a viernes. Estimación de la historia pendiente de validación.

## Problema y objetivo

Los miembros de una estantería necesitan localizar sus sitios guardados y
leer sus notas desde el mapa. La spec 014 ya muestra los marcadores y abre
el nombre al seleccionarlos. Esta historia añade la nota y verifica el
recorrido de consulta.

## Alcance incluido

- Consultar los marcadores del cajón Sitios de la estantería actual.
- Mostrar el nombre y la nota, cuando exista, al seleccionar un marcador.
- Comunicar el fallo del mapa y conservar los registros guardados.

## Alcance excluido

- Edición y borrado de sitios o listas.
- Panel lateral, ampliación del mapa y rediseño de Sitios.

## Actores

- Miembros autenticados de la estantería, incluido su propietario.

## Flujo principal

1. El miembro abre Sitios desde una estantería a la que pertenece.
2. El mapa muestra los marcadores de sus sitios guardados.
3. El miembro selecciona un marcador con clic, toque o teclado. El popup
   muestra el nombre y la nota cuando exista.
4. El miembro sale y vuelve al cajón. Los marcadores siguen disponibles.

## Flujos alternativos y errores

- Una estantería vacía muestra el mapa sin marcadores guardados.
- Un sitio sin nota muestra solo su nombre en el popup.
- Si no carga el mapa base, aparece un aviso. Cuando el mapa vuelve a
  estar disponible, se pueden consultar los registros al volver al cajón.
- Una cuenta ajena que intenta acceder mediante la URL no recibe los sitios.

## Reglas de negocio y casos límite

- Cada marcador corresponde a un sitio de una lista de la estantería actual.
- El marcador conserva el emoji de su lista o 📍 si carece de emoji.
- El nombre y la nota se muestran como texto. Consultarlos no modifica
  ningún registro.

## Criterios de aceptación

- **CA-01:** Al abrir Sitios con varios lugares guardados, el mapa muestra
  un marcador por lugar en sus coordenadas.
- **CA-02:** Clic, toque o teclado permiten abrir el popup. Se muestra el
  nombre y, si existe, la nota completa. El foco de teclado es visible y
  el contenido se puede leer en móvil.
- **CA-03:** Tras salir y volver a Sitios o recargar, reaparecen los
  marcadores con sus nombres y notas.
- **CA-04:** Con dos estanterías con datos, cada mapa muestra solo sus
  sitios. Una cuenta sin membresía no puede obtenerlos por URL.
- **CA-05:** Al bloquear la carga del mapa base, se muestra un aviso.
  Al restaurar la carga y volver a Sitios, se conservan los sitios.

## Decisiones aprobadas

- Leaflet y Geoapify Free según la investigación y la spec 014.
- Consulta limitada a la estantería autorizada y a los sitios guardados.
- Ángel aprobó el uso del popup existente para mostrar el nombre y la
  nota opcional debajo, con la estética actual de Sitios.

## Decisiones pendientes

- Ninguna.
