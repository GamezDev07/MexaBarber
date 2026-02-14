<div class="barra">
    <p>Hola: <?php echo $nombre ?? ''; ?></p>
    <a class="boton" href="/logout">Cerrar Sesión</a>
</div>

<?php if(isset($_SESSION['admin'])) { ?>
    <div class="barra-servicios">
        <a class="boton" href="/admin">Ver Citas</a>
        <a class="boton" href="/servicios">Ver Servicios</a>
        <a class="boton" href="/servicios/crear">Nuevo Servicio</a>
    </div>
<?php } else if(isset($_SESSION['login'])) { ?>
    <div class="barra-servicios">
        <a class="boton" href="/cita">Nueva Cita</a>
        <a class="boton" href="/mis-citas">Mis Citas</a>
    </div>
<?php } ?>
