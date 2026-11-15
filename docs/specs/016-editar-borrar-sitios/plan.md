---
spec: "016"
status: approved
---

# Plan técnico — Editar y eliminar sitios visitados

## Fases de implementación

1. **Rutas, permisos y validación.** Añadir las operaciones de actualización
   y eliminación a las rutas de Sitios bajo la estantería autenticada.
   Resolver el sitio a través de las listas de la estantería autorizada.
   Devolver 404 si el sitio no pertenece a ella. Reutilizar la validación
   de coordenadas del alta y sus límites para nombre y nota. Exigir nombre
   también cuando el punto se elija en el mapa. Escribir solo nombre,
   latitud, longitud y nota. El borrado afecta al sitio y conserva la
   lista. Usar las respuestas de validación y redirección de Inertia.
2. **Consulta y edición.** Mostrar latitud y longitud en el popup, junto
   al nombre y la nota. Añadir las acciones «Editar sitio» y «Eliminar
   sitio». Abrir la edición con los datos guardados. Reutilizar los
   controles de búsqueda, mapa y coordenadas del alta. En edición,
   actualizar solo la ubicación al elegir un resultado o pulsar el mapa.
   La respuesta inversa del alta no debe poder cambiar el nombre en
   edición. Mantener los campos escritos si falla el guardado.
3. **Confirmación y actualización del mapa.** Pedir confirmación antes
   de solicitar el borrado. Cancelar deja los datos intactos y devuelve
   el foco a un control pertinente. Informar de errores y conservar
   el marcador si falla la operación. Tras un alta, edición o borrado
   correctos, reconstruir los marcadores con los registros de la
   respuesta de Inertia. Comprobar que el marcador editado aparece en
   sus coordenadas nuevas y que el borrado desaparece también al recargar.
4. **Verificación y cierre.** Ampliar la cobertura de PHPUnit, ejecutar
   la suite con SQLite, el build de Vite y `git diff --check`. Registrar
   la revisión en escritorio, móvil y teclado. Publicar los documentos
   de la spec en `documentation` al cerrar, según el flujo SDD.

## Estrategia de pruebas

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Abrir un marcador con nota y otro sin ella. Comprobar el nombre, las coordenadas y el texto de la nota si existe. |
| CA-02 | Corregir la ubicación mediante búsqueda, mapa y coordenadas. Comprobar que el nombre permanece, que se puede editar y que se puede vaciar la nota. Probar nombre ausente y coordenadas no finitas o fuera de rango. Verificar que un fallo conserva el formulario y la base de datos. |
| CA-03 | Cancelar el borrado y comprobar que sigue el marcador. Confirmar y verificar que se elimina solo el sitio, aunque fuese el último de la lista. |
| CA-04 | Dar de alta, editar y borrar un sitio. Comprobar la posición y el contenido tras cada operación y tras recargar. |
| CA-05 | Probar lectura y escrituras como miembro, como cuenta ajena, sin sesión y con un identificador de sitio de otra estantería desde una ruta autorizada. Comprobar que las solicitudes rechazadas dejan los registros intactos. |

La verificación interactiva incluye toque y teclado, foco visible y acceso
a las acciones y a la confirmación. Si Ángel revisa la interfaz en su
navegador, se registrará su evidencia en las tareas.

## Compatibilidad

Se mantienen la estructura de listas, los datos de sitios, las reglas del
alta, Leaflet y Geoapify. El plan utiliza el esquema existente y el
despliegue habitual.
