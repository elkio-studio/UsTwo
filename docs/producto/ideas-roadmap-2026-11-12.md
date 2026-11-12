# UsTwo — Banco de ideas y roadmap posterior a la reunión (12/11/2026)

> Estado: recopilación de ideas y propuesta de agrupación, NO alcance aprobado. No sustituye las specs ni las historias validadas.

## Estado actual
- v0.1: base de cuentas, estanterías privadas compartidas, invitaciones, cajones Películas y Series y valoraciones. Historias históricas #4–#20 revisadas y cerradas.
- v0.2: siguiente versión, aún sin alcance ni fecha acordados.
- v1.0 y v2.0: horizontes de evolución, no compromisos.

## Ideas del documento original `ideas-ustwo-v1.md`
- Sitios: cajón con globo, mapa integrado, chinchetas y lugares guardados por estantería. Google Maps es una idea de proveedor, pendiente de investigación de coste/condiciones.
- Compras y regalos: enlaces de compras e ideas de regalos.
- Libros y mangas: lecturas compartidas; representación de lomos ocupando el hueco.
- Mantener identidad visual de madera, cajones y luz cálida.
- Visión: espacio privado compartible con pareja, amistades o familia; varias estanterías por cuenta.

## Aportaciones de reunión — corto plazo y v0.2
- Arrastrar cajones entre huecos, en vez de movimiento por clic.
- Confirmación visual propia de la aplicación al enviar una invitación.
- Sustituir alertas nativas de JavaScript al eliminar elementos por confirmaciones/avisos integrados. Distinguir confirmación previa de resultado posterior.
- Investigación de mapamundi interactivo en cajones, marcadores persistentes y compartidos, lugares/restaurantes por visitar; estudiar Google Maps y alternativas gratuitas.
- Investigación de APIs gratuitas para libros, mangas y datos de productos/regalos a partir de enlaces.
- Reglas de edición: evitar modificar indiscriminadamente campos comunes procedentes de APIs externas, pero permitir editar fichas creadas manualmente. Definir exactamente campos bloqueados, correcciones y vinculación de registros.
- Personalización visual de fondo y estantería, especialmente en espacios compartidos (mencionada como corto plazo, versión por decidir).

## Ideas de evolución posterior
- Identificar quién añadió cada elemento a una biblioteca; icono/indicador de autoría.
- Perfiles visuales mediante avatares/iconos predefinidos y colores.
- Cajones configurables en contenido, estructura y apariencia, reutilizando bloques de películas, series, libros, mapas, texto, ubicaciones, imágenes, etc. Requiere brainstorming de bloques, límites y migración de cajones actuales; propuesta a partir de v1.0.
- Listas de pendientes/deseos/planes para ver, leer, visitar o realizar; registrar nivel de interés de cada miembro y comparar preferencias.
- Búsqueda y filtros dentro de cajones grandes.
- Estantería individual automática por cuenta y estanterías compartidas; definir si un mismo elemento puede aparecer en varias y cómo se sincroniza.
- Entrar tras login directamente en la estantería propia, con cambio rápido a estanterías compartidas desde la interfaz principal.

## Propuesta de épicas para refinar (NO aprobadas)
| ID | Épica | Versión candidata |
|---|---|---|
| EP-01 | Experiencia y movimiento de cajones | 0.2 |
| EP-02 | Notificaciones y confirmaciones | 0.2 |
| EP-03 | Integridad y edición de contenido | 0.2 |
| EP-04 | Investigación de mapas y servicios externos | 0.2 |
| EP-05 | Cajón Sitios y mapa interactivo | 1.0 |
| EP-06 | Compras y regalos | 1.0 |
| EP-07 | Libros y mangas | 1.0 |
| EP-08 | Personalización visual de estanterías | 1.0 / por decidir |
| EP-09 | Biblioteca individual y navegación | 1.0 |
| EP-10 | Cajones configurables y modulares | 1.0 |
| EP-11 | Búsqueda y filtros | por decidir |
| EP-12 | Identidad visual y autoría de miembros | 2.0 |
| EP-13 | Pendientes, deseos e interés compartido | 2.0 |

## Decisiones que condicionan la arquitectura
1. ¿Estantería individual automática? ¿Qué ocurre con datos repetidos en espacios compartidos?
2. ¿Un cajón configurable reemplaza a los tipos existentes o los amplía? ¿Cómo migrar datos?
3. ¿Qué metadatos de API son inmutables y quién puede corregirlos?
4. ¿Cómo se modela privacidad de regalos y visibilidad de deseos?
5. ¿Interés individual por elemento, escala y efecto en el orden de pendientes?
6. ¿Qué proveedor de mapas y catálogos es viable con presupuesto 0 €?
7. ¿Personalización por cuenta, por estantería o por miembro? ¿Quién decide en espacios compartidos?

## Siguiente paso
Validar épicas y versiones con Rafa y Ángel; después redactar historias con criterios verificables; finalmente dividir en tareas y crear Issues/Project. No implementar ni cambiar main a partir de este documento sin aprobación.

## Actualización de decisiones — refinamiento v0.2 (12/11/2026)

### Foco y horizonte
- **Decisión aprobada:** trabajar exclusivamente en v0.2 hasta final de 2026, con horizonte de planificación 31/12/2026. No es una fecha de entrega garantizada ni un sprint comprometido.
- **Decisión aprobada:** v0.2 debe incluir funcionando las tres colecciones nuevas: **Sitios y mapas**, **Libros y mangas**, **Enlaces, compras y regalos**. No basta con sus representaciones visuales.
- **Decisión aprobada:** la v1.0 se abordará después del cambio de año, orientada a rediseño, interfaces, experiencia de usuario y cambios estructurales de cajones. No refinar ni crear Issues de v1.0/v2.0 todavía. Todas las ideas de las secciones anteriores permanecen archivadas como propuestas futuras, no descartadas.

### Épicas candidatas para v0.2 (por validar en conjunto)
1. EP-01 Sitios y mapas: investigación de proveedor, cajón, registro, mapa con marcadores persistentes y gestión de lugares.
2. EP-02 Libros y mangas: investigación de catálogos, cajón, registro por catálogo/manual, consulta, gestión y **notas/comentarios y puntuaciones individuales por miembro, siguiendo Películas y Series**. Esta última capacidad está **aprobada**, no es opcional.
3. EP-03 Enlaces, compras y regalos: investigación de extracción de datos, cajón, registro, consulta y gestión de enlaces; clasificación/privacidad pendientes de definición.
4. EP-04 Interacción con cajones: arrastrar y soltar y mantener alternativa accesible.
5. EP-05 Avisos y confirmaciones: invitaciones y eliminación sin alertas nativas.
6. EP-06 Gestión de contenido y metadatos: reglas de edición para datos manuales y procedentes de APIs.
- Búsqueda y filtros: idea registrada, asignación de versión pendiente; no asumir compromiso en v0.2.

### EP-01 — decisiones confirmadas
- Guardar **solo lugares visitados**; quedan fuera listas de pendientes en v0.2.
- Datos iniciales: **nombre, ubicación, estado «Visitado» y nota opcional**.
- Propuesta de UX: el estado puede asignarse internamente, sin selector, al ser único.
- Historias candidatas: investigar proveedor; crear cajón; registrar lugar; mostrar mapa y chinchetas; consultar/editar/eliminar.
- **Pendiente de decidir:** método de selección de ubicación (búsqueda por nombre/dirección, clic en mapa o ambos). No inferir aprobación.

### EP-02 — decisiones confirmadas
- Cada miembro podrá poner **su propia nota/comentario y puntuación** a libros y mangas, igual que en Películas y Series.
- Pendiente definir si una entrada representa una obra, un volumen o un tomo; catálogo y API por investigar.

### Ideas para v1.0 y v2.0 — conservar sin refinamiento
- v1.0 candidata: cajones configurables con bloques reutilizables (películas, series, libros, mapas, texto, ubicaciones, imágenes), personalización de forma y aspecto; fondos y estanterías; biblioteca personal automática, intercambio de elementos entre espacios, navegación directa a estantería personal y cambio rápido entre compartidas.
- Futuro/v2.0 candidata: avatares y colores de perfil; autoría visible de cada ítem; pendientes y niveles de interés por miembro para películas, series, libros y planes.
- Búsqueda y filtros en colecciones numerosas: prioridad/versión pendiente.
- Todas estas ideas son **propuestas**, no requisitos de la v0.2 ni compromisos aprobados.

### Próximo paso
Refinar solo las épicas de v0.2 con Rafa y Ángel, validar historias y criterios de aceptación y, después, crear Issues y planificarlas. Mantener este documento como banco de ideas hasta aprobación e integración de este PR.
