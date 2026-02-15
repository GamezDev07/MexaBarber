<h1 class="nombre-pagina">Crear Nueva Cita</h1>
<p class="descripcion-pagina">Elige tus servicios y coloca tus datos</p>

<?php
include_once __DIR__ . '/../templates/barra.php';
?>

<div id="app">
    <nav class="tabs">
        <button class="actual" type="button" data-paso="1">Servicios</button>
        <button type="button" data-paso="2">Barbero</button>
        <button type="button" data-paso="3">Información Cita</button>
        <button type="button" data-paso="4">Método de Pago</button>
        <button type="button" data-paso="5">Resumen</button>
    </nav>

    <!-- Paso 1: Servicios -->
    <div id="paso-1" class="seccion">
        <h2>Servicios</h2>
        <p class="text-center">Elige tus servicios a continuación</p>
        <div id="servicios" class="listado-servicios">
            <div class="servicio-skeleton"></div>
            <div class="servicio-skeleton"></div>
            <div class="servicio-skeleton"></div>
            <div class="servicio-skeleton"></div>
            <div class="servicio-skeleton"></div>
            <div class="servicio-skeleton"></div>
        </div>
    </div>

    <!-- Paso 2: Selección de Barbero -->
    <div id="paso-2" class="seccion">
        <h2>Elige tu Barbero</h2>
        <p class="text-center">Selecciona un barbero o deja que te asignemos uno</p>
        <div id="barberos" class="listado-barberos"></div>
    </div>

    <!-- Paso 3: Datos y Fecha -->
    <div id="paso-3" class="seccion">
        <h2>Tus Datos y Cita</h2>
        <p class="text-center">Coloca tus datos y fecha de tu cita</p>

        <form class="formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input id="nombre" type="text" placeholder="Tu Nombre" value="<?php echo $nombre; ?>" disabled />
            </div>

            <div class="campo">
                <label for="fecha">Fecha</label>
                <input id="fecha" type="text" placeholder="Selecciona la fecha" />
            </div>

            <div class="campo">
                <label for="hora">Hora</label>
                <input id="hora" type="text" placeholder="Selecciona la hora" />
            </div>
            <input type="hidden" id="id" value="<?php echo $id; ?>">

        </form>

        <div class="paginacion">
            <button id="siguiente-paso3" class="boton">Siguiente &raquo;</button>
        </div>
    </div>

    <!-- Paso 4: Método de Pago -->
    <div id="paso-4" class="seccion">
        <h2>Método de Pago</h2>
        <p class="text-center">Elige cómo deseas pagar</p>

        <form id="metodos-pago-form" class="formulario">
            <div class="campo">
                <div class="checkbox-grupo">
                    <input type="radio" id="metodo-efectivo" name="metodo-pago" value="efectivo"
                        class="metodo-pago-input">
                    <label for="metodo-efectivo" class="checkbox-label">
                        <span class="checkbox-icon">💵</span>
                        <span class="checkbox-texto">
                            <strong>Efectivo</strong>
                            <small>Paga al llegar al establecimiento</small>
                        </span>
                    </label>
                </div>

                <div class="checkbox-grupo">
                    <input type="radio" id="metodo-tarjeta" name="metodo-pago" value="tarjeta"
                        class="metodo-pago-input">
                    <label for="metodo-tarjeta" class="checkbox-label">
                        <span class="checkbox-icon">💳</span>
                        <span class="checkbox-texto">
                            <strong>Tarjeta en Establecimiento</strong>
                            <small>Paga con tarjeta al llegar</small>
                        </span>
                    </label>
                </div>

                <div class="checkbox-grupo">
                    <input type="radio" id="metodo-transferencia" name="metodo-pago" value="transferencia"
                        class="metodo-pago-input">
                    <label for="metodo-transferencia" class="checkbox-label">
                        <span class="checkbox-icon">🏦</span>
                        <span class="checkbox-texto">
                            <strong>Transferencia Bancaria</strong>
                            <small>Realiza una transferencia y sube tu comprobante</small>
                        </span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <!-- Paso 5: Resumen -->
    <div id="paso-5" class="seccion contenido-resumen">
        <h2>Resumen</h2>
        <p class="text-center">Verifica que la información sea correcta</p>
    </div>

    <div class="paginacion">
        <button id="anterior" class="boton">&laquo; Anterior</button>

        <button id="siguiente" class="boton">Siguiente &raquo;</button>
    </div>
</div>

<?php
$script = "
        <script src='//cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script src='build/js/app.js'></script>
    ";
?>