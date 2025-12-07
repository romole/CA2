/**
 * Patrones para verificar
 * @type {Object}
 * @property {String} email Patron para correo electronico
 */
const RegExr = {
  email:
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
};

//
// funciones comunes para validacion y estados
//
const setError = (elemento, mensaje) => {
  const inputControl = elemento.parentElement;
  const errorDisplay = inputControl.querySelector(".error");

  errorDisplay.innerText = mensaje;
  inputControl.classList.add("error");
  inputControl.classList.remove("succes");
};

const setSuccess = (elemento) => {
  const inputControl = elemento.parentElement;
  const errorDisplay = inputControl.querySelector(".error");

  errorDisplay.innerText = "";
  inputControl.classList.add("succes");
  inputControl.classList.remove("error");
};

const isValidEmail = (email) => {
  return RegExr.email.test(String(email).toLowerCase());
};

//
// carga del script/app
document.addEventListener("DOMContentLoaded", () => {
  const formulario = document.querySelector("#form");
  const emailTag = document.querySelector("#correo");

  // funciones de validacion
  function correo() {
    const emailValue = emailTag.value.trim();

    if (emailValue === "") {
      setError(emailTag, "Email is required");
    } else if (!isValidEmail(emailValue)) {
      setError(emailTag, "Provide a valid email address");
    } else {
      setSuccess(emailTag);
      return true;
    }
    return false;
  }

  correo();

  // estado input para validar cambios
  emailTag.addEventListener("blur", correo);

  // submit del formulario
  formulario.addEventListener("submit", (e) => {
    const isCorreoValid = correo();

    if (!isCorreoValid) {
      e.preventDefault();

      const primerError = document.querySelector(".error input, .error textarea");
      if (primerError) {
        primerError.focus();
      }
    }
  });
});

//
//
//
function debugear(valor) {
  console.log(valor);
  console.log(typeof valor);
}

function debugearAppend(valor) {
  console.log([...valor]);
}
