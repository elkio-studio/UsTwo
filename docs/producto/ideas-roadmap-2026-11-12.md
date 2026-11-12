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
