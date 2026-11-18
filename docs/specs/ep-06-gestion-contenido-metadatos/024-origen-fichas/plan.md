---
spec: "024"
status: approved
---

# Plan técnico — Identificar el origen de las fichas

## 1. Conservar la identificación existente

- Usar `tmdb_id` para identificar fichas vinculadas en Películas y
  Series. Mantener el vínculo posterior a TMDB sobre el mismo registro
  y conservar sus datos locales y aportaciones personales.
- Usar `provider` para distinguir el origen en Lecturas. Mantener
  `record_type` y `external_id` como datos separados del origen. Una
  lectura con proveedor y sin ID sigue siendo de catálogo, también
  al aplicar la regla de edición de la spec 022.
- Conservar el esquema y los datos existentes. La distinción utiliza
  los campos actuales y no requiere rellenar orígenes históricos ni
  ampliar el flujo de alta de los catálogos.

## 2. Corregir la presentación en el detalle de Lecturas

- Ajustar `ReadingInformation` para mostrar el nombre del proveedor
  en texto cuando falta el identificador externo. Conservar los
  enlaces actuales cuando el identificador está disponible, incluida
  la referencia general de MangaUpdates.
- Mantener «Añadida a mano» para una lectura sin proveedor. El texto
  de origen sigue visible aunque no haya enlace. Conservar el diseño
  del detalle y la navegación por teclado de los enlaces disponibles.

## 3. Pruebas y despliegue

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Comprobar el origen manual y de catálogo tras recarga. Verificar que el vínculo posterior a TMDB cambia el origen mostrado. |
| CA-02 | Comprobar los IDs persistidos y una lectura con proveedor e ID nulo. Verificar en el detalle que se reconoce el proveedor sin generar un enlace inválido. |
| CA-03 | Reutilizar las pruebas de vinculación de Películas y Series. Comprobar que conservan el ID local y las valoraciones, y que un fallo de vínculo mantiene la información previa. |
| CA-04 | Probar que una lectura con proveedor e ID nulo conserva las reglas de edición de catálogo. Verificar el aislamiento entre estanterías con las pruebas existentes. |

Añadir cobertura para el caso sin ID. Ejecutar PHPUnit con SQLite,
compilar con Vite y comprobar el diff. Verificar el detalle con y sin
enlace en móvil y con teclado, y registrar la evidencia en la spec.
El despliegue sigue el flujo habitual y no añade una migración.
