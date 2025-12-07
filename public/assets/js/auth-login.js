/**
 * Patrones para verificar
 * @type {Object}
 * @property {String} pass Patron para minimo 8chars mayusculas, minusculas, caracteres y numeros
 * @property {String} email Patron para correo electronico
 */
const RegExr = {
  pass: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/,
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

const isValidPassword = (pass) => {
  return RegExr.pass.test(String(pass));
};

//
// funciones de validacion
//
const formulario = document.querySelector("#form");

const emailTag = document.querySelector("#correo");
const passwordTag = document.querySelector("#contrasenna");

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

function contrasenna() {
  const passwordValue = passwordTag.value.trim();

  if (passwordValue === "") {
    setError(passwordTag, "Password is required");
  } else if (!isValidPassword(passwordValue)) {
    setError(passwordTag, "+7 characters. Uppercase/lowercase letters, numbers and symbols");
  } else {
    setSuccess(passwordTag);
    return true;
  }
  return false;
}

//
// carga del script/app
/**
 * Objeto con listado de metodos de validacion
 * @type {Object}
 * @property {Method} correo Metodo para validar correo electronico
 * @property {Method} contrasenna Metodo para validar contraseña segun critrdio
 */
const formularioInput = {
  correo: correo,
  contrasenna: contrasenna,
};

// estados input en carga
document.addEventListener("DOMContentLoaded", () => {
  Object.values(formularioInput).forEach((fn) => {
    if (typeof fn === "function") fn();
  });
});

// estado input para validar cambios
formulario.addEventListener("change", (e) => {
  let input = e.target.id;
  formularioInput[input]?.();
});

// submit del formulario
formulario.addEventListener("submit", (e) => {
  const isCorreoValid = correo();
  const isPassValid = contrasenna();

  const formularioValido = isCorreoValid && isPassValid;

  if (!formularioValido) {
    e.preventDefault();

    const primerError = document.querySelector(".error input, .error textarea");
    if (primerError) {
      primerError.focus();
    }
  }
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
