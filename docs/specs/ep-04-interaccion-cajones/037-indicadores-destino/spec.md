---
id: "037"
title: "Distinguir origen y destino durante la reorganización de cajones"
status: completed
---

# 037 — Distinguir origen y destino durante la reorganización de cajones

**GitHub Projects: `0.25` h estimadas.**

Inicio: 27/11/2026, 12:22 CET.
Rama: feat/indicadores-destino, desde dev23a3655.
Historia: [Indicar destinos válidos durante el arrastre, #47](https://github.com/elkio-studio/UsTwo/issues/47).
Épica: [EP-04, #26](https://github.com/elkio-studio/UsTwo/issues/26).
Referencia: [spec 036, movimiento e intercambio](../036-arrastrar-cajones/spec.md).
Sprint de cierre previsto: Sprint 2. Capacidad orientativa: 4 h/día de lunes
a viernes, 20 h/sprint; estimación pendiente de validación técnica.

## Problema y objetivo

El movimiento ya está implementado, integrado y validado en la spec 036.
El origen se marca con borde discontinuo y el destino activo con borde continuo.
Al recoger, target se inicia en origin y Shelf asigna ambas señales al origen:
la regla del destino prevalece, aunque confirmar ahí cancela sin guardar.

Corregir esa ambigüedad y verificar que la representación que acompaña al
puntero y los demás indicadores no impiden reconocer el destino antes de
soltar. La revisión previa fue por lectura de código: no acredita oclusión
real ni justifica un rediseño preventivo de los cajones.

## Alcance incluido

- Separar visualmente origen/cancelación y destino de colocación activo.
- Mantener los patrones existentes: origen discontinuo, destino continuo;
  no depender exclusivamente de un cambio de color.
- Comprobar que contornos e imagen de arrastre permiten reconocer destino y
  colección ocupante, con vacíos y ocupados, en escritorio y móvil.
- Incluir ratón, pulsación mantenida/arrastre, selección por toque y teclado
  existentes, sin cambiar su funcionamiento.
- Conservar anuncios accesibles y errores; sin recuperar guía estática ni
  botón Cancelar movimiento, retirados expresamente en la spec 036.

## Alcance excluido

- Nuevos gestos, modo Reorganizar, confirmaciones o mecanismo Mover aquí.
- Cambiar qué destinos permiten mover/intercambiar ni añadir restricciones.
- Cambios de backend, contratos, permisos, persistencia, datos o migraciones.
- Rediseño de assets, imágenes o animaciones de los cinco tipos de cajón.
- Nuevos paneles, textos visibles permanentes o cambios de animaciones normales.
- Limpieza de código muerto del informe transversal, reservada a otra historia.

## Actores

- Miembro que reorganiza su estantería mediante los mecanismos existentes.
- Usuario de teclado o lector de pantalla que necesita señales coherentes
  con la operación que realmente se realizará.

## Flujo principal

1. Recoger un cajón identifica únicamente su origen mediante la señal de origen.
2. Elegir otro hueco vacío lo identifica como destino activo de movimiento.
3. Elegir otro hueco ocupado lo identifica como destino activo de intercambio.
4. Antes de confirmar, se reconoce qué hueco está seleccionado y, si está
   ocupado, qué colección contiene; los indicadores no impiden esa lectura.
5. Al guardar o cancelar se retiran las señales temporales, conservando el
   resultado y el retorno de foco definidos en 036.

## Flujos alternativos, errores y límites

- Volver al origen muestra solo señal de origen; confirmar cancela sin escritura.
- Fuera de los huecos, incluidos sus espacios intermedios, no se muestra
  destino activo de colocación. Soltar ahí cancela según 036.
- Todos los otros huecos son válidos: vacío mueve y ocupado intercambia.
  No marcar un ocupado como inválido ni insinuar que su contenido será borrado.
- Cambiar de destino, cancelar, perder el gesto, cambiar de estantería o un
  error de guardado no dejan un contorno de destino obsoleto.
- La copia visual que sigue al puntero es inerte: no introduce un segundo
  origen/destino aparente ni una zona de interacción. Revisar también atributos
  de estado copiados, no solo su opacidad o pointer-events.
- Respetar movimiento reducido y conservar foco/teclado y anuncios de destino,
  origen/cancelación y resultado. Ocultar texto accesible no lo vuelve inútil.

## Criterios de aceptación

- **CA-01.** Al recoger o volver al origen, este mantiene su señal discontinua
  y no muestra la señal continua reservada a un destino válido distinto.
- **CA-02.** Al seleccionar un hueco diferente, vacío u ocupado, se identifica
  mediante el contorno continuo antes de confirmar; el origen sigue distinguible.
- **CA-03.** Fuera de los huecos no existe destino activo de colocación; al
  finalizar/cancelar no persisten indicadores temporales de la operación anterior.
- **CA-04.** En escritorio y móvil, contorno y colección del destino activo
  siguen siendo reconocibles aunque la copia de arrastre pase por encima;
  la copia no muestra contornos de estado que se confundan con huecos reales.
- **CA-05.** Ratón, arrastre táctil, toque de destino y teclado presentan señales
  coherentes con mover/intercambiar/cancelar. No cambian navegación, gestos,
  foco, anuncios accesibles ni preferencia de movimiento reducido de 036.
- **CA-06.** Se conserva el comportamiento anterior: vacíos mueven, ocupados
  intercambian, origen/fuera cancelan; no se cambia contenido ni persistencia,
  permisos o aislamiento. Los + y el diálogo de alta mantienen sus reglas.

## Decisiones confirmadas y revisión de diseño

Ángel solicita una rama desde dev y preparar esta spec sin implementar.
El alcance autorizado es corregir la señal del origen y verificar que los
indicadores no tapen el destino. Se reutilizan las reglas de ocupación de 036,
que sustituyeron expresamente la antigua prohibición de ocupado por intercambio.

La revisión previa de diseño identifica la superposición de estados en el
origen y la necesidad de comprobar la imagen de arrastre en pantalla.
No se ha probado que exista una oclusión concreta: comprobarla forma parte
de esta historia. No se fijan tamaños, colores o desplazamientos nuevos como
si ya estuviesen aprobados. Si para resolver un hallazgo hace falta cambiar
significativamente composición o interacción, se presentará antes de hacerlo.

## Validación prevista

- Matriz de origen, vacío, ocupado, espacios intermedios y fuera; cambio de
  destino, cancelación y final de gesto. Cubrir los cinco tipos de cajón.
- Confirmación visual humana en escritorio/móvil de contornos, nombres y copia
  sobre destino, incluyendo bordes de la estantería. No darlo por probado con
  build, pointer-events o una lectura de CSS.
- Teclado y alternativa táctil sin arrastrar; foco y anuncios, movimiento reducido.
- Pruebas automatizadas proporcionales a las reglas modificadas y regresión
  existente de geometría/cancelación. Sin tests que solo repitan CSS/JSX.
- Si fueran necesarias pruebas backend, usar exclusivamente SQLite en memoria
  con guardia antes de ejecutar tests, nunca la base local ni Supabase.
- Revisión de diseño antes/después y auditoría final de evidencias. No abrir
  navegador sin autorización; puede aportar evidencia visual Ángel.

## Estado

Spec aprobada expresamente por Ángel el 27/11/2026 tras revisión de diseño
coherente y spec_reviewer PASS. Solicita preparar plan y tareas sin tocar código.
No autoriza implementar, crear commits ni publicar.
Posteriormente Ángel aprueba el plan y solicita implementación/verificación,
sin navegador ni publicación. Corrección aplicada y controles automáticos
registrados en tasks.md; validación visual confirmada por Ángel y cierre local
registrado tras auditoría final PASS.

Revisión de diseño de solo lectura: coherente, sin decisiones funcionales
adicionales imprescindibles. Comprobar oclusión real es una evidencia pendiente,
no una afirmación sobre el resultado ni autorización para rediseñar el ghost.

## Cierre — 27/11/2026

Inicio: 12:22 CET. Cierre: 12:38 CET del mismo día. Transcurrido: 16 minutos,
sin pausas comunicadas. Estimación por tiempo transcurrido, no cronometraje de
trabajo efectivo. Un cuarto completo y un minuto restante, redondeado hacia
abajo según el proceso SDD. Valor GitHub Projects al principio del documento.

Ángel confirma que el origen se distingue y los indicadores no tapan el destino
en escritorio, móvil y teclado. CA-04 queda acreditado por esa evidencia humana;
el agente no abrió navegador ni atribuye la prueba visual al build o al CSS.
No fue necesario otro ajuste del ghost para resolver oclusión.

Revisión de diseño previa/posterior APTO; reauditoría test_reviewer PASS, sin
bloqueos. Node: tres pruebas correctas; build: 705 módulos; Prettier y diff
correctos. Sin cambios de CSS, backend, gestos, contratos ni datos.

Completada implementación y validación local. Commit, publicación, integración
en dev, CI y traslado de documentación pendientes de autorización.
