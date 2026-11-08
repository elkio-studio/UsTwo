# UsTwo

UsTwo es una aplicación web privada para Ángel y Alejandra. Su objetivo es
centralizar listas, experiencias, recuerdos e información compartida mediante
un dashboard desde el que cada lista abre su propia página.

## Estado del proyecto

El proyecto está en fase de definición de producto. Todavía no se ha aprobado
un stack tecnológico, una arquitectura, un modelo de datos ni el inicio de la
implementación de la aplicación.

## Decisiones de producto confirmadas

- La aplicación es privada y está pensada inicialmente para dos usuarios.
- El dashboard es el punto central de navegación.
- Cada lista se abre en una página independiente, no en una ventana emergente.
- La presentación puede adaptarse al contenido de cada lista.
- Películas y videojuegos tendrán necesidades funcionales similares.
- Los lugares se visualizarán preferentemente en un mapa interactivo.
- La sincronización con listas personales de Google Maps no es obligatoria.
- El foco principal es registrar experiencias pasadas, salvo deseos y regalos.

Las listas iniciales, la privacidad por contenido, los campos, las
integraciones externas, el diseño visual, el stack y la arquitectura siguen
pendientes de decisión.

## Proceso de trabajo

Los cambios relevantes siguen el proceso SDD descrito en
[`docs/proceso-sdd.md`](docs/proceso-sdd.md). Las decisiones técnicas aprobadas
se conservan en [`docs/decisiones-tecnicas.md`](docs/decisiones-tecnicas.md) y
los artefactos de cada cambio en `docs/specs/`.

Las skills locales `ui-ux-pro-max`, `frontend-design` e `impeccable` apoyan la
definición visual, la construcción de interfaces y su revisión dentro de ese
proceso. Su uso por fase y procedencia se describen en
[`docs/proceso-sdd.md`](docs/proceso-sdd.md#diseño-de-interfaces-dentro-del-flujo).
