<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Salón</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="/build/css/app.css">
</head>

<body>
    <div class="contenedor-app">
        <div class="imagen"></div>
        <div class="app">
            <?php echo $contenido; ?>
        </div>
    </div>

    <!-- Scripts en orden correcto -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ⚠️ IMPORTANTE: app.js SOLO UNA VEZ -->
    <script src="/build/js/app.js"></script>

    <!-- NO incluir $script si ya carga app.js arriba -->
    <!-- Comentar o eliminar la siguiente línea: -->
    <?php // echo $script ?? ''; ?>
</body>

</html>