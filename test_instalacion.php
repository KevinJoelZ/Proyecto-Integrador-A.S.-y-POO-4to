<?php
/**
 * Archivo de Test - Verificación de Instalación del Sistema POO
 * Acceso: http://localhost/proyecto/test_instalacion.php
 */

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN DE INSTALACIÓN - SISTEMA POO                    ║\n";
echo "║  DeporteFit - Plataforma de Entrenamiento Deportivo           ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errores = [];
$advertencias = [];
$exitos = 0;

// ========================================================================
// 1. VERIFICAR VERSIÓN DE PHP
// ========================================================================
echo "[1/5] Verificando versión de PHP...\n";
$version_php = phpversion();
if (version_compare($version_php, '7.4.0', '>=')) {
    echo "✓ PHP $version_php (OK)\n";
    $exitos++;
} else {
    $errores[] = "PHP versión antigua. Requerido: 7.4+, Actual: $version_php";
    echo "✗ PHP $version_php (Versión muy antigua)\n";
}
echo "\n";

// ========================================================================
// 2. VERIFICAR EXTENSIONES REQUERIDAS
// ========================================================================
echo "[2/5] Verificando extensiones requeridas...\n";
$extensiones = [
    'mysqli' => 'Conexión a MySQL',
    'json' => 'Procesamiento JSON',
    'filter' => 'Validación de datos'
];

foreach ($extensiones as $ext => $descripcion) {
    if (extension_loaded($ext)) {
        echo "✓ $ext - $descripcion\n";
        $exitos++;
    } else {
        echo "✗ $ext - $descripcion (NO CARGADA)\n";
        $errores[] = "Extension $ext no cargada";
    }
}
echo "\n";

// ========================================================================
// 3. VERIFICAR ARCHIVOS CRÍTICOS
// ========================================================================
echo "[3/5] Verificando archivos críticos...\n";
$archivos = [
    'conexion.php' => 'Configuración de conexión',
    'classes/Usuario.php' => 'Clase Usuario',
    'classes/Entrenador.php' => 'Clase Entrenador',
    'classes/Rutina.php' => 'Clase Rutina',
    'classes/Progreso.php' => 'Clase Progreso',
    'admin_api/rutinas.php' => 'API Rutinas',
    'admin_api/progresos.php' => 'API Progresos',
    'rutinas.html' => 'Página de Rutinas',
    'progresos.html' => 'Página de Progresos',
    'Scriptsindex/rutinas.js' => 'Script Rutinas',
    'Scriptsindex/progresos.js' => 'Script Progresos',
];

foreach ($archivos as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        echo "✓ $archivo - $descripcion\n";
        $exitos++;
    } else {
        echo "✗ $archivo - $descripcion (FALTANTE)\n";
        $errores[] = "Archivo faltante: $archivo";
    }
}
echo "\n";

// ========================================================================
// 4. VERIFICAR CARPETAS
// ========================================================================
echo "[4/5] Verificando carpetas...\n";
$carpetas = [
    'classes' => 'Clases PHP',
    'admin_api' => 'APIs REST',
    'BD' => 'Scripts de BD',
    'Scriptsindex' => 'Scripts JavaScript',
    'css' => 'Estilos CSS'
];

foreach ($carpetas as $carpeta => $descripcion) {
    if (is_dir($carpeta)) {
        echo "✓ $carpeta/ - $descripcion\n";
        $exitos++;
    } else {
        echo "✗ $carpeta/ - $descripcion (NO EXISTE)\n";
        $errores[] = "Carpeta faltante: $carpeta";
    }
}
echo "\n";

// ========================================================================
// 5. VERIFICAR CONEXIÓN A BASE DE DATOS
// ========================================================================
echo "[5/5] Verificando conexión a Base de Datos...\n";
try {
    if (file_exists('conexion.php')) {
        require_once 'conexion.php';
        
        // Verificar que la conexión no tiene errores
        if ($conexion->connect_error) {
            throw new Exception("Error de conexión: " . $conexion->connect_error);
        }
        
        echo "✓ Conexión a BD establecida\n";
        $exitos++;
        
        // Verificar tablas requeridas
        echo "\nVerificando tablas...\n";
        $tablas_requeridas = [
            'usuarios' => 'Información de usuarios',
            'rutinas' => 'Rutinas de entrenamiento',
            'entrenadores' => 'Datos de entrenadores',
            'progresos' => 'Registro de progresos',
            'ejercicios' => 'Ejercicios de rutinas',
            'planes' => 'Planes predefinidos'
        ];
        
        foreach ($tablas_requeridas as $tabla => $descripcion) {
            $check = $conexion->query("SHOW TABLES LIKE '$tabla'");
            if ($check && $check->num_rows > 0) {
                echo "  ✓ $tabla - $descripcion\n";
                $exitos++;
            } else {
                echo "  ✗ $tabla - $descripcion (NO EXISTE)\n";
                $advertencias[] = "Tabla $tabla no encontrada. Ejecuta el script SQL.";
            }
        }
    } else {
        $errores[] = "Archivo conexion.php no encontrado";
        echo "✗ No se puede verificar BD (falta conexion.php)\n";
    }
} catch (Exception $e) {
    echo "✗ Error de BD: " . $e->getMessage() . "\n";
    $errores[] = "Error de BD: " . $e->getMessage();
}
echo "\n";

// ========================================================================
// RESUMEN FINAL
// ========================================================================
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  RESUMEN DE VERIFICACIÓN                                       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "Verificaciones exitosas: $exitos\n";
echo "Errores encontrados: " . count($errores) . "\n";
echo "Advertencias: " . count($advertencias) . "\n";
echo "\n";

// ========================================================================
// RESULTADO
// ========================================================================
if (empty($errores) && empty($advertencias)) {
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  ✓ ¡SISTEMA LISTO PARA USAR!                                  ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "PRÓXIMOS PASOS:\n";
    echo "1. Acceder a: http://localhost/tu_proyecto/cliente.php\n";
    echo "2. Iniciar sesión con Google\n";
    echo "3. Hacer click en 'Mis Rutinas' para crear tu primera rutina\n";
    echo "4. Hacer click en 'Mi Progreso' para registrar entrenamientos\n";
    echo "\n";
    echo "DOCUMENTACIÓN:\n";
    echo "• Guía de Integración: Guías_de_uso/GUIA_INTEGRACION_POO.md\n";
    echo "• Documentación Técnica: Guías_de_uso/SISTEMA_POO_RUTINAS.md\n";
    echo "• Ejemplos de Código: ejemplo_uso_sistema.php\n";
    echo "\n";
    
} elseif (empty($errores) && !empty($advertencias)) {
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  ⚠ SISTEMA FUNCIONAL CON ADVERTENCIAS                          ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "ADVERTENCIAS:\n";
    foreach ($advertencias as $i => $adv) {
        echo ($i + 1) . ". $adv\n";
    }
    echo "\nPara resolver las advertencias:\n";
    echo "1. Ejecutar script SQL: mysql -u usuario -p < BD/crear_tabla_rutinas.sql\n";
    echo "2. Verificar credenciales en conexion.php\n";
    echo "\n";
    echo "El sistema debería funcionar una vez resueltas estas advertencias.\n";
    echo "\n";
    
} else {
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  ✗ ERRORES ENCONTRADOS - REVISAR ABAJO                         ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    
    if (!empty($errores)) {
        echo "ERRORES CRÍTICOS:\n";
        foreach ($errores as $i => $error) {
            echo ($i + 1) . ". $error\n";
        }
        echo "\n";
    }
    
    if (!empty($advertencias)) {
        echo "ADVERTENCIAS:\n";
        foreach ($advertencias as $i => $adv) {
            echo ($i + 1) . ". $adv\n";
        }
        echo "\n";
    }
    
    echo "SOLUCIÓN:\n";
    echo "1. Asegúrate que todos los archivos están en el lugar correcto\n";
    echo "2. Verifica que conexion.php tiene las credenciales correctas\n";
    echo "3. Ejecuta el script SQL: mysql -u usuario -p < BD/crear_tabla_rutinas.sql\n";
    echo "4. Recarga esta página para verificar nuevamente\n";
    echo "\n";
}

echo "═════════════════════════════════════════════════════════════════\n";
echo "Sistema: DeporteFit POO Rutinas v1.0\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "═════════════════════════════════════════════════════════════════\n";
?>
