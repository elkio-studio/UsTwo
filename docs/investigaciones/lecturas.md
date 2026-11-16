# Investigación de APIs para Lecturas

Pruebas del 16 de noviembre de 2026 para la [spec 018](../specs/018-investigacion-apis-lecturas/spec.md).
Consultas aisladas sin credenciales ni contratación. Las respuestas son
una muestra del catálogo de ese día. Un HTTP 200 con contenido de imagen
verifica su entrega, y el permiso para mostrarla se evalúa por separado.

## Comparativa

| Fuente | Datos y cobertura | Acceso y condiciones |
| --- | --- | --- |
| Open Library | Obras, ediciones, ISBN, autores, portadas e idiomas. Probada con novela, manga y cómic, incluido un tomo español. La ficha de obra puede usar el título original. | Sin clave. 1 petición/s, o 3/s con identificación y contacto en el User-Agent. Recomienda caché y consultas puntuales. Contribuciones al catálogo bajo CC0. Las portadas siguen pautas propias y tienen cuota adicional por ISBN. [API](https://openlibrary.org/developers/api) · [Datos](https://openlibrary.org/help/faq/using) · [Portadas](https://openlibrary.org/dev/docs/api/covers) |
| Wikidata | Entidades de sagas y obras, relaciones y etiquetas multilingües. Probado con Harry Potter. La cobertura y el orden de las relaciones dependen de cada registro. | Datos CC0. Lectura pública probada sin clave. Los límites dependen de la API y el acceso. Las imágenes enlazadas tienen sus propias licencias. [Acceso](https://www.wikidata.org/wiki/Help:Data_access) · [Licencia](https://www.wikidata.org/wiki/Wikidata:Licensing) |
| MangaUpdates | Fichas de manga, títulos alternativos, autoría, serie y datos editoriales. Probado con Naruto. La ficha recuperada aporta el total de tomos en texto y no enumera cada tomo. | Lectura pública probada sin clave. Exige crédito, espaciar peticiones y usar caché. La documentación consultada no fija una cuota numérica general. Verificar condiciones de reutilización de portadas antes de mostrarlas. [API y condiciones](https://mangaupdates.apidocumentation.com/) |
| Grand Comics Database (GCD) | Series, números, variantes y recopilatorios, con idioma, créditos y referencias a portadas. Probado con Batman y dos series españolas de Mortadelo. | 30 peticiones/h anónimas solo para pruebas. Cuenta registrada con 25/min y 2.000/día según su guía. El formato de respuesta puede cambiar. Datos CC BY-SA 4.0 salvo excepciones. Los titulares conservan los derechos de las portadas. [API](https://github.com/GrandComicsDatabase/gcd-django/wiki/API) · [Licencia](https://www.comics.org/) |
| Google Books | Búsqueda por ISBN e idioma y fichas de volúmenes. Candidato para ediciones ausentes. | Clave y cuota por proyecto. Requiere atribución junto a los resultados. La consulta sin clave obtuvo 429, por lo que queda sin validar con credenciales. Revisar las condiciones de almacenamiento antes de integrarlo. [API](https://developers.google.com/books/docs/v1/using) · [Condiciones](https://developers.google.com/books/terms) · [Atribución](https://developers.google.com/books/branding) |
| Metron | Series y números con identificadores y créditos. La documentación incluye un campo de reimpresiones en el detalle del número. | Las consultas sin cuenta devolvieron 401. 20 peticiones/min y 5.000/día por cuenta. Para apps multiusuario el proveedor pide cuentas por usuario. Datos CC BY-SA 4.0, con las portadas fuera de esa licencia. Quedan por verificar los recopilatorios y el español con acceso autenticado. [API](https://github.com/Metron-Project/metron/blob/master/api/README.md) · [Condiciones](https://metron-project.github.io/blog/commercial-api-use) |
| AniList | Catálogo de series de manga. | Descartada como primera opción por la restricción de sus términos a servicios de listas o seguimiento que considere competidores. No se probó la API para esta investigación. [Condiciones](https://docs.anilist.co/guide/terms-of-use) |

## Consultas y resultados

### Libros y sagas

- En `https://openlibrary.org/search.json`, enviar `q=isbn:9780439708180`
  y pedir `fields=key,title,author_name,cover_i,edition_count,first_publish_year`.
  Devolvió *Harry Potter and the Philosopher's Stone*, J. K. Rowling,
  obra `OL82563W` y 400 ediciones en la agrupación. La portada por ID
  `15155833` respondió 200 JPEG. El ISBN localizó una edición y el resultado
  de búsqueda identificó la obra. [Búsqueda](https://openlibrary.org/dev/docs/api/search)
- En el mismo endpoint, `q=title:"Harry Potter and the Chamber of Secrets"`
  devolvió 41 coincidencias. La novela de Rowling fue `OL82537W` con
  portada `15158664`. También devolvió registros homónimos con John Williams
  y Aristides Ruiz. El título solo no distingue obras y adaptaciones.
- `GET /wiki/Special:EntityData/Q8337.json` en Wikidata devolvió ocho
  relaciones `P527`. Con `wbgetentities` y `languages=es|en` se verificó
  que siete corresponden a las novelas principales y la octava a una
  precuela (`Q1148668`). No usar la posición de la respuesta como orden
  editorial ni importar todas las relaciones como saga principal. El
  registro enlaza imágenes, sin validar aquí sus licencias individuales.
  [Ficha de la saga](https://www.wikidata.org/wiki/Q8337)

### Manga

- `POST https://api.mangaupdates.com/v1/series/search` con
  `{"search":"Naruto","perpage":3}` y después `GET /v1/series/17360452316`
  devolvieron *Naruto*, el identificador de serie, Masashi Kishimoto y
  una portada PNG que respondió 200. El mismo ID de creador aparece con
  dos roles. El estado indica 72 tomos y 24 volúmenes de otra edición
  recopilatoria. El campo `publications` devolvió publicaciones como
  Shūkan Shōnen Jump y el piloto, sin inventario de tomos.
- Open Library, con `q=title:"Naruto"`, devolvió entre los primeros
  resultados los tomos 01, 33 y 25. La consulta `q=title:"Naruto 2"`
  devolvió en esta muestra un libro para colorear sin portada. La secuencia
  de resultados refleja relevancia y no el orden de lectura.
- `GET /isbn/9788415821816.json` en Open Library identificó el tomo
  español 1 como `OL28418437M`, obra `OL8755980W` y portada
  `10317911` (200 JPEG). `GET /isbn/9781421582849.json` recuperó el tomo
  inglés 72 como `OL27191229M`, obra `OL20003363W`, sin identificador de
  portada y con una colección editorial genérica en `series`. Se
  verificaron dos tomos concretos. Queda sin demostrar una enumeración
  automática completa con sus títulos, portadas e IDs.
  [Planeta Cómic](https://www.planetadelibros.com/libro-naruto-n-0172/92916) · [VIZ](https://www.viz.com/manga-books/manga/naruto-volume-72/product/3500)

### Cómics y recopilatorios

Para GCD, usar `Accept: application/json`.

- `GET /api/series/103016/` en GCD devolvió *Batman* (2016), idioma
  `en` y 897 referencias en `active_issues`. Esa cifra cuenta registros
  del catálogo y no equivale a 897 entregas de lectura distintas.
  `GET /api/issue/1608134/` devolvió el número 4 de esa serie, un
  identificador en `api_url`, portada y créditos de Tom King y David
  Finch en una de las historias.
- `GET /api/issue/1665158/` devolvió *I Am Gotham*, número 1 del
  catálogo de recopilatorios (`series/111241/`), ISBN `9781401267773`,
  portada y 60 entradas en `story_set`, que incluyen material de
  portada. La URL de imagen de GCD devolvió 403 en la prueba. La
  respuesta JSON inspeccionada no aportó una lista estructurada y
  verificada de números originales. DC confirma que recopila
  *Batman: Rebirth* #1 y *Batman* (2016) #1–6. El catálogo web de GCD
  muestra cruces de reimpresión que no quedaron acreditados en el JSON.
  [Recopilatorio de DC](https://www.dc.com/graphic-novels/batman-2016/batman-vol-1-i-am-gotham) · [Ficha GCD](https://www.comics.org/issue/1665158/) · [Número original en GCD](https://www.comics.org/issue/1608134/)
- La búsqueda GCD `/api/series/name/Mortadelo%20y%20Filem%C3%B3n/`
  devolvió dos series españolas, de 1987 (`68543`) y 1993 (`95827`).
  `/api/series/name/Batman%20A%C3%B1o%20Uno/` devolvió cero. En Open
  Library, `q=title:"Batman Year One"` encontró varios candidatos,
  entre ellos la obra `OL15016298W` titulada *Batman*, con los autores
  Frank Miller y David Mazzucchelli. Requiere confirmación.
- En Open Library, `q=title:"I am Gotham"` devolvió dos fichas con
  títulos genéricos. La primera, `OL20047353W`, incluía a Tom King y
  portada `12649568` (200 JPEG). La segunda carecía de portada en la
  respuesta. La consulta de control `q=zzzzustwo018nomatch` devolvió
  cero resultados.

## Recomendación para revisión

1. **Libros.** Open Library para identificar obras y ediciones. Usar
   Wikidata como candidato para sugerir los libros de una saga, con
   revisión de pertenencia y orden. Conservar el ISBN normalizado como
   pista de edición y el ID de obra para agrupar sus ediciones.
2. **Manga.** MangaUpdates como candidato para identificar la serie.
   Para los tomos, usar fichas verificadas por ISBN cuando existan y
   permitir completar manualmente número y título. Un total textual
   puede ayudar a revisar la lista, pero por sí solo no identifica
   las entregas ni distingue todas las ediciones.
3. **Cómic.** GCD es el candidato más útil de la muestra para identificar
   series y números. Open Library puede complementar historias
   independientes. Mostrar año, editorial e idioma para desambiguar.
   La regla de progreso de recopilatorios queda pendiente. La muestra
   permite reconocer el recopilatorio, pero no justifica marcar
   automáticamente los números que contiene.
4. **Alta manual.** Permitir crear la ficha o completar entregas cuando
   falten relaciones, autores o portadas, haya resultados vacíos o falle
   el catálogo. Usar una portada neutra cuando falte una utilizable.

## Presentación prevista del cajón

Lecturas tendrá tres pestañas: **Libros**, **Manga** y **Cómics**. En Libros
se podrá abrir un libro independiente o una saga como Harry Potter, con
sus libros dentro. En Manga, Naruto tendrá una sola ficha y una lista
ordenada de tomos en su interior, con número, título cuando esté disponible
y casilla de lectura. Cómics mostrará series e historias independientes.
La relación entre recopilatorios y números sigue pendiente de decisión.

El progreso es compartido por los miembros de la misma estantería. Marcar
una entrega o una obra independiente actualiza el estado que todos ven.

Para el diseño posterior, distinguir el identificador local de la ficha
y los de sus entregas. Guardar los IDs externos con su proveedor y tipo
de registro. En cómic, comprobar variantes y serie de origen antes de
agrupar por número. Entre fuentes, pedir confirmación si la equivalencia
solo se deduce por título. Vincular la marca de lectura compartida a la
entrega local de la estantería (o a la obra si es independiente), de modo
que se conserve al corregir metadatos o cambiar una portada.

La muestra resulta suficiente para recomendar una vía con alta manual.
Antes de implementar, queda aprobar qué fuentes entran primero, verificar
el acceso registrado a GCD y las condiciones de portadas que se quieran
mostrar, y decidir el tratamiento de recopilatorios con estas evidencias.
