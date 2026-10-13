<?php
/* =====================================================================
   ARCHIVO: login.php
   PARA QUÉ SIRVE: muestra el formulario de acceso y VALIDA los datos
   cuando el usuario lo envía.

   FLUJO COMPLETO (léalo de arriba hacia abajo):
     1. Cargamos la sesión y la "base de datos" simulada.
     2. Si el usuario ya está logueado, lo mandamos al panel.
     3. Si el formulario fue enviado (método POST), verificamos
        según el rol elegido: alumno, profesor o directivo.
     4. Si todo es correcto, guardamos sus datos en $_SESSION
        y redirigimos a dashboard.php.
     5. Si algo falla, guardamos un mensaje en $error y se muestra
        arriba del formulario.
   ===================================================================== */

// PASO 1: cargar sesión y datos de usuarios
require_once 'includes/sesion.php';
require_once 'includes/datos.php';      // Aquí están los DIRECTIVOS
require_once 'includes/usuarios.php';   // Funciones para leer alumnos y profesores registrados

// Cargamos alumnos y profesores desde data/usuarios.json
$BD = cargarUsuarios();

// PASO 2: si ya inició sesión, no tiene sentido mostrar el login
if (estaLogueado()) {
    header('Location: dashboard.php');
    exit;
}

// Variable donde guardaremos el mensaje de error (vacía = sin error)
$error = '';

// Variable para recordar qué pestaña estaba usando si hubo error
$rolElegido = 'alumno';


/* =====================================================================
   PASO 3: PROCESAR EL FORMULARIO
   $_SERVER['REQUEST_METHOD'] vale 'POST' solo cuando el usuario
   presionó el botón "Ingresar".
   ===================================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Leemos el rol que viene en un campo oculto del formulario.
    // El operador ?? '' evita errores si el campo no existe.
    $rolElegido = $_POST['rol'] ?? '';


    /* -------------------------------------------------------------
       CASO A: ALUMNO  (Gmail + DNI + contraseña)
       ------------------------------------------------------------- */
    if ($rolElegido === 'alumno') {

        // trim() quita espacios sobrantes al inicio y al final
        $correo = trim($_POST['correo'] ?? '');
        $dni    = trim($_POST['dni'] ?? '');
        $clave  = $_POST['clave'] ?? '';

        // Recorremos la lista de alumnos buscando coincidencia
        foreach ($BD['alumnos'] as $alumno) {

            // AQUÍ SE VERIFICAN CORREO, DNI Y CONTRASEÑA DEL ALUMNO
            // strtolower() ignora mayúsculas/minúsculas en el correo.
            // password_verify() compara la clave escrita con el hash guardado
            // (la clave que el sistema generó y envió por correo al alumno).
            if (strtolower($correo) === strtolower($alumno['correo'])
                && $dni === $alumno['dni']
                && password_verify($clave, $alumno['clave_hash'])) {

                // session_regenerate_id() crea un nuevo ID de sesión:
                // protege contra el robo de sesiones.
                session_regenerate_id(true);

                // Guardamos en la sesión lo que necesitaremos recordar
                $_SESSION['rol']    = 'alumno';
                $_SESSION['nombre'] = $alumno['nombre'];

                header('Location: dashboard.php'); // Vamos al panel
                exit;
            }
        }
        // Si terminó el foreach sin entrar al if, los datos son incorrectos
        $error = 'Correo, DNI o contraseña incorrectos.';
    }


    /* -------------------------------------------------------------
       CASO B: PROFESOR  (solo código alfanumérico)
       ------------------------------------------------------------- */
    elseif ($rolElegido === 'profesor') {

        // strtoupper() convierte a mayúsculas para no distinguir "prof-a1b2c3"
        $codigo = strtoupper(trim($_POST['codigo'] ?? ''));

        // AQUÍ SE VERIFICA EL CÓDIGO DEL PROFESOR
        // El código se guarda cifrado (hash), así que lo comparamos con
        // password_verify() contra cada profesor registrado.
        if ($codigo !== '') {
            foreach ($BD['profesores'] as $profesor) {
                if (password_verify($codigo, $profesor['codigo_hash'])) {

                    session_regenerate_id(true);
                    $_SESSION['rol']    = 'profesor';
                    $_SESSION['nombre'] = $profesor['nombre'];

                    header('Location: dashboard.php');
                    exit;
                }
            }
        }
        $error = 'Código de profesor no válido.';
    }


    /* -------------------------------------------------------------
       CASO C: DIRECTIVO  (correo + contraseña)
       ------------------------------------------------------------- */
    elseif ($rolElegido === 'directivo') {

        $correo = trim($_POST['correo'] ?? '');
        $clave  = $_POST['clave'] ?? '';

        foreach ($DIRECTIVOS as $directivo) {

            // AQUÍ SE VERIFICA LA CONTRASEÑA DEL DIRECTIVO
            if (strtolower($correo) === strtolower($directivo['correo'])
                && password_verify($clave, $directivo['clave_hash'])) {

                session_regenerate_id(true);
                $_SESSION['rol']    = 'directivo';
                $_SESSION['nombre'] = $directivo['nombre'];

                header('Location: dashboard.php');
                exit;
            }
        }
        $error = 'Correo o contraseña incorrectos.';
    }

    // Si el rol llegó vacío o con un valor raro
    else {
        $error = 'Seleccione un tipo de usuario válido.';
        $rolElegido = 'alumno';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar al campus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="fondo-login">

<main class="login">
    <!-- AQUÍ VA EL LOGO DE LA ESCUELA (mismo archivo: img/logo.png) -->
    <img src="img/logo.png" alt="Logo de la escuela" class="logo logo--login" onerror="this.style.display='none'">
    <h1>Campus Virtual</h1>
    <p class="login__sub">Seleccione su tipo de usuario</p>

    <!-- PESTAÑAS para elegir el rol (el JavaScript de abajo las hace funcionar) -->
    <div class="pestanas">
        <button type="button" class="pestana" data-rol="alumno">Alumno</button>
        <button type="button" class="pestana" data-rol="profesor">Profesor</button>
        <button type="button" class="pestana" data-rol="directivo">Directivo</button>
    </div>

    <!-- Mensaje de error (solo aparece si $error tiene texto) -->
    <?php if ($error !== ''): ?>
        <div class="error"><?php echo e($error); // e() escapa el texto por seguridad ?></div>
    <?php endif; ?>


    <!-- ============ FORMULARIO ALUMNO ============ -->
    <form method="post" action="login.php" class="formulario" id="form-alumno">
        <input type="hidden" name="rol" value="alumno"> <!-- Campo oculto: indica el rol -->

        <label for="a-correo">Correo de Gmail</label>
        <input type="email" id="a-correo" name="correo" placeholder="alumno@gmail.com" required>

        <label for="a-dni">DNI</label>
        <input type="text" id="a-dni" name="dni" inputmode="numeric" placeholder="Sin puntos" required>

        <label for="a-clave">Contraseña</label>
        <input type="password" id="a-clave" name="clave" required>
        <small>La contraseña fue generada por el sistema y enviada a su correo.</small>

        <button type="submit" class="boton">Ingresar</button>
    </form>


    <!-- ============ FORMULARIO PROFESOR ============ -->
    <form method="post" action="login.php" class="formulario" id="form-profesor">
        <input type="hidden" name="rol" value="profesor">

        <label for="p-codigo">Código de profesor</label>
        <input type="text" id="p-codigo" name="codigo" placeholder="Ej: PROF-A1B2C3" required>
        <small>Código entregado por la escuela.</small>

        <button type="submit" class="boton">Ingresar</button>
    </form>


    <!-- ============ FORMULARIO DIRECTIVO ============ -->
    <form method="post" action="login.php" class="formulario" id="form-directivo">
        <input type="hidden" name="rol" value="directivo">

        <label for="d-correo">Correo</label>
        <input type="email" id="d-correo" name="correo" required>

        <label for="d-clave">Contraseña</label>
        <input type="password" id="d-clave" name="clave" required>

        <button type="submit" class="boton">Ingresar</button>
    </form>

    <!-- ENLACE AL REGISTRO (para alumnos y profesores nuevos) -->
    <p class="login__volver">¿Primera vez? <a href="registro.php">Regístrese aquí</a></p>
    <p class="login__volver"><a href="index.php">← Volver al inicio</a></p>

    <!-- DATOS DE PRUEBA: BORRAR ESTE BLOQUE ANTES DE PUBLICAR EL SITIO -->
    <details class="prueba">
        <summary>Datos de prueba (solo desarrollo)</summary>
        <p><strong>Alumno:</strong> alumno@gmail.com · DNI 40123456 · Alumno2026</p>
        <p><strong>Profesor:</strong> PROF-A1B2C3</p>
        <p><strong>Directivo:</strong> directora@escuela.edu.ar · Directivo2026</p>
    </details>
</main>


<script>
/* =====================================================================
   JAVASCRIPT: muestra solo el formulario del rol elegido.
   No tiene que ver con la seguridad: la validación real la hace PHP.
   ===================================================================== */

// PHP nos pasa qué pestaña mostrar al cargar (por si hubo un error)
var rolInicial = "<?php echo e($rolElegido); ?>";

// Guardamos todas las pestañas y todos los formularios
var pestanas = document.querySelectorAll('.pestana');
var roles = ['alumno', 'profesor', 'directivo'];

// Función que muestra un rol y oculta los demás
function mostrarRol(rol) {
    roles.forEach(function (r) {
        // Muestra el formulario del rol elegido, oculta los otros
        document.getElementById('form-' + r).style.display = (r === rol) ? 'block' : 'none';
    });
    pestanas.forEach(function (p) {
        // Marca como "activa" la pestaña elegida
        p.classList.toggle('activa', p.dataset.rol === rol);
    });
}

// Al hacer clic en una pestaña, mostramos su formulario
pestanas.forEach(function (p) {
    p.addEventListener('click', function () { mostrarRol(p.dataset.rol); });
});

// Mostramos la pestaña inicial al cargar la página
mostrarRol(rolInicial);
</script>

</body>
</html>
