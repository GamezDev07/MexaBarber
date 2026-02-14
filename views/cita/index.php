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
        <div id="servicios" class="listado-servicios"></div>
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
                <input
                    id="nombre"
                    type="text"
                    placeholder="Tu Nombre"
                    value="<?php echo $nombre; ?>"
                    disabled
                />
            </div>

            <div class="campo">
                <label for="fecha">Fecha</label>
                <input
                    id="fecha"
                    type="date"
                    min="<?php echo date('Y-m-d', strtotime('+1 day') ); ?>"
                />
            </div>

            <div class="campo">
                <label for="hora">Hora</label>
                <input
                    id="hora"
                    type="time"
                />
            </div>
            <input type="hidden" id="id" value="<?php echo $id; ?>" >

        </form>
    </div>

    <!-- Paso 4: Método de Pago -->
    <div id="paso-4" class="seccion">
        <h2>Método de Pago</h2>
        <p class="text-center">Elige cómo deseas pagar</p>

        <div id="metodos-pago" class="metodos-pago-radio">
            <label class="metodo-radio">
                <input type="radio" name="metodoPago" value="efectivo">
                <span class="metodo-radio__check"></span>
                <span class="metodo-radio__icono">💵</span>
                <span class="metodo-radio__info">
                    <span class="metodo-radio__nombre">Efectivo</span>
                    <span class="metodo-radio__descripcion">Paga al llegar al establecimiento</span>
                </span>
            </label>
            <label class="metodo-radio">
                <input type="radio" name="metodoPago" value="tarjeta">
                <span class="metodo-radio__check"></span>
                <span class="metodo-radio__icono">💳</span>
                <span class="metodo-radio__info">
                    <span class="metodo-radio__nombre">Tarjeta en Establecimiento</span>
                    <span class="metodo-radio__descripcion">Paga con tarjeta al llegar</span>
                </span>
            </label>
            <label class="metodo-radio">
                <input type="radio" name="metodoPago" value="transferencia">
                <span class="metodo-radio__check"></span>
                <span class="metodo-radio__icono">🏦</span>
                <span class="metodo-radio__info">
                    <span class="metodo-radio__nombre">Transferencia Bancaria</span>
                    <span class="metodo-radio__descripcion">Realiza una transferencia y sube tu comprobante</span>
                </span>
            </label>
        </div>
    </div>

    <!-- Paso 5: Resumen -->
    <div id="paso-5" class="seccion contenido-resumen">
        <h2>Resumen</h2>
        <p class="text-center">Verifica que la información sea correcta</p>
    </div>

    <div class="paginacion">
        <button
            id="anterior"
            class="boton"
        >&laquo; Anterior</button>

        <button
            id="siguiente"
            class="boton"
        >Siguiente &raquo;</button>
    </div>
</div>

<?php
    $script = "
        <script src='//cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script src='build/js/app.js'></script>
    ";
?>
