<?php
/* =====================================================================
   ARCHIVO: dashboard.php  (PANEL DE CONTROL)
   PARA QUÉ SIRVE: página PRIVADA. Muestra "cuadros" (módulos) distintos
   según el ROL guardado en $_SESSION.

   REGLAS DE ACCESO:
     - Directivo : Proyector + Mesa + Biblioteca + Programa
     - Profesor  : Proyector + Mesa + Biblioteca + Programa
     - Alumno    : Mesa + Biblioteca + Programa (SIN proyector)
   ===================================================================== */
require_once 'includes/sesion.php';

// PASO 1: si no hay sesión, requerirLogin() redirige a login.php
requerirLogin();

// PASO 2: leemos los datos guardados al iniciar sesión
$rol    = $_SESSION['rol'];     // 'alumno', 'profesor' o 'directivo'
$nombre = $_SESSION['nombre'];  // Nombre para saludar

// Texto amigable para mostrar el rol (el operador ?? usa un valor por defecto)
$etiquetas = ['alumno' => 'Alumno/a', 'profesor' => 'Profesor/a', 'directivo' => 'Directivo/a'];
$etiquetaRol = $etiquetas[$rol] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi panel — Campus Virtual</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- =================== BARRA SUPERIOR =================== -->
<header class="barra">
    <div class="contenedor barra__interior">
        <!-- AQUÍ VA EL LOGO DE LA ESCUELA (mismo archivo: img/logo.png) -->
        <a href="index.php" class="barra__logo">
            <img src="img/logo.png" alt="Logo de la escuela" class="logo" onerror="this.style.display='none'">
            Campus Virtual
        </a>
        <nav class="barra__menu">
            <!-- Mostramos nombre y rol (con e() por seguridad) -->
            <span class="barra__usuario"><?php echo e($nombre); ?> · <?php echo e($etiquetaRol); ?></span>
            <a href="logout.php" class="boton boton--chico boton--borde">Cerrar sesión</a>
        </nav>
    </div>
</header>


<main class="contenedor panel">
    <h1>¡Hola, <?php echo e($nombre); ?>!</h1>
    <p class="seccion__intro">Estos son los módulos disponibles para usted:</p>

    <div class="modulos">

        <?php
        /* =============================================================
           MÓDULO 1: RESERVA DE PROYECTOR
           Solo DIRECTIVOS y PROFESORES. El alumno NO lo ve.
           ============================================================= */
        if ($rol === 'directivo' || $rol === 'profesor') {
        ?>
            <!-- AQUÍ SE CAMBIA EL ENLACE (href) cuando exista la página real del módulo -->
            <a class="modulo" href="modulo.php?m=proyector">
                
                <h2>Consultar y administrar Reserva de proyector</h2>
                <p>R.</p>
            </a>
        <?php
        } // fin del if del proyector (si es alumno, este HTML no se envía al navegador)
        ?>


        <?php
        /* =============================================================
           MÓDULO 2: PREINSCRIPCIÓN A MESA DE EXÁMENES
           Lo ven los TRES roles, por eso no necesita condición especial.
           Aun así usamos if/else para que sea fácil restringirlo después.
           ============================================================= */
        if ($rol === 'directivo' || $rol === 'profesor' || $rol === 'alumno') {
        ?>
            <a class="modulo" href="modulo.php?m=mesa">
                
                <h2>Preinscripción a mesa</h2>
                <p>Anótese en las mesas de exámenes.</p>
            </a>
        <?php } ?>


        <?php
        /* =============================================================
           MÓDULO 3: BIBLIOTECA (los tres roles)
           ============================================================= */
        if ($rol === 'directivo' || $rol === 'profesor' || $rol === 'alumno') {
        ?>
            <a class="modulo" href="modulo.php?m=biblioteca">
                
                <h2>Biblioteca</h2>
                <p>Consulte el catálogo y los materiales.</p>
            </a>
        <?php } ?>


        <?php
        /* =============================================================
           MÓDULO 4: DESCARGA DE PROGRAMA DE MESA DE EXAMEN (los tres roles)
           ============================================================= */
        if ($rol === 'directivo' || $rol === 'profesor' || $rol === 'alumno') {
        ?>
            <a class="modulo" href="modulo.php?m=programa">
               
                <h2>Programa de mesa</h2>
                <p>Descargue el programa de examen.</p>
            </a>
        <?php } ?>

    </div>
</main>

</body>
</html>
