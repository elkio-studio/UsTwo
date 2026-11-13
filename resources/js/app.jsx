// Punto de entrada que monta React y carga las páginas de Inertia.

import { createInertiaApp, router } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

const pages = {
  "Account/Account": () =>
    import("./pages/Account/AccountSettings/AccountSettings"),
  "Credits/Credits": () => import("./pages/Credits/Credits"),
  "Dashboard/Dashboard": () =>
    import("./pages/Dashboard/ShelfDashboard/ShelfDashboard"),
  "Login/Login": () => import("./pages/Login/LoginAccessForm/LoginAccessForm"),
  "MovieCreate/MovieCreate": () =>
    import("./pages/MovieCreate/AddCollectionEntry/AddCollectionEntry"),
  "MovieDetail/MovieDetail": () =>
    import("./pages/MovieDetail/CollectionEntryDetail/CollectionEntryDetail"),
  "Movies/Movies": () =>
    import("./pages/Movies/WatchedCollection/WatchedCollection"),
  "Places/Places": () => import("./pages/Places/Places"),
};

router.on("navigate", ({ detail: { page, type } }) => {
  if (type === "history" && page.component === "Movies/Movies") {
    router.reload({ only: ["movies", "people", "pagination"] });
  }
});

createInertiaApp({
  resolve: (name) => resolvePageComponent(name, pages),
});
