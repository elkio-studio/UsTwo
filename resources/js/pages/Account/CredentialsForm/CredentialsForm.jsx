// Edición de usuario y contraseña de la sesión.

import { useForm, usePage } from "@inertiajs/react";
import Field from "../../../components/Field/Field";

export default function CredentialsForm({ className }) {
  const { sessionUsername } = usePage().props;
  const form = useForm({
    username: sessionUsername,
    current_password: "",
    password: "",
    password_confirmation: "",
  });

  return (
    <section className={className}>
      <h2>Editar cuenta</h2>
      <form
        onSubmit={(event) => {
          event.preventDefault();
          form.patch("/mi/cuenta", {
            preserveScroll: true,
            onSuccess: () =>
              form.reset(
                "current_password",
                "password",
                "password_confirmation",
              ),
          });
        }}
      >
        <Field
          label="Nombre de usuario"
          required
          maxLength={32}
          autoComplete="username"
          value={form.data.username}
          onChange={(event) => form.setData("username", event.target.value)}
          error={form.errors.username || form.errors.username_key}
        />
        <Field
          label="Contraseña actual"
          type="password"
          required
          autoComplete="current-password"
          value={form.data.current_password}
          onChange={(event) =>
            form.setData("current_password", event.target.value)
          }
          error={form.errors.current_password}
        />
        <Field
          label="Nueva contraseña (opcional, mínimo 12 caracteres)"
          type="password"
          autoComplete="new-password"
          value={form.data.password}
          onChange={(event) => form.setData("password", event.target.value)}
          error={form.errors.password}
        />
        {form.data.password && (
          <Field
            label="Repite la nueva contraseña"
            type="password"
            required
            autoComplete="new-password"
            value={form.data.password_confirmation}
            onChange={(event) =>
              form.setData("password_confirmation", event.target.value)
            }
          />
        )}
        <button disabled={form.processing}>Guardar cambios</button>
        {form.recentlySuccessful && <p role="status">Cambios guardados.</p>}
      </form>
    </section>
  );
}
