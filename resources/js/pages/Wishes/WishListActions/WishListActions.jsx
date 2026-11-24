// Gestión compartida de listas: renombrado modal y borrado confirmado.

import { useCallback, useEffect, useId, useRef, useState } from "react";
import { router, useForm } from "@inertiajs/react";
import Field from "../../../components/Field/Field";
import WishMenu from "../WishMenu/WishMenu";
import styles from "./WishListActions.module.css";

export default function WishListActions({
  list,
  spaceId,
  selectedListId,
  onDeleted,
}) {
  const [open, setOpen] = useState(false);
  const [renaming, setRenaming] = useState(false);
  const [step, setStep] = useState(0);
  const [count, setCount] = useState(0);
  const trigger = useRef(null);
  const dialog = useRef(null);
  const titleId = useId();
  const rename = useForm({ name: list.name, selected_list_id: selectedListId });
  const deletion = useForm({
    confirmed: true,
    confirmed_count: 0,
    selected_list_id: selectedListId,
  });
  const url = `/espacios/${spaceId}/deseos/listas/${list.id}`;

  const close = useCallback(() => {
    if (deletion.processing) return;
    setOpen(false);
    setStep(0);
    deletion.clearErrors();
    trigger.current?.focus();
  }, [deletion.processing, deletion.clearErrors]);

  useEffect(() => {
    if (!renaming) return;
    const popup = dialog.current;
    popup.showModal();
    return () => popup.close();
  }, [renaming]);

  function cancelRename() {
    if (rename.processing) return;
    dialog.current?.close();
    setRenaming(false);
    trigger.current?.focus();
  }

  function remove(event) {
    event.preventDefault();
    if (deletion.processing) return;
    if (step === 1 && count > 0) {
      setStep(2);
      return;
    }
    deletion.transform(() => ({
      confirmed: true,
      confirmed_count: count,
      selected_list_id: selectedListId,
    }));
    deletion.delete(url, {
      preserveScroll: true,
      onSuccess: () => {
        setOpen(false);
        setStep(0);
        onDeleted();
      },
      onError: (errors) => {
        if (errors.confirmed_count) {
          setStep(0);
          router.reload({ only: ["lists", "selectedListId"] });
        }
      },
    });
  }

  return (
    <>
      <WishMenu
        label={`Opciones de la lista ${list.name}`}
        open={open}
        onOpen={() => {
          deletion.clearErrors();
          setOpen(true);
        }}
        onClose={close}
        processing={deletion.processing || rename.processing}
        wide={step > 0 || !!deletion.errors.confirmed_count}
        view={step}
        trigger={trigger}
      >
        {step > 0 ? (
          <form onSubmit={remove}>
            <p>
              {step === 1
                ? `¿Eliminar la lista «${list.name}»?`
                : `Se eliminarán definitivamente «${list.name}» y sus ${count} deseos.`}
            </p>
            {step === 1 && count > 0 && (
              <p>
                Contiene {count} deseos. El siguiente paso confirma también su
                borrado.
              </p>
            )}
            <div className={styles.buttons}>
              <button
                data-initial-focus
                type="button"
                disabled={deletion.processing}
                onClick={close}
              >
                Cancelar
              </button>
              <button className={styles.danger} disabled={deletion.processing}>
                {deletion.processing
                  ? "Eliminando…"
                  : step === 1 && count > 0
                    ? "Continuar"
                    : "Confirmar borrado"}
              </button>
            </div>
            {(deletion.errors.delete || deletion.errors.confirmed) && (
              <p role="alert">
                {deletion.errors.delete || deletion.errors.confirmed}
              </p>
            )}
          </form>
        ) : (
          <div className={styles.buttons}>
            {deletion.errors.confirmed_count && (
              <p role="alert">{deletion.errors.confirmed_count}</p>
            )}
            <button
              className={styles.edit}
              type="button"
              onClick={() => {
                rename.setData({
                  name: list.name,
                  selected_list_id: selectedListId,
                });
                rename.clearErrors();
                setOpen(false);
                setRenaming(true);
              }}
            >
              Editar
            </button>
            <button
              className={styles.danger}
              type="button"
              onClick={() => {
                deletion.clearErrors();
                setCount(list.wishes.length);
                setStep(1);
              }}
            >
              Eliminar
            </button>
          </div>
        )}
      </WishMenu>
      {renaming && (
        <dialog
          ref={dialog}
          className={styles.dialog}
          aria-labelledby={titleId}
          onCancel={(event) => {
            event.preventDefault();
            cancelRename();
          }}
        >
          <form
            className={styles.form}
            aria-busy={rename.processing}
            onSubmit={(event) => {
              event.preventDefault();
              if (rename.processing) return;
              rename.patch(url, {
                preserveScroll: true,
                onSuccess: () => {
                  dialog.current?.close();
                  setRenaming(false);
                  trigger.current?.focus();
                },
              });
            }}
          >
            <h2 id={titleId}>Editar lista</h2>
            <Field
              label="Nombre de la lista"
              autoFocus
              required
              maxLength={255}
              value={rename.data.name}
              onChange={(event) => rename.setData("name", event.target.value)}
              error={rename.errors.name}
            />
            {rename.errors.save && <p role="alert">{rename.errors.save}</p>}
            <div className={styles.buttons}>
              <button disabled={rename.processing}>
                {rename.processing ? "Guardando…" : "Guardar cambios"}
              </button>
              <button
                type="button"
                disabled={rename.processing}
                onClick={cancelRename}
              >
                Cancelar
              </button>
            </div>
          </form>
        </dialog>
      )}
    </>
  );
}
