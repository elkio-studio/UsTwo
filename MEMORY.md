# Memoria operativa de UsTwo

## Foco actual

Definir el MVP y la experiencia de uso, y completar la definición técnica antes
de comenzar la implementación de la aplicación.

## Estado inmediato

- La base local del stack está instalada en la raíz con una página técnica;
  todavía no hay funcionalidades de producto.
- El flujo SDD local está disponible para futuros cambios relevantes.
- Las tres skills de diseño están instaladas en `.agents/skills/` e integradas
  en el flujo SDD; todavía no existe una dirección visual aprobada.
- La base del stack está aprobada: Laravel + Inertia + React (JavaScript/JSX),
  CSS puro y CSS Modules; véase DT-001.
- `docs/specs/001-stack-tecnologico/spec.md` está aprobada tras revisión `PASS`
  y confirmación humana. No existe plan ni autorización de implementación.
- React 19, Inertia 3 y Vite 8 confirmados, con aplicación en la raíz y sin
  base de datos por ahora; sesiones/caché locales en archivos (DT-005).
- La instalación 004 está completada en `main`. La auditoría final del
  2026-11-09 aprobó los nueve criterios. Los cambios están pendientes de commit.
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

## Decisiones pendientes que afectan al siguiente paso

- Listas incluidas en la primera versión.
- Contenido compartido, individual y especialmente sensible.
- Campos y acciones esenciales de cada lista inicial.
- Reglas de privacidad y acceso para los dos usuarios.

## Próximo paso útil

Continuar la definición funcional del MVP. Los siguientes cambios se trabajarán
con ramas según la instrucción del desarrollador y el flujo Git del proyecto.

