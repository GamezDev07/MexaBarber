let paso = 1;
const pasoInicial = 1;
const pasoFinal = 5;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: [],
    barberoId: '',
    barberoNombre: '',
    metodoPago: ''
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion();
    tabs();
    botonesPaginador();
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); // Servicios
    consultarBarberos(); // Barberos

    idCliente();
    nombreCliente();
    seleccionarFecha();
    seleccionarHora();
    seleccionarMetodoPago();

    mostrarResumen();
}

function mostrarSeccion() {
    // Ocultar la sección que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    // Seleccionar la sección con el paso...
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    // Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    // Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');
    botones.forEach( boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            paso = parseInt( e.target.dataset.paso );
            mostrarSeccion();
            botonesPaginador();
        });
    });
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === pasoFinal) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function() {
        if(paso <= pasoInicial) return;
        paso--;
        botonesPaginador();
    })
}
function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {
        if(paso >= pasoFinal) return;
        paso++;
        botonesPaginador();
    })
}

// --- PASO 1: SERVICIOS ---

async function consultarAPI() {
    try {
        const url = window.location.origin + '/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);
    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio);
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    });
}

function seleccionarServicio(servicio) {
    const { id } = servicio;
    const { servicios } = cita;

    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    if( servicios.some( agregado => agregado.id === id ) ) {
        cita.servicios = servicios.filter( agregado => agregado.id !== id );
        divServicio.classList.remove('seleccionado');
    } else {
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }
}

// --- PASO 2: BARBEROS ---

async function consultarBarberos() {
    try {
        const url = window.location.origin + '/api/barberos';
        const resultado = await fetch(url);
        const barberos = await resultado.json();
        mostrarBarberos(barberos);
    } catch (error) {
        console.log(error);
    }
}

function mostrarBarberos(barberos) {
    const contenedor = document.querySelector('#barberos');

    // Opción "Sin preferencia"
    const sinPreferencia = document.createElement('DIV');
    sinPreferencia.classList.add('barbero');
    sinPreferencia.dataset.barberoId = '';
    sinPreferencia.onclick = function() {
        seleccionarBarbero('', 'Sin preferencia');
    }

    const iconoSin = document.createElement('P');
    iconoSin.classList.add('barbero__icono');
    iconoSin.textContent = '🔄';

    const nombreSin = document.createElement('P');
    nombreSin.classList.add('barbero__nombre');
    nombreSin.textContent = 'Sin Preferencia';

    const descSin = document.createElement('P');
    descSin.classList.add('barbero__especialidad');
    descSin.textContent = 'Asignación automática';

    sinPreferencia.appendChild(iconoSin);
    sinPreferencia.appendChild(nombreSin);
    sinPreferencia.appendChild(descSin);
    contenedor.appendChild(sinPreferencia);

    // Barberos disponibles
    barberos.forEach( barbero => {
        const { id, nombre, especialidad } = barbero;

        const barberoDiv = document.createElement('DIV');
        barberoDiv.classList.add('barbero');
        barberoDiv.dataset.barberoId = id;
        barberoDiv.onclick = function() {
            seleccionarBarbero(id, nombre);
        }

        const icono = document.createElement('P');
        icono.classList.add('barbero__icono');
        icono.textContent = '✂️';

        const nombreBarbero = document.createElement('P');
        nombreBarbero.classList.add('barbero__nombre');
        nombreBarbero.textContent = nombre;

        const espBarbero = document.createElement('P');
        espBarbero.classList.add('barbero__especialidad');
        espBarbero.textContent = especialidad || 'Barbero';

        barberoDiv.appendChild(icono);
        barberoDiv.appendChild(nombreBarbero);
        barberoDiv.appendChild(espBarbero);
        contenedor.appendChild(barberoDiv);
    });
}

function seleccionarBarbero(id, nombre) {
    cita.barberoId = id;
    cita.barberoNombre = nombre;

    // Quitar seleccionado anterior
    const barberoAnterior = document.querySelector('.barbero.seleccionado');
    if(barberoAnterior) {
        barberoAnterior.classList.remove('seleccionado');
    }

    // Marcar seleccionado
    const barberoSeleccionado = document.querySelector(`[data-barbero-id="${id}"]`);
    if(barberoSeleccionado) {
        barberoSeleccionado.classList.add('seleccionado');
    }
}

// --- PASO 3: DATOS ---

function idCliente() {
    cita.id = document.querySelector('#id').value;
}
function nombreCliente() {
    cita.nombre = document.querySelector('#nombre').value;
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) {
        const dia = new Date(e.target.value).getUTCDay();

        if( [6, 0].includes(dia) ) {
            e.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
    });
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {
        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        if(hora < 10 || hora > 18) {
            e.target.value = '';
            mostrarAlerta('Hora No Válida', 'error', '.formulario');
        } else {
            cita.hora = e.target.value;
        }
    })
}

// --- PASO 4: MÉTODO DE PAGO ---

function seleccionarMetodoPago() {
    const metodos = document.querySelectorAll('.metodo-pago');
    metodos.forEach( metodo => {
        metodo.addEventListener('click', function(e) {
            const metodoSeleccionado = e.currentTarget.dataset.metodo;
            cita.metodoPago = metodoSeleccionado;

            // Quitar seleccionado anterior
            const anterior = document.querySelector('.metodo-pago.seleccionado');
            if(anterior) {
                anterior.classList.remove('seleccionado');
            }

            // Marcar nuevo
            e.currentTarget.classList.add('seleccionado');
        });
    });
}

// --- ALERTAS ---

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    }

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece) {
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
}

// --- PASO 5: RESUMEN ---

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    // Limpiar el Contenido de Resumen
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    if(Object.values(cita).includes('') || cita.servicios.length === 0 ) {
        mostrarAlerta('Faltan datos de Servicios, Barbero, Fecha, Hora o Método de Pago', 'error', '.contenido-resumen', false);
        return;
    }

    const { nombre, fecha, hora, servicios, barberoNombre, metodoPago } = cita;

    // Heading para Servicios en Resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    let totalPrecio = 0;
    servicios.forEach(servicio => {
        const { id, precio, nombre } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
        totalPrecio += parseFloat(precio);
    });

    // Total
    const totalDiv = document.createElement('P');
    totalDiv.classList.add('total');
    totalDiv.innerHTML = `<span>Total:</span> $${totalPrecio.toFixed(2)}`;
    resumen.appendChild(totalDiv);

    // Heading para Cita en Resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // Formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date( Date.UTC(year, mes, dia));

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    const barberoCita = document.createElement('P');
    barberoCita.innerHTML = `<span>Barbero:</span> ${barberoNombre}`;

    const metodoPagoMap = {
        'efectivo': 'Efectivo',
        'tarjeta': 'Tarjeta en Establecimiento',
        'transferencia': 'Transferencia Bancaria'
    };
    const pagoCita = document.createElement('P');
    pagoCita.innerHTML = `<span>Método de Pago:</span> ${metodoPagoMap[metodoPago] || metodoPago}`;

    // Boton para Crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(barberoCita);
    resumen.appendChild(pagoCita);
    resumen.appendChild(botonReservar);
}

async function reservarCita() {

    const { nombre, fecha, hora, servicios, id, barberoId, metodoPago } = cita;

    const idServicios = servicios.map( servicio => servicio.id );

    const datos = new FormData();

    datos.append('fecha', fecha);
    datos.append('hora', hora );
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);
    datos.append('barberoId', barberoId);
    datos.append('metodoPago', metodoPago);

    try {
        const url = window.location.origin + '/api/citas'
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if(resultado.resultado) {
            Swal.fire({
                icon: 'success',
                title: 'Cita Creada',
                text: resultado.turno
                    ? `Tu cita fue creada correctamente. Tu turno es el #${resultado.turno}`
                    : 'Tu cita fue creada correctamente',
                button: 'OK'
            }).then( () => {
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            })
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al guardar la cita'
        })
    }
}
