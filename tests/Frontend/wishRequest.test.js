import assert from "node:assert/strict";
import { test } from "node:test";
import wishRequest from "../../resources/js/pages/Wishes/utils/wishRequest.js";

test("las peticiones JSON mantienen CSRF y explican errores sin perder los datos del llamador", async () => {
  const originalFetch = globalThis.fetch;
  const originalDocument = globalThis.document;
  try {
    globalThis.document = { cookie: "XSRF-TOKEN=token%3D; otra=valor" };
    const data = { name: "Mi lista" };
    globalThis.fetch = async (url, options) => {
      assert.equal(url, "/listas");
      assert.equal(options.headers["X-XSRF-TOKEN"], "token=");
      assert.equal(options.credentials, "same-origin");
      assert.equal(options.method, "POST");
      assert.deepEqual(JSON.parse(options.body), data);
      return { ok: true, json: async () => ({ id: 1, name: data.name }) };
    };
    assert.equal((await wishRequest("/listas", data)).id, 1);
    globalThis.fetch = async () => {
      throw new TypeError("Failed to fetch");
    };
    await assert.rejects(
      wishRequest("/listas", data),
      /No se pudo conectar.*Conservamos/,
    );
    assert.deepEqual(data, { name: "Mi lista" });
    globalThis.fetch = async () => ({
      ok: false,
      status: 422,
      json: async () => ({
        message: "Inválido",
        errors: { name: ["Escribe un nombre"] },
      }),
    });
    await assert.rejects(
      wishRequest("/listas", data),
      (error) => error.fields.name[0] === "Escribe un nombre",
    );
    globalThis.fetch = async () => ({
      ok: false,
      status: 429,
      json: async () => ({}),
    });
    await assert.rejects(
      wishRequest("/listas", data),
      /demasiadas peticiones/,
    );
    globalThis.fetch = async () => {
      throw new DOMException("Cancelada", "AbortError");
    };
    await assert.rejects(
      wishRequest("/listas", data),
      (error) => error.name === "AbortError",
    );
  } finally {
    globalThis.fetch = originalFetch;
    if (originalDocument === undefined) delete globalThis.document;
    else globalThis.document = originalDocument;
  }
});
