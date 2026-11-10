---
spec: "009"
status: approved
---

# Plan técnico — 009

El desarrollador aprobó el plan y las tres propuestas sobre nombres de
usuario, rutas de Películas y transición de producción.

## Resumen

Implementar El recibidor en Laravel, Inertia y React. Proteger todas las
colecciones por pertenencia antes de habilitar el registro abierto. Mantener
la estantería y las operaciones de Películas dentro del espacio seleccionado.

## Componentes afectados

- `Login` y `Field` para acceso y registro, reutilizando la validación y
  el envío con Inertia. Dirección aprobada de pared cacao, luna cálida y
  formulario de lino, con composición lateral en escritorio y apilada
  en móvil. Centralizar colores y estilos en `global.css` y componer clases
  desde los módulos. Conservar una carpeta por página o componente.
- `Dashboard` y la cabecera compartida para el estado sin espacios, el
  selector, el alta y la gestión del espacio. Añadir el botón circular de
  invitaciones abajo a la izquierda en el área autenticada, incluso sin
  pertenencia. Presentar los avisos pendientes al entrar o navegar.
- Rutas, controladores y modelos de cuentas, espacios, pertenencias e
  invitaciones. Adaptar `MovieController` y los enlaces de Películas al
  contexto explícito de espacio. Mantener TMDB y las reglas de valoración.

## Contratos, datos e interfaces

- Mantener el inicio de sesión por correo y contraseña. El registro valida
  nombre, nombre de usuario, correo y contraseña en servidor, guarda el
  hash de la contraseña con Laravel, limita la frecuencia de intentos
  y regenera la sesión al autenticar. Preservar la grafía del nombre de
  usuario, con clave normalizada única para registrar y buscar sin distinguir
  mayúsculas. Admitir de 3 a 32 caracteres, con letras ASCII, números y
  guion bajo. Reutilizar el límite compatible de 72 bytes para contraseña.
- Crear tablas para espacios, pertenencias e invitaciones, con referencias
  y restricciones de unicidad. Crear el espacio y la pertenencia del
  propietario en una transacción. El borrado confirmado elimina el espacio,
  sus pertenencias, invitaciones, películas y valoraciones asociadas sin
  eliminar cuentas ni registros de otros espacios.
- Añadir `space_id` a películas. La unicidad de TMDB pasa a ser por espacio.
  Acotar también búsqueda de duplicados, selección de candidatas, detalle,
  mutaciones y personas mostradas para las valoraciones. La valoración
  conserva su vínculo con película y autor.
- Usar rutas de Películas con el identificador del espacio
  en la URL y en cada envío. La selección se guarda en sesión para volver al
  panel, pero una pestaña no puede redirigir por error una escritura de otra.
  Verificar pertenencia en cada petición y resolver la película dentro
  del espacio solicitado. Los accesos antiguos solo podrán redirigir en
  lectura tras validar el destino y la pertenencia. Las escrituras usarán
  el destino explícito.
- Solo el propietario puede crear o retirar invitaciones y borrar su
  espacio. Solo la cuenta destinataria puede aceptar o rechazar. Las
  invitaciones pendientes se guardan en la base y no tienen caducidad.
  Limitar a una pendiente por destinatario y espacio. Usar transacciones,
  cambios de estado condicionados a que siga pendiente y unicidad de
  pertenencia para resolver reintentos y acciones simultáneas.

## Fases de implementación

1. Preparar migraciones compatibles con SQLite y PostgreSQL. En una base
   existente, identificar Ángel y Alejandra por sus cuentas actuales,
   asignar sus nombres de usuario, crear el espacio inicial con Ángel como
   propietario y asociar las películas conservando sus IDs y valoraciones.
   En base vacía, permitir migrar sin inventar cuentas. Ante datos heredados
   sin propietario identificable o colisiones, detener la migración y
   conservar los datos. Adaptar los seeders locales y de producción y el
   comando de cuentas a las nuevas restricciones, sin cambiar credenciales
   existentes.
2. Aplicar la autorización por pertenencia y por propietario a las rutas
   antes de conectar la UI pública. Adaptar los enlaces y consultas de
   Películas y verificar aislamiento y retrocompatibilidad.
3. Integrar El recibidor, conectar acceso y registro y verificar errores de
   campo, foco y sesión. Tras el alta, mostrar el estado sin espacios.
4. Conectar alta, selector, invitaciones y borrado con confirmación. Al
   borrar el espacio activo o perder acceso, resolver otro espacio válido
   o mostrar el estado vacío si no queda ninguno.
5. Ejecutar build y pruebas, verificar el recorrido visual y documentar
   la migración y la operación en el README.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Revisar escritorio y 375 px, alternancia de formularios, errores, foco, teclado, tacto y ausencia de desbordamiento. |
| CA-02 | Registrar sin invitación, comprobar sesión regenerada, duplicados de correo y nombre de usuario (incluida distinta capitalización) y ausencia de pertenencia inicial. |
| CA-03 | Ver estado vacío y rechazar lectura y escritura directas desde una cuenta sin pertenencia. |
| CA-04 | Probar destinatario correcto e incorrecto, persistencia tras nueva sesión, aceptación repetida, rechazo y retirada, incluidas transiciones en competencia. |
| CA-05 | Migrar una copia aislada con las dos cuentas, películas y valoraciones. Comparar IDs, autoría y hashes antes y después y probar inicio de sesión. |
| CA-06 | Probar creación y propiedad, uso por un invitado, denegación de envío o retirada de invitaciones y de borrado por no propietarios, cancelación y borrado confirmado limitado al espacio. |
| CA-07 | Con dos espacios, probar acceso cruzado a listas, detalle, IDs de candidatas, altas, ediciones, borrados y valoraciones. Verificar que un TMDB ID puede existir en dos espacios. |
| CA-08 | Cambiar entre espacios propios y verificar las colecciones resultantes, con peticiones de selección ajena y dos pestañas activas. |

## Documentación, despliegue y compatibilidad

- Actualizar el README con registro, espacios, invitaciones y transición de
  datos. Registrar las comprobaciones y sus limitaciones en `tasks.md`.
- Ejecutar `npm run build`, las suites SQLite y PostgreSQL aislado y una
  prueba de migración sobre datos existentes antes de integrar. Mantener
  el flujo de publicación `dev` → `main` y hacer una copia cifrada antes
  de la migración de producción, según la spec 007.
- Abrir una ventana de mantenimiento
  y retirar temporalmente el acceso de `ustwo_app` a las tablas de películas
  y valoraciones. Ensayar el procedimiento en PostgreSQL aislado con una
  conexión ya abierta y otra nueva. Verificar que ambas rechazan lecturas
  y escrituras y que han terminado las operaciones iniciadas antes del
  bloqueo. Mantener el acceso de `ustwo_migrator` para el flujo de GitHub Actions.
- Ejecutar la migración y publicar el commit desde `main` con el bloqueo
  vigente. Confirmar en Render que la nueva revisión está activa y la
  anterior ya no recibe tráfico antes de restaurar los permisos de
  `ustwo_app`. Si falla la migración o la publicación, mantener el bloqueo
  hasta validar una corrección con aislamiento. Una vez habilitados varios
  espacios, cualquier versión de recuperación debe aplicar ese aislamiento.
- Tras desplegar, verificar acceso de ambas cuentas y que sus datos se
  conservan. Cualquier recuperación debe mantener el aislamiento de espacios
  y evitar volver a una revisión que lea datos de todos los espacios.
