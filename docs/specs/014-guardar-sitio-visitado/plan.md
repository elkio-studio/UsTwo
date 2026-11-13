---
spec: "014"
status: approved
---

# Plan técnico — Guardar un sitio visitado

## Enfoque

Ampliar la vista «Sitios» y las rutas protegidas de la spec 013. Mantener
el enlace de vuelta y la interfaz de la estantería. Usar Leaflet y
Geoapify Free según el [informe de mapas](../../investigaciones/mapas.md).

## Fases de implementación

1. **Persistencia.** Añadir migraciones y relaciones para listas por
   estantería y sitios por lista. La lista guarda nombre y emoji opcional.
   El sitio guarda nombre, coordenadas con precisión decimal, nota opcional
   y estado asignado por el servidor. La pertenencia del sitio a una
   estantería se obtiene a través de su lista. Las estanterías existentes
   empiezan sin listas. Comprobar migraciones en SQLite y PostgreSQL.
2. **Permisos y guardado.** Extender las rutas autenticadas que usan
   `RequireSpaceMembership`. Consultar las listas y los sitios desde
   la estantería autorizada. Validar en el servidor los rangos de las
   coordenadas, el nombre y la pertenencia de la lista. Aplicar el nombre
   a partir de las coordenadas únicamente en el modo de punto elegido
   en el mapa. Asignar «Visitado» en el servidor. En el primer guardado,
   crear la lista y el sitio en una transacción. Serializar las altas de
   listas sobre la estantería en PostgreSQL y usar una transacción de
   escritura en SQLite para comprobar dentro de ella si ya existe la
   primera lista. Si una petición concurrente llega con el formulario
   inicial desactualizado, devolver un error recuperable para elegir una
   lista existente. Permitir listas adicionales vacías tras el primer
   guardado.
3. **Geoapify.** Añadir configuración por entorno y un servicio Laravel
   para la geocodificación directa e inversa. La clave de búsqueda se usa
   en servidor. Servir el mapa base desde Geoapify con una clave de teselas
   restringida a los orígenes autorizados, visible en el cliente. Aplicar
   límites de peticiones a las búsquedas y evitar consultas por pulsación
   de tecla. Mostrar estados de error, límite y ausencia de resultados.
   Mantener visible la atribución de Geoapify y sus fuentes.
4. **Vista de Sitios.** Separar mapa, búsqueda y formularios siguiendo las
   convenciones de frontend. Mostrar las listas disponibles y permitir
   crear una lista adicional vacía. En el alta inicial, solicitar el
   nombre y emoji opcional de la primera lista junto con el sitio. Permitir
   elegir resultado, tocar el mapa o introducir coordenadas directamente.
   Una respuesta inversa solo puede sugerir el nombre para el punto vigente
   y respeta el texto escrito por la persona. El punto elegido conserva
   sus coordenadas aunque la dirección devuelta sea aproximada. Tras
   guardar, mostrar los marcadores con el emoji de su lista o 📍 y el
   nombre del sitio. Conservar el formulario ante errores. Permitir
   completar el alta por teclado y colocar un punto por toque.
5. **Verificación y documentación.** Añadir pruebas del nuevo comportamiento,
   ejecutar PHPUnit con SQLite y el build de Vite. Documentar la
   configuración local y de Render en la documentación existente bajo
   `docs/`. Verificar el flujo con Geoapify Free y medir su consumo con
   datos de prueba. El CI existente ejecutará también PHPUnit con
   PostgreSQL cuando corresponda publicar.

## Estrategia de pruebas

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 y CA-02 | Guardar con y sin nota. Comprobar datos persistidos, asignación de Visitado y marcador con emoji de lista o 📍. |
| CA-03 | Simular Geoapify en pruebas para nombre, dirección y búsqueda inversa. Comprobar que el nombre se puede editar, que la respuesta inversa mantiene el punto elegido y que la entrada directa exige nombre. |
| CA-04 | Probar rangos, coordenadas no finitas, acceso sin sesión, cuentas ajenas y dos estanterías. Recargar y comprobar datos y marcadores. |
| CA-05 | Simular errores, límites y búsquedas vacías. Comprobar que el formulario mantiene los datos ante un fallo de guardado y permite continuar por coordenadas si falla el mapa base. |
| CA-06 | Comprobar teclado, foco y toque en móvil y atribución visible. Si Ángel no autoriza navegador, dejar estas comprobaciones para su revisión manual. |
| CA-07 | Probar estantería nueva, creación conjunta, reversión ante fallo, rechazo de lista ajena o ausente y petición inicial desactualizada. |
| CA-08 | Crear una lista vacía después del primer guardado y comprobar que tras recargar se puede elegir para el siguiente sitio. |

## Compatibilidad y publicación

Conservar los recorridos del cajón de la spec 013. Las migraciones se
aplicarán con el flujo de despliegue existente cuando se autorice publicar.
El CI externo lo comprueba Ángel. Las claves se configuran fuera de Git.
