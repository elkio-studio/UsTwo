---
id: "014"
title: "Guardar un sitio visitado"
status: completed
---

# 014 — Guardar un sitio visitado

Historia de la [épica EP-01](https://github.com/elkio-studio/UsTwo/issues/23).
Continúa la [spec 013](../013-cajon-sitios/spec.md) y se apoya en el
[informe de investigación de mapas](../../investigaciones/mapas.md).

La especificación comenzó el 13 de noviembre de 2026 a las 16:11, según
la creación de la rama de trabajo.
Se cerró el mismo día a las 20:47 CET. El tiempo efectivo de trabajo,
descontada la pausa, fue de 2 h 45 min (`2.75` h en GitHub Projects),
según confirmó Ángel.

Sprint de cierre previsto: Sprint 1. Capacidad de referencia: 4 horas
diarias de lunes a viernes, 20 horas por sprint. El esfuerzo queda pendiente
de validación técnica.

## Objetivo y alcance

Un miembro de una estantería puede guardar un lugar visitado con un nombre
y una ubicación precisa. Puede añadir una nota. El emoji del marcador
lo determina la lista. El estado se asigna automáticamente como «Visitado».

La historia empieza en la vista «Sitios» de la estantería. Incluye el mapa 2D
necesario para elegir la ubicación y comprobar el marcador tras guardar.
Quedan fuera los sitios pendientes de visitar, las fotos, las valoraciones,
la edición y el borrado de sitios guardados y el rediseño del cajón.

Para una historia posterior queda ampliar el mapa para que ocupe la mayor
parte de la pantalla y situar la búsqueda, el formulario y las listas en
un panel lateral izquierdo plegable en escritorio. En móvil, el panel
se abrirá desde abajo. La gestión completa de listas y sitios guardados
también queda para esa historia.

## Flujo principal

1. La persona abre el formulario de alta en «Sitios».
2. Puede buscar una calle, dirección o establecimiento. Al elegir un
   resultado, el mapa muestra su punto y el formulario sugiere la dirección
   o el nombre del establecimiento. El nombre es editable.
3. También puede pulsar o tocar el mapa. El marcador conserva las coordenadas
   exactas elegidas y la búsqueda inversa puede sugerir una calle o un
   establecimiento. Si el punto queda sin nombre, puede escribir uno o
   guardar usando las coordenadas como nombre. Si introduce las coordenadas
   directamente, escribe el nombre a mano.
4. Puede añadir una nota al sitio.
5. La persona elige una lista de esa estantería. Si todavía no hay listas,
   crea la primera al guardar el sitio y le pone un nombre. Las listas
   pueden llevar un emoji opcional como distintivo.
6. El sitio queda guardado en la lista elegida como «Visitado» y aparece
   en el mapa con su nombre y un marcador con el emoji de la lista.
   Si la lista carece de emoji, el marcador usa 📍.

## Reglas y errores

- El nombre siempre se persiste con un valor. La ubicación requiere latitud
  y longitud numéricas, finitas y dentro de sus rangos válidos
  (−90 a 90 y −180 a 180, respectivamente).
- El servidor determina la estantería desde el acceso autorizado y asigna
  «Visitado». Solo sus miembros pueden consultar y guardar sus sitios.
- El cajón empieza sin listas. Cada sitio pertenece obligatoriamente a
  una lista de su estantería. Al guardar la primera ubicación, la primera
  lista y el sitio se guardan juntos. Si falla uno de los dos, se conservan
  los datos del formulario y no se guarda ninguno.
- Una vez creada la primera lista con su sitio, los miembros pueden crear
  listas adicionales con nombre y emoji opcional, dejarlas vacías y
  elegirlas al guardar sitios más adelante.
- Si la búsqueda falla, se limita o no encuentra resultados, se informa
  y se ofrece elegir el punto a mano. La entrada directa de coordenadas
  queda disponible si falla el mapa base.
- Si falla la búsqueda inversa, se conserva el punto pulsado y se puede
  continuar con un nombre manual o las coordenadas como nombre.
- Los errores de búsqueda y guardado se muestran sin perder el nombre,
  la ubicación, la nota ni los datos de la lista. Si el guardado falla,
  se puede corregir el dato y reintentar.
- La atribución del mapa y sus fuentes es visible según el informe.

## Criterios de aceptación

- **CA-01.** El alta guarda un nombre y una ubicación válida. La nota es
  opcional. Todos los marcadores de una lista muestran el emoji de esa
  lista. Si la lista carece de emoji, aparece 📍.
- **CA-02.** El servidor asigna «Visitado» al crear el sitio. El formulario
  carece de selector de estado.
- **CA-03.** La búsqueda por nombre y dirección permite elegir el punto y
  editar el nombre sugerido. Pulsar el mapa conserva las coordenadas del
  clic y permite guardar con el nombre sugerido, uno manual o las propias
  coordenadas si el punto queda sin nombre. Al introducir las coordenadas
  directamente, el alta exige escribir el nombre.
- **CA-04.** El servidor rechaza ubicaciones inválidas y escrituras de
  cuentas ajenas. Con dos estanterías, cada una muestra solo sus sitios.
  Tras recargar se conservan el marcador y los datos guardados.
- **CA-05.** Una búsqueda fallida o vacía permite continuar por vía manual.
  Si falla la búsqueda inversa, el punto elegido se conserva. Si falla el
  mapa base, se pueden introducir las coordenadas directamente.
  Un error de guardado explica el problema y conserva los datos del
  formulario para reintentar.
- **CA-06.** En móvil se puede elegir un punto tocando el mapa. Con teclado
  se puede buscar, elegir un resultado o introducir coordenadas, editar
  los campos, elegir una lista o indicar el emoji de la primera lista al
  crearla, y guardar. La atribución sigue visible.
- **CA-07.** En un cajón nuevo no hay listas. Al guardar la primera
  ubicación, la persona crea su primera lista con nombre y emoji opcional
  y el sitio queda dentro. Si hay varias listas, puede elegir una de su
  estantería. Se rechaza guardar un sitio sin lista o en una lista ajena.
  Si falla el primer guardado, no queda creada una lista vacía.
- **CA-08.** Después del primer guardado, un miembro puede crear otra
  lista vacía. Tras recargar, la lista sigue disponible y se puede
  elegir al guardar una nueva ubicación.

## Decisiones aprobadas y verificación

Ángel eligió Leaflet para el mapa 2D y Geoapify Free para el mapa base y
las búsquedas directa e inversa. También confirmó que la lista puede
tener un emoji opcional y que este determina el marcador de sus sitios.
El informe valida por separado la búsqueda, las coordenadas y el marcador.
La verificación del flujo integrado, los errores del proveedor y el consumo
observado de créditos se recoge en [las tareas](tasks.md).

Ángel confirmó que la primera lista la crea la persona al guardar la
primera ubicación. Las listas posteriores pueden crearse vacías y
rellenarse más adelante.

Ángel aprobó la especificación y solicitó el plan de implementación.
