<?php
/* =====================================================================
   ARCHIVO: registro.php
   PARA QUÉ SIRVE: permite que alumnos y profesores se registren.

   FLUJO:
     ALUMNO   → completa nombre, Gmail y DNI
              → el sistema GENERA una contraseña y la ENVÍA AL CORREO.
     PROFESOR → completa nombre, correo y la clave de la escuela
              → el sistema GENERA un código y lo ENVÍA AL CORREO.
     DIRECTIVO→ NO se registra aquí (los crea la escuela en datos.php).

   En MODO_PRUEBA (includes/config.php) el correo no sale de verdad:
   se guarda en data/correos_simulados.txt y se muestra en pantalla.
   ===================================================================== */
require_once 'includes/sesion.php';
require_once 'includes/usuarios.php';

// Si ya inició sesión, no necesita registrarse
if (estaLogueado()) {
    header('Location: dashboard.php');
    exit;
}

$error  = '';          // Mensaje de error (vacío = sin error)
$exito  = '';          // Mensaje de éxito
$avisoPrueba = '';     // Datos que se muestran en pantalla SOLO en modo prueba
$tipo   = 'alumno';    // Pestaña activa


/* =====================================================================
   PROCESAR EL FORMULARIO (solo si fue enviado)
   ===================================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo   = $_POST['tipo'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = strtolower(trim($_POST['correo'] ?? ''));

    // Cargamos los usuarios existentes para detectar duplicados
    $bd = cargarUsuarios();

    // ---- Validaciones comunes a ambos tipos ----
    if ($tipo !== 'alumno' && $tipo !== 'profesor') {
        $error = 'Tipo de registro no válido.';
        $tipo  = 'alumno';
    } elseif ($nombre === '' || mb_strlen($nombre) > 80) {
        $error = 'Escriba su nombre completo.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        // filter_var comprueba que el correo tenga formato válido
        $error = 'El correo no es válido.';
    }


    /* -------------------------------------------------------------
       REGISTRO DE ALUMNO
       ------------------------------------------------------------- */
    elseif ($tipo === 'alumno') {

        $dni = trim($_POST['dni'] ?? '');

        // AQUÍ SE VALIDAN LOS DATOS DEL ALUMNO
        if (substr($correo, -10) !== '@gmail.com') {
            $error = 'El alumno debe usar un correo de Gmail (@gmail.com).';
        } elseif (!preg_match('/^\d{7,8}$/', $dni)) {
            // Expresión regular: solo 7 u 8 números
            $error = 'El DNI debe tener 7 u 8 números, sin puntos.';
        } else {
            // ¿Ya existe ese correo o ese DNI?
            $duplicado = false;
            foreach ($bd['alumnos'] as $a) {
                if ($a['correo'] === $correo || $a['dni'] === $dni) {
                    $duplicado = true;
                }
            }

            if ($duplicado) {
                $error = 'Ya existe un alumno registrado con ese correo o DNI.';
            } else {
                // AQUÍ SE GENERA LA CONTRASEÑA DEL ALUMNO
                // random_bytes() crea datos aleatorios seguros; bin2hex() los
                // pasa a texto. 4 bytes = contraseña de 8 caracteres.
                $clave = bin2hex(random_bytes(4));

                // Guardamos al alumno (con la contraseña CIFRADA, nunca en texto)
                $bd['alumnos'][] = [
                    'nombre'     => $nombre,
                    'correo'     => $correo,
                    'dni'        => $dni,
                    'clave_hash' => password_hash($clave, PASSWORD_DEFAULT),
                ];

                if (!guardarUsuarios($bd)) {
                    $error = 'No se pudo guardar el registro. Verifique los permisos de la carpeta data/.';
                } else {
                    // AQUÍ SE ARMA Y ENVÍA EL CORREO CON LA CONTRASEÑA
                    $mensaje = "Hola $nombre,\n\nSu registro en el Campus Virtual fue exitoso.\n\n"
                             . "Correo: $correo\nDNI: $dni\nContraseña: $clave\n\n"
                             . "Ingrese en el campus con esos tres datos.";
                    enviarCorreo($correo, 'Su contraseña del Campus Virtual', $mensaje);

                    $exito = '¡Registro exitoso! Le enviamos su contraseña al correo ' . $correo . '.';
                    if (MODO_PRUEBA) {
                        $avisoPrueba = 'Contraseña generada: ' . $clave;
                    }
                }
            }
        }
    }


    /* -------------------------------------------------------------
       REGISTRO DE PROFESOR
       ------------------------------------------------------------- */
    elseif ($tipo === 'profesor') {

        $claveEscuela = trim($_POST['clave_escuela'] ?? '');

        // AQUÍ SE VERIFICA LA CLAVE DE LA ESCUELA (definida en includes/config.php)
        // hash_equals() compara textos de forma segura.
        if (CLAVE_REGISTRO_PROFESOR !== '' && !hash_equals(CLAVE_REGISTRO_PROFESOR, $claveEscuela)) {
            $error = 'La clave de la escuela no es correcta.';
        } else {
            // ¿Ya existe un profesor con ese correo?
            $duplicado = false;
            foreach ($bd['profesores'] as $p) {
                if ($p['correo'] === $correo) {
                    $duplicado = true;
                }
            }

            if ($duplicado) {
                $error = 'Ya existe un profesor registrado con ese correo.';
            } else {
                // AQUÍ SE GENERA EL CÓDIGO DEL PROFESOR (ejemplo: PROF-4F9A2C)
                $codigo = 'PROF-' . strtoupper(bin2hex(random_bytes(3)));

                $bd['profesores'][] = [
                    'nombre'      => $nombre,
                    'correo'      => $correo,
                    'codigo_hash' => password_hash($codigo, PASSWORD_DEFAULT),
                ];

                if (!guardarUsuarios($bd)) {
                    $error = 'No se pudo guardar el registro. Verifique los permisos de la carpeta data/.';
                } else {
                    // AQUÍ SE ARMA Y ENVÍA EL CORREO CON EL CÓDIGO
                    $mensaje = "Hola $nombre,\n\nSu registro como profesor fue exitoso.\n\n"
                             . "Su código de acceso es: $codigo\n\n"
                             . "En el campus, elija «Profesor» e ingrese ese código.";
                    enviarCorreo($correo, 'Su código de acceso al Campus Virtual', $mensaje);

                    $exito = '¡Registro exitoso! Le enviamos su código al correo ' . $correo . '.';
                    if (MODO_PRUEBA) {
                        $avisoPrueba = 'Código generado: ' . $codigo;
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — Campus Virtual</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="fondo-login">

<main class="login">
    <!-- AQUÍ VA EL LOGO DE LA ESCUELA (mismo archivo: img/logo.png) -->
    <img src="img/logo.png" alt="Logo de la escuela" class="logo logo--login" onerror="this.style.display='none'">
    <h1>Registro</h1>
    <p class="login__sub">Cree su acceso al campus</p>

    <!-- Pestañas: Alumno / Profesor (los directivos NO se registran aquí) -->
    <div class="pestanas">
        <button type="button" class="pestana" data-rol="alumno">Alumno</button>
        <button type="button" class="pestana" data-rol="profesor">Profesor</button>
    </div>

    <!-- MENSAJE DE ERROR -->
    <?php if ($error !== ''): ?>
        <div class="error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <!-- MENSAJE DE ÉXITO -->
    <?php if ($exito !== ''): ?>
        <div class="exito">
            <?php echo e($exito); ?>
            <?php if ($avisoPrueba !== ''): ?>
                <!-- ESTE RECUADRO SOLO APARECE EN MODO_PRUEBA (config.php) -->
                <p class="exito__prueba"> Modo prueba (no se envió correo real).<br>
                <strong><?php echo e($avisoPrueba); ?></strong></p>
            <?php endif; ?>
            <p><a href="login.php">Ir a ingresar →</a></p>
        </div>
    <?php endif; ?>


    <!-- ============ FORMULARIO: REGISTRO DE ALUMNO ============ -->
    <form method="post" action="registro.php" class="formulario" id="form-alumno">
        <input type="hidden" name="tipo" value="alumno">

        <label for="a-nombre">Nombre y apellido</label>
        <input type="text" id="a-nombre" name="nombre" required>

        <label for="a-correo">Correo de Gmail</label>
        <input type="email" id="a-correo" name="correo" placeholder="alumno@gmail.com" required>

        <label for="a-dni">DNI</label>
        <input type="text" id="a-dni" name="dni" inputmode="numeric" placeholder="Sin puntos" required>
        <small>Le enviaremos una contraseña a su correo.</small>

        <button type="submit" class="boton">Registrarme</button>
    </form>


    <!-- ============ FORMULARIO: REGISTRO DE PROFESOR ============ -->
    <form method="post" action="registro.php" class="formulario" id="form-profesor">
        <input type="hidden" name="tipo" value="profesor">

        <label for="p-nombre">Nombre y apellido</label>
        <input type="text" id="p-nombre" name="nombre" required>

        <label for="p-correo">Correo</label>
        <input type="email" id="p-correo" name="correo" required>

        <label for="p-clave">Clave de la escuela</label>
        <input type="password" id="p-clave" name="clave_escuela">
        <small>Se la entrega la escuela a sus docentes. Le enviaremos su código de acceso por correo.</small>

        <button type="submit" class="boton">Registrarme</button>
    </form>

    <p class="login__volver">¿Ya tiene cuenta? <a href="login.php">Ingresar</a></p>
    <p class="login__volver"><a href="index.php">← Volver al inicio</a></p>
</main>


<script>
/* JAVASCRIPT: muestra solo el formulario de la pestaña elegida.
   (La validación real la hace PHP arriba.) */
var tipoInicial = "<?php echo e($tipo); ?>";
var pestanas = document.querySelectorAll('.pestana');
var tipos = ['alumno', 'profesor'];

function mostrarTipo(tipo) {
    tipos.forEach(function (t) {
        document.getElementById('form-' + t).style.display = (t === tipo) ? 'block' : 'none';
    });
    pestanas.forEach(function (p) {
        p.classList.toggle('activa', p.dataset.rol === tipo);
    });
}
pestanas.forEach(function (p) {
    p.addEventListener('click', function () { mostrarTipo(p.dataset.rol); });
});
mostrarTipo(tipoInicial);
</script>

</body>
</html>
