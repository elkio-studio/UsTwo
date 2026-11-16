---
id: "018"
title: "Investigación de APIs para Lecturas"
status: completed
---

# 018 — Investigación de APIs para Lecturas

Historia vinculada a la [épica EP-02, issue #24](https://github.com/elkio-studio/UsTwo/issues/24).

Inicio: 16 de noviembre de 2026, 15:14 CET.
Cierre local: 16 de noviembre de 2026, 15:35 CET.
Tiempo transcurrido: 21 minutos. Valor estimado para GitHub Projects: `0.50` h.
Sprint de cierre previsto: Sprint 1. Capacidad orientativa de 4 h al día
de lunes a viernes, 20 h por sprint. Esfuerzo pendiente de validación.

## Objetivo y entregable

Determinar cómo encontrar fichas de libros, manga y cómic con fuentes
gratuitas. El [informe de investigación](../../../investigaciones/lecturas.md)
recoge la comparativa, las pruebas y la recomendación.
Ángel revisa la especificación y la recomendación.

## Obras, series y entregas

El futuro cajón de Lecturas tendrá tres pestañas: **Libros**, **Manga** y
**Cómics**. La pestaña muestra fichas con portada cuando esté disponible.
Al abrir una saga o serie, se muestra su lista ordenada de entregas. En
manga, cada fila indica el número de tomo y su título si está disponible.

El progreso es compartido dentro de cada estantería. Si un miembro marca
una entrega como leída o no leída, los demás miembros ven ese mismo estado.
La misma regla se aplica a las obras independientes.

- **Libros.** Al añadir «Harry Potter», se muestran los libros de la
  saga, con una marca por libro. Las traducciones y ediciones de un mismo
  libro corresponden a la misma entrega. Un libro independiente puede
  registrarse y marcarse por sí solo.
- **Manga.** Al añadir «Naruto», se muestran sus tomos, con una marca por
  tomo. La serie agrupa las entregas en una sola ficha del cajón.
- **Cómic.** Una serie editorial agrupa sus entregas. Una historia
  independiente puede registrarse y marcarse por sí sola. Las series
  homónimas se distinguen por año de inicio. Si un recopilatorio contiene
  varios números, la investigación comprobará cómo los relaciona el
  catálogo y si permite evitar que aparezcan duplicados.

## Alcance

Comparar Open Library y Google Books como catálogos generales y fuentes
especializadas como Metron para cómic y AniList para manga. Evaluar primero
las condiciones de uso de cada fuente y hacer consultas aisladas cuando
se puedan realizar sin coste. Registrar por separado la cobertura
bibliográfica, la disponibilidad de portadas y sus condiciones de uso.

Quedan fuera el cajón, el buscador de la web, el guardado, la gestión
del estado de lectura, las valoraciones y los cambios de código, datos
o infraestructura. Las pruebas en móvil,
teclado y aislamiento entre estanterías corresponden a la implementación.

## Criterios de aceptación

- **CA-01.** Comparar coste, cuotas, credenciales, licencias o términos,
  atribución, condiciones para reutilizar metadatos y portadas, y
  cobertura por idioma y tipo de lectura, con atención al español.
- **CA-02.** Registrar consultas reproducibles y comprobar título, autor
  o creadores, portada e identificador. Incluir un libro con varias
  ediciones, una saga con libros distintos, un manga con varios tomos
  y un cómic buscado por serie, número e historia independiente.
  Comprobar si se pueden enumerar las entregas de una ficha, en qué orden
  vienen, qué identificador tiene cada una y si la lista está completa.
  Probar búsqueda por título y, cuando proceda, por ISBN. Distinguir una
  URL de portada devuelta de una imagen cuya carga se ha comprobado.
- **CA-03.** Documentar resultados irrelevantes, datos ausentes, ediciones
  duplicadas, tomos o números sin vínculo fiable a la serie, respuestas
  vacías y límites de uso. Distinguir los fallos observados de los riesgos
  documentados. Explicar qué identificadores distinguen la ficha principal
  de sus entregas, qué datos permiten evitar duplicados y cuándo hace
  falta confirmar o completar las relaciones manualmente.
- **CA-04.** Recomendar qué fuente o combinación usar por tipo de lectura,
  con alternativa, riesgos y alta manual para casos sin resultado fiable.
  Si una vía exige pago o depende de condiciones incompatibles, elevar
  la decisión antes de implementar.

## Validación y cierre

Ángel aprobó el informe el 16 de noviembre de 2026. La comparativa cubre
CA-01, las consultas reproducibles cubren CA-02, y los fallos y límites
observados cubren CA-03. La recomendación por tipo de lectura, con alta
manual como alternativa, cubre CA-04. El informe distingue las fuentes
probadas de las pendientes de validar con credenciales.

## Decisiones para la especificación de implementación

- Proveedor o combinación definitiva tras revisar las pruebas y condiciones.
- Cómo tratar los recopilatorios de cómic. Usar como referencia las
  pruebas del informe sobre su vínculo con los números originales.
- Campos obligatorios y reglas de alta manual en la futura especificación
  de implementación.

Recordatorio para Ángel: dejar un comentario en la historia de
implementación sobre cómo presentar Libros, incluyendo los libros
independientes y las sagas con sus entregas en la ficha.
