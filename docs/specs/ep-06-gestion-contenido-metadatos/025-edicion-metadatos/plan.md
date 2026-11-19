---
spec: "025"
status: approved
---

# Plan — Política de edición de metadatos externos

## Resumen

Entregar una política documental para Películas, Series y Lecturas,
según la [spec aprobada](spec.md). No implementar restricciones,
formularios, almacenamiento de procedencia ni migraciones en esta historia.

## Componentes afectados

- `docs/decisiones-tecnicas.md`: incorporar la decisión aprobada de EP-06,
  con enlace a la spec como inventario detallado, evitando duplicarlo.
- `spec.md` y `tasks.md` de esta carpeta: registrar verificación y cierre.

## Contratos, datos e interfaces

La política distingue valores de catálogo protegidos, aportaciones
manuales corregibles y puntuaciones y notas personales de cada miembro.
Incluye la conservación de datos existentes, la prevalencia del catálogo
al vincular y la conservación editable de datos manuales que el catálogo
no devuelva. Las portadas ausentes mantienen el reemplazo.

No se elige un modelo técnico para guardar la procedencia por campo.
Ese diseño y su aplicación corresponden a las historias posteriores.

## Fases de ejecución

1. Revisar la política aprobada frente a las specs 022 y 024 y distinguir
   el comportamiento actual de las reglas que se aplicarán después.
2. Añadir en decisiones técnicas una entrada concisa con contexto,
   reglas aprobadas, límites y referencia a esta spec. Indicar que la
   aprobación documental no significa que el código ya cumpla la política.
3. Verificar los casos de la tabla siguiente, los enlaces relativos y el
   diff. Registrar evidencias y pendientes de integración sin modificar
   el estado de las historias anteriores.
4. Tras la verificación y la solicitud de cierre, registrar inicio, fin,
   pausas conocidas y horas para GitHub Projects según el proceso SDD.

## Estrategia de pruebas y criterios

| Criterio | Verificación documental prevista |
| --- | --- |
| CA-01 | Contrastar el inventario: título, fechas, autoría, sinopsis, portadas, identidad externa, visionado y aportaciones personales en las tres colecciones. |
| CA-02 | Revisar ejemplos de valor importado protegido, campo vacío completado y corrección posterior de ese valor manual; confirmar la exclusión de portadas manuales. |
| CA-03 | Revisar los casos de catálogo incorrecto, ficha existente de producción, vínculo con valor externo y vínculo sin valor externo; comprobar conservación de fichas, IDs y valoraciones y ausencia de autorización para borrar datos. |
| CA-04 | Registrar la aprobación de Ángel y verificar que la decisión remite la aplicación y sus pruebas de persistencia, permisos y conservación de aportaciones a las historias posteriores. |

No ejecutar pruebas de aplicación ni modificar SQLite: el entregable no
cambia comportamiento. Comprobar formato, destinos de enlaces y que el
diff se limita a documentación. Solicitar revisión final de evidencias
mediante el flujo de verificación del proyecto.

## Documentación

Mantener el detalle en esta spec y una referencia estable en decisiones
técnicas. No crear otro informe que repita la política. Registrar las
discrepancias con el código como trabajo posterior, sin corregirlas aquí.

## Despliegue y compatibilidad

No hay despliegue funcional, cambios de contratos ni migraciones.
Publicación, integración en dev y traslado de documentación a
documentation son pasos posteriores sujetos a autorización específica;
antes de cada integración se actualiza la rama de destino según el flujo
del proyecto.

## Riesgos y medidas

- Confundir política y comportamiento actual: indicar expresamente que
  las restricciones y la procedencia por campo aún requieren implementación.
- Duplicar reglas: enlazar el inventario desde la decisión técnica.
- Deducir procedencia histórica o permisos de borrado: conservar la
  confirmación concreta de Ángel y la preservación de todos los registros.
