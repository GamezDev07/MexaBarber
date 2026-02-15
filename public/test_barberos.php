<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Barberos API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .result {
            background: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #4CAF50;
            font-family: monospace;
            white-space: pre-wrap;
        }

        .error {
            border-left-color: #f44336;
            background: #ffebee;
        }

        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #45a049;
        }

        .barbero-card {
            background: #fff;
            border: 2px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            display: inline-block;
            width: 250px;
            margin-right: 10px;
        }

        .barbero-icon {
            font-size: 40px;
            text-align: center;
        }

        .barbero-nombre {
            font-weight: bold;
            font-size: 18px;
            margin: 10px 0;
        }

        .barbero-especialidad {
            color: #666;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <h1>🧪 Test de API de Barberos</h1>

    <div class="test-section">
        <h2>1. Test de Sesión PHP</h2>
        <div class="result">
            <?php
            session_start();
            echo "Session ID: " . session_id() . "\n";
            echo "Login status: " . (isset($_SESSION['login']) && $_SESSION['login'] ? '✅ Logged in' : '❌ Not logged in') . "\n";
            if (isset($_SESSION['id'])) {
                echo "User ID: " . $_SESSION['id'] . "\n";
                echo "User name: " . $_SESSION['nombre'] . "\n";
                echo "Barbershop ID: " . (isset($_SESSION['barbershop_id']) ? $_SESSION['barbershop_id'] : '❌ NOT SET') . "\n";
            } else {
                echo "⚠️ No user logged in - please login first at http://localhost:3000\n";
            }
            ?>
        </div>
    </div>

    <div class="test-section">
        <h2>2. Test Directo de la Base de Datos</h2>
        <div class="result">
            <?php
            require __DIR__ . '/../includes/app.php';
            use Model\Barbero;
            use Model\Usuario;

            // Use global database connection
            global $db;

            // Check database
            $query = "SELECT b.id, b.barbershop_id, CONCAT(u.nombre, ' ', u.apellido) as nombre, b.especialidad, b.activo 
          FROM barberos b 
          JOIN usuarios u ON u.id = b.usuario_id";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $all_barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "Total barberos in database: " . count($all_barberos) . "\n\n";
            foreach ($all_barberos as $b) {
                echo "ID: {$b['id']}, Barbershop: {$b['barbershop_id']}, Nombre: {$b['nombre']}, Especialidad: {$b['especialidad']}, Activo: {$b['activo']}\n";
            }
            ?>
        </div>
    </div>

    <div class="test-section">
        <h2>3. Test de Barbero::activosPorBarbershop(1)</h2>
        <div class="result">
            <?php
            $barberos = Barbero::activosPorBarbershop(1);
            echo "Barberos returned by activosPorBarbershop(1): " . count($barberos) . "\n\n";
            if (count($barberos) > 0) {
                echo "✅ SUCCESS - Barberos encontrados:\n";
                foreach ($barberos as $b) {
                    echo "  - ID: {$b['id']}, Nombre: {$b['nombre']}, Especialidad: {$b['especialidad']}\n";
                }
            } else {
                echo "❌ ERROR - No barberos found!\n";
            }
            ?>
        </div>
    </div>

    <div class="test-section">
        <h2>4. Simulación del API Endpoint</h2>
        <button onclick="testAPI()">Test /api/barberos</button>
        <div id="api-result" class="result" style="margin-top: 10px;">
            Click the button to test the API
        </div>
    </div>

    <div class="test-section">
        <h2>5. Vista Previa de Barberos (Como deberían verse)</h2>
        <div id="barberos-preview"></div>
    </div>

    <script>
        // Test the API endpoint
        function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.textContent = 'Loading...';

            fetch('/api/barberos')
                .then(response => response.json())
                .then(data => {
                    resultDiv.textContent = 'API Response:\n' + JSON.stringify(data, null, 2);

                    // Show preview
                    showBarberosPreview(data);

                    if (data.length === 0) {
                        resultDiv.classList.add('error');
                        resultDiv.textContent += '\n\n❌ ERROR: API returned empty array!';
                    } else {
                        resultDiv.classList.remove('error');
                    }
                })
                .catch(error => {
                    resultDiv.classList.add('error');
                    resultDiv.textContent = '❌ ERROR: ' + error.message;
                });
        }

        function showBarberosPreview(barberos) {
            const preview = document.getElementById('barberos-preview');

            if (barberos.length === 0) {
                preview.innerHTML = '<p style="color: red;">❌ No barberos to display</p>';
                return;
            }

            preview.innerHTML = '';
            barberos.forEach(barbero => {
                const card = document.createElement('div');
                card.className = 'barbero-card';
                card.innerHTML = `
                    <div class="barbero-icon">✂️</div>
                    <div class="barbero-nombre">${barbero.nombre}</div>
                    <div class="barbero-especialidad">${barbero.especialidad || 'Barbero'}</div>
                `;
                preview.appendChild(card);
            });
        }

        // Auto-test on page load
        window.addEventListener('load', () => {
            setTimeout(testAPI, 500);
        });
    </script>

    <div class="test-section">
        <h2>📋 Instrucciones</h2>
        <p>Este archivo prueba la configuración completa de barberos:</p>
        <ol>
            <li><strong>Sesión</strong>: Verifica que estés logueado y tengas barbershop_id</li>
            <li><strong>Base de Datos</strong>: Muestra todos los barberos en la DB</li>
            <li><strong>Modelo PHP</strong>: Prueba el método activosPorBarbershop</li>
            <li><strong>API</strong>: Prueba el endpoint real que usa el frontend</li>
            <li><strong>Vista Previa</strong>: Muestra cómo deberían verse los barberos</li>
        </ol>
        <p><strong>⚠️ IMPORTANTE:</strong> Si no ves barberos, asegúrate de:</p>
        <ul>
            <li>Haber cerrado sesión y vuelto a entrar</li>
            <li>Que la sección 1 muestre "Barbershop ID: 1"</li>
            <li>Que la sección 2 muestre 3 barberos</li>
        </ul>
    </div>
</body>

</html>