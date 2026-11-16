---
spec: "019"
status: approved
---

# Plan técnico — Añadir y abrir el cajón de Lecturas

## Fases de implementación

1. **Posición y permisos.** Añadir una migración reversible para la
   posición opcional de Lecturas en la estantería. Seguir la protección
   de colisiones de Sitios en SQLite y PostgreSQL. Incluir la posición
   en el modelo y en los datos que recibe la estantería. Reutilizar la
   operación de colocación y comprobar los cuatro tipos de cajón en
   ambas direcciones. Proteger lectura y colocación con la membresía
   de la estantería, como en Sitios.
2. **Lomos y apertura.** Incorporar Lecturas al selector de huecos.
   Representar los lomos como un componente con su CSS Module y colores
   de `global.css`. Ajustar la fila al ancho del hueco sin deformar los
   lomos. Usar títulos decorativos inventados y un único enlace con
   nombre accesible «Lecturas». Con hover o foco, inclinar un libro
   desde su base con perspectiva y devolverlo al retirar el puntero o
   el foco. Mantener foco visible, toque directo y movimiento reducido.
3. **Vista vacía.** Registrar la página de Lecturas en Inertia y
   reutilizar la cabecera y el enlace de vuelta al espacio de origen.
   Mostrar Libros, Manga y Cómics como pestañas accesibles, cada una con
   su mensaje vacío. La vista carece de operaciones de catálogo, alta
   y progreso en esta historia.
4. **Verificación y cierre.** Aplicar la migración a SQLite local y
   verificar su estado. Ejecutar PHPUnit, el build de Vite y
   `git diff --check`. Registrar las comprobaciones visuales y de
   interacción. Al terminar la historia, registrar el esfuerzo para
   GitHub Projects y publicar la documentación en `documentation`
   según el flujo SDD.

## Pruebas por criterio

| Criterio | Verificación prevista |
| --- | --- |
| CA-01 | Colocar Lecturas desde un hueco libre. Verificar la ruta y el enlace de vuelta. Abrir con clic, toque y teclado. |
| CA-02 | Verificar ajustes en estantería horizontal y vertical. Comprobar inclinación de la parte superior, base apoyada, foco visible y movimiento reducido. |
| CA-03 | Probar persistencia, movimiento, liberación del hueco anterior, rangos, colisiones en ambos sentidos, aislamiento, acceso sin sesión y reversibilidad de la migración. |
| CA-04 | Cambiar entre las tres pestañas, comprobar su mensaje vacío, la navegación con teclado y la vuelta a la estantería. |

## Compatibilidad y despliegue

Las estanterías existentes empiezan con la posición de Lecturas vacía.
El flujo de despliegue vigente aplica la nueva migración. El CI cubre
SQLite y PostgreSQL. La revisión visual se realiza en local y se registra
antes del cierre.
