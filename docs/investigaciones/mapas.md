# Investigación de mapas para UsTwo

Resultado de la [spec 012](../specs/012-cajon-sitios/spec.md), vinculada a la
[épica EP-01](https://github.com/elkio-studio/UsTwo/issues/23).

## Comparativa

| Opción | Cuotas y acceso | Implicaciones |
| --- | --- | --- |
| Leaflet + Geoapify Free | Leaflet es libre y carece de cuotas propias. Geoapify requiere clave de API, ofrece 3.000 créditos diarios compartidos y hasta 5 peticiones por segundo, sin tarjeta. | Geoapify sirve el mapa base y las búsquedas directa e inversa. Una tesela cuesta 0,25 créditos y una consulta estándar de geocodificación cuesta 1 crédito. Permite guardar resultados con la atribución correspondiente. [Leaflet](https://leafletjs.com/) · [Tarifas](https://www.geoapify.com/pricing/) · [Consumo](https://www.geoapify.com/pricing-details/) · [Geocodificación](https://www.geoapify.com/geocoding-api/) |
| Google Maps Platform | Requiere clave y facturación habilitada. Dynamic Maps y Geocoding tienen 10.000 eventos mensuales gratuitos cada uno. Places tiene cuotas y precios según la operación. | En el EEE, Places limita a 30 días el almacenamiento de las coordenadas devueltas. La excepción de almacenamiento indefinido de Geocoding exige aislamiento por usuario, lo que complica el uso compartido. [Tarifas](https://developers.google.com/maps/billing-and-pricing/pricing) · [Condiciones del EEE](https://cloud.google.com/terms/maps-platform/eea/maps-service-terms) |
| Leaflet + OpenStreetMap + Nominatim público | Sin clave. Nominatim limita el uso a una petición por segundo para toda la aplicación y excluye el autocompletado. | Alternativa para poco tráfico con búsqueda al pulsar un botón. Requiere atribución visible y cumplir la política de teselas y de búsqueda. La disponibilidad del servicio público se presta sin garantías. [Nominatim](https://operations.osmfoundation.org/policies/nominatim/) · [Teselas](https://operations.osmfoundation.org/policies/tiles/) |

## Decisión y límites

Ángel eligió **Leaflet + Geoapify Free**. Leaflet muestra el mapa 2D y los
marcadores. Geoapify aporta las teselas y convierte nombres o direcciones en
coordenadas y viceversa. En el mapa se debe mostrar la atribución a Geoapify
y a las fuentes de datos correspondientes, como OpenStreetMap.

La búsqueda por nombre puede omitir comercios o devolver datos
desactualizados. La entrada manual de un nombre y la selección directa de
coordenadas permiten guardar el sitio en esos casos. Si la vía elegida
resulta inviable, Ángel revisará el alcance antes de continuar. La
alternativa sin clave es Leaflet con teselas de OpenStreetMap y Nominatim
público, dentro de sus límites de uso.

## Persistencia prevista

UsTwo guardaría el identificador de la estantería, la latitud, la longitud y
un nombre obligatorio. Si al pulsar el mapa se identifica una calle o un
establecimiento, se sugiere un nombre editable y se conserva el punto
pulsado. Si la consulta falla o no devuelve un nombre, se puede escribir
uno o guardar las coordenadas como nombre. Al introducir las coordenadas
directamente, se pide un nombre. La dirección y el emoji son opcionales.
La implementación deberá comprobar la validación de coordenadas, el
aislamiento entre estanterías y la persistencia tras recargar.

El globo del cajón abre el mapa 2D. El usuario puede elegir el emoji del
marcador y el nombre se muestra al activarlo. El marcador usa 📍 por defecto.
Fotos, valoraciones y globo interactivo quedan fuera del alcance acordado
para el cajón. Estos detalles sirven de referencia para sus historias.

## Evidencias

| Prueba | Resultado observado |
| --- | --- |
| Búsqueda por nombre | Ángel encontró algunos establecimientos en Geoapify y observó que otros faltaban o podían estar desactualizados. [Playground de Geoapify](https://apidocs.geoapify.com/playground/geocoding/) |
| Búsqueda por dirección | Una dirección completa de Córdoba devolvió una coincidencia de tipo edificio con confianza del 100 % según la demo y se mostró en el mapa. [Playground de Geoapify](https://apidocs.geoapify.com/playground/geocoding/) |
| Coordenadas y marcador al pulsar | La demo de Leaflet devolvió las coordenadas del punto pulsado. En Leaflet.draw se activó la herramienta de marcador y el marcador apareció en el punto elegido. El complemento se usó en la demo. [Leaflet](https://leafletjs.com/examples/quick-start/) · [Leaflet.draw](https://leaflet.github.io/Leaflet.draw/docs/examples/full.html) |
| Búsqueda inversa | El punto público de ejemplo de Berlín devolvió Netto Marken-Discount y la dirección Rathausstraße 5. [Playground de Geoapify](https://apidocs.geoapify.com/playground/geocoding/) |

Las pruebas se hicieron por separado. La integración en UsTwo deberá
verificar el flujo unido, los errores del proveedor, el consumo real de
créditos y el uso en móvil y con teclado.

## Configuración de UsTwo

Configura `GEOAPIFY_SEARCH_KEY` y `GEOAPIFY_TILES_KEY` en el `.env` local
y como variables de entorno del servicio de Render. La primera clave se
usa únicamente en Laravel. La segunda se envía al navegador para cargar
las teselas y debe restringirse al origen local y al dominio desplegado
que corresponda. Las dos claves quedan fuera de Git.

La búsqueda se envía al pulsar «Buscar». Elegir un punto consulta la
búsqueda inversa. Si el proveedor falla, el formulario permite introducir
las coordenadas directamente. Comprueba el consumo del proyecto en el
panel de Geoapify al verificar el flujo integrado.
