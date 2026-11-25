import assert from "node:assert/strict";
import { test } from "node:test";
import {
  nextSlot,
  positionsFor,
  releaseAction,
  slotAt,
} from "../../resources/js/utils/shelfMovement.js";

const grid = (columns) =>
  Array.from({ length: 8 }, (_, i) => ({
    slot: i + 1,
    left: (i % columns) * 110,
    right: (i % columns) * 110 + 100,
    top: Math.floor(i / columns) * 90,
    bottom: Math.floor(i / columns) * 90 + 80,
  }));

test("el destino usa límites reales y no convierte el espacio entre huecos en un destino", () => {
  assert.equal(slotAt(grid(4), 50, 40), 1);
  assert.equal(slotAt(grid(4), 105, 40), null);
  assert.equal(slotAt(grid(4), -1, 40), null);
});
test("flechas siguen la geometría horizontal o vertical sin saltar de fila en un borde", () => {
  assert.equal(nextSlot(grid(4), 4, "ArrowRight"), 4);
  assert.equal(nextSlot(grid(4), 2, "ArrowDown"), 6);
  assert.equal(nextSlot(grid(2), 2, "ArrowDown"), 4);
  assert.equal(nextSlot(grid(2), 4, "ArrowLeft"), 3);
  assert.equal(nextSlot(grid(2), 1, "ArrowUp"), 1);
});
test("recoger sin arrastre espera otro toque, arrastrar fuera cancela y el snapshot no cambia", () => {
  assert.equal(releaseAction("picked", 1, 1), "wait");
  assert.equal(releaseAction("dragging", 1, null), "cancel");
  assert.equal(releaseAction("dragging", 1, 1), "cancel");
  assert.equal(releaseAction("dragging", 1, 2), "save");
  const space = { movie_slot: 2, wish_slot: null };
  const snapshot = positionsFor(space, [
    { position: "movie_slot" },
    { position: "wish_slot" },
  ]);
  space.movie_slot = 4;
  assert.deepEqual(snapshot, { movie_slot: 2, wish_slot: null });
});
