export function positionsFor(space, drawers) {
  return Object.fromEntries(
    drawers.map(({ position }) => [position, space[position] ?? null]),
  );
}

export function slotAt(rectangles, x, y) {
  return (
    rectangles.find(
      ({ left, right, top, bottom }) =>
        x >= left && x <= right && y >= top && y <= bottom,
    )?.slot ?? null
  );
}

export function nextSlot(rectangles, current, key) {
  const origin = rectangles.find(({ slot }) => slot === current);
  if (!origin) return current;
  const cx = (origin.left + origin.right) / 2;
  const cy = (origin.top + origin.bottom) / 2;
  const horizontal = key === "ArrowLeft" || key === "ArrowRight";
  const sign = key === "ArrowLeft" || key === "ArrowUp" ? -1 : 1;
  const size = horizontal
    ? origin.bottom - origin.top
    : origin.right - origin.left;
  const candidates = rectangles
    .map((rect) => {
      const dx = (rect.left + rect.right) / 2 - cx;
      const dy = (rect.top + rect.bottom) / 2 - cy;
      return {
        slot: rect.slot,
        forward: (horizontal ? dx : dy) * sign,
        cross: Math.abs(horizontal ? dy : dx),
      };
    })
    .filter(({ forward, cross }) => forward > 1 && cross < size / 2);
  candidates.sort((a, b) => a.forward - b.forward || a.cross - b.cross);
  return candidates[0]?.slot ?? current;
}

export function releaseAction(mode, origin, target) {
  if (mode === "picked") return "wait";
  return target !== null && target !== origin ? "save" : "cancel";
}

export function gestureDistance(start, point) {
  return Math.hypot(point.x - start.x, point.y - start.y);
}

export function createShelfGesture(path, origin, start, id, input) {
  return { path, origin, start: { ...start }, id, input };
}
