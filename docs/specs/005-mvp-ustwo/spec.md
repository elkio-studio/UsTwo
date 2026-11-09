---
id: "005"
title: "MVP de UsTwo"
status: completed
---

# 005 — MVP de UsTwo

## Problema y objetivo

Ángel y Alejandra necesitan un lugar privado donde guardar las películas que
han visto y volver a consultarlas. El primer recorrido de UsTwo va desde el
panel principal hasta una lista compartida donde ambos pueden consultar,
añadir, editar y borrar registros.

## Alcance incluido

- Acceso privado mediante dos cuentas individuales, una para Ángel y otra
  para Alejandra. Las cuentas se crean de antemano, el acceso es por
  contraseña y la recuperación se gestiona manualmente.
- Panel principal con acceso a la página independiente de películas vistas.
- Una sola lista compartida que ambos pueden consultar, ampliar, editar y
  de la que ambos pueden borrar entradas.
- Un único registro por película, con título y fecha de visionado
  obligatorios, y puntuación de 0 a 100 y nota opcionales por persona.
- Alta de películas mediante búsqueda y selección en TMDB. El alta manual
  está disponible si la película no aparece o falla la consulta.

## Alcance excluido

- Otras listas, incluidas las de series, videojuegos, lugares, deseos y regalos.
- Listas personales y contenido público.
- Registro público, recuperación automática de cuentas y papelera.

## Actores

- Ángel y Alejandra pueden consultar, añadir, editar y borrar entradas de
  la misma lista.
- Las personas sin acceso concedido no pueden consultar ni editar su contenido.

## Flujo principal

1. Ángel o Alejandra inicia sesión con su cuenta y llega al panel principal.
2. Abre «Películas vistas» en su propia página.
3. Para añadir una película, busca por título en TMDB, elige un resultado
   e indica la fecha de visionado. Tras guardarla, la entrada queda
   disponible para ambos.
4. Cualquiera de los dos puede corregir una entrada compartida. La otra
   persona ve la corrección al volver a consultar la lista.
5. Cada persona puede añadir o editar su propia puntuación y nota. Ambos
   pueden leer las aportaciones de los dos.
6. Cualquiera puede solicitar el borrado de una entrada compartida. Si
   confirma, se eliminan definitivamente la entrada y las aportaciones
   de ambos.

## Flujos alternativos y errores

- Si alguien sin acceso abre directamente la página, su contenido permanece
  privado. Quien haya perdido el acceso debe solicitar su recuperación manual.
- Si una persona cancela la confirmación de borrado, la entrada y sus
  aportaciones se conservan.
- Lista vacía. Mostrar que aún no hay películas registradas y
  ofrecer la acción para añadir la primera.
- Validación. Identificar el campo que requiere una corrección y
  conservar los datos introducidos.
- Fallo al guardar. Mostrar que la operación no se ha completado,
  conservar los datos introducidos y permitir reintentar.
- Si la película no aparece o falla la consulta a TMDB, se puede continuar
  mediante el alta manual con título y fecha de visionado.
- En el alta manual, el año de estreno es opcional y se exige si el título
  coincide. Si coinciden título y año o falta el año en la entrada previa,
  se pide confirmar si es la misma película. Se abre el registro existente
  si es la misma y se permite el alta si se confirma que es distinta.
- Al elegir una ficha de TMDB, si su identificador ya está guardado, se
  abre el registro existente. En otro caso, si el título y el año de
  estreno coinciden con una entrada manual, UsTwo pregunta si se trata de
  la misma película antes de vincularla. Si falta el año en cualquiera de
  las dos, se sugiere la coincidencia por título y se pide confirmación.
  Si se indica que son películas distintas, se puede continuar con el alta
  sin modificar la entrada manual.
- Si hay varias entradas manuales candidatas, UsTwo las muestra todas.
  La persona puede elegir cuál vincular y confirmar la elección, o crear
  una entrada nueva si ninguna corresponde.

## Reglas de negocio y casos límite

- La lista recoge películas ya vistas y es compartida por ambos usuarios.
- Ambos pueden editar los datos comunes de las entradas compartidas.
- Si vuelven a ver la misma película, pueden elegir manualmente la fecha
  que queda guardada en el registro existente.
- El título y la fecha son obligatorios al guardar. La puntuación y la nota
  pueden añadirse después, de forma independiente.
- La puntuación admite únicamente números enteros de 0 a 100, incluidos
  ambos extremos.
- La puntuación y la nota de cada persona son visibles para ambos y solo
  puede editarlas su autor.
- Si ambos editan campos comunes distintos desde la misma versión, se
  conservan ambos cambios. Si cambian el mismo campo, prevalece el último
  guardado correcto para ese campo. Guardar datos comunes conserva las
  puntuaciones y notas.

## Interfaz

- Dirección aprobada. Un cuaderno de visionados que prioriza los títulos,
  sin pósteres ni búsqueda o filtros en la lista guardada. Se ordena por
  fecha de visionado, de más reciente a más antigua.
- Cada entrada muestra solo el título y las puntuaciones de Ángel y
  Alejandra. Al abrirla se ven la fecha de visionado, el año de estreno
  si consta, las notas y las acciones disponibles.
- El buscador de TMDB se usa al añadir una película. Cada resultado muestra
  el título y el año cuando está disponible.
  Al elegirlo, su identificador permite distinguir películas y evitar
  duplicados. UsTwo conserva sus fechas, notas y puntuaciones.
- Acceso a la lista y acciones mediante teclado con foco
  visible, errores identificados en texto junto al campo y recorrido completo
  en móvil sin desplazamiento horizontal.

## Criterios de aceptación

- **CA-01**. Al acceder con permiso, Ángel y Alejandra pueden abrir
  «Películas vistas» desde el panel principal en una página independiente.
- **CA-02**. Al abrir la página, ambos ven las mismas entradas compartidas.
- **CA-03**. Si uno corrige una entrada, el otro ve los nuevos datos al volver
  a consultar la lista.
- **CA-04**. Una persona sin acceso no puede leer ni modificar la lista
  accediendo directamente a sus páginas o acciones.
- **CA-05**. Desde una lista vacía se puede abrir el formulario
  de alta y, al guardar una entrada válida, ambos pueden consultarla.
- **CA-06**. Si falla el guardado, se informa del fallo y se
  mantienen los datos introducidos para poder reintentar.
- **CA-07**. Si cualquiera de los dos guarda una entrada, ambos
  pueden consultarla también al acceder en una sesión posterior.
- **CA-08**. Ángel y Alejandra acceden mediante cuentas distintas y consultan
  la misma lista.
- **CA-09**. Al actualizar la fecha de una película que han vuelto a ver,
  la lista conserva una sola entrada y muestra la fecha elegida manualmente.
- **CA-10**. Al introducir una puntuación, se aceptan 0 y 100 y se rechazan
  valores inferiores a 0, superiores a 100 o con parte fraccionaria.
- **CA-11**. Ambos pueden ver las dos puntuaciones y las dos notas. Cada
  persona puede editar las suyas y se rechaza cualquier intento de modificar
  las de la otra cuenta, también mediante acceso directo a la acción.
- **CA-12**. Se puede guardar una película con título y fecha aunque falten
  las puntuaciones y notas. Si falta el título o la fecha, se rechaza el
  guardado.
- **CA-13**. En el alta manual, si dos películas tienen el mismo título y
  años de estreno distintos, pueden guardarse como entradas distintas. Si
  el título coincide, el año de estreno es obligatorio para la nueva entrada.
- **CA-14**. Tanto Ángel como Alejandra pueden añadir, editar y borrar una
  entrada de la lista. Tras el borrado, la entrada deja de aparecer en la
  lista compartida para ambos.
- **CA-15**. Cancelar la confirmación conserva la entrada y las aportaciones
  de ambos. Confirmar las elimina definitivamente.
- **CA-16**. Ambos pueden iniciar sesión por contraseña con sus cuentas
  precreadas. Tras cerrar sesión, abrir directamente la lista requiere
  volver a iniciar sesión.
- **CA-17**. Si ambos cambian la fecha del mismo registro y guardan en
  momentos distintos desde copias de la misma versión inicial, se muestra
  la fecha del último guardado. Las puntuaciones y notas se conservan.
- **CA-18**. Al buscar un título en el alta, se pueden elegir películas de
  TMDB y guardar el resultado con la fecha de visionado. Volver a elegir
  el mismo identificador de TMDB no crea un segundo registro.
- **CA-19**. Si una película no aparece entre los resultados o falla la
  consulta, se puede guardar manualmente con título y fecha de visionado,
  aplicando la regla de coincidencia de títulos.
- **CA-20**. UsTwo muestra la atribución a TMDB en la sección de créditos.
- **CA-21**. Al elegir una ficha de TMDB cuyo título y año coinciden con
  una entrada manual, UsTwo solicita confirmación. Al confirmar, vincula
  el identificador al registro existente y conserva su fecha de visionado,
  sus puntuaciones y sus notas. Si se indica que son películas distintas,
  la entrada manual queda intacta.
- **CA-22**. Si el título coincide y falta el año en la ficha de TMDB o
  en la entrada manual, UsTwo propone la coincidencia y pide confirmación
  antes de vincularla, con las mismas garantías que CA-21.
- **CA-23**. En un alta manual con título y año coincidentes, o con título
  coincidente y sin año en la entrada previa, UsTwo pide confirmación.
  Si se confirma que es la misma película, abre el registro existente.
  Si se confirma que es distinta, permite guardar otra entrada.
- **CA-24**. La lista muestra las películas de la fecha de visionado más
  reciente a la más antigua. Cada entrada muestra solo el título y ambas
  puntuaciones. La fecha, el año si consta, las notas y las acciones
  aparecen al abrir el detalle.
- **CA-25**. Si una ficha de TMDB coincide con varias entradas manuales,
  UsTwo muestra todas las candidatas. Al elegir una y confirmar, vincula
  solo esa entrada y conserva sus datos. Si ninguna corresponde, permite
  guardar una entrada nueva y deja intactas las candidatas.
- **CA-26**. Si desde la misma versión inicial una persona cambia el título
  y la otra la fecha de visionado, tras ambos guardados se conservan el
  nuevo título y la nueva fecha.

## Decisiones aprobadas

- UsTwo es privada, el panel principal centraliza la navegación y cada lista
  abre su propia página. Fuente: decisiones de producto del README.
- El MVP empieza solo con películas vistas y un espacio compartido que Ángel
  y Alejandra pueden consultar y editar. Fuente: confirmación del desarrollador
  al abrir el MVP.
- Habrá dos cuentas individuales y un único registro por película. El
  registro incluye título, fecha, puntuación de 0 a 100 y nota por persona.
  Fuente: confirmación del desarrollador sobre campos, acceso y visionados.
- El título y la fecha son obligatorios. Cada persona puede añadir después
  su puntuación y nota, visibles para ambos y editables por su autor. Si
  vuelven a ver la película, pueden elegir manualmente qué fecha conservar.
  Fuente: confirmación del desarrollador sobre valoración, campos y fecha.
- Ambos pueden añadir, editar y borrar entradas. El año de estreno
  distingue las películas cuando coincide el título. La puntuación se
  introduce en números enteros. Fuente: confirmación del desarrollador
  sobre acciones, títulos y puntuación.
- El borrado exige confirmación y elimina definitivamente la entrada y sus
  aportaciones. Las cuentas se crean de antemano, con inicio de sesión por
  contraseña y recuperación manual. En los datos comunes prevalece la
  última edición guardada. Fuente: confirmación del desarrollador sobre
  borrado, cuentas y edición simultánea.

- El MVP usa TMDB para elegir películas, deja las series para una fase
  posterior y permite el alta manual si la película no aparece o falla la
  API. Fuente: confirmación del desarrollador sobre el catálogo.
  [Búsqueda y consulta de TMDB](https://developer.themoviedb.org/docs/search-and-query-for-details)
  · [Condiciones y atribución](https://developer.themoviedb.org/docs/faq).
- La coincidencia entre una entrada manual y una ficha de TMDB se detecta
  por título y año de estreno. El vínculo requiere confirmación y conserva
  la fecha de visionado, las puntuaciones y las notas. Fuente: confirmación
  del desarrollador sobre la detección de duplicados.
- Si falta un año, se propone la coincidencia con TMDB por título y se
  solicita confirmación. También se confirma si un alta manual coincide en
  título y año o falta el año de la entrada previa. La lista ordena los
  visionados del más reciente al más antiguo y muestra solo título y ambas
  puntuaciones hasta abrir el detalle. Fuente: confirmación del desarrollador
  sobre coincidencias, orden y contenido de las entradas.

- Si TMDB coincide con varias entradas manuales, se puede elegir cuál
  vincular o continuar con un alta nueva. Las ediciones simultáneas
  conservan los cambios en campos distintos. Fuente: confirmación del
  desarrollador tras la revisión del borrador.

- El desarrollador aprobó la especificación con las propuestas de lista
  vacía, validación, fallos de guardado, persistencia entre sesiones y
  accesibilidad.

## Decisiones pendientes

- Ninguna decisión funcional pendiente.
