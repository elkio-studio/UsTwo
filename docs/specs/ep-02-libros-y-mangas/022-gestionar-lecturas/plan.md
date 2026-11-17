---
spec: "022"
status: approved
---

# Plan técnico — Gestionar las fichas compartidas de Lecturas

## 1. Rutas y permisos

- Añadir `PATCH` y `DELETE` sobre la URL actual de detalle de Lecturas,
  con el mismo parámetro numérico y el mismo grupo de autenticación y
  membresía. Resolver la ficha mediante `$space->readings()` en ambas
  operaciones y devolver 404 si no pertenece a la estantería de la ruta.
- Volver al detalle tras una edición correcta. Tras un borrado correcto,
  redirigir al listado con la categoría de la ficha eliminada activa.

## 2. Edición y borrado

- En fichas manuales, validar los campos enviados con las reglas del
  alta. Conservar los campos omitidos y recalcular la clave normalizada
  del título cuando cambie, para que el alta siga detectando coincidencias.
- En fichas de catálogo, admitir solo autoría, fecha y sinopsis si el
  valor de la ficha sigue vacío. Rechazar cualquier intento de modificar
  un campo protegido antes de guardar. La categoría, portada e identidad
  de catálogo quedan fuera de la edición.
- Validar toda la solicitud antes de escribir. En catálogo, condicionar
  la actualización a que los campos que se rellenan sigan vacíos en la
  base de datos. Si hubo una edición simultánea, rechazar la solicitud
  completa y pedir que se recargue la ficha.
- Exigir confirmación explícita en el servidor para borrar. Ante un
  fallo de escritura, devolver un error de formulario y conservar la
  ficha. Si la ficha ya desapareció, responder 404.

## 3. Interfaz

- Activar los controles de «Editar datos comunes» y «Borrar lectura» en
  el detalle existente. Reutilizar `Field` y los patrones de formulario
  y confirmación de Películas, Series o Sitios.
- Enviar solo los cambios permitidos. Mostrar los valores protegidos
  en modo lectura y permitir rellenar los huecos. Mantener el borrador
  y mostrar los errores sin salir del detalle.
- Comprobar foco, teclado y toque en edición, cancelación y confirmación.

## 4. Verificación

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Editar una ficha manual, comprobar persistencia tras recarga y rechazar un título vacío. Verificar que la clave de título se actualiza. |
| CA-02 | Completar los datos ausentes y recargar. Rechazar cambios de datos importados, intentos de alterar el origen o el ID y una segunda actualización que intente ocupar el mismo campo. |
| CA-03 | Comprobar que una petición sin confirmar no borra. Verificar en la interfaz que cancelar conserva la ficha. Confirmar el borrado y comprobar que la categoría queda activa y la ficha sigue ausente al recargar. |
| CA-04 | Probar edición y borrado sin sesión, con una cuenta ajena y mediante la ruta de otra estantería con membresía en ambas. |
| CA-05 | Simular validación fallida, solicitud mixta y errores de persistencia. Comprobar que la ficha anterior se conserva y el formulario muestra el error. Revisar móvil, teclado y foco. |

Ampliar `ReadingsTest`, ejecutar PHPUnit con SQLite, compilar con Vite
y comprobar el diff. Registrar las evidencias y las horas de cierre.
El cambio usa los campos existentes y se despliega con el flujo habitual,
sin migración ni cambios en la configuración de los catálogos.
