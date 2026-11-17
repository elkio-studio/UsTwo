---
id: "020"
title: "Registrar lecturas desde catálogo o manualmente"
status: completed
---

# 020 — Registrar lecturas desde catálogo o manualmente

Historia propuesta para v0.2 de la [épica EP-02](https://github.com/elkio-studio/UsTwo/issues/24).
Continúa el [cajón de Lecturas](../019-cajon-lecturas/spec.md) y usa el
[informe de catálogos](../../../investigaciones/lecturas.md).

Inicio: 17 de noviembre de 2026, 12:10 CET.
Cierre local: 17 de noviembre de 2026, 13:51 CET.
Tiempo transcurrido: 1 h 41 min.
GitHub Projects: **1.75 h** (estimación basada en el tiempo transcurrido).

Cierre previsto en Sprint 2. Capacidad de referencia de 4 h/día y
20 h por sprint. Esfuerzo y asignación pendientes de validar.

## Objetivo y alcance

Un miembro puede añadir una lectura desde la pestaña correspondiente
del cajón y consultarla en esa misma estantería tras recargar.

Cada pestaña muestra las portadas con la cuadrícula y las tarjetas de
Películas y Series. «Añadir» abre una página propia. Al guardar o
seleccionar una lectura guardada se abre su ficha, con la portada,
los datos y la sinopsis en el diseño compartido. Volver a Lecturas
abre la pestaña de origen. Las secciones de puntuación, notas,
edición y borrado son visuales y no permiten guardar cambios.

Los resultados del catálogo muestran portadas en una cuadrícula
adaptable de hasta tres columnas. Las tarjetas del listado comparten
la animación de Películas y Series. El título conserva el estilo
sin subrayado al pasar el cursor en las tres colecciones.

La vista previa y la ficha presentan la sinopsis con párrafos
compactos. Se limpian las referencias y los enlaces del catálogo
en el texto mostrado, también para las fichas ya guardadas.
La traducción automática queda fuera de esta historia.

| Pestaña | Unidad de alta | Ejemplo |
| --- | --- | --- |
| Libros | Un libro por ficha | Cada libro de Harry Potter tiene su propia ficha. |
| Manga | Una serie completa por ficha | Naruto se guarda una vez como serie. |
| Cómics | Un cómic por ficha | Cada cómic de Batman que se añade tiene su propia ficha. |

Quedan fuera las fichas contenedoras de sagas de libros o series de
cómics, los tomos internos del manga y el seguimiento del progreso.
Cada recopilatorio de cómic se guarda como una ficha independiente.
La relación con los números originales queda fuera de esta historia.

## Flujo

1. El miembro abre Libros, Manga o Cómics y elige añadir una lectura.
2. Puede rellenar la ficha manualmente o buscar en el catálogo disponible
   para esa pestaña. La búsqueda devuelve candidatos de la unidad
   correspondiente y permite revisar la ficha antes de guardarla.
3. Si confirma un resultado de catálogo, se conserva el proveedor, el
   tipo de registro y su identificador externo cuando existan.
4. La ficha se guarda en la estantería actual. El servidor comprueba la
   membresía y aplica la validación y la regla de duplicados acordadas.

### Duplicados

- Si ya existe una ficha con el mismo identificador de catálogo en la
  estantería actual, se bloquea el alta y se explica el motivo. El
  identificador se compara dentro de su proveedor y tipo de registro.
- Si la única coincidencia es el título, se muestra un aviso y el
  miembro puede continuar con el alta.
- Las fichas de otras estanterías quedan fuera de la comprobación.

### Campos y portadas

- El título es obligatorio tanto en el alta manual como desde catálogo.
- Si el catálogo proporciona descripción, autoría, fecha de publicación
  o portada utilizable, la ficha incorpora esos datos. Los valores
  ausentes pueden quedar vacíos y el alta manual admite solo el título.
- Cuando falta una portada utilizable o la imagen falla al cargar, se
  muestra una portada de reemplazo con el título en diagonal, en negro
  sobre fondo gris claro. La portada de reemplazo es visual y no se
  guarda como una imagen del catálogo.
- Para Cómics se usa la URL original de la portada JPEG de Comic Vine.
  La imagen se carga desde esa URL, sin convertirla ni guardar una copia.

### Catálogos y casos de error

- **Libros.** Open Library, seleccionando una obra individual. Las
  distintas ediciones de una obra pueden aparecer como resultados.
- **Manga.** MangaUpdates, seleccionando la serie completa.
- **Cómics.** Comic Vine, seleccionando un número individual o un
  recopilatorio. La búsqueda debe permitir distinguir colecciones
  homónimas por año o editorial y elegir la ficha concreta.
- Una búsqueda sin coincidencias, un fallo o un límite del proveedor
  se comunica y deja disponible el alta manual. Si falla el guardado,
  la ficha conserva los datos introducidos para que se puedan corregir
  o enviar de nuevo.

### Prueba de Comic Vine

Las consultas autenticadas del 17 de noviembre de 2026 devolvieron las
siguientes fichas. Las URLs originales respondieron HTTP 200 con
`Content-Type: image/jpeg` y firma JPEG.

| Ficha | ID | Datos devueltos |
| --- | --- | --- |
| [Batman (2016) #4 — I Am Gotham Part Four](https://comicvine.gamespot.com/batman-4-i-am-gotham-part-four/4000-542596/) | `542596` | Descripción, guion de Tom King, fecha de portada de octubre de 2016 y portada. |
| [Mortadelo y Filemón #1 — Va la T.I.A. y se pone al día](https://comicvine.gamespot.com/coleccion-ole-de-mortadelo-y-filemon-1-va-la-tia-y/4000-348147/) | `348147` | Título en español, descripción, un crédito, fecha de portada de enero de 1989 y portada. |

La búsqueda por «Mortadelo y Filemón» encontró la colección y permitió
recuperar el número. Para Batman se consultó el ID exacto porque la
búsqueda por «Batman» no mostró el volumen de 2016 entre los primeros
resultados. La prueba acredita el detalle y la portada de ese número.

## Criterios de aceptación

- **CA-01.** Desde cada pestaña con proveedor aprobado, el miembro puede
  buscar, elegir un resultado y revisar sus datos antes del alta. En
  Manga obtiene una ficha de serie. En Libros y Cómics obtiene fichas
  independientes. Guardar un recopilatorio crea una sola ficha en
  Cómics, sin crear ni vincular fichas de los números que recopila.
- **CA-02.** En las tres pestañas se puede completar el alta manual con
  el catálogo indisponible. Tras recargar, la ficha aparece en la
  pestaña y estantería correctas.
- **CA-03.** El alta desde catálogo conserva el proveedor, el tipo de
  registro y su identificador cuando el resultado los aporta.
- **CA-04.** Repetir un identificador de catálogo en la misma estantería
  bloquea el alta. Si solo coincide el título, se avisa y se puede
  continuar. La misma ficha puede guardarse en dos estanterías distintas.
  Una cuenta ajena no puede consultar ni añadir fichas de la estantería.
- **CA-05.** Una búsqueda vacía o fallida permite continuar manualmente.
  Un fallo de guardado conserva lo escrito. El flujo se puede completar
  con toque y teclado, con foco visible y errores asociados a sus campos.
- **CA-06.** Una ficha sin título se rechaza. Si el catálogo proporciona
  descripción, autoría, fecha o portada utilizable, se incorporan a la
  ficha. Una portada ausente o que falla al cargar muestra el reemplazo
  con el título.
