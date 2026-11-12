// Formulario de acceso y registro, incluida la visibilidad de contraseña.

import { useState } from "react";
import { useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";
import styles from "./AccessForm.module.css";

export default function AccessForm({ registering, onModeChange }) {
  const [showPassword, setShowPassword] = useState(false);
  const login = useForm({ email: "", password: "" });
  const registration = useForm({
    name: "",
    username: "",
    email: "",
    password: "",
  });
  const form = registering ? registration : login;

  function chooseMode(register) {
    setShowPassword(false);
    onModeChange(register);
  }

  function field(name, props) {
    return (
      <Field
        {...props}
        required
        value={form.data[name]}
        onChange={(event) => form.setData(name, event.target.value)}
        error={form.errors[name]}
      />
    );
  }

  return (
    <section className={styles.linen}>
      <div
        className={styles.options}
        role="group"
        aria-label="Opciones de acceso"
      >
        <button
          type="button"
          aria-pressed={!registering}
          onClick={() => chooseMode(false)}
        >
          Iniciar sesión
        </button>
        <button
          type="button"
          aria-pressed={registering}
          onClick={() => chooseMode(true)}
        >
          Crear cuenta
        </button>
      </div>
      <h1>{registering ? "Crea tu cuenta" : "Qué bien que vuelvas"}</h1>
      {registering && (
        <p>Crea un espacio propio o únete a otro por invitación.</p>
      )}
      <form
        key={registering ? "registration" : "login"}
        className={styles.form}
        onSubmit={(event) => {
          event.preventDefault();
          form.post(registering ? "/registro" : "/acceso", {
            onFinish: () => {
              form.reset("password");
              setShowPassword(false);
            },
          });
        }}
      >
        {registering &&
          field("name", {
            label: "Nombre",
            autoComplete: "name",
            maxLength: 255,
          })}
        {registering && (
          <div>
            {field("username", {
              label: "Nombre de usuario",
              autoComplete: "username",
              maxLength: 32,
            })}
            <small>
              De 3 a 32 caracteres. Usa letras de la A a la Z, números o guion
              bajo.
            </small>
          </div>
        )}
        {field("email", {
          label: "Correo",
          type: "email",
          autoComplete: registering ? "email" : "username",
          maxLength: 255,
        })}
        <div>
          {field("password", {
            label: "Contraseña",
            name: "password",
            type: showPassword ? "text" : "password",
            autoComplete: registering ? "new-password" : "current-password",
            action: (
              <button
                type="button"
                aria-label={
                  showPassword ? "Ocultar contraseña" : "Mostrar contraseña"
                }
                onClick={() => setShowPassword(!showPassword)}
              >
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="1.7"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  aria-hidden="true"
                >
                  <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z" />
                  <circle cx="12" cy="12" r="2.8" />
                  {!showPassword && <path d="m4 4 16 16" />}
                </svg>
              </button>
            ),
          })}
          {registering && <small>Usa al menos 12 caracteres.</small>}
        </div>
        <button type="submit" disabled={form.processing}>
          {registering ? "Crear mi cuenta" : "Entrar"}
        </button>
      </form>
    </section>
  );
}
