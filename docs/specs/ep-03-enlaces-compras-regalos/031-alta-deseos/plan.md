---
spec: "031"
status: approved
---

# Plan técnico — 031

## Alcance aprobado y revisión

Ángel aprobó spec/plan y ejecución. Durante la validación, el 23/11/2026,
solicitó **solo euros** y confirmó **solo alta manual**, retirando extracción.
Este plan refleja esa decisión vigente. No hay APIs, nuevos paquetes,
transporte externo, parser ni hook de autocompletado que implementar o mantener.

## Datos y componentes

- Migración 013: wish_lists (space_id, name, timestamps), wishes (wish_list_id,
  author_id, url, title, description nullable, price_minor/currency nullable,
  status pending, timestamps). FK en cascada al eliminar estantería/lista
  según patrón actual; FK restrictiva de autor. Sin listas por defecto.
- Conservar 013 ya aplicada. Migración 014 añade restricción EUR en SQLite
  mediante triggers y PostgreSQL mediante CHECK. Si hay otra moneda, detener
  sin tocar valores. No convertir ni borrar datos ni reetiquetar USD como EUR.
- La pertenencia a estantería se hereda de la lista, sin space_id duplicado.
  No cambia wish_slot ni posiciones de otros cajones.
- WishList/Wish con relaciones y Space::wishLists. Autor/pending asignados por
  servidor; no mass assignment libre de identidad, estado o pertenencia.
- WishController: index, create y store. WishListController: solo store.
- WishUrl valida URL sintácticamente sin resolver o consultar; WishPrice
  convierte cadenas a céntimos exactos y formatea la salida.

## Contratos y validación

Rutas bajo auth y RequireSpaceMembership:

| Ruta | Resultado |
| --- | --- |
| GET /espacios/{space}/deseos | Wishes/Wishes con listas propias, deseos y lista elegida por ?list=ID validado. |
| GET /espacios/{space}/deseos/nuevo | Wishes/Create con listas propias. |
| POST /espacios/{space}/deseos/listas | JSON 201 id/name; CSRF y errores 422, sin navegar. |
| POST /espacios/{space}/deseos | Validar, revalidar membresía/lista en transacción, crear con autor de sesión y redirect a ?list=ID. |

No existe POST /deseos/metadatos. No se consulta red al guardar/listar.
URL HTTP(S) absoluta y sin credenciales/controles/barras inversas/host ambiguo,
max 2048. Lista y título 1–255 tras trim. Descripción opcional hasta 5000.
Precio opcional 0–99999999.99, coma/punto decimal, hasta dos cifras, sin
miles/exponentes ni float. Servidor asigna EUR con precio y null sin él.
Moneda distinta enviada por cliente se rechaza; no se ofrece selector.
Mantener CHECK de pareja/precio y añadir EUR-only, sin duplicación destructiva.

El espacio se bloquea siguiendo PlaceController, se revalida membresía y la
lista propia, y se escribe el deseo completo. Error SQL -> save/input retenido.
Crear lista es independiente y puede dejarla vacía. No bloquear URLs duplicadas.

## Frontend

- WishesView: acceso de alta, navegación sencilla por listas y estado vacío.
- WishCreate: JournalLayout y composición del alta, mapping en app.jsx y
  tests/TestCase.php conservando verificación de archivo Inertia real.
- WishForm: formulario manual, lista obligatoria y precio en euros opcional;
  estado local conservado al crear lista o fallar el guardado.
- WishListForm: creación explícita con JSON/CSRF, sin formularios anidados,
  foco al nombre al abrir y regreso a control de lista al terminar.
- WishList: título/enlace, texto escapado, precio EUR o ausencia y autor;
  noopener/noreferrer en enlaces a nueva pestaña, URLs sin desbordar.
- wishRequest: utilidad usada para listas, errores de red comprensibles,
  errores por campo y CSRF. Test con node:test instalado, sin nuevo runner.
- Sin selector de moneda, botón de extracción, mensajes de obtención de datos,
  servicio/parser/controlador de extracción ni hook huérfano.
- Respetar funciones default nombradas, carpetas por responsabilidad, CSS puro
  con variables globales, ningún CSS vacío y formato de Prettier local.

## Fases

1. Confirmar revisión aprobada y revisar consumidores antes de retirar extracción.
2. Modelos, rutas, validación manual, transacciones y autoría/lista protegidas.
3. Precio EUR y migración aditiva de restricción, sin alterar la ya aplicada.
4. Simplificar formulario a manual y actualizar listas/listado. Eliminar código
   y pruebas exclusivos de extracción; mantener utilidad JSON usada por listas.
5. Copia consistente de SQLite local, migrate y migrate:status, comparación
   de todos los valores anteriores. Nunca fresh/reset ni recrear la base.
6. Suite, Node, build, formato y revisión de solo lectura. Validación manual
   posterior antes de cierre; no publicar automáticamente.

## Pruebas y criterios

| Criterio | Evidencia prevista |
| --- | --- |
| CA-01 | Crear lista vacía explícita, guardar con/sin opcionales; sin defaults. |
| CA-02 | Autor/pending reales, payload y recarga, redirect lista elegida. |
| CA-03 | Campos inválidos, importe/rango y moneda manipulada, fallo SQL/input intacto. |
| CA-04 | Alta con red deshabilitada, sin DNS/transporte de URLs en el código. |
| CA-05 | Ruta metadatos ausente y búsqueda de referencias de extracción sin restos. |
| CA-06 | Invitado/ajeno, lista ajena, autor/space/status manipulados, membresía retirada. |
| CA-07 | URLs de esquemas maliciosos y texto como texto, enlaces seguros sin incrustación. |
| CA-08 | Coma/punto, límite y cero/null; USD/GBP rechazados por servidor/base; migración se detiene sin convertir registros existentes de otra moneda. |
| CA-09 | Formulario/listas/errores/foco/navegación en escritorio/móvil/teclado. |
| CA-10 | Migraciones aisladas y comparación de SQLite local, suite de los demás cajones. |

Comandos: vendor/bin/phpunit --display-skipped; PHPUnit con phpunit.pgsql.xml
solo si PostgreSQL aislado está disponible; node --test tests/Frontend/wishRequest.test.js;
npm run build, Prettier de extensión y git diff --check.
Tests de migración solo en SQLite en memoria o ustwo_ci aislado, nunca sobre
desarrollo/producción. Build no sustituye observación real de interacciones.

## Documentación, compatibilidad y publicación

Actualizar spec/tareas y MEMORY para la revisión. El informe 029 conserva
pruebas de investigación y referencia la decisión posterior de alta manual,
sin afirmar que los bloqueos ocurren en todas las tiendas.

Conservar los datos anteriores, incluidas listas/deseos ya probados por Ángel.
No reiniciar servidores si no hace falta. La spec sigue aprobada hasta
validación manual y cierre. Commit, push, integración, PostgreSQL/CI y despliegue
requieren sus autorizaciones y comprobaciones posteriores. Tras integrar,
trasladar solo documentación autorizada a documentation actualizada.
