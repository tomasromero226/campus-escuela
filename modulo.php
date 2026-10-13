<?php
/* =====================================================================
   ARCHIVO: modulo.php
   PARA QUÉ SIRVE: página de ejemplo para cada módulo (se abre con
   modulo.php?m=proyector, ?m=mesa, ?m=biblioteca o ?m=programa).

   ¿POR QUÉ EXISTE? Ocultar un botón en el panel NO alcanza como
   seguridad: un alumno podría escribir la dirección a mano.
   Por eso VERIFICAMOS EL PERMISO TAMBIÉN AQUÍ, en el servidor.
   ===================================================================== */
require_once 'includes/sesion.php';
requerirLogin(); // Sin sesión → al login

// $_GET['m'] trae el módulo pedido en la dirección (?m=...)
$modulo = $_GET['m'] ?? '';

// Títulos de cada módulo. AQUÍ SE CAMBIAN LOS NOMBRES MOSTRADOS.
$titulos = [
    'proyector'  => 'Reserva de proyector',
    'mesa'       => 'Preinscripción a mesa de exámenes',
    'biblioteca' => 'Biblioteca',
    'programa'   => 'Descarga de programa de mesa de examen',
];

// AQUÍ SE CONTROLA EL ACCESO: si el módulo no existe o el rol no tiene
// permiso (ver tabla $PERMISOS en includes/sesion.php), se bloquea.
if (!isset($titulos[$modulo]) || !tienePermiso($modulo)) {
    http_response_code(403); // 403 = "Prohibido"
    $bloqueado = true;
} else {
    $bloqueado = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo — Campus Virtual</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="contenedor panel">

<?php if ($bloqueado): ?>
    <h1>⛔ Acceso denegado</h1>
    <p>Su tipo de usuario no tiene permiso para ver este módulo.</p>
<?php else: ?>
    <h1><?php echo e($titulos[$modulo]); ?></h1>
    <!-- AQUÍ VA EL CONTENIDO REAL DEL MÓDULO (formularios, listados, descargas, etc.) -->
    <p>Este módulo está en construcción.</p>
<?php endif; ?>

    <p><a href="dashboard.php" class="boton boton--chico">← Volver al panel</a></p>
</main>
</body>
</html>
