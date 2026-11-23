# Memoria operativa de UsTwo

## Foco actual

Spec 030 en `feat/cajon-deseos`: especificación, plan y propuesta técnica
aprobados; implementación solicitada y realizada sin navegador ni publicación.
Deseos incorpora caja/tarjeta WebP, vista vacía y alta deshabilitada con aviso.
Migración `2026_11_23_000012_add_wish_slot_to_spaces` aplicada en SQLite local
(lote 5), con copia consistente y comparación de datos anteriores idéntica.
Suite SQLite: 73 pruebas, 2 160 aserciones, cero fallos; dos pruebas omitidas
porque requieren PostgreSQL aislado. Build correcto.

Spec 030 cerrada localmente con revisión PASS tras la comprobación de Ángel
en escritorio y móvil, teclado y movimiento reducido. Horario 23/11/2026,
14:14–14:53 CET; GitHub Projects: 0.75 h estimadas.
Ángel autoriza commit/publicación, actualizar dev antes de integrar y actualizar
documentation antes de copiar solo los artefactos de la spec 030. CI/PostgreSQL
pendientes de comprobación por Ángel tras publicar; no darlos por validados.
Web local 8000 y Vite 5173 arrancados en segundo plano; no detener mientras
Ángel los use. El servidor local fuerza SQLite sin modificar el archivo .env.

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

