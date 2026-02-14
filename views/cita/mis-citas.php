<h1 class="nombre-pagina">Mis Citas</h1>
<p class="descripcion-pagina">Administra tus citas pendientes</p>

<?php
    include_once __DIR__ . '/../templates/barra.php';
?>

<div id="mis-citas-app">
    <div id="listado-citas" class="listado-citas">
        <p class="text-center">Cargando tus citas...</p>
    </div>
</div>

<!-- Modal para modificar cita -->
<div id="modal-modificar" class="modal" style="display:none;">
    <div class="modal__contenido">
        <h2>Modificar Cita</h2>
        <form class="formulario" id="form-modificar">
            <input type="hidden" id="modificar-id">
            <div class="campo">
                <label for="modificar-fecha">Nueva Fecha</label>
                <input id="modificar-fecha" type="date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>
            <div class="campo">
                <label for="modificar-hora">Nueva Hora</label>
                <input id="modificar-hora" type="time">
            </div>
            <div class="campo">
                <label for="modificar-barbero">Barbero</label>
                <select id="modificar-barbero" class="campo__select">
                    <option value="">Sin preferencia</option>
                </select>
            </div>
            <div class="campo">
                <button type="submit" class="boton">Guardar Cambios</button>
                <button type="button" class="boton boton--secundario" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
    $script = "
        <script src='//cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script src='build/js/mis-citas.js'></script>
    ";
?>
