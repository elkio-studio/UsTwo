---
id: "001"
title: "Definición de la base tecnológica de UsTwo"
status: approved
---

# 001 — Definición de la base tecnológica de UsTwo

## Problema y objetivo

El repositorio contiene contexto y proceso, pero todavía no existe aplicación.
Consolidar la selección tecnológica expresada por el desarrollador y delimitar
las decisiones necesarias antes de preparar la instalación de la base técnica.
Esta especificación define el stack; no solicita construir la aplicación.

## Alcance incluido

- Laravel como backend y responsable de las rutas de las páginas Inertia.
- Inertia como conexión entre Laravel y las páginas React.
- React con JavaScript y JSX para los componentes de interfaz.
- CSS puro con CSS Modules para estilos de componentes.
- Convención confirmada: cada componente `NombreComponente.jsx` tiene su
  `NombreComponente.module.css` para los estilos propios.
- `global.css` contiene los estilos compartidos y se importa únicamente desde
  las páginas principales; no desde componentes reutilizables, layouts ni
  el punto de entrada de la aplicación.
- Sincronizar el contexto existente con la decisión DT-001.

## Alcance excluido

- Instalar dependencias, generar el proyecto o implementar pantallas.
- Elegir versiones, compilador, gestores de paquetes o starter kit.
- Definir base de datos, modelos, autenticación, autorización o despliegue.
- Incorporar TypeScript, frameworks CSS o bibliotecas de componentes.
- Diseñar funcionalidades de películas u otras listas.

## Actores

- Desarrollador: aprueba las decisiones y las fases del proceso.
- Agente principal: documenta y propone; no completa decisiones pendientes.

## Flujo principal

1. Registrar en DT-001 las tecnologías confirmadas por el desarrollador.
2. Revisar esta especificación y su coherencia con el contexto del proyecto.
3. El desarrollador aprueba o ajusta esta especificación completa.
4. Antes de planificar una instalación, resolver las decisiones técnicas que
   esa instalación necesite. Cada fase conserva sus aprobaciones independientes.

## Flujos alternativos y errores

- Si una herramienta inicial introduce TypeScript o un framework CSS, no
  adoptarla automáticamente: proponer una configuración que respete este stack.
- Si falta una versión, dependencia o decisión necesaria, presentarla antes de
  preparar un plan que dependa de ella; no instalar para resolverla por defecto.
- Si cambia una elección aprobada, actualizar la decisión y los artefactos
  afectados con aprobación expresa.

## Reglas de negocio y casos límite

- Esta definición técnica no modifica reglas de negocio ni privacidad.
- Inertia conecta las páginas de la aplicación; no aprueba ni prohíbe por sí
  solo futuras APIs o integraciones externas.
- Las recomendaciones de las skills de diseño respetarán el stack aprobado.

## Criterios de aceptación

- **CA-01:** DT-001 registra Laravel, Inertia, React con JavaScript/JSX y CSS
  puro con CSS Modules como decisiones confirmadas, con sus límites.
- **CA-02:** README y MEMORY reflejan esa base sin afirmar que ya está instalada
  ni que están aprobadas las demás decisiones técnicas.
- **CA-03:** La especificación y DT-001 recogen un `.module.css` por componente
  `.jsx` y la importación de `global.css` únicamente desde páginas principales,
  diferenciando estas decisiones confirmadas de las pendientes para instalar.
- **CA-04:** Este cambio contiene únicamente documentación; no añade
  manifiestos de aplicación, dependencias, código de producto ni configuración
  de infraestructura.

## Decisiones aprobadas

- Las recogidas en DT-001, confirmadas por el desarrollador en la conversación.
- Un archivo `.module.css` por componente `.jsx`; `global.css` se importa
  únicamente desde las páginas principales.
- Especificación completa aprobada expresamente por el desarrollador el
  2026-11-08, tras la revisión `PASS`.

## Decisiones pendientes

- Ninguna pendiente para esta definición documental del stack.
- Antes de planificar la instalación: versiones compatibles de Laravel, PHP,
  React e Inertia; compilación de JSX/CSS Modules y gestores de dependencias;
  instalación mínima o starter kit y sus dependencias.
- Antes de tareas que lo necesiten: persistencia, autenticación, autorización,
  pruebas e infraestructura. No se eligen como efecto secundario del stack.
