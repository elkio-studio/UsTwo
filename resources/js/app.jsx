// Punto de entrada que monta React y carga las páginas de Inertia.

import { createInertiaApp, router } from '@inertiajs/react';

router.on('navigate', ({ detail: { page, type } }) => {
  if (type === 'history' && page.component === 'Movies/Movies') {
    router.reload({ only: ['movies', 'people', 'pagination'] });
  }
});

createInertiaApp({
  pages: './pages',
});
