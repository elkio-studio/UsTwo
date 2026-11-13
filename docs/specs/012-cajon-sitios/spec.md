---
id: "012"
title: "Investigación de mapas"
status: completed
---

# 012 — Investigación de mapas

Historia vinculada a la [épica EP-01, issue #23](https://github.com/elkio-studio/UsTwo/issues/23).
Ángel revisa y aprueba la spec y sus evidencias.
Rafa organiza las historias y las prioridades de producto.

## Objetivo y entregable

Determinar una solución gratuita y sostenible para mostrar un mapa 2D,
buscar por nombre o dirección y elegir coordenadas al pulsar el mapa.
El entregable es el [informe de investigación de mapas](../../investigaciones/mapas.md).

## Alcance

Comparar proveedores, comprobar las funciones en demos aisladas y documentar
las condiciones de uso y la persistencia prevista. La integración del cajón,
el guardado real, el aislamiento entre estanterías y la verificación en móvil
y con teclado corresponden a historias de implementación. Esta historia
se valida con el informe y sus evidencias.

## Criterios de aceptación

- **CA-01.** El informe compara Google Maps y alternativas e indica las
  condiciones, cuotas y claves requeridas.
- **CA-02.** El informe registra las pruebas de búsqueda por nombre y
  dirección, coordenadas y marcador al pulsar, y búsqueda inversa.
- **CA-03.** El informe explica cómo se guardarían las coordenadas y qué
  atribución y límites de uso se deben respetar.
- **CA-04.** El informe recoge la elección, una alternativa y los riesgos
  observados. Si la vía elegida resulta inviable, Ángel revisa el alcance
  antes de implementar. La investigación se realiza con los planes gratuitos
  y las demos públicas.

## Decisión aprobada

Usar Leaflet para el mapa 2D y Geoapify Free para el mapa base, la búsqueda
y la geocodificación inversa. Las razones y evidencias están en el informe.

## Validación y cierre

La revisión documental comprobó la trazabilidad de CA-01 a CA-04.
Ángel aprobó la especificación y el informe el 13 de noviembre de 2026.
La historia de investigación queda cerrada con las evidencias de las demos.
La prueba conjunta y el consumo real de créditos se verificarán en la
integración del cajón.
