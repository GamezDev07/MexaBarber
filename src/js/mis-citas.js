document.addEventListener('DOMContentLoaded', function() {
    cargarCitas();
    cargarBarberosSelect();

    document.querySelector('#form-modificar').addEventListener('submit', function(e) {
        e.preventDefault();
        guardarModificacion();
    });
});

async function cargarCitas() {
    try {
        const url = window.location.origin + '/api/mis-citas';
        const respuesta = await fetch(url);
        const citas = await respuesta.json();
        mostrarCitas(citas);
    } catch (error) {
        console.log(error);
    }
}

function mostrarCitas(citas) {
    const contenedor = document.querySelector('#listado-citas');
    contenedor.innerHTML = '';

    if(citas.length === 0) {
        contenedor.innerHTML = '<p class="text-center">No tienes citas pendientes</p>';
        return;
    }

    citas.forEach(cita => {
        const { id, fecha, hora, estado, barbero_nombre, servicios, total, turno, metodo_pago } = cita;

        const citaDiv = document.createElement('DIV');
        citaDiv.classList.add('cita-card');

        const estadoClass = estado === 'confirmada' ? 'cita-card__estado--confirmada' : 'cita-card__estado--pendiente';

        // Formatear fecha
        const fechaObj = new Date(fecha + 'T00:00:00');
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const fechaFormateada = fechaObj.toLocaleDateString('es-MX', opciones);

        const metodoPagoMap = {
            'efectivo': 'Efectivo',
            'tarjeta': 'Tarjeta',
            'transferencia': 'Transferencia'
        };

        citaDiv.innerHTML = `
            <div class="cita-card__header">
                <span class="cita-card__estado ${estadoClass}">${estado}</span>
                ${turno ? `<span class="cita-card__turno">Turno #${turno}</span>` : ''}
            </div>
            <div class="cita-card__body">
                <p><strong>Fecha:</strong> ${fechaFormateada}</p>
                <p><strong>Hora:</strong> ${hora}</p>
                <p><strong>Barbero:</strong> ${barbero_nombre || 'Por asignar'}</p>
                <p><strong>Servicios:</strong> ${servicios}</p>
                <p><strong>Método de pago:</strong> ${metodoPagoMap[metodo_pago] || metodo_pago || 'No especificado'}</p>
                <p class="cita-card__total"><strong>Total:</strong> $${parseFloat(total).toFixed(2)}</p>
            </div>
            <div class="cita-card__acciones">
                <button class="boton boton--small" onclick="abrirModificar(${id}, '${fecha}', '${hora}')">Modificar</button>
                <button class="boton boton--danger boton--small" onclick="cancelarCita(${id})">Cancelar</button>
            </div>
        `;

        contenedor.appendChild(citaDiv);
    });
}

async function cancelarCita(id) {
    const confirmacion = await Swal.fire({
        title: '¿Cancelar cita?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No'
    });

    if(!confirmacion.isConfirmed) return;

    try {
        const datos = new FormData();
        datos.append('id', id);

        const url = window.location.origin + '/api/citas/cancelar';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if(resultado.resultado) {
            Swal.fire('Cancelada', 'Tu cita ha sido cancelada', 'success');
            cargarCitas();
        } else {
            Swal.fire('Error', resultado.mensaje || 'No se pudo cancelar la cita', 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Hubo un error al cancelar', 'error');
    }
}

function abrirModificar(id, fecha, hora) {
    document.querySelector('#modificar-id').value = id;
    document.querySelector('#modificar-fecha').value = fecha;
    document.querySelector('#modificar-hora').value = hora;
    document.querySelector('#modal-modificar').style.display = 'flex';
}

function cerrarModal() {
    document.querySelector('#modal-modificar').style.display = 'none';
}

async function cargarBarberosSelect() {
    try {
        const url = window.location.origin + '/api/barberos';
        const respuesta = await fetch(url);
        const barberos = await respuesta.json();

        const select = document.querySelector('#modificar-barbero');
        barberos.forEach(barbero => {
            const option = document.createElement('option');
            option.value = barbero.id;
            option.textContent = barbero.nombre;
            select.appendChild(option);
        });
    } catch (error) {
        console.log(error);
    }
}

async function guardarModificacion() {
    const id = document.querySelector('#modificar-id').value;
    const fecha = document.querySelector('#modificar-fecha').value;
    const hora = document.querySelector('#modificar-hora').value;
    const barberoId = document.querySelector('#modificar-barbero').value;

    const datos = new FormData();
    datos.append('id', id);
    if(fecha) datos.append('fecha', fecha);
    if(hora) datos.append('hora', hora);
    datos.append('barberoId', barberoId);

    try {
        const url = window.location.origin + '/api/citas/modificar';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if(resultado.resultado) {
            Swal.fire('Modificada', 'Tu cita ha sido actualizada', 'success');
            cerrarModal();
            cargarCitas();
        } else {
            Swal.fire('Error', resultado.mensaje || 'No se pudo modificar la cita', 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Hubo un error al modificar', 'error');
    }
}
