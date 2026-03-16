<header class="main-header">
    <nav class="main-nav">
        <div class="logo">
            <i class="fas fa-dumbbell" style="font-size: 2.2rem; color: #ffb74d;"></i>
            <span style="font-size: 1.5rem; color: #fff; font-weight: 700; letter-spacing: 1px;">DeporteFit</span>
        </div>
        <ul class="main-menu">
            <?php 
            // Obtener la página actual
            $current_page = basename($_SERVER['PHP_SELF'] ?? $_SERVER['REQUEST_URI'] ?? 'cliente.php');
            
            // Función para verificar si un enlace está activo
            function isActive($page, $current) {
                return (strpos($current, $page) !== false) ? 'class="activo"' : '';
            }
            ?>
            <li><a href="cliente.php" <?php echo isActive('cliente.php', $current_page); ?>>Inicio</a></li>
            <li><a href="servicios.php" <?php echo isActive('servicios.php', $current_page); ?>>Servicios</a></li>
            <li><a href="entrenadores.php" <?php echo isActive('entrenadores.php', $current_page); ?>>Entrenadores</a></li>
            <li><a href="planes.php" <?php echo isActive('planes.php', $current_page); ?>>Planes y Precios</a></li>
            <li><a href="seguimiento_avance.php" <?php echo isActive('seguimiento_avance.php', $current_page); ?>>Seguimiento y Avance</a></li>
            <li><a href="contacto.php" <?php echo isActive('contacto.php', $current_page); ?>>Contacto</a></li>
        </ul>
    </nav>
</header>
