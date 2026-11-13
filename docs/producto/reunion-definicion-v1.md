# UsTwo — Reunión de definición y priorización de la versión 1.0

**Fecha prevista:** viernes 13 de noviembre de 2026  
**Participantes:** Rafa (producto, alcance y prioridades) y Ángel (viabilidad e investigación técnica).  
**Estado:** guía de discusión; ninguna respuesta ni prioridad de este documento constituye una decisión aprobada.

## Objetivo y resultados esperados

Acordar qué problema resuelve UsTwo, para quién, qué capacidades son imprescindibles para la 1.0, cuáles se posponen y qué incertidumbres requieren investigación. Salir con un alcance provisional, responsables de dudas y siguiente paso para refinar historias. **No convertir automáticamente ideas en Issues.**

## Agenda sugerida (75 minutos)

1. **Visión, usuarios y éxito (10 min).** ¿UsTwo es para parejas, amistades y familias o para un caso más concreto? ¿Cuál es la situación de uso principal? ¿Qué evidencia indicaría que la 1.0 aporta valor?
2. **Inventario y necesidades imprescindibles (20 min).** Revisar lo existente y recoger, sin filtrar, las funcionalidades que Ángel considera imprescindibles. Para cada una: ¿qué necesidad resuelve?, ¿qué pasa si no está?, ¿es un requisito de lanzamiento o una preferencia?
3. **Recorte de alcance (20 min).** Clasificar cada capacidad como P0/P1/P2/P3. Si todo parece P0, comparar el coste de retrasar el lanzamiento frente al valor concreto.
4. **Definir el corte 1.0 (15 min).** Describir un recorrido completo que debe poder realizar una persona invitada; decidir qué cajones entran, qué queda fuera y qué condiciones de calidad/publicación bloquean el lanzamiento.
5. **Riesgos y próximos pasos (10 min).** Registrar preguntas abiertas, investigaciones de Ángel y decisiones de producto de Rafa. No asignar soluciones técnicas desde producto.

## Preguntas de producto que debemos resolver

### A. Visión y usuarios
- ¿Qué problema cotidiano resuelve UsTwo mejor que una lista compartida convencional?
- ¿A quién queremos invitar a usarlo inicialmente? ¿Ángel y Alejandra son usuarios piloto o el límite de público?
- ¿Una estantería representa siempre un grupo privado? ¿Qué tamaño y tipos de grupo queremos soportar en 1.0?
- ¿Qué significa «1.0 terminada»: uso interno, piloto con invitados o lanzamiento accesible a usuarios externos?
- ¿Qué recorrido de principio a fin demuestra su utilidad? ¿Cómo comprobaremos que funciona con usuarios reales?

### B. Funcionalidades existentes: validar antes de dar por cerradas
- ¿Registro, inicio de sesión, Mi cuenta, espacios, roles e invitaciones funcionan correctamente en el entorno objetivo?
- ¿Crear, renombrar, abandonar y eliminar estanterías tiene el comportamiento y las confirmaciones esperadas?
- ¿Películas y Series cubren búsqueda TMDB, alta manual, duplicados, edición, valoraciones individuales, borrado y permisos?
- ¿La experiencia visual de recibidor, estantería y cajones funciona en móvil, teclado y movimiento reducido?
- ¿Qué incidencias o limitaciones conocidas existen? ¿Qué está implementado pero no validado por ambos?
- ¿Hay despliegue funcional, persistencia, copias de seguridad y procedimiento de recuperación adecuados para una 1.0? Ángel investigará las opciones técnicas; producto definirá el resultado exigido.

### C. Cajón Sitios (propuesta, no aprobado para 1.0)
- ¿Guardamos lugares visitados, por visitar o ambos? ¿Qué datos son imprescindibles?
- ¿Qué debe poder hacer una persona de principio a fin: buscar, marcar, editar, compartir, eliminar?
- ¿El globo y el mapa interactivo son imprescindibles para el valor del cajón o una dirección visual deseable?
- ¿Qué ocurre sin mapas o sin conexión? ¿Se requiere búsqueda geográfica?
- **Investigación para Ángel:** alternativas y condiciones de uso/coste de Google Maps u otras opciones con presupuesto inicial de 0 €. No elegir proveedor antes de revisar la necesidad.

### D. Compras y regalos (propuesta)
- ¿Son una única colección o dos comportamientos distintos?
- ¿Solo guardamos enlaces o también precio, imagen, destinatario, estado y notas?
- ¿Hay que evitar que alguien vea su propio regalo? ¿Quién puede editar, reservar o marcar comprado?
- ¿Qué mínimo aporta valor sin convertirlo en un sistema de comercio?

### E. Libros y mangas (propuesta)
- ¿Una colección común o dos categorías? ¿Leídos, pendientes o ambos?
- ¿Qué información y valoración se necesita? ¿Hay obras, tomos o volúmenes?
- ¿La representación con lomos de libros es requisito del primer lanzamiento o mejora visual posterior?
- ¿Se necesita catálogo externo o basta alta manual?

### F. Otras ideas y experiencia
- ¿Videojuegos sigue en la visión? ¿Qué otras funcionalidades quiere Ángel sí o sí?
- ¿Qué debe mantenerse de la identidad de madera, cajones y luz cálida?
- ¿Qué mejoras actuales de UX son bloqueantes y cuáles pueden esperar?

### G. Alcance, calidad y entrega
- ¿Qué tres capacidades perderían más valor si se eliminaran de 1.0?
- ¿Qué no vamos a hacer en 1.0, aunque resulte atractivo?
- ¿Qué criterios mínimos de privacidad, seguridad, accesibilidad, fiabilidad y pruebas bloquean el lanzamiento?
- ¿Existe fecha objetivo real? ¿Qué alcance recortaríamos si no llegamos?
- ¿Qué incertidumbres impiden estimar? ¿Cuáles son investigaciones técnicas de Ángel y cuáles decisiones de producto de Rafa?

## Inventario inicial para clasificar

| Capacidad | Evidencia disponible | Prioridad 1.0 | Estado a validar | Observaciones |
|---|---|---|---|---|
| Registro, login y cuenta | Spec 009 y código | Por decidir | Implementación reportada; validar | Registro abierto sustituye cuentas precreadas de spec 005 |
| Estanterías, roles e invitaciones | Spec 009 y código | Por decidir | Implementación reportada; validar | Espacios privados y múltiples pertenencias |
| Recibidor e identidad visual | Specs 006, 008, 009 | Por decidir | Implementación reportada; validar | Accesibilidad y móvil |
| Posición y movimiento de cajones | Spec 009 | Por decidir | Implementación reportada; validar | Huecos disponibles |
| Películas y valoraciones | Specs 005, 009 | Por decidir | Implementación reportada; validar | TMDB, alta manual y permisos |
| Series y valoraciones | Spec 010 | Por decidir | Implementación reportada; validar | Serie completa, no episodios |
| Sitios y mapa/globo | ideas-ustwo-v1.md | Por decidir | Propuesta | Investigar valor y coste de mapas |
| Compras y regalos | ideas-ustwo-v1.md | Por decidir | Propuesta | Definir privacidad y flujo |
| Libros y mangas | ideas-ustwo-v1.md | Por decidir | Propuesta | Definir unidad de colección |
| Videojuegos | Menciones históricas | Por decidir | Sin implementación confirmada | Confirmar vigencia |
| Despliegue y calidad de lanzamiento | Documentación técnica | Por decidir | Pendiente de auditoría | No confundir código con servicio validado |

**Nota:** «Implementación reportada» no significa validación funcional reciente ni autorización para marcar historias como Done.

## Matriz de priorización para completar juntos

**P0:** imprescindible para completar el recorrido principal y lanzar 1.0.  
**P1:** importante, pero se puede lanzar sin ello.  
**P2:** deseable para una versión posterior.  
**P3:** aparcado o descartado por ahora.

| Funcionalidad o necesidad | Problema/valor | ¿Qué ocurre sin ella? | Prioridad P0–P3 | 1.0 / Posterior / Pendiente | Dudas y responsable |
|---|---|---|---|---|---|
| [Completar en reunión] | | | | | |
| [Completar en reunión] | | | | | |
| [Completar en reunión] | | | | | |
| [Completar en reunión] | | | | | |
| [Completar en reunión] | | | | | |

## Registro de decisiones y dudas

| Tema | Decisión o pregunta | Estado (propuesta/aprobada/pendiente) | Motivo | Responsable | Próxima acción |
|---|---|---|---|---|---|
| Público objetivo | ¿Piloto concreto o grupos privados en general? | Pendiente | README y documento de ideas usan encuadres distintos | Rafa y Ángel | Acordar |
| Alcance 1.0 | ¿Qué cajones son P0? | Pendiente | Evitar expansión de scope | Rafa y Ángel | Priorizar |
| Sitios | ¿Mapa imprescindible y con qué condiciones? | Pendiente | Valor, costes y dependencia externa | Ángel investiga; Rafa decide alcance | Investigación |
| Validación histórica | ¿Qué está realmente probado? | Pendiente | Evitar cerrar trabajo solo por existir código | Ángel ejecuta pruebas; Rafa valida producto | Revisar |

## Después de la reunión

1. Rafa confirma por escrito alcance, exclusiones y prioridades.
2. Ángel propone investigaciones, riesgos y alternativas técnicas; no se le impone implementación desde producto.
3. Se refina el trabajo pendiente con criterios de aceptación verificables y dependencias.
4. Se revisa un inventario separado de historias históricas reconstruidas desde specs/código; se crean Issues solo tras aprobación.
5. Solo después se incorporan Issues al Project y se asignan estados/sprints. No atribuir sprints históricos ficticios.

## Fuentes

- `ideas-ustwo-v1.md` (documento de ideas 1.0 del proyecto).
- `README.md`, `MEMORY.md`, `docs/decisiones-tecnicas.md`.
- `docs/specs/005-mvp-ustwo/spec.md`, `008-nuestra-estanteria/spec.md`, `009-el-recibidor/spec.md`, `010-cajon-series/spec.md`.
- Repositorio `elkio-studio/UsTwo` y GitHub Project #1, consultados el 12/11/2026.
