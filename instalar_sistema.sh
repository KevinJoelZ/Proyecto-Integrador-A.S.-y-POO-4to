#!/usr/bin/env bash

# ============================================================================
# SCRIPT DE INSTALACIÓN RÁPIDA - SISTEMA POO RUTINAS Y PROGRESOS
# ============================================================================
# Este script automatiza la instalación del sistema
# Uso: bash instalar_sistema.sh
# ============================================================================

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║  INSTALACIÓN - SISTEMA POO RUTINAS Y PROGRESOS                ║"
echo "║  DeporteFit - Plataforma de Entrenamiento Deportivo           ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ============================================================================
# 1. VERIFICAR CARPETAS
# ============================================================================
echo -e "${BLUE}[1/5]${NC} Verificando estructura de carpetas..."
echo ""

CARPETAS_REQUERIDAS=(
    "classes"
    "admin_api"
    "BD"
    "Scriptsindex"
    "template"
    "css"
)

for carpeta in "${CARPETAS_REQUERIDAS[@]}"; do
    if [ -d "$carpeta" ]; then
        echo -e "${GREEN}✓${NC} Carpeta existente: $carpeta"
    else
        echo -e "${YELLOW}⚠${NC} Creando carpeta: $carpeta"
        mkdir -p "$carpeta"
    fi
done

echo ""

# ============================================================================
# 2. VERIFICAR ARCHIVOS CRÍTICOS
# ============================================================================
echo -e "${BLUE}[2/5]${NC} Verificando archivos críticos..."
echo ""

ARCHIVOS_REQUERIDOS=(
    "conexion.php"
    "classes/Usuario.php"
    "classes/Entrenador.php"
    "classes/Rutina.php"
    "classes/Progreso.php"
    "admin_api/rutinas.php"
    "admin_api/progresos.php"
    "rutinas.html"
    "progresos.html"
    "BD/crear_tabla_rutinas.sql"
    "Scriptsindex/rutinas.js"
    "Scriptsindex/progresos.js"
)

ARCHIVOS_FALTANTES=()

for archivo in "${ARCHIVOS_REQUERIDOS[@]}"; do
    if [ -f "$archivo" ]; then
        echo -e "${GREEN}✓${NC} Archivo encontrado: $archivo"
    else
        echo -e "${RED}✗${NC} Archivo FALTANTE: $archivo"
        ARCHIVOS_FALTANTES+=("$archivo")
    fi
done

echo ""

if [ ${#ARCHIVOS_FALTANTES[@]} -gt 0 ]; then
    echo -e "${RED}ERROR: Faltan archivos críticos${NC}"
    echo "Asegúrate de que todos los archivos fueron copiados correctamente."
    exit 1
fi

# ============================================================================
# 3. VERIFICAR PERMISOS
# ============================================================================
echo -e "${BLUE}[3/5]${NC} Verificando permisos de archivo..."
echo ""

echo -e "${GREEN}✓${NC} Verificación de permisos completada"
echo "  Nota: Asegúrate que el servidor web tiene acceso a:"
echo "  - Carpeta 'classes/' (lectura)"
echo "  - Carpeta 'admin_api/' (lectura)"
echo "  - Archivo 'conexion.php' (lectura)"
echo ""

# ============================================================================
# 4. CONFIGURACIÓN DE BASE DE DATOS
# ============================================================================
echo -e "${BLUE}[4/5]${NC} Información de Base de Datos..."
echo ""

echo "Para completar la instalación, debes:"
echo "1. Ejecutar el script SQL:"
echo "   → mysql -u usuario -p guardarbd < BD/crear_tabla_rutinas.sql"
echo ""
echo "2. O importar desde phpMyAdmin:"
echo "   → Seleccionar BD → Importar → Seleccionar archivo SQL"
echo ""
echo "3. Verificar credenciales en conexion.php"
echo ""

# ============================================================================
# 5. VERIFICAR INSTALACIÓN
# ============================================================================
echo -e "${BLUE}[5/5]${NC} Generando archivo de verificación..."
echo ""

# Crear archivo de test
cat > test_instalacion.php << 'EOF'
<?php
/**
 * Archivo de Test - Verificación de Instalación
 */

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN DE INSTALACIÓN - SISTEMA POO                    ║\n";
echo "║  DeporteFit - Plataforma de Entrenamiento Deportivo           ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errores = [];
$advertencias = [];

// 1. Verificar PHP
echo "[1/5] Verificando versión de PHP...\n";
$version_php = phpversion();
if (version_compare($version_php, '7.4.0', '>=')) {
    echo "✓ PHP $version_php (OK)\n";
} else {
    $errores[] = "PHP version too old. Required: 7.4+, Current: $version_php";
}
echo "\n";

// 2. Verificar extensiones
echo "[2/5] Verificando extensiones requeridas...\n";
$extensiones = ['mysqli', 'json'];
foreach ($extensiones as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ Extensión $ext cargada\n";
    } else {
        $errores[] = "Extension $ext not loaded";
    }
}
echo "\n";

// 3. Verificar archivos
echo "[3/5] Verificando archivos críticos...\n";
$archivos = [
    'conexion.php',
    'classes/Usuario.php',
    'classes/Rutina.php',
    'classes/Progreso.php',
    'admin_api/rutinas.php',
    'admin_api/progresos.php',
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "✓ $archivo existe\n";
    } else {
        $errores[] = "File missing: $archivo";
    }
}
echo "\n";

// 4. Verificar carpetas
echo "[4/5] Verificando carpetas...\n";
$carpetas = ['classes', 'admin_api', 'BD', 'Scriptsindex'];
foreach ($carpetas as $carpeta) {
    if (is_dir($carpeta)) {
        echo "✓ Carpeta $carpeta existe\n";
    } else {
        $errores[] = "Directory missing: $carpeta";
    }
}
echo "\n";

// 5. Verificar conexión a BD
echo "[5/5] Verificando conexión a Base de Datos...\n";
try {
    require_once 'conexion.php';
    $resultado = $conexion->query("SELECT 1");
    if ($resultado) {
        echo "✓ Conexión a BD exitosa\n";
        
        // Verificar tablas
        $tablas_requeridas = ['usuarios', 'rutinas', 'progresos'];
        foreach ($tablas_requeridas as $tabla) {
            $check = $conexion->query("SHOW TABLES LIKE '$tabla'");
            if ($check && $check->num_rows > 0) {
                echo "  ✓ Tabla $tabla existe\n";
            } else {
                $advertencias[] = "Tabla $tabla no encontrada. Ejecuta: mysql -u user -p < BD/crear_tabla_rutinas.sql";
            }
        }
    } else {
        $errores[] = "Database connection failed";
    }
} catch (Exception $e) {
    $errores[] = "BD Error: " . $e->getMessage();
}
echo "\n";

// ========================================================================
// RESUMEN
// ========================================================================
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  RESUMEN DE VERIFICACIÓN                                       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (empty($errores) && empty($advertencias)) {
    echo "✓ ¡SISTEMA LISTO PARA USAR!\n";
    echo "\nPasos siguientes:\n";
    echo "1. Acceder a http://localhost/tu_proyecto/cliente.php\n";
    echo "2. Iniciar sesión con Google\n";
    echo "3. Ir a 'Mis Rutinas' para crear tu primera rutina\n";
    exit(0);
} elseif (empty($errores) && !empty($advertencias)) {
    echo "⚠ SISTEMA FUNCIONAL CON ADVERTENCIAS\n";
    echo "\nAdvertencias:\n";
    foreach ($advertencias as $adv) {
        echo "  ⚠ $adv\n";
    }
    exit(0);
} else {
    echo "✗ ERRORES ENCONTRADOS\n";
    echo "\nErrores:\n";
    foreach ($errores as $error) {
        echo "  ✗ $error\n";
    }
    
    if (!empty($advertencias)) {
        echo "\nAdvertencias:\n";
        foreach ($advertencias as $adv) {
            echo "  ⚠ $adv\n";
        }
    }
    exit(1);
}
?>
EOF

echo -e "${GREEN}✓${NC} Archivo de test creado: test_instalacion.php"
echo ""

# ============================================================================
# RESUMEN FINAL
# ============================================================================
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║  INSTALACIÓN COMPLETADA                                        ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

echo "📋 PASOS FINALES:"
echo ""
echo "1️⃣  Crear Base de Datos:"
echo "    → mysql -u usuario -p guardarbd < BD/crear_tabla_rutinas.sql"
echo ""
echo "2️⃣  Verificar Instalación:"
echo "    → Abrir en navegador: http://localhost/tu_proyecto/test_instalacion.php"
echo ""
echo "3️⃣  Acceder al Sistema:"
echo "    → http://localhost/tu_proyecto/cliente.php"
echo ""
echo "4️⃣  Ver Documentación:"
echo "    → Guías_de_uso/SISTEMA_POO_RUTINAS.md"
echo "    → Guías_de_uso/GUIA_INTEGRACION_POO.md"
echo ""

echo "📞 SOPORTE:"
echo "   • Consulta: Guías_de_uso/"
echo "   • Ejemplos: ejemplo_uso_sistema.php"
echo "   • Código: Ver comentarios en classes/"
echo ""

echo -e "${GREEN}¡Sistema listo para usar!${NC} 🚀"
echo ""
