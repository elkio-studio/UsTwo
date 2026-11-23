---
id: "029"
title: "Investigar metadatos de enlaces para la wishlist"
status: completed
---

# 029 — Investigar metadatos de enlaces para la wishlist

Investigación de la [épica EP-03](https://github.com/elkio-studio/UsTwo/issues/25),
Enlaces, compras y regalos, para v0.2.

Inicio: 23 de noviembre de 2026, 13:12 CET.
Sprint de cierre previsto: Sprint 2. Capacidad orientativa: 4 h/día y
20 h por sprint. Estimación y asignación pendientes de validación técnica.

## Problema y objetivo

Centralizar listas de cosas que los miembros quieren comprar. Investigar
qué título, descripción y precio pueden obtenerse al introducir una URL,
sin costes obligatorios ni depender de una API externa de extracción.
No se presupone que todas las tiendas permitan o faciliten recuperar datos.

La petición inicial mencionaba comparar APIs gratuitas. Ángel concreta que
la solución deseada parte de guardar enlaces y sus metadatos: esta investigación
prioriza los estándares públicos de las páginas y la alternativa manual,
sin seleccionar ni integrar una API externa.

## Entregable y alcance incluido

Un informe en [docs/investigaciones/enlaces.md](../../../investigaciones/enlaces.md), con:

- Comparativa de fuentes dentro del HTML: título y descripción estándar,
  Open Graph y datos estructurados de productos/ofertas cuando existan.
- Evaluación de precio e identificación de moneda, precios múltiples,
  rangos, variantes, ofertas y datos ausentes o ambiguos.
- Pruebas de lectura sobre un conjunto acotado de URLs públicas sin sesión,
  incluyendo páginas con metadatos, sin ellos, redirecciones y páginas
  inaccesibles o que bloqueen la consulta. Fechar y registrar los resultados.
- Límites de extracción, condiciones publicadas, dependencias técnicas,
  mantenimiento y posibles costes, sin contratar servicios.
- Análisis de seguridad de las URLs y de las peticiones realizadas desde
  servidor, con recomendaciones verificables para la futura implementación.
- Recomendación de alcance automático, sus límites y alta/corrección manual
  cuando los datos no puedan recuperarse de manera fiable.

El informe concentra resultados y fuentes. La spec define el contrato
de investigación; los resultados se mantienen en el informe.

## Contexto funcional aprobado para historias posteriores

Estas decisiones orientan la investigación; no se implementan en la 029:

- El cajón permite crear varias listas de wishlist.
- Cada artículo conserva URL, título, descripción, precio, estado y quién
  lo publicó. La imagen queda fuera.
- El estado es pendiente o comprado. Marcarlo como comprado lo tacha sin
  borrarlo y se puede volver a pendiente.
- Al cambiar el estado se pide confirmación y se elige mostrar el cambio
  a todos los usuarios de la lista o excluir participantes.
- La persona excluida sigue viendo el artículo como pendiente: solo se
  oculta el cambio de estado, no el artículo ni sus otros datos.
- El autor y el estado son datos propios de UsTwo, no metadatos extraídos
  del comercio. El precio se guarda como referencia; no se incluye seguimiento
  automático de sus variaciones.

## Alcance excluido

- Implementar cajón, listas, formularios, tarjetas, persistencia, permisos,
  visibilidad selectiva del estado o marcado como comprado.
- Extraer o mostrar imágenes, stock, alertas, recomendaciones o seguimiento
  periódico de precios; pagos, compras o reservas en tiendas.
- Contratar APIs, instalar dependencias o integrar servicios nuevos.
- Eludir bloqueos, autenticaciones, CAPTCHA o restricciones de acceso;
  usar sesiones, cookies personales o credenciales para extraer información.
- Desplegar cambios, abrir Brave o modificar datos y bases de UsTwo.

## Actores y flujo de investigación

El agente realiza la investigación autorizada; Ángel revisa las decisiones
técnicas y valida el informe. Rafa organiza el producto, sin sustituir la
aprobación técnica de Ángel.

1. Identificar los formatos candidatos y consultar sus fuentes primarias.
2. Definir ejemplos públicos de tiendas y páginas genéricas representativos;
   no enviar enlaces privados, firmados o con datos personales.
3. Consultar los ejemplos de forma acotada, sin guardarlos en la aplicación,
   y anotar qué metadatos devuelve realmente cada uno.
4. Distinguir resultado ausente, ambiguo, bloqueado y fallo técnico; no
   inventar título, descripción, precio ni moneda para completar una prueba.
5. Evaluar riesgos y recomendar qué se podría rellenar automáticamente,
   qué tendría que confirmar el usuario y cuándo usar entrada manual.
6. Documentar resultados y limitaciones y presentar el informe a Ángel.

## Precio, errores y casos límite a investigar

- El precio publicado puede depender de variante, cantidad, ubicación,
  impuestos, sesión u oferta. Investigar cuándo se puede identificar un
  importe y moneda y cuándo mostrarlo como no recuperable o ambiguo.
- No convertir un precio ausente en cero ni elegir silenciosamente una
  cifra de entre varios productos, cuotas o precios tachados.
- Evaluar separadores decimales, moneda y vigencia de los datos. La prioridad
  de las fuentes y la política para ambigüedades serán recomendaciones del
  informe, sujetas a aprobación antes de implementarlas.
- Las páginas que cargan metadatos solo mediante JavaScript, cambian de
  estructura o bloquean robots no se consideran compatibles sin evidencia.
- Un fallo de extracción debe permitir, en el alcance recomendado, guardar
  el enlace y completar los datos manualmente; no depender de que la tienda
  responda para poder mantener la wishlist.

## Seguridad a investigar

- Validación de esquema, host, puertos, credenciales incrustadas y resolución
  DNS antes de una petición; no basta con comprobar el texto del enlace.
- Prevención de acceso a localhost, redes privadas y direcciones reservadas,
  incluidas IPv4/IPv6 y servicios de metadatos de infraestructura (SSRF).
- Revalidación de cada salto de redirección, límite de saltos y riesgo de
  que un dominio cambie su resolución entre la comprobación y la conexión.
- Límites de conexión, tiempo total, tamaño y tipo de respuesta; tratamiento
  del HTML como contenido no confiable, sin ejecutar scripts ni instrucciones.
- Posibles URLs relativas en los datos y riesgos de recuperar recursos
  adicionales; no realizar peticiones a imágenes ni subrecursos por esta historia.
- Evitar secretos, URLs privadas, cookies y contenido sensible en registros
  o en servicios externos.

Los ejemplos de destinos internos y ataques se describirán con casos
sintéticos o respuestas controladas, sin sondear redes internas, servicios
de infraestructura ni la base de datos real.

## Criterios de aceptación

- **CA-01.** El informe compara las fuentes estándar para título, descripción
  y precio, con fuentes primarias, límites y motivos para prescindir de una
  API externa. No presenta la cobertura de todas las tiendas como garantizada.
- **CA-02.** Se registran URLs públicas y fecha de prueba, respuesta observada
  y campos recuperables en casos con metadatos, sin ellos, con redirecciones
  y con errores o bloqueos. Lo no probado queda explícito.
- **CA-03.** Se documentan importe y moneda, sus ausencias y ambigüedades,
  incluyendo variantes o múltiples ofertas. No se declara recuperado un precio
  cuando la evidencia solo permite una suposición.
- **CA-04.** Se documentan SSRF, DNS, IPv4/IPv6, redirecciones y límites de
  respuesta con medidas propuestas y casos de validación para la futura
  implementación, sin realizar consultas reales a destinos internos.
- **CA-05.** Se entrega recomendación de alcance automático y alternativa
  manual para fallos, datos incompletos y tiendas incompatibles, sin costes
  obligatorios, imágenes ni seguimiento automático del precio.
- **CA-06.** El informe distingue las conclusiones técnicas de las decisiones
  futuras sobre listas, autoría y estado con visibilidad selectiva. Ángel
  valida el resultado antes de cualquier implementación de la funcionalidad.

## Validación y decisiones pendientes

Verificación documental de cobertura de criterios, fuentes, pruebas y enlaces
del informe. Las comprobaciones de persistencia, aislamiento entre estanterías,
móvil y teclado corresponden a las futuras historias con interfaz y datos;
no se afirman realizadas con esta investigación.

El informe determina las fuentes recomendadas, sus límites y la alternativa
manual ante precios ambiguos. Ángel aprobó expresamente la spec y el informe
el 23 de noviembre de 2026 y solicitó cerrar la investigación.
Campos obligatorios, gestión de listas y detalle técnico de visibilidad del
estado se concretarán en sus historias. Esta aprobación no implementa ni
autoriza publicar la funcionalidad.
## Comprobación breve realizada

El informe registra cinco URLs públicas y la comprobación separada del
destino de una redirección, con resultados y alternativas manuales.
Se observaron metadatos y ofertas asociadas a dos variantes, HTML con precio
visible sin oferta estructurada, ausencia de metadatos, una pantalla de bloqueo
con HTTP 200 y redirección a una categoría en vez de a un producto.

La seguridad se analiza documentalmente; no se ha sondeado ningún destino
interno ni implementado extracción, interfaz, persistencia o controles de acceso.
Casos no probados y recomendaciones pendientes se identifican en el informe.

## Cierre de investigación

- Aprobación de la spec y del informe: Ángel, 23 de noviembre de 2026.
- Inicio: 23 de noviembre de 2026, 13:12 CET.
- Cierre: 23 de noviembre de 2026, 13:26 CET.
- Tiempo transcurrido estimado: 14 minutos, sin pausas comunicadas.
- **GitHub Projects: `0.25` h estimadas**, aplicando el redondeo del proyecto.
- Revisión final documental: `PASS`, sin bloqueos. CA-01 a CA-06 cubiertos
  por las fuentes, ejemplos y límites del informe y la aprobación de Ángel.
- Enlaces relativos comprobados y `git diff --check` correctos.

Se cierra exclusivamente como investigación. No requiere implementación,
plan ni tareas de código para esta comprobación breve autorizada.
No se han modificado aplicación, migraciones, pruebas ni bases de datos.
No se ha abierto el navegador. No hay commit, publicación ni integración;
el traslado a documentation queda pendiente de autorización posterior.
