let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
  id: "",
  nombre: "",
  fecha: "",
  hora: "",
  servicios: [],
};

document.addEventListener("DOMContentLoaded", () => {
  iniciarApp();
});

function iniciarApp() {
  mostrarSeccion(); // Muestra y oculta las secciones
  tabs(); // Cambia la seccion cuando se presionen los tabs
  botonesPaginador(); // Agrega o quita los botones del paginador
  paginaSiguiente();
  paginaAnterior();
  consultarAPI(); // Consulta la API en el backend de PHP
  idCliente(); // Almacena el id del cliente
  nombreCliente(); // Almacenar el nombre del usuario en el objeto cita
  seleccionarFecha(); // Almacena la fecha de la cita en el objeto cita
  seleccionarHora(); // Añade la hora de la cita en el objeto cita
  mostrarResumen(); // Muestra el resumen de la vista
}

function mostrarSeccion() {
  // Ocultar la seccion que tenga la clase de mostrar
  const seccionAnterior = document.querySelector(".mostrar");
  if (seccionAnterior) {
    seccionAnterior.classList.remove("mostrar");
  }
  // Seleccionar la seccion con el paso
  const pasoSelector = `#paso-${paso}`;
  const seccion = document.querySelector(pasoSelector);
  seccion.classList.add("mostrar");

  // Quita la clase actual al tab anterior
  const tabAnterior = document.querySelector(".actual");
  if (tabAnterior) {
    tabAnterior.classList.remove("actual");
  }

  // Resaltar el tab Actual
  const tab = document.querySelector(`button[data-paso="${paso}"]`);
  tab.classList.add("actual");
}

function tabs() {
  const botones = document.querySelectorAll(".tabs button");
  botones.forEach((boton) => {
    boton.addEventListener("click", (e) => {
      paso = parseInt(e.target.dataset.paso);
      mostrarSeccion();
      botonesPaginador();
    });
  });
}

function botonesPaginador() {
  const btnAnterior = document.querySelector("#anterior");
  const btnSiguiente = document.querySelector("#siguiente");
  if (paso === 1) {
    btnAnterior.classList.add("ocultar");
    btnSiguiente.classList.remove("ocultar");
  } else if (paso === 2) {
    btnAnterior.classList.remove("ocultar");
    btnSiguiente.classList.remove("ocultar");
  } else if (paso === 3) {
    btnAnterior.classList.remove("ocultar");
    btnSiguiente.classList.add("ocultar");
    mostrarResumen();
  }
  mostrarSeccion();
}

function paginaAnterior() {
  const btnAnterior = document.querySelector("#anterior");
  btnAnterior.addEventListener("click", () => {
    if (paso <= pasoInicial) return;
    paso--;
    botonesPaginador();
  });
}

function paginaSiguiente() {
  const btnSiguiente = document.querySelector("#siguiente");
  btnSiguiente.addEventListener("click", () => {
    if (paso >= pasoFinal) return;
    paso++;
    botonesPaginador();
  });
}

async function consultarAPI() {
  try {
    const url = "/api/servicios";
    const res = await fetch(url);
    const servicios = await res.json();
    mostrarServicios(servicios);
  } catch (error) {
    console.log(error);
  }
}

function mostrarServicios(servicios) {
  servicios.forEach((servicio) => {
    // Creamos las variables y le asignamos el valor del objeto
    const { id, nombre, precio } = servicio;
    // Creamos los elementos HTML con sus atributos y contenido
    const servicioNombre = document.createElement("P");
    servicioNombre.classList.add("nombre-servicio");
    servicioNombre.textContent = nombre;

    const precioServicio = document.createElement("P");
    precioServicio.classList.add("precio-servicio");
    precioServicio.textContent = `$${precio}`;

    const servicioDIV = document.createElement("DIV");
    servicioDIV.classList.add("servicio");

    // Creamos un atributo personalizado data-id-servicio = "id";
    servicioDIV.dataset.idServicio = id;

    // Añadir servicios al arreglo servicios del objeto cita
    servicioDIV.onclick = () => {
      seleccionarServicio(servicio);
    };

    // Agregamos como hijos los parrafos
    servicioDIV.appendChild(servicioNombre);
    servicioDIV.appendChild(precioServicio);

    // Agregamos el DIV con los parrafos en el HTML
    document.querySelector("#servicios").appendChild(servicioDIV);
  });
}

function seleccionarServicio(servicio) {
  const { id } = servicio;
  const { servicios } = cita;

  // Identificar al elemento al que se le da clic
  const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

  // Comprobar si un servicio ya fue agregado
  if (servicios.some((agregado) => agregado.id === id)) {
    // Eliminarlo
    cita.servicios = servicios.filter(
      (agregado) => agregado.id !== servicio.id
    );
    divServicio.classList.remove("seleccionado");
  } else {
    // Agregarlo
    cita.servicios = [...servicios, servicio];
    divServicio.classList.add("seleccionado");
  }
}

function idCliente() {
  cita.id = document.querySelector("#id").value;
}

function nombreCliente() {
  cita.nombre = document.querySelector("#nombre").value;
}

function seleccionarFecha() {
  const inputFecha = document.querySelector("#fecha");
  inputFecha.addEventListener("input", (e) => {
    const dia = new Date(e.target.value).getUTCDay();
    if ([6, 0].includes(dia)) {
      e.target.value = "";
      mostrarAlerta("Fines de semana no permitidos", "error", ".formulario");
    } else {
      cita.fecha = e.target.value;
    }
  });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
  // Previene que se genera mas de una alerta
  const alertaPrevia = document.querySelector(".alerta");
  if (alertaPrevia) {
    alertaPrevia.remove();
  }

  // Scripting para crear la alerta
  const alerta = document.createElement("DIV");
  alerta.textContent = mensaje;
  alerta.classList.add("alerta");
  alerta.classList.add(tipo);

  // Agreganod la alerrta al HTML
  const formulario = document.querySelector(elemento);
  formulario.appendChild(alerta);

  if (desaparece) {
    // Establecer el tiempo de aparición de la alerta (eliminar la alerta).
    setTimeout(() => {
      alerta.remove();
    }, 3000);
  }
}

function seleccionarHora() {
  const inputHora = document.querySelector("#hora");
  inputHora.addEventListener("input", (e) => {
    const horaCita = e.target.value;

    // Hora va a tener un arreglo y con el [0] vamos a seleccionar el proimer elemento
    const hora = horaCita.split(":")[0];

    // Evaluamos la hora
    if (hora < 10 || hora > 18) {
      e.target.value = "";
      mostrarAlerta("Hora no valida", "error", ".formulario");
    } else {
      cita.hora = e.target.value;
    }
  });
}

function mostrarResumen() {
  const resumen = document.querySelector(".contenido-resumen");
  // Limpiar el contenido de  Resumen
  while (resumen.firstChild) {
    resumen.removeChild(resumen.firstChild);
  }
  // Validamos si en el objeto de cita hay campos vacios
  if (Object.values(cita).includes("") || cita.servicios.length === 0) {
    mostrarAlerta(
      "Faltan datos de Servicios, Fecha u Hora",
      "error",
      ".contenido-resumen",
      false
    );
    return;
  }
  // Formatear el DIV de resumen
  const { nombre, fecha, hora, servicios } = cita;

  // Creamos los elementos HTMl
  const nombreCliente = document.createElement("P");
  nombreCliente.innerHTML = `<span>Nombre</span> ${nombre}`;

  // Formatear la fecha en español
  const fechaObj = new Date(fecha);
  const dia = fechaObj.getDate() + 2;
  const mes = fechaObj.getMonth();
  const year = fechaObj.getFullYear();
  const fechaUTC = new Date(Date.UTC(year, mes, dia));
  const opciones = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  const fechaFormateada = fechaUTC.toLocaleDateString("es-MX", opciones);

  const fechaCita = document.createElement("P");
  fechaCita.innerHTML = `<span>Fecha</span> ${fechaFormateada}`;

  const horaCita = document.createElement("P");
  horaCita.innerHTML = `<span>Hora</span> ${hora} Horas`;

  //Boton para crear una cita
  const botonReservar = document.createElement("BUTTON");
  botonReservar.classList.add("boton");
  botonReservar.textContent = "Reservar Cita";
  botonReservar.onclick = reservarCita;

  // Heading para servicios en resumen
  const HeadingServicios = document.createElement("H3");
  HeadingServicios.textContent = "Resumen de Servicios";
  resumen.appendChild(HeadingServicios);

  servicios.forEach((servicio) => {
    const { id, nombre, precio } = servicio;

    const contenedorServicio = document.createElement("DIV");
    contenedorServicio.classList.add("contenedor-servicio");

    const textoServicio = document.createElement("P");
    textoServicio.textContent = nombre;

    const precioservicio = document.createElement("P");
    precioservicio.innerHTML = `<span>Precio:</span> $${precio}`;

    contenedorServicio.appendChild(textoServicio);
    contenedorServicio.appendChild(precioservicio);

    resumen.appendChild(contenedorServicio);
  });

  // Heading para Cita en resumen
  const headingCita = document.createElement("H3");
  headingCita.textContent = "Resumen de Cita";
  resumen.appendChild(headingCita);

  resumen.appendChild(nombreCliente);
  resumen.appendChild(fechaCita);
  resumen.appendChild(horaCita);
  resumen.appendChild(botonReservar);
}

async function reservarCita() {
  // Destructuring al objeto cita
  const { nombre, fecha, hora, servicios, id } = cita;
  // Extraemos los id de servicios
  const idServicios = servicios.map((servicio) => servicio.id);

  const datos = new FormData();
  datos.append("fecha", fecha);
  datos.append("hora", hora);
  datos.append("usuariosId", id);
  datos.append("servicios", idServicios);
  try {
    // Peticion hacia la api
    const url = "/api/citas";
    const respuesta = await fetch(url, {
      method: "POST",
      body: datos,
    });
    const resultado = await respuesta.json();
    if (resultado.resultado) {
      Swal.fire({
        icon: "success",
        title: "Cita Creada",
        text: "Tu cita fue creada correctamente",
        button: "OK",
      }).then(() => {
        setTimeout(() => {
          window.location.reload();
        }, 3000);
      });
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Hubo un error al guardar la cita",
    });
  }

  // Para consultar lo que se esta enviando al servidor
  // console.log([...datos]);
}
