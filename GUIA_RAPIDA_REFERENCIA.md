# ⚡ GUÍA RÁPIDA DE REFERENCIA - SISTEMA POO

**Versión:** 1.0  
**Última actualización:** 26 de enero de 2026

---

## 🚀 INICIO RÁPIDO (5 Minutos)

### 1. Importar Base de Datos
```bash
mysql -u usuario -p base_datos < BD/crear_tabla_rutinas.sql
```

### 2. Verificar Instalación
```
Abrir navegador: http://localhost/proyecto/test_instalacion.php
Resultado esperado: Todos los ✓ en verde
```

### 3. Acceder
```
http://localhost/proyecto/cliente.php
→ Click botón Google
→ Click "Mis Rutinas"
→ ¡Listo!
```

---

## 📚 ESTRUCTURA DE CARPETAS

```
proyecto/
├── classes/              ← Clases POO
├── admin_api/            ← APIs REST
├── BD/                   ← Base de datos
├── Scriptsindex/         ← JavaScript
├── Guías_de_uso/         ← Documentación
├── rutinas.html          ← Página rutinas
├── progresos.html        ← Página progresos
└── template/maincliente.php ← Menú principal
```

---

## 🔧 CLASES POO (Referencia)

### Usuario.php
```php
$usuario = new Usuario($conexion);
$usuario->setNombre('Juan');
$usuario->guardar();
$usuario->obtenerPorUid($uid);
```

### Rutina.php
```php
$rutina = new Rutina($conexion);
$rutina->setNombre('Mi Rutina');
$rutina->setDeporte('Fitness');
$rutina->crear();
$rutina->obtenerPorUsuario($usuario_id);
$rutina->cambiarEstado($id, 'pausada');
```

### Progreso.php
```php
$progreso = new Progreso($conexion);
$progreso->setTipoMedida('peso');
$progreso->registrar();
$progreso->obtenerEstadisticas($usuario_id, 'peso');
$progreso->calcularMejora($usuario_id, 'peso', 30);
```

---

## 🔌 APIs (Endpoints)

### Rutinas
```
GET  /admin_api/rutinas.php?action=obtener&usuario_id=1
POST /admin_api/rutinas.php?action=crear
PUT  /admin_api/rutinas.php?action=actualizar
DEL  /admin_api/rutinas.php?action=eliminar&id=1
```

### Progresos
```
GET  /admin_api/progresos.php?action=obtener&usuario_id=1
POST /admin_api/progresos.php?action=registrar
GET  /admin_api/progresos.php?action=estadisticas&usuario_id=1
GET  /admin_api/progresos.php?action=mejora&usuario_id=1&dias=30
```

---

## 💻 JavaScript (Referencia)

### Cargar Rutinas
```javascript
const gestor = new GestorRutinas();
gestor.cargarRutinas(); // Automático en constructor
```

### Crear Rutina
```javascript
const datos = {
    nombre: "Mi Rutina",
    deporte: "Fitness",
    nivel: "intermedio",
    objetivo: "ganar_masa"
};
gestor.guardarRutina(datos);
```

### Registrar Progreso
```javascript
const progreso = new GestorProgresos();
const datos = {
    usuario_id: 1,
    rutina_id: 5,
    tipo_medida: "peso",
    valor_actual: 75.5,
    esfuerzo: 7
};
progreso.guardarProgreso(datos);
```

---

## 🎨 Estilos y Colores

| Elemento | Color | Hex |
|----------|-------|-----|
| Primario | Azul | #2563eb |
| Éxito | Verde | #10b981 |
| Acento | Naranja | #f97316 |
| Peligro | Rojo | #ef4444 |
| Fondo | Gris | #f3f4f6 |

---

## 📱 Puntos de Ruptura Responsive

```css
/* Desktop */
1200px y mayor

/* Tablet */
768px - 1199px

/* Mobile */
320px - 767px
```

---

## 🏃 Flujo Básico Usuario

```
1. Login (Google)
2. Ver "Mis Rutinas"
3. Crear rutina → Aparecer en grid
4. Click en rutina → Ver detalles
5. Ir a "Mi Progreso"
6. Registrar progreso
7. Ver estadísticas
```

---

## ❌ Errores Comunes

| Error | Solución |
|-------|----------|
| "Tabla no encontrada" | Ejecutar SQL en BD |
| "UID inválido" | Verificar autenticación Firebase |
| "No se ve estilos" | Limpiar cache (Ctrl+Shift+Del) |
| "API responde 404" | Verificar ruta de carpeta admin_api |
| "Error CORS" | Verificar mismo dominio |

---

## 🔐 Seguridad

✅ **Obligatorio:**
- Usar Prepared Statements (ya implementado)
- Validar en servidor (ya implementado)
- Autenticación Firebase (ya implementado)
- Sanitizar HTML (ya implementado)

✅ **Recomendado:**
- HTTPS en producción
- Rate limiting en APIs
- Backup diario de BD

---

## 📊 Tipos de Medida (Progresos)

```
• peso         (kg/lbs)
• distancia    (km/mi)
• tiempo       (minutos)
• series       (repeticiones)
• repeticiones (número)
• velocidad    (km/h)
• fuerza       (kg levantado)
• resistencia  (escala 1-10)
```

---

## 🏋️ Deportes Soportados

```
• Fitness & Musculación
• Running & Maratón
• Natación
• Ciclismo
• Yoga & Pilates
• Fútbol
```

---

## 📈 Niveles de Dificultad

```
• Principiante (nivel 1)
• Intermedio   (nivel 2)
• Avanzado     (nivel 3)
```

---

## 🎯 Estados de Rutina

```
• activa      (en progreso)
• pausada     (pausada)
• completada  (finalizada)
```

---

## 📄 Archivos Principales

| Archivo | Propósito | Tamaño |
|---------|----------|--------|
| `classes/Usuario.php` | Gestión usuarios | 150 líneas |
| `classes/Rutina.php` | Gestión rutinas | 220 líneas |
| `classes/Progreso.php` | Registro progresos | 200 líneas |
| `admin_api/rutinas.php` | API rutinas | 280 líneas |
| `admin_api/progresos.php` | API progresos | 300 líneas |
| `rutinas.html` | Interfaz rutinas | 350 líneas |
| `progresos.html` | Interfaz progresos | 320 líneas |
| `Scriptsindex/rutinas.js` | Lógica rutinas | 650 líneas |
| `Scriptsindex/progresos.js` | Lógica progresos | 700 líneas |
| `BD/crear_tabla_rutinas.sql` | Schema BD | 400+ líneas |

---

## 🧪 Testing

### Verificar Sistema
```
http://localhost/proyecto/test_instalacion.php
```

### Probar API en Consola
```javascript
fetch('./admin_api/rutinas.php?action=obtener&usuario_id=1')
    .then(r => r.json())
    .then(d => console.log(d))
```

### Ver Logs
- Browser console: F12
- Server logs: `/var/log/apache2/error.log`

---

## 📞 Documentación Disponible

| Documento | Ubicación | Para |
|-----------|-----------|------|
| README_SISTEMA_POO.md | Root | General |
| RESUMEN_EJECUTIVO.md | Root | Directivos |
| SISTEMA_POO_RUTINAS.md | Guías_de_uso/ | Técnicos |
| GUIA_INTEGRACION_POO.md | Guías_de_uso/ | Instaladores |
| DIAGRAMAS_FLUJOS.md | Root | Arquitectos |
| INVENTARIO_ARCHIVOS.md | Root | Verificación |
| ejemplo_uso_sistema.php | Root | Desarrolladores |

---

## 🔍 Debugging

### Habilitar Errores PHP
```php
// Al inicio de un archivo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

### Ver Errores BD
```php
$conexion = new mysqli(...);
if ($conexion->connect_error) {
    die("Error: " . $conexion->connect_error);
}
```

### Logs JavaScript
```javascript
console.log('Debug:', variable);
console.error('Error:', error);
console.table(array_de_datos);
```

---

## 🚀 Deployment Checklist

- [ ] BD creada con SQL
- [ ] Conexión verificada (test_instalacion.php)
- [ ] Permisos de carpetas correctos
- [ ] Firebase config actual
- [ ] URLs correctas
- [ ] HTTPS activo
- [ ] Backups configurados
- [ ] Monitoring activo
- [ ] Usuarios pueden crear rutinas
- [ ] Usuarios pueden registrar progresos

---

## 💡 Tips Útiles

### Performance
- Minimizar requests API
- Cache en localStorage
- Lazy loading en grids

### UX
- Mostrar loading spinner
- Confirmar antes de eliminar
- Validar en cliente
- Mensajes de error claros

### Mantenimiento
- Backup diario de BD
- Revisar logs semanalmente
- Actualizar dependencias

---

## 📞 Contacto Rápido

| Aspecto | Contacto |
|--------|----------|
| Bugs | GitHub Issues |
| Documentación | Ver Guías_de_uso/ |
| Soporte | test_instalacion.php |
| Código | Ver clases/ y admin_api/ |

---

## 🎓 Próximas Características

- [ ] Chart.js para gráficos
- [ ] Email notifications
- [ ] App móvil
- [ ] IA recommendations
- [ ] Wearables integration
- [ ] Social features
- [ ] Payments

---

## 📋 Última Actualización

**Fecha:** 26 de enero de 2026  
**Versión:** 1.0  
**Estado:** ✅ Producción  
**Archivos:** 20 nuevos  
**Líneas:** 5400+  

---

**¡Guía Rápida Completa!** 🎉

*Para más información, ver README_SISTEMA_POO.md*
