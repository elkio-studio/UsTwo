---
id: "009"
title: "El recibidor"
status: completed
---

# 009 — El recibidor

## Problema y objetivo

El acceso actual permite entrar a las cuentas precreadas del MVP. El
recibidor incorpora el registro abierto y da al acceso el mismo ambiente
cálido que Nuestra estantería. El alta de una cuenta y la entrada a un
espacio compartido son decisiones independientes.

## Alcance incluido

- Inicio de sesión y registro desde El recibidor.
- Nombre de usuario único, creación de espacios e invitación por nombre
  de usuario desde un espacio compartido.
- Selector para cambiar entre los espacios de la cuenta y botón circular
  flotante abajo a la izquierda para abrir las invitaciones pendientes.
- Estado para una cuenta que todavía no pertenece a un espacio.
- Continuidad de las cuentas existentes, su estantería y sus datos.
- Página Mi cuenta y menú de sesión bajo el nombre de usuario.
- Administración de estanterías, rutas por nombre y ubicación del cajón.
- Rediseño del listado, el alta y el detalle de Películas con metadatos de
  TMDB. Luna cálida como favicon.

## Alcance excluido

- Crear nuevas clases de colecciones o varias colecciones de Películas
  dentro de una misma estantería.
- Añadir espacios públicos o mostrar sus datos a cuentas sin acceso.

## Actores

- Visitante que inicia sesión o crea una cuenta.
- Persona con cuenta, con o sin acceso a un espacio compartido.
- Propietario que puede invitar a otras cuentas y borrar su espacio.
- Miembro invitado que usa las colecciones del espacio.

## Dirección visual

- El recibidor usa pared cacao, luna en forma de C con luz cálida y un
  formulario sobre una superficie clara de lino.
- En escritorio, la escena y la marca están a la izquierda y el formulario
  a la derecha. En móvil, la luna y la marca preceden al formulario.
- «Iniciar sesión» y «Crear cuenta» se distinguen en la misma entrada.
  Solo se muestra el formulario correspondiente a la opción elegida.
- Los colores, el foco y los estilos compartidos se centralizan en
  `global.css`, según la convención del proyecto.
- Tus estanterías usa una paleta de cacao, ámbar y lino. Películas mantiene
  la luz cálida con portadas en perspectiva en escritorio y completas en
  dispositivos táctiles. La ficha muestra sinopsis y valoraciones.

## Flujos principales

1. Una persona con cuenta inicia sesión. Si ya pertenece a un espacio,
   conserva su acceso. Si aún no pertenece a ninguno, ve el estado sin
   espacios y puede crear uno propio o aceptar una invitación.
2. Cualquier visitante puede registrarse sin invitación. El formulario de
   registro pide nombre, nombre de usuario, correo y contraseña. Al terminar
   inicia sesión automáticamente sin verificar el correo. El registro por
   sí solo no añade a la persona a ningún espacio.
3. Una persona autenticada puede crear un espacio y pasa a ser su propietaria.
   Solo el propietario puede invitar a otra cuenta existente indicando su
   nombre de usuario.
4. La persona destinataria pulsa el botón circular flotante de la esquina
   inferior izquierda para consultar y aceptar una invitación pendiente.
   A partir de entonces puede entrar al espacio y usar sus colecciones.
5. Si la cuenta pertenece a varios espacios, puede cambiar de espacio
   activo desde un selector que muestra únicamente aquellos a los que tiene acceso.
6. La raíz muestra Tus estanterías. Cada estantería tiene una URL basada
   en su nombre, y el formulario de alta está al final de la lista.
7. El menú del `@usuario` permite cerrar sesión y abrir Mi cuenta. La
   cuenta puede cambiar su nombre de usuario y contraseña con la contraseña
   actual. Puede gestionar desde ahí sus estanterías según su rol.
8. El propietario puede renombrar su estantería, invitar y retirar
   acceso. Al renombrarla cambia su URL. Un miembro puede salir y requerirá
   otra invitación para volver. Salir o retirar acceso conserva las
   películas y las valoraciones existentes.
9. Los miembros pueden elegir un hueco con `+` para colocar o mover el
   único cajón de Películas. Las estanterías existentes conservan el cajón
   inicial y las nuevas empiezan vacías.
10. Películas muestra 12 entradas por página. Pulsar un resultado de
    TMDB guarda la película con la fecha de hoy y abre su ficha. Si se
    llegó al alta desde el listado, Atrás vuelve al listado. Las
    coincidencias con altas manuales requieren confirmación y los datos
    compartidos y la valoración propia siguen editándose en la ficha.

## Flujos alternativos y errores

- El acceso y el registro comunican los errores de validación con texto.
  Un correo o un nombre de usuario ya utilizado no genera una segunda cuenta.
- Una invitación inválida no da acceso ni cambia la pertenencia. La
  interfaz ofrece una explicación sin mostrar datos del espacio.
- La persona destinataria puede rechazar una invitación pendiente y el
  propietario puede retirarla mientras siga pendiente. En ambos casos
  deja de estar disponible para aceptar y no concede acceso.
- Al pedir el borrado de un espacio, se informa al propietario de que
  también se borrarán sus películas y valoraciones. Si cancela, se
  conservan el espacio y sus datos.
- Las cuentas sin pertenencia tampoco pueden acceder por URL directa a
  películas, valoraciones o acciones de un espacio compartido.

## Reglas de negocio y casos límite

- La sesión identifica a la persona. La pertenencia al espacio determina
  qué datos y acciones compartidos puede usar.
- Los miembros pueden usar las colecciones. Solo el propietario puede
  invitar a otras personas o borrar el espacio. Cada persona sigue
  editando únicamente su propia valoración.
- Las invitaciones pendientes no caducan y se pueden consultar después
  de volver a iniciar sesión.
- La bandeja muestra el nombre de usuario del propietario que envió la
  invitación. El contador de pendientes solo aparece cuando hay avisos.
- El borrado confirmado elimina definitivamente el espacio y sus películas
  y valoraciones. Las cuentas y los demás espacios se conservan.
- La transición mantiene a Ángel y Alejandra dentro de su espacio actual
  con las películas y valoraciones que ya tienen. Ángel queda como
  propietario con el nombre de usuario `angl_j_rgl`. Alejandra queda como
  miembro con el nombre de usuario `Aleismowgli`.
- Repetir la aceptación no crea una pertenencia duplicada.

## Criterios de aceptación

- **CA-01.** En escritorio y móvil, El recibidor muestra las dos opciones
  y un solo formulario a la vez. A 375 px se puede completar cualquiera
  sin scroll horizontal y con foco visible y etiquetas legibles.
- **CA-02.** Un visitante puede crear una cuenta sin invitación. Un registro
  válido inicia sesión automáticamente sin verificación de correo. Un
  registro inválido no se guarda y se rechaza un correo o nombre de usuario
  que ya esté en uso.
- **CA-03.** Una cuenta autenticada sin pertenencia ve el estado sin
  espacios. Las rutas directas de lectura y escritura de colecciones de un
  espacio al que no pertenece rechazan el acceso.
- **CA-04.** Solo el propietario puede invitar a otra cuenta por nombre de
  usuario. La cuenta destinataria puede consultar las invitaciones desde
  el botón circular de abajo a la izquierda incluso tras cerrar sesión y
  volver a entrar. Las invitaciones pendientes no caducan. Aceptar una
  invitación válida da acceso al espacio indicado.
  Una invitación inválida no lo concede y repetir la aceptación no duplica
  la pertenencia. Si la persona destinataria la rechaza o el propietario
  la retira mientras está pendiente, la invitación ya no se puede aceptar.
- **CA-05.** Ángel y Alejandra pueden iniciar sesión con sus cuentas
  existentes y conservan acceso a su estantería, películas y valoraciones.
  Sus nombres de usuario son `angl_j_rgl` y `Aleismowgli` respectivamente.
  Ángel es el propietario del espacio actual.
- **CA-06.** Una cuenta puede crear un espacio y queda como propietaria.
  Un miembro invitado puede usar las colecciones. Si intenta invitar o
  borrar el espacio, la petición se rechaza. El propietario sí puede
  realizar ambas acciones. Al solicitar el borrado se muestra una
  confirmación. Cancelarla conserva los datos y confirmarla elimina
  definitivamente el espacio, sus películas y sus valoraciones.
- **CA-07.** Una cuenta con acceso a un espacio no puede consultar ni
  modificar datos de otro al que no pertenece, incluso por URL directa.
- **CA-08.** Una cuenta con acceso a varios espacios puede elegir el activo
  desde el selector. Al cambiar, se muestran las colecciones del espacio
  elegido y el selector excluye los espacios ajenos.
- **CA-09.** La raíz muestra el selector incluso después de visitar una
  estantería. Una URL privada solo abre para miembros. Renombrar actualiza
  la URL y distingue nombres que generarían la misma ruta.
- **CA-10.** Solo el propietario puede renombrar y retirar a otro miembro.
  Un invitado puede salir. En ambos casos se deniega el acceso posterior.
- **CA-11.** Mi cuenta exige autenticación y contraseña actual para cambiar
  credenciales, rechaza nombres de usuario duplicados y muestra únicamente
  las estanterías de esa cuenta con su rol.
- **CA-12.** Los ocho huecos se alinean en ambas orientaciones. El diálogo
  se puede cancelar y solo permite colocar Películas en un hueco válido.
  La posición persiste y mover el cajón conserva su contenido.
- **CA-13.** El listado pagina en orden de visionado. La portada inclinada
  se pone de frente con ratón o foco en escritorio, y se ve completa en
  móvil. La ficha y el catálogo muestran las portadas disponibles, y la
  ficha muestra la sinopsis si TMDB la proporciona.
- **CA-14.** El alta directa desde TMDB utiliza la fecha actual, conserva
  datos al vincular manuales y vuelve al listado con Atrás. El alta manual
  y los desplegables de edición siguen disponibles.

## Decisiones aprobadas

- Dirección visual «El recibidor», con inicio de sesión y registro.
- Registro abierto y acceso a espacios compartidos por invitación.
- Registro con nombre de usuario único, sin verificación de correo y con
  inicio de sesión automático.
- Creación y pertenencia a varios espacios con selector. Invitaciones
  por nombre de usuario que permanecen pendientes sin caducar y se
  consultan desde el botón circular de la esquina inferior izquierda.
- Invitación y borrado exclusivos del propietario. Ángel es el propietario
  del espacio actual y ambos nombres de usuario iniciales están definidos.
- El borrado del espacio requiere confirmación y es definitivo para el
  espacio, sus películas y sus valoraciones. La persona destinataria puede
  rechazar una invitación pendiente y el propietario puede retirarla.
- La apertura del registro sustituye la regla de cuentas exclusivamente
  precreadas del MVP 005. Los datos compartidos siguen siendo privados.
- El desarrollador aprobó esta especificación antes de solicitar el plan.
  Durante la implementación solicitó también los ajustes recogidos en
  CA-09 a CA-14 y el favicon de luna. El plan original conserva el alcance
  con el que empezó la implementación.

## Decisiones pendientes

- Ninguna.
