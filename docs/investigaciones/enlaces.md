# Metadatos de enlaces para la wishlist

Comprobación breve del 23 de noviembre de 2026, aproximadamente 13:18–13:20 CET,
para la [spec 029](../specs/ep-03-enlaces-compras-regalos/029-investigacion-metadatos-enlaces/spec.md).
Lectura de cinco URLs públicas desde este equipo, sin sesión, cookies,
ejecución de JavaScript, APIs externas ni evasión de bloqueos. No se ha
implementado extracción en UsTwo ni comprobado su funcionamiento desde Render.

Informe aprobado por Ángel el 23 de noviembre de 2026. La investigación queda cerrada; su aprobación no autoriza implementar la funcionalidad.

Decisión posterior de implementación: en la
[spec 031](../specs/ep-03-enlaces-compras-regalos/031-alta-deseos/spec.md),
Ángel elige alta exclusivamente manual y precios solo en euros, y retira
el autocompletado tras probar tiendas que bloqueaban las consultas. Los
resultados de investigación siguientes se conservan como evidencia histórica;
no describen una extracción implementada actualmente en Deseos.

## Resultado

Es viable proponer título y descripción desde los metadatos públicos de
algunas páginas. El precio exige más cuidado: tiene que estar asociado al
producto y variante correctos y tener moneda identificable. No hay cobertura
universal. El formulario manual debe permitir guardar el enlace aunque la
extracción falle o solo consiga parte de los datos.

## Enlaces comprobados

Los precios siguientes son valores observados durante la prueba, no una
garantía de precio actual ni un cálculo del coste final de compra.

| URL pública | Respuesta observada | Título y descripción | Precio | Cuándo completar a mano |
| --- | --- | --- | --- | --- |
| [PcComponentes: Logitech G203 blanco](https://www.pccomponentes.com/logitech-g203-lightsync-2nd-gen-raton-gaming-8000dpi-rgb-blanco) | HTTP 200, HTML completo | Título de producto en JSON-LD y Open Graph. Descripción comercial en meta/OG y una descripción del producto en JSON-LD. | `19.99 EUR` en la oferta del producto blanco, SKU 287351. | Si no se puede asociar con seguridad la oferta a la URL, dejar el precio para confirmación manual. |
| [Amazon: producto B07W5JKFQC](https://www.amazon.es/dp/B07W5JKFQC) | HTTP 200, pero muestra una pantalla para continuar navegando | Solo título genérico del sitio; no se obtuvo título ni descripción del producto. | No recuperado. | Título, descripción y precio manuales. No dar por válida una ficha solo porque responde 200. |
| [IKEA: antigua URL de BILLY](https://www.ikea.com/es/es/p/billy-libreria-blanco-00263850/) | HTTP 301 hacia el catálogo general de productos; destino comprobado aparte y responde 200 | El destino es una categoría, no la ficha solicitada. No se obtuvo título ni descripción del producto. | No recuperado para el artículo solicitado. | Pedir un enlace vigente o introducir los datos manualmente. No importar el título o precios de una categoría. |
| [Books to Scrape: A Light in the Attic](https://books.toscrape.com/catalogue/a-light-in-the-attic_1000/index.html) | HTTP 200; sitio de demostración, no comercio real | Título en HTML y sinopsis extensa en meta description. | `£51.77` visible en un elemento HTML específico; sin oferta JSON-LD ni precio en las metaetiquetas examinadas. | La recomendación mínima deja este precio para entrada manual; no introduce un selector particular para cada tienda. |
| [Example Domain](https://example.com/) | HTTP 200; página genérica | Título HTML disponible, sin descripción meta ni producto estructurado. No es una ficha de compra. | No existe precio de producto. | Editar el título genérico y completar descripción/precio si se quiere guardar como referencia. Precio ausente nunca significa cero. |

La URL antigua de IKEA redirigió a
`https://www.ikea.com/es/es/cat/productos-products/`. No se siguió la redirección
automáticamente: se comprobó el destino público del mismo dominio y se consultó
por separado. No se buscó una forma de saltar la pantalla de Amazon.

### Un mismo HTML puede contener varios productos

PcComponentes incluía dos nodos Product con ofertas distintas:

- Blanco, SKU 287351, URL coincidente con la consultada: **19.99 EUR**.
- Negro, SKU 287353, URL de otra variante: **18.1 EUR**.

La oferta principal también declaraba gastos de envío de **3.95 EUR** y
vigencia hasta el 24 de noviembre de 2026. No confundir envío, precio de otra
variante o una oferta vencida con el precio del artículo. El ejemplo demuestra
que buscar la primera cifra o el primer campo `price` del documento no basta.

## Alcance automático recomendado y aprobado

| Campo | Fuente recomendada | Alternativa manual |
| --- | --- | --- |
| Título | Nombre del Product que corresponda al enlace; si no se identifica, `og:title` y después `<title>`, como sugerencias revisables. | Siempre editable, especialmente ante títulos genéricos o pantallas de bloqueo. |
| Descripción | Descripción del Product identificado, `og:description` o `meta name="description"`, sin ejecutar contenido. | Opcional y editable si falta, es publicidad genérica o no describe el artículo. |
| Precio | Oferta inequívoca del Product identificado, con importe y moneda. | Si falta moneda, hay varias ofertas sin selección clara, un rango o datos solo en HTML particular, pedir al usuario que lo introduzca/confirme. |

Mostrar los datos como sugerencias que el usuario puede revisar antes de
guardar. Precio como referencia puntual, sin actualización automática.
No contratar una API ni construir adaptadores por tienda para este alcance.
El uso de metadatos estándar evita claves y cuotas de una API de extracción,
pero no elimina los costes de alojamiento, transferencia o mantenimiento.

No se han probado rangos de precio, impuestos según ubicación, sesiones,
variantes elegidas mediante JavaScript ni múltiples monedas. Ante esos casos,
la recomendación conservadora es entrada/confirmación manual, no inferencia.
Se observaron importes con punto decimal y el símbolo £; no se ha validado
todavía una implementación que interprete todos los formatos regionales.

## Condiciones y seguridad para la futura implementación

Recibir HTML público no garantiza permiso de extracción ni disponibilidad
estable. No se han auditado las condiciones particulares de cada tienda:
deberán revisarse antes de adoptar una integración. No eludir CAPTCHA,
autenticación o bloqueos ni depender de cookies del usuario.

Antes de consultar una URL desde el servidor:

- Admitir solo HTTP/HTTPS y rechazar credenciales incrustadas y puertos no
  autorizados. Definir límites de conexión, respuesta y tamaño.
- Resolver y bloquear destinos internos, locales, reservados o de metadatos
  de infraestructura en IPv4 e IPv6. Validar también la dirección utilizada
  al conectar para evitar cambios de DNS entre validación y petición.
- Revalidar cada salto de redirección y limitar su número; un destino
  diferente puede ser interno o dejar de ser una ficha de producto.
- Tratar HTML/JSON-LD como datos no confiables: no ejecutar scripts, no
  renderizar HTML externo sin tratar y no cargar imágenes o subrecursos.
- No enviar cookies o cabeceras de autenticación ni registrar URLs privadas,
  claves o cuerpos de respuesta completos.

Los destinos internos se analizaron documentalmente, sin sondearlos. La
implementación futura deberá probar con respuestas controladas el bloqueo
de localhost, IPv4/IPv6 privados, redirección pública a dirección interna,
cambio de DNS, respuesta excesiva y timeout. Estas garantías **no están
implementadas ni verificadas** por la presente comprobación. Referencia:
[OWASP, prevención de SSRF](https://cheatsheetseries.owasp.org/cheatsheets/Server_Side_Request_Forgery_Prevention_Cheat_Sheet.html).

## Relación con la wishlist

Esta comprobación solo cubre título, descripción y precio desde la URL.
Autoría, listas y estado comprado/pendiente son datos propios de UsTwo,
definidos en la spec para historias posteriores. La imagen queda fuera.
La visibilidad selectiva del estado tendrá que protegerse en servidor;
no basta con ocultarlo visualmente a la persona excluida.

No hay cambios de aplicación, dependencias, migraciones ni bases de datos.
No se ha usado Brave ni guardado artículos en UsTwo.

## Fuentes de los formatos

- [Open Graph](https://ogp.me/): campos de título y descripción.
- [HTML meta description](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/meta/name): descripción del documento.
- [Schema.org Product](https://schema.org/Product), [Offer](https://schema.org/Offer)
  y [priceCurrency](https://schema.org/priceCurrency): asociación producto/oferta,
  importe y moneda.

Las URLs de la tabla son las fuentes de las observaciones. La lectura se hizo
con un cliente HTTP y análisis temporal de HTML/JSON-LD, sin parser persistido
en el repositorio. Límite de prueba: 5 s de conexión, 20 s total y 4 MiB por
respuesta, sin redirección automática. Son parámetros de esta comprobación,
no límites aprobados todavía para UsTwo.

