# Memoria operativa de UsTwo

## Foco actual

Spec 031 en feat/alta-deseos: Ángel aprueba la revisión a **solo alta manual y
solo euros**, retirando autocompletado por la escasa cobertura de las tiendas
probadas. No mantener servicios/parser/endpoint/hook de extracción sin uso.
Listas obligatorias creadas expresamente, URL/título y opcionales descripción
/precio EUR; autor de sesión y estado pending. Sin selector de moneda.

Migración 013 aplicada en SQLite local, lote 6. Migración 014 de EUR-only
aplicada en lote 7, con copia consistente y datos anteriores idénticos;
conserva también una lista y un deseo EUR ya creados por Ángel. Si encuentra
otra moneda, la migración se detiene sin convertir o borrar.
Copia: %TEMP%/ustwo-spec031-eur/backups/before-spec031-20261123-191410.sqlite.

Suite SQLite tras revisión: 81 pruebas, 2280 aserciones, cero fallos y dos
omisiones por PostgreSQL aislado. Test Node y build (692 módulos) correctos;
auditoría sin defectos adicionales. Ángel confirma el alta manual final en euros
en escritorio, móvil y teclado; spec 031 cerrada localmente con revisión PASS.
23/11/2026, 15:02–19:23 CET: descontadas 2 horas fuera comunicadas por Ángel,
quedan 2 h 21 min efectivos; GitHub Projects: 2.5 h tras redondeo.
Ángel autoriza commit/publicación, actualizar dev antes de integrar y actualizar
documentation antes de copiar la spec 031 y el informe de enlaces. La
comprobación de CI/PostgreSQL tras publicar queda a cargo de Ángel.

Web 8000 y Vite 5173 se dejaron activos; no detener mientras Ángel los use.
Servidor local fuerza SQLite sin cambiar .env. No abrir navegador.

## Último despliegue

Despliegue del diagnóstico de Open Library completado el 20 de noviembre
de 2026 con el commit `d983f63`: CI y Render en verde; salud y acceso HTTP 200.
Ángel confirma que la búsqueda vuelve a funcionar. No se ha identificado la
causa exacta del fallo anterior (cURL 28 tras unos 3 segundos); no se han
cambiado tiempos de espera, TLS ni lógica pública para resolverlo.

Se conservan las métricas seguras en stderr por decisión de Ángel. Si vuelve
a fallar, reproducir y revisar `Reading catalog request failed`: host, motivo,
duración, código cURL/HTTP e hitos acumulados DNS/TCP/TLS/primer byte/total,
sin URLs completas, claves, consultas ni IPs. Un cero no identifica por sí
solo una fase fallida; puede haber caché o conexión reutilizada. Sin métricas
del transporte se indica `timings_available=false`.

Ambas ramas de diagnóstico están integradas en dev y main y borradas en
local y origin. La copia original queda en dev. No hay acceso de terminal/API
a Render configurado en este entorno. El usuario ha indicado no usar Brave.

La especificación 009 «El recibidor» está completada y verificada en
`feat/login-calido`. Las pruebas automatizadas y la comprobación manual
del desarrollador constan en las tareas de la spec.

## Estado inmediato

- El despliegue 007 está verificado y su especificación está completada.
- Nuestra estantería (008) está completada y verificada.
- El MVP 005 está implementado con SQLite local y tiene auditoría final
  `PASS`, incluida la integración real de TMDB. Véase la evidencia de
  verificación en sus tareas.
- El flujo SDD local está disponible para futuros cambios relevantes.
- Las tres skills de diseño están instaladas en `.agents/skills/` e integradas
  en el flujo SDD. La identidad visual 006 está implementada y verificada
  en móvil y escritorio. La evidencia está en las tareas de la especificación.
- La base del stack está aprobada: Laravel + Inertia + React (JavaScript/JSX),
  CSS puro y CSS Modules; véase DT-001.
- `docs/specs/ep-00-base-proyecto/001-stack-tecnologico/spec.md` está aprobada tras revisión `PASS`
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

El cierre documental está registrado en la rama de trabajo. La primera
publicación de la spec 009 requiere la ventana de mantenimiento del README.
