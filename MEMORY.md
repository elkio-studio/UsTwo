# Memoria operativa de UsTwo

## Foco actual

El despliegue 007 está verificado y la especificación está completada.

## Estado inmediato

- El MVP 005 está implementado con SQLite local y tiene auditoría final
  `PASS`, incluida la integración real de TMDB. Véase la evidencia de
  verificación en sus tareas.
- El flujo SDD local está disponible para futuros cambios relevantes.
- Las tres skills de diseño están instaladas en `.agents/skills/` e integradas
  en el flujo SDD. La identidad visual 006 está implementada y verificada
  en móvil y escritorio. La evidencia está en las tareas de la especificación.
- La base del stack está aprobada: Laravel + Inertia + React (JavaScript/JSX),
  CSS puro y CSS Modules; véase DT-001.
- `docs/specs/001-stack-tecnologico/spec.md` está aprobada tras revisión `PASS`
  y confirmación humana. No existe plan ni autorización de implementación.
- React 19, Inertia 3 y Vite 8 confirmados, con aplicación en la raíz.
  Sesiones y caché locales en archivos (DT-005), datos del MVP en SQLite
  (DT-006).
- La instalación 004 está completada en `main`. La auditoría final del
  2026-11-09 aprobó los nueve criterios. La base está en el commit `28e6dd5`.
- Vite, Composer y npm con configuración mínima aprobados en DT-002.
- Laravel 13 y PHP 8.5 separado de XAMPP aprobados en DT-003. La preparación
  de PHP (especificación 002) está completada con verificación final `PASS`.
  XAMPP, Apache y PATH no se han modificado.
- PHP independiente 8.5.12 instalado bajo `%LOCALAPPDATA%\UsTwo\php\8.5`.
- Composer 2.11.1 independiente instalado bajo
  `%LOCALAPPDATA%\UsTwo\composer\2.11.1`. Su invocación de versión con PHP
  8.5.12 devuelve código 0 sin avisos; especificación 003 completada con
  verificación final `PASS`. El Composer global 2.8.9 permanece intacto.
  Descargas e instalación de paquetes y PHP de subprocesos Composer comprobados
  durante la instalación 004; el entorno previo permanece intacto.
- Entorno previo conservado: PHP 8.2.12 (XAMPP), Composer 2.8.9, Node 22.16.0
  y npm 10.9.2. Laravel 13.40.0, Inertia y React instalados según el plan 004.

## Próximo paso útil

La fase posterior de despliegue contempla Render, GitHub Actions y
PostgreSQL (DT-006).

