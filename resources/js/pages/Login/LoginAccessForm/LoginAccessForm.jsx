// Coordinación del modo de acceso y registro de UsTwo.

import { useState } from "react";
import AccessLayout from "../../../layouts/AccessLayout/AccessLayout";
import AccessForm from "../AccessForm/AccessForm";
import "../../../../css/global.css";

export default function LoginAccessForm() {
  const [registering, setRegistering] = useState(false);

  return (
    <AccessLayout registering={registering}>
      <AccessForm registering={registering} onModeChange={setRegistering} />
    </AccessLayout>
  );
}
