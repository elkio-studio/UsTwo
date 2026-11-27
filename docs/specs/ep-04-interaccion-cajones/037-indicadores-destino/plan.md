---
spec: "037"
status: approved
---

# Plan técnico — 037

## Resumen

Aplicar una corrección de presentación a la [spec aprobada](spec.md): el
origen conserva borde discontinuo, solo otro hueco recibe el continuo y el
ghost no duplica indicadores. Verificar legibilidad sin asumir oclusión.
Sin backend, contratos nuevos, dependencias ni migraciones. No implementar
hasta aprobar este plan y recibir solicitud explícita de ejecución.

## Componentes y responsabilidades

- `resources/js/pages/Dashboard/Shelf/Shelf.jsx`: al derivar data-target,
  exigir target coincidente con slot y distinto de origin. Conservar data-origin.
- `resources/js/hooks/useShelfMovement.js`, creación del ghost: retirar del
  clon data-origin/data-target, además de los atributos ya retirados. No
  trasladar ni eliminar esos estados del cajón real ni alterar el snapshot.
- Shelf.module.css: conservar reglas/colores/patrones actuales. No hace falta
  una regla que anule otra ni !important para resolver esta superposición.
- `tests/Frontend/shelfMovement.test.js` y utilidad existente: regresión de
  geometría, origen/fuera cancelan y destino válido guarda, sin reescribirlos
  ni extraer una función solo para probar un booleano de JSX.

No modificar ReadingSpines/WishBox/GlobeSurface, assets ni layout de estantería
para una oclusión que aún no se ha demostrado. La detección y las transiciones
del hook siguen siendo la fuente de verdad; cambia únicamente su representación.

## Contratos y datos

Se mantienen íntegros los ocho huecos, cinco tipos, origen/target internos y
semántica de cancelar. Un target interno igual al origen sigue siendo útil
para Enter/cancelación; no convertirlo a null para corregir una señal visual.
No cambian PUT mover, snapshot, bloqueo/transacción, alta, permisos o contenido.
Anuncios accesibles/error, + persistentes y ausencia de botones/textos retirados
por Ángel se conservan. La copia permanece inerte y sin nuevos controles.

## Fases de implementación

1. Revisar rama/registro, aprobaciones, diff y revisión previa de diseño.
2. Corregir derivación visual del destino y limpiar estado visual copiado.
3. Revisar casos por lectura y ejecutar controles automáticos proporcionales.
4. Ángel valida visualmente la matriz en escritorio y móvil, incluido teclado.
   El agente no abre navegador sin nueva autorización.
5. Revisión de diseño posterior y auditoría final; documentar y cerrar solo
   con evidencia de todos los criterios.

### Puerta de validación de oclusión

Comprobar ghost sobre cada tipo, borde/nombre del destino y huecos de extremos.
Si son reconocibles, registrar la evidencia sin introducir otro cambio.
Si el ghost oculta información crítica, registrar caso/tipo/entrada/viewport y
presentar la corrección visual mínima antes de modificar tamaño, posición,
opacidad o capas. Una decisión significativa exige aprobación; no inventar una
solución preventiva durante esta implementación. Hasta resolverlo, CA-04 y
el cierre permanecen pendientes, aunque el resto de la corrección esté terminado.

## Estrategia de pruebas y trazabilidad

| Criterio | Verificación |
| --- | --- |
| CA-01 | Recoger y volver al origen: solo borde discontinuo, con ratón, toque y teclado. Revisión de derivación JSX y validación humana. |
| CA-02 | Elegir cada tipo ocupado y un vacío: borde continuo único en destino distinto, antes de confirmar. |
| CA-03 | Separaciones/fuera, Escape, origen, cambio de estantería y final: ningún destino sólido obsoleto. Regresión Node y validación humana. |
| CA-04 | Ghost sin atributos de origen/destino; validación visual de borde y nombre de los cinco tipos, especialmente globo/libros/caja. No atribuir prueba visual a pointer-events o build. |
| CA-05 | Ratón/táctil/toque/teclado, foco, anuncios y reduced motion: comparación con 036 y validación humana. |
| CA-06 | Node existente de límites/teclado/cancelación, diff sin cambios de backend/contratos y confirmación de mover/intercambiar/cancelar sin perder contenido. |

Controles: `node --test tests/Frontend/shelfMovement.test.js`, `npm.cmd run build`,
Prettier de archivos modificados y `git diff --check`. Comprobar ausencia de
!important, archivos generados o cambios ajenos. Sin nuevo runner ni tests de
selectores CSS que reproduzcan la implementación. Si aparecen cambios de reglas
reales, revisar alcance antes de tocar backend. No repetir toda la suite PHP
para cambios exclusivamente visuales; sus resultados de 036 no se presentan
como una nueva ejecución. Cualquier ejecución necesaria de PHP exige guardia
previa de SQLite :memory:/testing/URL vacía/sin caché, sin base local/Supabase.

## Documentación y compatibilidad

Actualizar spec/tasks/MEMORY con evidencias, atribución de validación visual,
limitaciones, inicio/cierre/pausas y horas GitHub Projects bajo título. No nuevos
informes ni cambios en las reglas generales del proyecto. Conservar .env,
SQLite y servidores locales; no crear ni borrar registros para probar contornos.

No migración ni despliegue especial. Publicación frontend posterior con permiso;
commit, push, actualización/integración dev, CI y traslado a documentation son
fases posteriores independientes. No cambiar main ni borrar ramas.

## Riesgos y estado

Riesgos: cambiar target interno al corregir CSS rompería cancelación; no retirar
atributos del clon mantendría una señal duplicada; solo una lectura de código
no acredita legibilidad. Medidas: separación estado/presentación, revisión de
clon y matriz visual con puerta explícita si falla CA-04.

Plan aprobado expresamente por Ángel, con solicitud de implementación y
verificación recibida. Sin autorización para navegador, commit o publicación.
Revisión plan_reviewer: PASS, sin bloqueos; aprobación y ejecución autorizadas
posteriormente por Ángel.
Revisión previa de diseño de solo lectura: coherente, sin bloqueos técnicos;
conservar estado interno y no acreditar oclusión mediante Node/build.
