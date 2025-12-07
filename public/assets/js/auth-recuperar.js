/**
 * Patrones para verificar
 * @type {Object}
 * @property {String} nombre Patron de cadena de hasta maximo 100char
 * @property {String} pass Patron para minimo 8chars mayusculas, minusculas, caracteres y numeros
 * @property {String} email Patron para correo electronico
 */
const RegExr = {
  pass: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/,
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
const passwordCheckTag = document.querySelector("#contrasennaVerificar");


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

function contrasennaVerificar() {
  const passwordValue = passwordTag.value.trim();
  const passwordCheckValue = passwordCheckTag.value.trim();

  if (passwordCheckValue === "") {
    setError(passwordCheckTag, "Password is required");
  } else if (passwordCheckValue !== passwordValue) {
    setError(passwordCheckTag, "Paswword doesn't match");
  } else {
    setSuccess(passwordCheckTag);
    return true;
  }
  return false;
}

//
// carga del script/app
/**
 * Objeto con listado de metodos de validacion
 * @type {Object}
 * @property {Method} contrasenna Metodo para validar contraseña segun critrdio
 * @property {Method} contrasennaVerificar Metodo para verificar escritura correcta de contraseña
 */
const formularioInput = {
  contrasenna: contrasenna,
  contrasennaVerificar: contrasennaVerificar,
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
  const isPassValid = contrasenna();
  const isPassCheckValid = contrasennaVerificar();

  const formularioValido = isPassValid && isPassCheckValid;

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
