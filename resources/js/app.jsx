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
  "Places/Places": () => import("./pages/Places/PlacesView/PlacesView"),
  "Readings/Readings": () =>
    import("./pages/Readings/ReadingsView/ReadingsView"),
  "Readings/Create": () =>
    import("./pages/Readings/ReadingCreate/ReadingCreate"),
  "Readings/Detail": () =>
    import("./pages/Readings/ReadingDetail/ReadingDetail"),
  "Wishes/Wishes": () => import("./pages/Wishes/WishesView/WishesView"),
};

router.on("navigate", ({ detail: { page, type } }) => {
  if (type !== "history") return;

  if (page.component === "Movies/Movies") {
    router.reload({ only: ["movies", "people", "pagination"] });
  }
  if (page.component === "Readings/Readings") {
    router.reload({ only: ["readings", "people"] });
  }
});

createInertiaApp({
  resolve: (name) => resolvePageComponent(name, pages),
});
