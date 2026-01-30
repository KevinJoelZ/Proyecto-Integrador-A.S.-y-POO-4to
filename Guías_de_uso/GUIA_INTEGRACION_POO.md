# 📚 Guía de Integración del Sistema POO

## ✅ Verificación de Instalación

Antes de usar el sistema, asegúrate de completar estos pasos:

### 1️⃣ Crear las Tablas de Base de Datos

Ejecuta el archivo SQL en tu gestor de base de datos:

```bash
# Desde terminal
mysql -u usuario -p nombre_base_datos < BD/crear_tabla_rutinas.sql

# O importa desde phpMyAdmin
1. Ir a Panel de Control → phpMyAdmin
2. Seleccionar base de datos
3. Click en "Importar"
4. Seleccionar archivo: BD/crear_tabla_rutinas.sql
5. Click en "Ejecutar"
```

**Tablas creadas:**
- usuarios
- entrenadores
- rutinas
- ejercicios
- progresos
- resultados
- planes
- suscripciones

---

### 2️⃣ Verificar Conexión a Base de Datos

Editar archivo `conexion.php` con tus credenciales:

```php
<?php
$host = 'localhost';      // Tu servidor MySQL
$usuario = 'root';        // Tu usuario MySQL
$contraseña = '';         // Tu contraseña
$base_datos = 'guardarbd'; // Tu base de datos

// El resto no necesita cambios
?>
```

**Prueba la conexión:**
```bash
# Desde navegador
http://localhost/tu_proyecto/test_db.php
```

---

### 3️⃣ Verificar Autenticación Firebase

Las páginas de rutinas y progresos requieren autenticación Firebase.

**Configuración ya incluida en:**
- `rutinas.html` (línea ~250)
- `progresos.html` (línea ~250)

**No es necesario cambiar nada** si usas las mismas credenciales Firebase.

---

## 🔄 Flujo de Uso

```
┌─────────────────────────────────────────────────────────┐
│                    USUARIO ACCEDE                       │
│                  (Firebase Auth)                        │
└─────────────────┬───────────────────────────────────────┘
                  │
        ┌─────────▼──────────┐
        │  PÁGINA PRINCIPAL  │
        │ (cliente.html)     │
        └─────────┬──────────┘
                  │
     ┌────────────┼────────────┐
     │            │            │
     ▼            ▼            ▼
┌────────┐  ┌──────────┐  ┌──────────┐
│Rutinas │  │Progresos │  │Servicios │
│        │  │          │  │          │
└──┬─────┘  └────┬─────┘  └──────────┘
   │             │
   │      ┌──────▼──────┐
   │      │  API REST   │
   │      │  /admin_api │
   │      └──────┬──────┘
   │             │
   └─────────────┼────────────┐
                 │            │
            ┌────▼───┐   ┌────▼──────┐
            │Rutinas │   │Progresos  │
            │.php    │   │.php       │
            └────┬───┘   └────┬──────┘
                 │            │
                 └─────┬──────┘
                       │
              ┌────────▼────────┐
              │  Base de Datos  │
              │   MySQL         │
              └─────────────────┘
```

---

## 🎯 Pasos para Crear Primera Rutina

### Por Interfaz Web:

1. **Acceder a la aplicación**
   ```
   http://localhost/tu_proyecto/cliente.php
   ```

2. **Iniciar sesión con Google**
   - Click en "Iniciar sesión con Google"
   - Completar autenticación

3. **Ir a Mis Rutinas**
   - Click en botón "Mis Rutinas" (en hero section)
   - O acceder a: `http://localhost/tu_proyecto/rutinas.html`

4. **Crear nueva rutina**
   - Click en "Nueva Rutina"
   - Llenar formulario:
     - Nombre: "Mi Primera Rutina"
     - Deporte: "Fitness"
     - Objetivo: "Ganar fuerza"
     - Nivel: "Principiante"
     - Duración: 8 semanas
     - Frecuencia: 3 días/semana
   - Click en "Guardar Rutina"

5. **Ver rutina creada**
   - Rutina aparece en grid principal
   - Puedes ver detalles, editar o eliminar

---

## 📝 Pasos para Registrar Progreso

1. **Ir a Mi Progreso**
   - Click en "Mi Progreso" (en hero section)
   - O acceder a: `http://localhost/tu_proyecto/progresos.html`

2. **Registrar nuevo progreso**
   - Click en "Registrar Progreso"
   - Llenar formulario:
     - Tipo de medida: "peso" (o seleccionar otra)
     - Valor actual: 75.5
     - Valor objetivo: 80.0
     - Fecha: (hoy automático)
     - Esfuerzo: Mover slider a 7
     - Notas: "Entrenamientos yendo bien"
   - Click en "Registrar Progreso"

3. **Ver análisis**
   - Verás registros en grid principal
   - Click en tab "Gráficos" para ver estadísticas
   - Automáticamente calcula:
     - Promedio
     - Máximo y mínimo
     - Porcentaje completado

---

## 🔧 Personalización

### Agregar Nuevo Deporte

1. **Actualizar HTML en `rutinas.html`:**

```html
<!-- Línea ~200 en rutinas.html -->
<select id="deporte" required>
    <option value="">Selecciona un deporte</option>
    <option value="Fitness">Fitness & Musculación</option>
    <option value="Running">Running & Maratón</option>
    <option value="Natación">Natación</option>
    <option value="Ciclismo">Ciclismo</option>
    <option value="Yoga">Yoga & Pilates</option>
    <option value="Fútbol">Fútbol</option>
    <!-- AGREGAR AQUÍ -->
    <option value="Tenis">Tenis</option>
</select>
```

2. **Agregar icono en `rutinas.js`:**

```javascript
// Línea ~450 en rutinas.js
obtenerIconoDeporte(deporte) {
    const iconos = {
        'Fitness': '<i class="fas fa-dumbbell"></i>',
        'Running': '<i class="fas fa-running"></i>',
        'Natación': '<i class="fas fa-swimming-pool"></i>',
        'Ciclismo': '<i class="fas fa-bicycle"></i>',
        'Yoga': '<i class="fas fa-yoga"></i>',
        'Fútbol': '<i class="fas fa-futbol"></i>',
        'Tenis': '<i class="fas fa-table-tennis"></i>' // NUEVO
    };
    return iconos[deporte] || '<i class="fas fa-dumbbell"></i>';
}
```

---

### Agregar Nuevo Tipo de Medida

1. **Actualizar HTML en `progresos.html`:**

```html
<!-- Línea ~200 en progresos.html -->
<select id="tipoMedida" required>
    <option value="">Selecciona una medida</option>
    <option value="peso">Peso (kg)</option>
    <option value="distancia">Distancia (km)</option>
    <option value="tiempo">Tiempo (min)</option>
    <option value="series">Series (unidades)</option>
    <option value="repeticiones">Repeticiones (unidades)</option>
    <option value="velocidad">Velocidad (km/h)</option>
    <option value="fuerza">Fuerza (kg)</option>
    <option value="resistencia">Resistencia (min)</option>
    <!-- AGREGAR AQUÍ -->
    <option value="flexiones">Flexiones (unidades)</option>
</select>
```

---

## 🐛 Debugging

### Ver Errores en Consola

1. **Abrir Developer Tools:**
   - Windows/Linux: `F12` o `Ctrl+Shift+I`
   - Mac: `Cmd+Option+I`

2. **Ir a la pestaña "Consola"**

3. **Buscar errores en rojo**

---

### Ver Respuestas de API

Agregar en navegador:
```javascript
// En consola del navegador
fetch('./admin_api/rutinas.php?action=obtener&usuario_id=1')
    .then(r => r.json())
    .then(data => console.log(data))
```

---

### Probar Conexión DB

```php
<!-- Crear archivo: test_conexion_classes.php -->
<?php
require_once 'conexion.php';
require_once 'classes/Usuario.php';

$usuario = new Usuario($conexion);
$usuarios = $usuario->obtenerTodos();

if (count($usuarios) > 0) {
    echo "✓ Conexión OK. Usuarios en DB: " . count($usuarios);
} else {
    echo "✓ Conexión OK. Sin usuarios en DB.";
}
?>
```

---

## 📱 Responsive Design

El sistema está optimizado para:

- ✅ **Desktop** (1920x1080)
- ✅ **Tablet** (768x1024)
- ✅ **Mobile** (320x480)

**Breakpoints:**
```css
@media (max-width: 768px) { /* Tablet */ }
@media (max-width: 480px) { /* Mobile */ }
```

---

## 🔐 Mantenimiento

### Backup de Datos

```bash
# Exportar base de datos
mysqldump -u usuario -p guardarbd > backup_rutinas.sql

# Restaurar base de datos
mysql -u usuario -p guardarbd < backup_rutinas.sql
```

---

### Limpiar Datos de Prueba

```sql
-- Eliminar progresos de prueba
DELETE FROM progresos WHERE usuario_id = 1;

-- Eliminar rutinas de prueba
DELETE FROM rutinas WHERE usuario_id = 1;

-- Resetear auto_increment
ALTER TABLE rutinas AUTO_INCREMENT = 1;
```

---

## 📞 Soporte

### Documentación Disponible:
1. `Guías_de_uso/SISTEMA_POO_RUTINAS.md` - Documentación técnica
2. `ejemplo_uso_sistema.php` - Ejemplos de código
3. Comentarios en código fuente

### Archivos Importantes:
- `classes/` - Clases POO
- `admin_api/` - APIs REST
- `BD/crear_tabla_rutinas.sql` - Script de BD
- `Scriptsindex/rutinas.js` - JavaScript frontend
- `Scriptsindex/progresos.js` - JavaScript frontend

---

## ✨ Checklist de Verificación

- [ ] Base de datos creada y accesible
- [ ] Archivo `conexion.php` configurado correctamente
- [ ] Clases PHP en carpeta `classes/`
- [ ] APIs REST en `admin_api/`
- [ ] Páginas HTML `rutinas.html` y `progresos.html`
- [ ] Scripts JavaScript en `Scriptsindex/`
- [ ] Firebase autenticación funcionando
- [ ] Conexión HTTPS (recomendado)

---

**¡Sistema listo para usar!** 🚀

Para más información, consulta la documentación completa en:
`Guías_de_uso/SISTEMA_POO_RUTINAS.md`
