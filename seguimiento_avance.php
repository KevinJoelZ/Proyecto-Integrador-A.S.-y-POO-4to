<?php
session_start();
require_once 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    $user_id = 0;
    $nombre = 'Invitado';
    $email = '';
    $usuario = null;
    $plan = null;
    $progreso = ['rutinas_completadas' => 0, 'total_rutinas' => 0];
    $ultima_sesion = null;
    $loggedIn = false;
} else {
    $user_id = $_SESSION['user_id'];
    $nombre = $_SESSION['user_nombre'] ?? 'Usuario';
    $email = $_SESSION['user_email'] ?? '';
    $loggedIn = true;
    
    // Obtener datos del usuario
    $sql = "SELECT nombre, email, rol, foto_perfil FROM usuarios WHERE id = $user_id";
    $result = mysqli_query($conexion, $sql);
    $usuario = mysqli_fetch_assoc($result);

    // Obtener plan activo del usuario
    $sql_plan = "SELECT p.nombre as plan_nombre, p.precio, p.descripcion
                 FROM suscripciones s 
                 JOIN planes p ON s.plan_id = p.id 
                 WHERE s.usuario_id = $user_id AND s.estado = 'activa' 
                 LIMIT 1";
    $result_plan = mysqli_query($conexion, $sql_plan);
    $plan = mysqli_fetch_assoc($result_plan);

    // Obtener progreso del usuario
    $sql_progreso = "SELECT COUNT(*) as total_rutinas, 
                     SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as rutinas_completadas
                     FROM rutinas WHERE usuario_id = $user_id";
    $result_progreso = mysqli_query($conexion, $sql_progreso);
    $progreso = mysqli_fetch_assoc($result_progreso);

    // Obtener última sesión
    $sql_sesion = "SELECT fecha_creacion FROM rutinas WHERE usuario_id = $user_id ORDER BY fecha_creacion DESC LIMIT 1";
    $result_sesion = mysqli_query($conexion, $sql_sesion);
    $ultima_sesion = mysqli_fetch_assoc($result_sesion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Avance - DeporteFit</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(180deg, #f7fbff 0%, #e3f0ff 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .header { background: linear-gradient(135deg, #1565c0, #0d47a1); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; }
        .header h1 { font-size: 2rem; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card h3 { color: #1565c0; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        
        .stat-number { font-size: 2.5rem; font-weight: bold; color: #1565c0; }
        .stat-label { color: #666; font-size: 0.9rem; margin-top: 5px; }
        
        .progress-bar { height: 20px; background: #e0e0e0; border-radius: 10px; overflow: hidden; margin: 15px 0; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #4caf50, #8bc34a); border-radius: 10px; transition: width 0.5s ease; }
        
        .btn { display: inline-block; padding: 12px 24px; background: #1565c0; color: white; text-decoration: none; border-radius: 8px; transition: background 0.3s; border: none; cursor: pointer; }
        .btn:hover { background: #0d47a1; }
        .btn-secondary { background: #fff; color: #333; border: 1px solid #ddd; }
        .btn-secondary:hover { background: #f5f5f5; }
        
        .plan-badge { display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #ffd700, #ffb300); color: #333; border-radius: 20px; font-weight: bold; }
        
        .info-box { background: #e3f2fd; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #1565c0; }
        .info-box i { color: #1565c0; margin-right: 8px; }
        
        .quick-stats { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 20px; }
        .quick-stat { background: white; padding: 15px 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; min-width: 120px; }
        .quick-stat i { font-size: 1.5rem; color: #1565c0; margin-bottom: 5px; }
        
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .feature-card { background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .feature-card i { font-size: 2.5rem; color: #4caf50; margin-bottom: 15px; }
        .feature-card h4 { color: #1565c0; margin-bottom: 10px; }
        
        .section-title { font-size: 1.5rem; color: #1565c0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e3f0ff; }
        
        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
            .quick-stats { flex-direction: column; }
        }
    </style>
</head>
<body>
    <?php include 'template/headercliente.php'; ?>
    
    <!-- Barra de autenticación -->
    <div class="auth-toolbar" style="display:flex; justify-content:center; gap:1rem; padding:0.8rem; background:#f1f5ff; border-bottom:1px solid #e0e7ff;">
        <?php if (!$loggedIn): ?>
            <button id="googleSignIn" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:.5rem;">
                <i class="fab fa-google" style="color:#DB4437;"></i>
                Iniciar sesión con Google
            </button>
        <?php else: ?>
            <span style="align-self:center; color:#333;">
                <i class="fas fa-user-circle"></i> Bienvenido, <strong><?php echo htmlspecialchars($nombre); ?></strong>
            </span>
            <a href="logout.php" class="btn btn-secondary" style="text-decoration:none; display:inline-flex; align-items:center; gap:.5rem;">
                <i class="fas fa-sign-out-alt" style="color:#c62828;"></i>
                Cerrar sesión
            </a>
        <?php endif; ?>
    </div>
    
    <main class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Mi Seguimiento de Avance</h1>
            <p>Hola, <?php echo htmlspecialchars($nombre); ?>. Controla tu progreso y alcanza tus metas fitness.</p>
            
            <?php if ($loggedIn && $plan): ?>
                <div style="margin-top: 15px;">
                    <span class="plan-badge"><i class="fas fa-crown"></i> Plan <?php echo htmlspecialchars($plan['plan_nombre']); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Estadísticas Rápidas -->
        <div class="quick-stats">
            <div class="quick-stat">
                <i class="fas fa-dumbbell"></i>
                <div class="stat-number"><?php echo $progreso['rutinas_completadas'] ?? 0; ?></div>
                <div class="stat-label">Rutinas Completadas</div>
            </div>
            <div class="quick-stat">
                <i class="fas fa-fire"></i>
                <div class="stat-number"><?php echo $progreso['total_rutinas'] ?? 0; ?></div>
                <div class="stat-label">Rutinas Totales</div>
            </div>
            <div class="quick-stat">
                <i class="fas fa-calendar-check"></i>
                <div class="stat-number" style="font-size: 1.2rem;">
                    <?php echo $ultima_sesion ? date('d M', strtotime($ultima_sesion['fecha_creacion'])) : '--'; ?>
                </div>
                <div class="stat-label">Última Sesión</div>
            </div>
            <div class="quick-stat">
                <i class="fas fa-percentage"></i>
                <div class="stat-number">
                    <?php 
                    $porcentaje = ($progreso['total_rutinas'] > 0) ? round($progreso['rutinas_completadas'] / $progreso['total_rutinas'] * 100) : 0;
                    echo $porcentaje;
                    ?>%
                </div>
                <div class="stat-label">Progreso</div>
            </div>
        </div>
        
        <!-- Plan Actual y Progreso -->
        <div class="grid">
            <div class="card">
                <h3><i class="fas fa-crown"></i> Mi Plan</h3>
                <?php if ($plan): ?>
                    <div class="plan-badge"><?php echo htmlspecialchars($plan['plan_nombre']); ?></div>
                    <p style="margin-top: 15px; color: #666;"><?php echo htmlspecialchars($plan['descripcion']); ?></p>
                    <div style="margin-top: 15px; font-size: 2rem; font-weight: bold; color: #4caf50;">
                        $<?php echo number_format($plan['precio'], 2); ?>
                        <span style="font-size: 0.9rem; color: #666;">/mes</span>
                    </div>
                <?php else: ?>
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>No tienes un plan activo</p>
                    </div>
                    <a href="planes.php" class="btn" style="margin-top: 15px;">Ver Planes Disponibles</a>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-chart-pie"></i> Mi Progreso</h3>
                <div class="stat-number"><?php echo $progreso['rutinas_completadas'] ?? 0; ?></div>
                <div class="stat-label">rutinas completadas</div>
                <div class="progress-bar">
                    <?php 
                    $porcentaje = ($progreso['total_rutinas'] > 0) ? 
                        ($progreso['rutinas_completadas'] / $progreso['total_rutinas'] * 100) : 0;
                    ?>
                    <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%"></div>
                </div>
                <div class="stat-label"><?php echo round($porcentaje); ?>% de completion</div>
                
                <div class="info-box" style="margin-top: 20px;">
                    <i class="fas fa-trophy"></i>
                    <strong>Meta:</strong> Completa todas tus rutinas para obtener el máximo beneficio.
                </div>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-calendar-alt"></i> Última Sesión</h3>
                <?php if ($ultima_sesion): ?>
                    <div style="text-align: center; padding: 20px 0;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; color: #4caf50;"></i>
                        <div style="font-size: 1.5rem; font-weight: bold; margin-top: 10px; color: #1565c0;">
                            <?php echo date('d M, Y', strtotime($ultima_sesion['fecha_creacion'])); ?>
                        </div>
                        <div class="stat-label">Último entrenamiento completado</div>
                    </div>
                <?php else: ?>
                    <div class="info-box">
                        <i class="fas fa-running"></i>
                        <p>¡Aún no has iniciado tu primera rutina!</p>
                    </div>
                    <a href="rutinas.php" class="btn" style="margin-top: 15px;">Comenzar Ahora</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sección: Funciones del Sistema -->
        <h2 class="section-title"><i class="fas fa-star"></i> Funciones del Sistema</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-dumbbell"></i>
                <h4>Rutinas Personalizadas</h4>
                <p>Accede a rutinas adaptadas a tu nivel y objetivos</p>
                <a href="rutinas.php" class="btn" style="margin-top: 10px; font-size: 0.9rem;">Ver Rutinas</a>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h4>Seguimiento de Progreso</h4>
                <p>Registra y visualiza tu evolución día a día</p>
                <a href="progresos.php" class="btn" style="margin-top: 10px; font-size: 0.9rem;">Ver Progresos</a>
            </div>
            <div class="feature-card">
                <i class="fas fa-user-tie"></i>
                <h4>Entrenadores Certificados</h4>
                <p>Contacta con profesionales del deporte</p>
                <a href="entrenadores.php" class="btn" style="margin-top: 10px; font-size: 0.9rem;">Ver Entrenadores</a>
            </div>
            <div class="feature-card">
                <i class="fas fa-envelope"></i>
                <h4>Soporte Personalizado</h4>
                <p>Estamos aquí para ayudarte en tu journey</p>
                <a href="contacto.php" class="btn" style="margin-top: 10px; font-size: 0.9rem;">Contactar</a>
            </div>
        </div>
        
        <!-- Acciones Rápidas -->
        <h2 class="section-title" style="margin-top: 40px;"><i class="fas fa-bolt"></i> Acciones Rápidas</h2>
        <div class="grid">
            <div class="card">
                <h3><i class="fas fa-plus-circle"></i> Nueva Rutina</h3>
                <p style="color: #666; margin-bottom: 15px;">Inicia una nueva rutina de entrenamiento</p>
                <a href="rutinas.php" class="btn">Ir a Rutinas</a>
            </div>
            <div class="card">
                <h3><i class="fas fa-edit"></i> Registrar Progreso</h3>
                <p style="color: #666; margin-bottom: 15px;">Registra tu avance de hoy</p>
                <a href="progresos.php" class="btn">Ir a Progresos</a>
            </div>
            <div class="card">
                <h3><i class="fas fa-crown"></i> Actualizar Plan</h3>
                <p style="color: #666; margin-bottom: 15px;">Mejora tu plan de entrenamiento</p>
                <a href="planes.php" class="btn">Ver Planes</a>
            </div>
        </div>
    </main>
    
    <?php include 'template/footercliente.php'; ?>
    
    <!-- Script de Firebase para login -->
    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';
        import { getAuth, GoogleAuthProvider, signInWithPopup } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js';

        const firebaseConfig = {
            apiKey: "AIzaSyBZoUGrSk3V-yFW6QHxXLeXQfPMgnYUeQo",
            authDomain: "proyectoweb-fc2d2.firebaseapp.com",
            projectId: "proyectoweb-fc2d2",
            storageBucket: "proyectoweb-fc2d2.firebasestorage.app",
            messagingSenderId: "508269230145",
            appId: "1:508269230145:web:d183a7c70873785487eec0"
        };
        
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        async function saveUserToDatabase(user) {
            const userData = {
                uid: user.uid,
                name: user.displayName,
                email: user.email,
                photoURL: user.photoURL,
                emailVerified: user.emailVerified,
                rol: 'cliente'
            };
            const response = await fetch('./login/guardar_usuario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });
            return response.ok;
        }

        async function getUserRole(uid) {
            const response = await fetch('./login/obtener_rol.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ uid })
            });
            if (!response.ok) return 'cliente';
            const result = await response.json();
            return result.success ? result.rol : 'cliente';
        }

        const googleSignInBtn = document.getElementById('googleSignIn');
        if (googleSignInBtn) {
            googleSignInBtn.addEventListener('click', async () => {
                try {
                    const result = await signInWithPopup(auth, provider);
                    const user = result.user;
                    
                    // Guardar usuario en la base de datos
                    await saveUserToDatabase(user);
                    const userRole = await getUserRole(user.uid);
                    
                    // Redireccionar según el rol
                    if (userRole === 'admin') {
                        window.location.href = 'admin.php';
                    } else {
                        window.location.href = 'cliente.php';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al iniciar sesión: ' + error.message);
                }
            });
        }
    </script>
</body>
</html>
