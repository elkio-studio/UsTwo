---
id: "004"
title: "Instalación mínima de Laravel 13, Inertia 3 y React 19"
status: completed
---

# 004 — Instalación mínima de Laravel 13, Inertia 3 y React 19

## Problema y objetivo

UsTwo dispone de contexto, proceso, PHP 8.5.12 y Composer 2.11.1 independientes,
pero todavía no tiene aplicación ni manifiestos de dependencias. Preparar una
base local ejecutable de Laravel + Inertia + React que compile JSX y CSS puro
con CSS Modules, sin convertir la instalación en desarrollo de funcionalidades.

Esta especificación desarrolla las decisiones DT-001 a DT-005. No sustituye
las evidencias de los cambios 002 y 003 ni autoriza su implementación por sí sola.

## Alcance incluido

- Instalar la aplicación Laravel 13 en la raíz del repositorio existente,
  conservando su documentación, skills, configuración de agentes e historial.
- Conectar Laravel con Inertia 3 y React 19, sin una API separada para páginas.
- Integrar Vite 8 con JSX y JavaScript; configuración propia en JavaScript.
- Un archivo `NombreComponente.module.css` junto a cada componente
  `NombreComponente.jsx`, incluidas las páginas. El punto de entrada que solo
  inicializa Inertia no se considera un componente y no necesita un módulo vacío.
- `global.css` para estilos compartidos, importado solo desde las páginas
  principales; no desde el punto de entrada, layouts, Blade o componentes
  reutilizables. No declararlo como entrada global independiente de Vite.
- Una página técnica de comprobación y un componente mínimo, con contenido
  ficticio y sin reglas de negocio, para verificar el recorrido Laravel →
  Inertia → React, la ejecución de JSX y el aislamiento de CSS Modules.
- Desarrollo local y compilación de producción comprobables, con instrucciones
  en README y archivos de bloqueo de Composer y npm versionados.
- Sin base de datos en esta fase: sesiones y caché locales en archivos. Los
  procesos de comprobación no crean, consultan ni migran una base de datos.

## Alcance excluido

- Funcionalidades, dashboard de producto, tarjetas de películas o listas reales.
- Elegir base de datos, modelos de negocio, tablas, migraciones o datos iniciales.
- Autenticación, autorización, registro de usuarios o decisiones de privacidad.
- Starter kits con funcionalidades, Tailwind, Bootstrap, Sass, CSS-in-JS,
  bibliotecas de componentes o TypeScript en el código propio de la aplicación.
- SSR, React Server Components, API separada, broadcasting, colas asíncronas,
  servicios externos de producto, contenedores o despliegue.
- Cambios persistentes de PATH, actualización de Node/npm, PHP, Composer,
  certificados, XAMPP, Apache o herramientas globales.
- Commits, push, integración o cambios de rama sin su autorización correspondiente.

## Actores

- Desarrollador: aprueba esta especificación y el plan y solicita implementar.
- Agente principal: único escritor de la instalación y sus pruebas.
- Usuario local de la página técnica: comprueba que funciona el stack;
  no representa todavía un usuario autenticado de UsTwo.
- Revisores SDD: revisan artefactos y evidencias en modo de solo lectura.

## Flujo principal

1. Comprobar las herramientas y el estado del repositorio, protegiendo los
   cambios existentes. Confirmar que los comandos y sus subprocesos usan el
   PHP y Composer independientes de UsTwo, sin cambios persistentes del entorno.
2. Preparar el esqueleto oficial de Laravel de forma controlada, sin ejecutar
   automatismos de instalación antes de revisarlos ni sobrescribir el repositorio.
3. Incorporar únicamente los archivos necesarios de la aplicación en la raíz,
   conservando y adaptando README y `.gitignore`, nunca reemplazándolos completos.
4. Instalar las dependencias aprobadas con versiones estables compatibles;
   registrar resolución e integridad en `composer.lock` y `package-lock.json`.
5. Configurar Laravel e Inertia, la entrada React, Vite y los estilos según las
   convenciones confirmadas; preparar entorno local sin base de datos.
6. Servir la página técnica solo en loopback: cargar HTML inicial, recibir
   props ficticias desde Laravel y comprobar una interacción local de React.
7. Verificar que las peticiones Inertia reciben su respuesta de página correcta
   y que los estilos propios de página/componente y `global.css` se aplican
   sin colisiones entre clases locales de CSS Modules.
8. Comprobar cambios en desarrollo, compilación y carga de los assets compilados
   sin depender del servidor Vite; ejecutar pruebas automatizadas de integración.
9. Documentar comandos reproducibles y evidencias, cerrar solo tras verificación
   final y mantener el producto y su diseño pendientes de definición.

## Flujos alternativos y errores

- Si hay colisión de archivos con el contexto existente, inspeccionar y adaptar
  solo el alcance aprobado; no eliminar ni restaurar trabajo previo ni iniciar
  operaciones Git de reparación.
- Si una versión no es compatible, informar y volver a la decisión necesaria;
  no forzar dependencias con `--force`, `--legacy-peer-deps` o ignorando los
  requisitos de plataforma. El plan fijará parches y restricciones compatibles.
- Si la instalación solicita datos o decisiones excluidas, detener esa parte:
  no aceptar SQLite, autenticación o servicios externos por defecto.
- Si Composer/npm fallan por TLS, certificados, permisos o requisitos del
  sistema, informar sin desactivar TLS ni modificar herramientas globales.
- Si falla una prueba o aparecen errores de carga, no declarar la base instalada
  correctamente por el mero hecho de que existan dependencias descargadas.
- Si hay puertos ocupados, usar otro puerto libre de loopback y documentarlo;
  no terminar procesos ajenos. Parar solo los servidores iniciados por la tarea
  al acabar las comprobaciones.

## Reglas de negocio y casos límite

- No se decide ninguna regla de negocio ni se utiliza información real o privada.
- La página técnica es una comprobación de instalación. No fija navegación,
  estética, colores, tipografía o estructura del futuro producto.
- No crear SQLite ni ejecutar migraciones como efecto secundario del esqueleto.
  Sesiones y caché usan archivos; si el esqueleto requiere configuración de
  colas, el alcance aprobado es ejecución síncrona sin workers ni base de datos.
- Las pruebas deben funcionar sin driver PDO específico ni base de datos,
  también en el entorno de testing; no introducir SQLite en memoria para ellas.
- Se permite `.env` local y generar la clave de aplicación sin mostrarla,
  conservándolos fuera de Git; `.env.example` solo contiene valores no secretos.
- No ocultar errores para superar los controles. Mantener los avisos del PHP
  independiente y la comprobación de integridad y TLS de los gestores.
- No ejecutar a ciegas scripts del esqueleto: algunos crean SQLite/migran o
  encadenan comandos globales. Ejecutar solo pasos revisados que respeten el alcance.
- No añadir dependencias opcionales por aparecer en ejemplos o recomendaciones.
  Las dependencias transitivas necesarias quedan resueltas en los archivos de
  bloqueo; sus tipos internos no convierten el código propio en TypeScript.

## Dependencias directas aprobadas con esta especificación

Además de las tecnologías ya confirmadas, se aprueba este conjunto mínimo:

| Uso | Paquetes y familia aprobada |
| --- | --- |
| Backend | `laravel/framework` 13.x, `inertiajs/inertia-laravel` 3.x. |
| Interfaz | `react` y `react-dom` 19.x con el mismo parche; `@inertiajs/react` 3.x. |
| Compilación | `vite` 8.x, `laravel-vite-plugin` 3.x, `@vitejs/plugin-react` 6.x y `@inertiajs/vite` 3.x. |
| Pruebas backend | `phpunit/phpunit` 12.x y `mockery/mockery` 1.x, para pruebas de la integración Laravel/Inertia sin base de datos. |

El esqueleto oficial es una plantilla, no un starter kit funcional. Esta base
omite sus herramientas opcionales no necesarias para este alcance, como Tinker,
Pail, Pao, Collision y Faker; no añade Vitest, Playwright o nuevas herramientas
de calidad. Las comprobaciones visuales usarán las herramientas ya disponibles.
Los comandos finales de pruebas se derivarán del manifiesto realmente preparado,
sin presuponer que existe `artisan test` al omitir Collision.

El plan fijará parches estables y comprobará requisitos y peer dependencies
antes de instalar. No habilitar React Compiler, Echo, fuentes externas ni
plugins opcionales. No instalar dependencias desde ramas de desarrollo.

## Criterios de aceptación

- **CA-01:** La raíz contiene una aplicación Laravel 13 funcional, con el
  contexto y los cambios previos preservados. Los manifiestos y ambos archivos
  de bloqueo reflejan las familias aprobadas y ninguna dependencia directa
  adicional no justificada y aprobada.
- **CA-02:** PHP 8.5.12 y Composer 2.11.1 locales ejecutan los comandos PHP y
  las operaciones/subprocesos de Composer comprobados; Node y npm satisfacen
  los requisitos de los paquetes. No se modifica permanentemente su selección.
- **CA-03:** La página técnica devuelve HTML inicial correcto y la petición
  Inertia correspondiente devuelve el componente y props ficticias esperados.
  El navegador muestra esas props mediante React y una interacción local
  actualiza la interfaz sin errores de ejecución o consola.
- **CA-04:** Cada componente/página JSX propio tiene su CSS Module; dos clases
  locales con el mismo nombre en página y componente no colisionan. Se verifica
  que `global.css` se importa solo desde páginas principales y funciona tanto
  en desarrollo como con los assets compilados.
- **CA-05:** El servidor Vite sirve desarrollo con actualización observable
  de JSX/CSS y el build de producción termina correctamente. Laravel carga
  la página y sus assets compilados sin servidor Vite activo ni recursos fallidos.
- **CA-06:** La página y sus pruebas funcionan sin conectar, crear o migrar una
  base de datos; sesiones y caché de desarrollo usan archivos. No se incorpora
  autenticación ni funcionalidad de producto por defecto.
- **CA-07:** Las pruebas backend de respuesta inicial y respuesta Inertia,
  los controles de plataforma/consistencia de manifiestos aplicables y la
  revisión de dependencias pasan. No quedan errores o avisos de incompatibilidad
  ignorados ni herramientas de calidad anunciadas que no se hayan ejecutado.
- **CA-08:** XAMPP, Composer global, PHP independiente y PATH persistente
  conservan los archivos/valores protegidos que el plan inventaríe. Los servidores
  de prueba solo escuchan en loopback y no quedan procesos de la tarea abandonados.
- **CA-09:** README contiene instalación, desarrollo, build y pruebas con las
  herramientas locales correctas; `.gitignore` protege `.env`, secretos,
  `vendor`, `node_modules`, assets generados y archivos de ejecución locales.
  Los archivos de bloqueo sí se versionan y la documentación no afirma que
  la aplicación esté lista para producción o que su producto esté definido.

## Decisiones aprobadas

- DT-001 a DT-004: Laravel 13, PHP 8.5, Composer 2.11.1, Inertia, React con
  JavaScript/JSX, Vite, npm, CSS puro y convenciones de módulos/global.
- Confirmación del desarrollador durante esta tarea: React 19 + Inertia 3 +
  Vite 8, aplicación en la raíz y sin base de datos todavía, con sesiones y
  caché locales en archivos (DT-005).
- Continuar trabajando en `main` hasta completar la instalación del stack.

## Decisiones pendientes

- Ninguna para este alcance. El desarrollador aprobó expresamente esta
  especificación con las dependencias complementarias y colas síncronas el
  2026-11-08, tras revisión `PASS`. La aprobación permite preparar el plan.
- La selección de persistencia, autenticación, producto y diseño queda fuera
  de este cambio y no bloquea su definición técnica local sin datos reales.

## Fuentes revisadas — 2026-11-08

- [Instalación de Laravel 13](https://laravel.com/framework/docs/13.x/installation).
- [Manifiesto PHP del esqueleto Laravel 13](https://github.com/laravel/laravel/blob/13.x/composer.json).
- [Manifiesto JavaScript del esqueleto Laravel 13](https://github.com/laravel/laravel/blob/13.x/package.json).
- [Configuración local del esqueleto](https://github.com/laravel/laravel/blob/13.x/.env.example).
- [Inertia: instalación del servidor](https://inertiajs.com/docs/v3/installation/server-side-setup).
- [Inertia: instalación del cliente](https://inertiajs.com/docs/v3/installation/client-side-setup).
- [Adaptador Laravel de Inertia 3](https://github.com/inertiajs/inertia-laravel/blob/3.x/composer.json).
- [Vite: JSX y CSS Modules](https://vite.dev/guide/features).
- Metadatos oficiales de npm de `react`, `react-dom`, `@inertiajs/react`,
  `@inertiajs/vite`, `vite`, `laravel-vite-plugin` y `@vitejs/plugin-react`.
  Se comprobaron requisitos y peers; esta revisión no instala paquetes ni
  garantiza todavía la resolución completa, que debe verificarse en el plan.
