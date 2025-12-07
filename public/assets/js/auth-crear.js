/**
 * Patrones para verificar
 * @type {Object}
 * @property {String} nombre Patron de cadena de hasta maximo 100char
 * @property {String} pass Patron para minimo 8chars mayusculas, minusculas, caracteres y numeros
 * @property {String} email Patron para correo electronico
 */
const RegExr = {
  nombre: /^[a-zA-ZÄ-ÿ\s]{1,100}$/,
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

const nombreTag = document.querySelector("#nombre");
const emailTag = document.querySelector("#correo");
const passwordTag = document.querySelector("#contrasenna");
const passwordCheckTag = document.querySelector("#contrasennaVerificar");
const ckeckBox1Tag = document.querySelector("#ckeckBox1");
const ckeckBox2Tag = document.querySelector("#ckeckBox2");

const nombre = () => {
  let nombreValue = nombreTag.value.trim();

  if (!RegExr.nombre.test(nombreValue) || nombreValue === "" || nombreValue == null) {
    setError(nombreTag, "Username is required");
    return false;
  } else {
    setSuccess(nombreTag);
    return true;
  }
};

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

/**
 * Funcion para validar aceptacion de condiciones en formulario
 * @returns {Boolean}
 */
function condiciones() {
  const checkBox1Value = ckeckBox1Tag.checked;
  const checkBox2Value = ckeckBox2Tag.checked;

  if (checkBox1Value && checkBox2Value) {
    return true;
  }
  return false;
}

//
// carga del script/app
/**
 * Objeto con listado de metodos de validacion
 * @type {Object}
 * @property {Method} nombre Metodo para verificar existencia de una cadena
 * @property {Method} correo Metodo para validar correo electronico
 * @property {Method} contrasenna Metodo para validar contraseña segun critrdio
 * @property {Method} contrasennaVerificar Metodo para verificar escritura correcta de contraseña
 */
const formularioInput = {
  nombre: nombre,
  correo: correo,
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
  const isNombreValid = nombre();
  const isCorreoValid = correo();
  const isPassValid = contrasenna();
  const isPassCheckValid = contrasennaVerificar();
  const isCondicionesValid = condiciones();

  const formularioValido = isNombreValid && isCorreoValid && isPassValid && isPassCheckValid && isCondicionesValid;

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
