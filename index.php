<?php
/* =====================================================================
   ARCHIVO: index.php  (PÁGINA PRINCIPAL / LANDING PAGE)
   PARA QUÉ SIRVE: es lo primero que ve el visitante. Es pública:
   no requiere iniciar sesión.
   Incluimos sesion.php para saber si el visitante ya está logueado
   y así cambiar el botón "Ingresar al campus" por "Ir a mi panel".
   ===================================================================== */
require_once 'includes/sesion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- AQUÍ SE CAMBIA EL NOMBRE DE LA ESCUELA (título de la pestaña del navegador) -->
    <title>Escuela Técnica — Inicio</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- =================== BARRA DE NAVEGACIÓN =================== -->
    <header class="barra">
        <div class="contenedor barra__interior">
            <!-- AQUÍ VA EL LOGO Y EL NOMBRE CORTO DE LA ESCUELA
             - LOGO: guarde su imagen como img/logo.png (si usa otro nombre o formato, cámbielo en src="...")
             - NOMBRE: cambie el texto "Escuela Técnica" por el nombre corto de su escuela
             - El onerror oculta la imagen mientras todavía no exista el archivo -->
            <a href="index.php" class="barra__logo">
                <img src="img/logo.png" alt="Logo de la escuela" class="logo" onerror="this.style.display='none'">
                Escuela Técnica
            </a>

            <nav class="barra__menu">
                <a href="#ciclo-basico">Ciclo Básico</a>
                <a href="#cuarto-anio">4to Año</a>
                <a href="#formacion-profesional">Formación Profesional</a>

                <?php if (estaLogueado()): ?>
                    <!-- Si ya inició sesión, mostramos acceso directo al panel -->
                    <a href="dashboard.php" class="boton boton--chico">Ir a mi panel</a>
                <?php else: ?>
                    <!-- Si NO inició sesión, mostramos el botón de ingreso -->
                    <a href="login.php" class="boton boton--chico">Ingresar al campus</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>


    <!-- =================== ENCABEZADO (HERO) =================== -->
    <section class="hero">
        <div class="contenedor hero__interior">
            <div class="hero__texto">
                <!-- AQUÍ SE CAMBIA EL NOMBRE COMPLETO DE LA ESCUELA -->
                <h1>E.E.T. N° 2 "Independencia"</h1>
                <!-- AQUÍ VA LA DESCRIPCIÓN GENERAL DE LA ESCUELA (historia, lema, ubicación, teléfono, etc.) -->
                <p>
                    Bienvenidos al espacio virtual de la E.E.T. N° 2 "Independencia", una institución de nivel secundario y formación profesional consolidada como un pilar fundamental para el desarrollo educativo y socioproductivo de la ciudad de Concordia.

                    Nuestra propuesta educativa trasciende la transmisión teórica: nos enfocamos en el aprendizaje empírico, la construcción de capacidades prácticas y el fomento de la empleabilidad y la movilidad social de nuestros jóvenes y adultos. Dependemos orgánicamente del Consejo General de Educación (CGE) de la Provincia de Entre Ríos y nos regimos por los más altos estándares de la Educación Técnico Profesional.
                </p>
                
            </div>

            <div class="hero__foto">
                <!-- AQUÍ VA LA FOTO DE LA ESCUELA -->
                <img src="img/Imagen_frente_escuela.webp" alt="fachada de la escuela">
            </div>
        </div>
    </section>


    <main>

        <!-- =================== SECCIÓN: CICLO BÁSICO =================== -->
        <section id="ciclo-basico" class="seccion">
            <div class="contenedor">
                <h2>Ciclo Básico (1.º a 3.er año)</h2>
                <p class="seccion__intro">
                    El Ciclo Básico tiene una duración de tres años y se enfoca en la "Vinculación con el Mundo del Trabajo y la Producción". A través de un sistema inmersivo de talleres rotativos, los alumnos ingresantes tienen su primer contacto práctico con las herramientas y lógicas de nuestras tres grandes áreas de especialización: Computación, Gastronomía y Administración.
                </p>

                <div class="tarjetas">
                    <!-- AQUÍ SE PUEDE EDITAR CADA TALLER (nombre y descripción) -->
                    <article class="tarjeta">
                        
                        <h3>Gastronomía</h3>
                        <p>Prácticas en entornos de cocina reales, aprendiendo normas fundamentales de higiene, seguridad y manipulación de alimentos.</p>
                    </article>
                    <article class="tarjeta">
                        
                        <h3>Computación</h3>
                        <p>Desarrollo del pensamiento computacional, resolución de problemas y uso de herramientas digitales para proyectos reales.</p>
                    </article>
                    <article class="tarjeta">
                        
                        <h3>Organización Empresarial</h3>
                        <p>Primeros pasos en la organización de empresas, nociones de costos, presupuestos y mentalidad emprendedora.</p>
                    </article>
                </div>
            </div>
        </section>


        <!-- =================== SECCIÓN: 4TO AÑO =================== -->
        <section id="cuarto-anio" class="seccion seccion--gris">
            <div class="contenedor">
                <h2>4.º Año: Tecnicaturas</h2>
                <p class="seccion__intro">
                    Al finalizar el Ciclo Básico, el estudiante opta por una de nuestras tres tecnicaturas, cuidadosamente diseñadas para dar respuesta a la matriz económica, industrial y de servicios de la región:
                    <!-- AQUÍ SE LISTAN LAS TECNICATURAS REALES DE LA ESCUELA cuando estén definidas -->
                </p>
                <div class="aviso">
                    📌 <strong>Importante:</strong> en 4.º año la materia
                    «Organización Empresarial» pasa a llamarse
                    <strong>«Administración Empresarial»</strong>.
                </div>
                <p> <strong>Técnico en Computación:</strong>Una especialidad de vanguardia enfocada en la lógica computacional y el análisis de datos. Forma perfiles capacitados para el desarrollo de software, programación de algoritmos y gestión integral de redes, dotándolos de las herramientas para liderar la transformación digital.

                    <strong>Técnico en Administración de Empresas:</strong> Dota al egresado de sólidos conocimientos en contabilidad financiera, control de gestión, legislación comercial y tácticas de marketing. Prepara tanto para la inserción inmediata en PyMEs locales como para la creación y gestión de proyectos emprendedores propios.

                    <strong>Técnico en Gastronomía:</strong> Trasciende la formación culinaria básica para abarcar la gestión comercial integral de empresas de alimentos. Incluye nutrición, diseño de menús, estrictas normativas bromatológicas y altos estándares de hospitalidad, alineando el perfil con el turismo regional.
                </p>

            </div>
        </section>


        <!-- =================== SECCIÓN: FORMACIÓN PROFESIONAL =================== -->
        <section id="formacion-profesional" class="seccion">
            <div class="contenedor">
                <h2>Formación Profesional</h2>
                <p class="seccion__intro">El Anexo de Formación Profesional de la E.E.T. N° 2 "Independencia" es el espacio de nuestra institución pensado específicamente para jóvenes y adultos que buscan una capacitación práctica, dinámica y con salida laboral directa.
                    A diferencia del trayecto secundario tradicional, este espacio está diseñado bajo la convicción de que nunca es tarde para aprender un oficio. Sabemos que muchos adultos regresan a estudiar con lógicas dudas o después de años de inactividad; por eso, nuestros talleres ofrecen un ambiente cordial, horizontal y enfocado 100% en la aplicación práctica inmediata. Aquí, la teoría se aprende haciendo.</p>

                <div class="tarjetas tarjetas--chicas">
                    <!-- AQUÍ SE AGREGAN O QUITAN CURSOS: copie una <article> completa y cambie icono y nombre -->
                    <article class="tarjeta">
                       
                        <h3>Panadería</h3>
                    </article>
                    <article class="tarjeta">
                        
                        <h3>Pastelería</h3>
                    </article>
                    <article class="tarjeta">
                        
                        <h3>Vitrofusión</h3>
                    </article>
                    <article class="tarjeta">
                        
                        <h3>Imprenta</h3>
                    </article>
                    <article class="tarjeta">
                        
                        <h3>Cocina</h3>
                    </article>
                    <article class="tarjeta">
                        
                        <h3>Telar</h3>
                    </article>
                    <article class="tarjeta">
                       
                        <h3>Modista</h3>
                    </article>
                </div>
            </div>
        </section>

    </main>


    <!-- =================== PIE DE PÁGINA =================== -->
    <footer class="pie">
        <!-- AQUÍ SE CAMBIA EL TEXTO DEL PIE DE PÁGINA -->
        <p>© <?php echo date('Y'); // date('Y') imprime el año actual automáticamente 
                ?> Escuela de Educación Técnica N.º 2· Todos los derechos reservados</p>
    </footer>

</body>

</html>