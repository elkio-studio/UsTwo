---
spec: "013"
status: approved
---

# Plan técnico — Añadir y abrir el cajón de Sitios

## Enfoque

Ampliar el selector y las reglas de colocación existentes con el tipo
Sitios. La vista de destino muestra el título y el regreso a la
estantería. El [informe de mapas](../../investigaciones/mapas.md) queda como
referencia para las historias que incorporen el mapa y el contenido.

## Fases de implementación

1. **Persistencia.** Añadir una migración con posición opcional de Sitios
   por estantería. Las existentes comienzan con Sitios sin colocar.
   Ampliar la protección actual contra posiciones repetidas a cada par
   de cajones en SQLite y PostgreSQL. Comprobar que la reversión recupera
   la protección anterior y conserva las posiciones de Películas y Series.
2. **Colocación.** Exponer la posición de Sitios en los datos de la
   estantería. Ampliar la operación común de colocación para que cada
   tipo valide el rango 1–8 y compruebe los otros dos tipos en la misma
   actualización. Una colisión deja intactas las posiciones guardadas y
   devuelve el error de hueco existente.
3. **Apertura.** Incorporar la colocación y la vista de Sitios al grupo de
   rutas autenticadas con comprobación de pertenencia a la estantería.
   Registrar la vista en el resolutor de Inertia y enlazar el regreso con
   la estantería que autorizó el servidor.
4. **Interfaz.** Añadir Sitios al selector de huecos libres y representar
   su acceso con un globo y el nombre accesible «Sitios». Mantener la
   cuadrícula, el comportamiento de los otros cajones y sus dos
   orientaciones. Aplicar las convenciones de componentes, CSS y formato.
5. **Verificación.** Añadir pruebas del nuevo comportamiento, revisar las
   de Películas y Series, compilar el frontend y registrar las evidencias.

## Estrategia de pruebas

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Verificar que Sitios se puede elegir en un hueco libre. Las pruebas comprueban la posición guardada y el valor que devuelve una nueva carga de la estantería. |
| CA-02 | Comprobar que el destino resuelve un JSX existente en Inertia, muestra «Sitios» y enlaza de vuelta a su estantería. Probar globo y foco visible con ratón, teclado y toque en escritorio y móvil. |
| CA-03 | Probar colocación y movimiento como propietario y como miembro, persistencia, liberación del hueco anterior y rechazo de destinos inválidos u ocupados en ambos sentidos. Simular una petición que llega después de que se ocupe su destino y comprobar la protección de la base de datos. |
| CA-04 | Probar dos estanterías, invitado sin pertenencia y sesión ausente mediante acceso y escritura directos. Confirmar que mover Sitios en una estantería conserva la distribución de la otra. |

Ejecutar PHPUnit con SQLite y el build de Vite en local. El CI existente
ejecuta también PHPUnit con PostgreSQL. El resultado externo lo confirma
Ángel. Registrar cualquier comprobación de interfaz que quede pendiente.

## Compatibilidad y despliegue

Conservar rutas, permisos y posiciones existentes. La migración seguirá
el flujo de despliegue habitual cuando se autorice la publicación. Esta
historia no incorpora dependencias de mapas, claves ni cambios al CI/CD.
