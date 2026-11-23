// Peticiones JSON del alta de Deseos con la protección CSRF de Laravel.

export default async function wishRequest(url, data, signal) {
  const token = document.cookie
    .split("; ")
    .find((cookie) => cookie.startsWith("XSRF-TOKEN="));
  const response = await fetch(url, {
    method: "POST",
    credentials: "same-origin",
    signal,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      "X-XSRF-TOKEN": token ? decodeURIComponent(token.slice(11)) : "",
    },
    body: JSON.stringify(data),
  }).catch((failure) => {
    if (failure.name === "AbortError") throw failure;
    throw new Error(
      "No se pudo conectar. Conservamos los datos para que puedas reintentarlo.",
    );
  });
  const result = await response.json().catch(() => ({}));
  if (!response.ok) {
    const error = new Error(
      response.status === 429
        ? "Has hecho demasiadas peticiones. Inténtalo de nuevo en un momento."
        : result.message ||
            "No se pudo completar la operación. Conservamos los datos.",
    );
    error.fields = result.errors || {};
    throw error;
  }
  return result;
}
