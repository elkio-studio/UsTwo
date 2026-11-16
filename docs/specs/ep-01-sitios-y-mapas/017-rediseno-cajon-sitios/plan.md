---
spec: "017"
status: approved
---

# Plan técnico — Rediseño del cajón de Sitios

## Fases de implementación

1. **Mapa y panel adaptable.** Reorganizar `PlacesView` con un mapa amplio
   y un panel abierto a la izquierda en escritorio y plegado al entrar en
   móvil. En móvil se abre desde abajo. Al cambiar el espacio del mapa,
   actualizar el tamaño de Leaflet sin recrear la instancia ni perder su
   posición o marcadores. Mantener visibles sus controles y atribución
   y aplicar al panel la paleta cálida de `global.css` mediante CSS Modules.
2. **Consulta y gestión de sitios.** Reutilizar los formularios, la búsqueda
   y las operaciones existentes. Los marcadores seleccionan la ficha
   correspondiente en el panel sin abrir un popup. Elegir un punto para
   el formulario abre el panel si estaba plegado. La ficha contiene nombre,
   coordenadas y nota opcional, y da acceso a editar o eliminar. Alojar
   la confirmación y los errores en el panel. Mantener el borrador al
   plegarlo y conservar la entrada de coordenadas si falla el mapa.
3. **Gestión de listas.** Mantener la lectura y creación existentes. Añadir
   edición de nombre y emoji y borrado bajo las rutas protegidas de Sitios.
   Resolver cada lista desde la estantería autorizada y reutilizar los
   límites del alta. Confirmar una vez si está vacía y dos si tiene sitios,
   con aviso de borrado definitivo en el segundo paso. Enviar la petición
   solo tras la confirmación final. Dentro de la transacción de escritura,
   contrastar el número de sitios confirmado con el actual. Si cambió,
   devolver un error recuperable y pedir volver a confirmar con los datos
   actualizados. Usar la cascada existente para eliminar sus sitios en la
   misma operación. Refrescar panel y marcadores con la respuesta de Inertia.
4. **Verificación.** Ampliar las pruebas de Sitios, ejecutar PHPUnit con
   SQLite, el build de Vite y `git diff --check`. Registrar la verificación
   interactiva de escritorio, móvil y teclado. Al cerrar, publicar los
   documentos de la spec en `documentation` según el flujo SDD.

## Estrategia de pruebas

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Abrir Sitios en escritorio, plegar y reabrir el panel. Comprobar tamaño, posición del mapa y marcadores. |
| CA-02 | En móvil, comprobar el estado inicial, apertura desde abajo y uso por toque sin desplazamiento horizontal. |
| CA-03 | Seleccionar un marcador y comprobar que su ficha y acciones están en el panel. Buscar, añadir, editar y borrar un sitio. Plegar y reabrir con un formulario a medio rellenar. |
| CA-04 | Recorrer panel, marcadores, formularios y confirmaciones con teclado y foco visible. Verificar que controles y atribución siguen accesibles. |
| CA-05 | Bloquear el mapa base y comprobar el aviso y la entrada manual. Recargar y verificar la persistencia. Probar el aislamiento con SQLite. |
| CA-06 | Crear, consultar y editar listas desde el panel. En SQLite, validar campos, probar un miembro que no sea propietario y rechazar accesos cruzados, sin membresía o sin sesión. |
| CA-07 | Cancelar en cada paso y comprobar que no hay borrado. Confirmar el borrado de una lista vacía y de otra con sitios. En SQLite, verificar cascada, conservación de otras listas y rechazo si cambió el número de sitios. |

## Compatibilidad

Se conservan Leaflet, Geoapify, las reglas del alta y los permisos por
estantería. Se reutiliza el esquema actual con borrado en cascada.
