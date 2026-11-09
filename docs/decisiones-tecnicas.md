# Decisiones técnicas de UsTwo

## Estado actual

La base del stack está aprobada en DT-001 y las herramientas en DT-002.
Laravel 13 y PHP 8.5 separado de XAMPP están aprobados en DT-003.
Composer 2.11.1 independiente para UsTwo está aprobado en DT-004.
La base mínima de instalación está confirmada en DT-005.
La instalación local mínima está completada con auditoría final `PASS`.
La especificación y el plan del MVP 005 están aprobados. El MVP usa SQLite en
local y TMDB como catálogo de películas. Render, GitHub Actions y la
verificación con PostgreSQL corresponden a la fase de despliegue (DT-006).

Hasta que exista una decisión aprobada, una especificación o un plan puede
presentar alternativas, pero no elegir ni implementar una de ellas.

## Registro

### DT-001 — Laravel, Inertia y React con JSX y CSS puro

- Fecha: 2026-11-08.
- Estado: aprobado por el desarrollador en la conversación.
- Contexto: UsTwo necesita una base tecnológica para su aplicación web privada.
- Decisión: Laravel para el backend y React con JavaScript y JSX para la
  interfaz, conectados mediante Inertia. Estilos con CSS puro y CSS Modules
  para componentes, sin introducir un framework de CSS. Cada componente
  `NombreComponente.jsx` tiene su `NombreComponente.module.css`. Los estilos
  compartidos se guardan en `global.css`, importado únicamente desde las
  páginas principales, no desde componentes reutilizables, layouts ni el
  punto de entrada de la aplicación.
- Consecuencias: Laravel gestiona las rutas de las páginas servidas con
  Inertia y React las presenta. No se necesita una API separada para conectar
  esas páginas. Esto no prohíbe futuras integraciones o endpoints aprobados.
- Límites: no elige versiones, herramienta de compilación, base de datos,
  autenticación, librerías adicionales ni despliegue; tampoco autoriza
  instalaciones o implementación. La estructura concreta de páginas se
  precisará en el plan correspondiente.
- Especificación: [001-stack-tecnologico/spec.md](specs/001-stack-tecnologico/spec.md).

### DT-002 — Herramientas y configuración mínima

- Fecha: 2026-11-08.
- Estado: aprobado expresamente por el desarrollador en la conversación.
- Contexto: completar las herramientas necesarias para preparar la instalación
  del stack definido en la especificación 001.
- Decisión: Vite para compilar JSX y CSS Modules; Composer para dependencias
  PHP y npm para dependencias JavaScript. Configuración mínima, sin starter kit
  que añada funcionalidades, TypeScript, framework CSS o componentes no pedidos.
- Consecuencias: integrar Laravel, Inertia y React de forma explícita y añadir
  solo dependencias necesarias y aprobadas para esa integración.
- Límites: versiones y paquetes concretos pendientes de decisión y comprobación
  de compatibilidad; no autoriza instalar herramientas, actualizar el entorno,
  generar código, implementar autenticación ni elegir persistencia.
- Relación: complementa DT-001 y
  [001-stack-tecnologico/spec.md](specs/001-stack-tecnologico/spec.md).
  No cambia el alcance documental de la especificación aprobada.

### DT-003 — Laravel 13 y actualización de PHP

- Fecha: 2026-11-08.
- Estado: aprobado expresamente por el desarrollador en la conversación.
- Contexto: el PHP disponible es 8.2.12 de XAMPP; Laravel 13 requiere al menos
  PHP 8.3 según sus requisitos oficiales.
- Decisión: usar Laravel 13 y PHP 8.5 instalado por separado de XAMPP.
- Consecuencias: preparar un entorno PHP compatible antes de instalar Laravel.
- Límites: parche y procedimiento de instalación se concretan en el cambio
  002; no se ha aprobado sustituir el PHP de XAMPP ni modificar Apache o el
  PATH global.
- Relación: complementa DT-001 y DT-002; no amplía el alcance documental de
  la especificación 001 ni autoriza implementar la aplicación.
- Fuente: [requisitos de Laravel 13](https://laravel.com/framework/docs/13.x/releases).
- Preparación del entorno: [002-entorno-php/spec.md](specs/002-entorno-php/spec.md),
  completada con revisión final `PASS`; plan aprobado y evidencias en sus tareas.

### DT-004 — Composer independiente para PHP 8.5

- Fecha: 2026-11-08.
- Estado: elección de versión y aislamiento aprobada expresamente por el
  desarrollador en la conversación; especificación y plan aprobados.
  Instalación ejecutada por solicitud expresa y verificación final `PASS`;
  especificación 003 completada y plan aprobado.
- Contexto: el Composer global 2.8.9 muestra un aviso de obsolescencia de
  `react/promise` al ejecutarse con PHP 8.5.12.
- Decisión: usar Composer 2.11.1 por separado para UsTwo y comprobar que el
  aviso desaparece al ejecutarlo con el PHP independiente.
- Consecuencias: descargar el PHAR oficial, verificar su integridad y usarlo
  por ruta explícita, conservando la instalación global existente.
- Límites: no modificar Composer global, XAMPP, PHP ni PATH persistente; no
  instalar la aplicación o sus dependencias ni ocultar avisos.
- Especificación: [003-composer-local/spec.md](specs/003-composer-local/spec.md).
- Fuente: [descarga oficial de Composer](https://getcomposer.org/download/).

### DT-005 — Base mínima para instalar la aplicación

- Fecha: 2026-11-08.
- Estado: decisiones y especificación 004 aprobadas expresamente por el
  desarrollador, incluidas las propuestas complementarias. Plan aprobado;
  instalación completada en `main` con auditoría final `PASS` el 2026-11-09.
- Contexto: preparar la instalación del resto del stack tras completar PHP y
  Composer independientes, sin anticipar decisiones del producto.
- Decisión: React 19 + Inertia 3 + Vite 8 junto a Laravel 13, aplicación en la
  raíz del repositorio y ninguna base de datos todavía; sesiones y caché
  locales en archivos.
- Consecuencias: excluir creación de SQLite y migraciones automáticas y conservar
  el contexto existente al incorporar el esqueleto de Laravel.
- Complementos aprobados: plugins de integración listados en la especificación
  004, PHPUnit 12, Mockery 1 y colas síncronas sin workers.
- Ajuste aprobado durante implementación: Laravel `^13.40.0` e Inertia
  `^3.7.0` en el manifiesto Composer; los locks conservan las versiones exactas
  probadas. No autoriza actualizar las versiones instaladas.
- Límites: parches y comprobaciones de compatibilidad se concretan en el plan.
  La configuración local temporal
  no decide persistencia, sesiones o arquitectura de producción.
- Especificación: [004-instalacion-stack/spec.md](specs/004-instalacion-stack/spec.md).

### DT-006 — Render y GitHub Actions para el despliegue

- Estado: plataformas y separación de fases aprobadas por el desarrollador.
- Decisión: desplegar UsTwo en Render y usar GitHub Actions para CI/CD.
- Alcance: el primer plan del MVP cubre implementación y verificación local.
  La configuración de Render y de GitHub Actions se planificará en la fase
  de despliegue.
- Persistencia aprobada: SQLite para el MVP y sus pruebas locales.
  PostgreSQL se utilizará para verificar el despliegue en la fase posterior.
  La especificación y el plan 007 concretan Render Free con Docker y PHP 8.5,
  PostgreSQL y sesiones en Supabase Free por TLS, y CI en cada push a
  `dev` o `main`. Tras CI en `main`, GitHub Actions migra con una credencial
  separada y solicita el despliegue del mismo commit. El seeder inicial se
  ejecuta desde un equipo autorizado.
- Despliegue: [007-despliegue-render/spec.md](specs/007-despliegue-render/spec.md).
- Especificación: [005-mvp-ustwo/spec.md](specs/005-mvp-ustwo/spec.md).

Cuando se apruebe una decisión relevante, se añadirá aquí de forma concisa con:

- identificador y título;
- fecha y estado;
- contexto y decisión;
- consecuencias y límites;
- especificación relacionada, si existe.

