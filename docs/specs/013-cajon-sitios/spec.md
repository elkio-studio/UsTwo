---
id: "013"
title: "Añadir y abrir el cajón de Sitios"
status: completed
---

# 013 — Añadir y abrir el cajón de Sitios

Historia de la [épica EP-01](https://github.com/elkio-studio/UsTwo/issues/23).
Referencia técnica: [investigación de mapas](../../investigaciones/mapas.md).

## Objetivo y alcance

Un miembro de una estantería puede añadir un cajón de Sitios a un hueco libre
y abrirlo desde la estantería. El cajón se identifica por un globo terráqueo
que actúa como acceso a la vista de Sitios de esa estantería.

Se reutilizan el selector de tipos, los huecos y las reglas de movimiento
actuales. El mapa interactivo, las búsquedas, los marcadores y el alta de
sitios corresponden a historias posteriores. El informe de mapas conserva
la elección de Leaflet y Geoapify Free para cuando se incorpore el mapa 2D.

## Flujo y reglas

1. Un miembro pulsa el control de un hueco libre y elige Sitios entre los
   tipos disponibles.
2. Sitios ocupa ese hueco. Al pulsar el globo se abre la vista de Sitios de
   la misma estantería.
3. Si Sitios ya está colocado, elegirlo en otro hueco libre mueve el cajón
   y libera el anterior. Se conserva un único cajón de Sitios por estantería.

- El destino debe ser un hueco entre 1 y 8. Si está ocupado por otro cajón,
  incluso si se ocupó mientras estaba abierto el selector, se rechaza la
  operación con un mensaje y se conserva la distribución guardada.
- El servidor comprueba la pertenencia al abrir, colocar o mover Sitios,
  también al acceder directamente o al alterar el identificador de la
  estantería. Se mantiene la respuesta de acceso existente para usuarios
  sin sesión y para cuentas ajenas a la estantería.

## Criterios de aceptación

- **CA-01.** Sitios aparece en el selector de un hueco libre. Al elegirlo
  ocupa ese hueco y permanece allí tras recargar.
- **CA-02.** El globo identifica el cajón y abre una vista titulada «Sitios»
  con ratón, teclado y toque. El foco es visible y el enlace de vuelta
  devuelve a la estantería de origen.
- **CA-03.** El propietario y los miembros pueden mover el cajón a otro
  hueco libre. El hueco anterior se libera y la posición nueva se conserva
  tras recargar. Se rechazan los huecos fuera de rango y los ocupados,
  también al intentar colocar Películas o Series sobre Sitios.
- **CA-04.** El acceso y la colocación están aislados por estantería. Con
  dos estanterías, operar en una conserva la distribución de la otra.
  Una cuenta ajena no puede abrir ni modificar Sitios por URL directa.

## Validación y aprobación

Sprint 1. La capacidad de referencia es de 20 horas por sprint, a razón
de 4 horas al día de lunes a viernes. El esfuerzo queda pendiente de
validación técnica.

Ángel aprobó la especificación, incluida la vista inicial «Sitios» con
enlace de vuelta a su estantería. Rafa organiza las historias y prioridades.

Ángel dio el visto bueno al globo y autorizó el cierre el 13 de noviembre
de 2026. La Tierra gira sobre su eje al pasar el cursor, con el aro, el
pedestal y el letrero fijos. La variante a color es la predeterminada.
Las variantes sepia y sepia oscura quedan disponibles.
