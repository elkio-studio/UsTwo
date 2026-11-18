---
id: "024"
title: "Identificar el origen de las fichas"
status: completed
---

# 024 — Identificar el origen de las fichas

[Historia #56](https://github.com/elkio-studio/UsTwo/issues/56) de la
[épica EP-06](https://github.com/elkio-studio/UsTwo/issues/28), propuesta
para v0.2.

Inicio: 18 de noviembre de 2026, 13:24 CET.
Cierre local: 18 de noviembre de 2026, 13:48 CET.
Tiempo transcurrido: 24 min.
GitHub Projects: **0.5 h** (estimación basada en el tiempo transcurrido).

Cierre previsto en Sprint 1. Capacidad de referencia de 4 h/día y
20 h por sprint. Esfuerzo y asignación pendientes de validar.

## Alcance

Distinguir las fichas manuales de las vinculadas a un catálogo en
Películas, Series y Lecturas. El origen refleja su vínculo actual.
Una película o serie registrada manualmente que después se vincula
a TMDB pasa a identificarse como ficha de TMDB.

Películas y Series ya conservan el identificador de TMDB. Lecturas
conserva el proveedor, el tipo de registro y el identificador externo
cuando existe. Estos datos permiten identificar el origen con los
campos actuales. La historia conserva los registros existentes y su
información asociada, sin requerir una migración ni un historial de
orígenes. La política general de campos editables corresponde a la
[historia #57](https://github.com/elkio-studio/UsTwo/issues/57).

## Criterios de aceptación

- **CA-01.** Tras guardar y recargar, una ficha manual se distingue en
  el detalle de una ficha vinculada a un catálogo. Una ficha manual
  vinculada posteriormente a TMDB muestra el catálogo actual.
- **CA-02.** Se conserva el identificador externo cuando el catálogo
  lo aporta. En Lecturas el proveedor distingue el origen aunque
  falte el identificador.
- **CA-03.** Las fichas existentes conservan sus datos y valoraciones.
  Vincular una ficha manual a TMDB conserva la ficha local y sus
  aportaciones personales. Si el vínculo falla, se conserva el
  estado anterior.
- **CA-04.** El servidor puede usar el origen guardado para aplicar
  las reglas de edición. En Lecturas, una ficha de catálogo conserva
  los datos importados y permite completar los campos vacíos según
  la [spec 022](../../ep-02-libros-y-mangas/022-gestionar-lecturas/spec.md).
  Se conservan los permisos por estantería.

## Validación prevista

Revisar las pruebas existentes de alta manual, alta desde catálogo,
vinculación posterior, edición por origen y acceso entre estanterías.
Comprobar la persistencia tras recarga y que el origen se distingue
en la ficha en móvil y mediante navegación por teclado.
