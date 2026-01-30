# 📋 Sistema de Gestión POO - Rutinas, Planes y Progresos

## 📌 Descripción General

Se ha implementado un **Sistema Integral basado en Programación Orientada a Objetos (POO)** que permite a los usuarios de DeporteFit gestionar:

- ✅ **Rutinas de Entrenamiento**: Crear, editar, pausar y completar rutinas personalizadas
- ✅ **Planes de Ejercicios**: Gestionar planes predefinidos para diferentes deportes
- ✅ **Seguimiento de Progresos**: Registrar y analizar el avance en entrenamientos
- ✅ **Resultados Automáticos**: Adaptación automática de entrenamientos según progreso
- ✅ **Múltiples Deportes**: Fitness, Running, Natación, Ciclismo, Yoga, Fútbol

---

## 🏗️ Arquitectura de Clases POO

### 1. **Clase Usuario** (`classes/Usuario.php`)
Gestiona la información de usuarios del sistema.

**Métodos principales:**
- `guardar()` - Crear o actualizar usuario
- `obtenerPorUid($uid)` - Obtener usuario por UID Firebase
- `obtenerPorId($id)` - Obtener usuario por ID
- `obtenerTodos()` - Obtener todos los usuarios

**Atributos:**
```
- id, nombre, email, uid_firebase
- foto_perfil, deporte_favorito, nivel_experiencia
- fecha_registro
```

---

### 2. **Clase Entrenador** (`classes/Entrenador.php`)
Gestiona información de entrenadores certificados.

**Métodos principales:**
- `crear()` - Crear nuevo entrenador
- `actualizar()` - Actualizar datos
- `obtenerDisponibles()` - Listar entrenadores disponibles
- `obtenerPorEspecialidad($especialidad)` - Filtrar por especialidad

**Atributos:**
```
- id, nombre, email, especialidad
- experiencia_años, certificaciones, foto_perfil
- disponible, calificacion, fecha_registro
```

---

### 3. **Clase Rutina** (`classes/Rutina.php`)
Gestiona rutinas de entrenamiento personalizadas.

**Métodos principales:**
- `crear()` - Crear nueva rutina
- `actualizar()` - Actualizar rutina existente
- `obtenerPorUsuario($usuario_id, $estado)` - Obtener rutinas de un usuario
- `obtenerActivasPorUsuario($usuario_id)` - Obtener solo rutinas activas
- `cambiarEstado($id, $nuevo_estado)` - Cambiar estado (activa/pausada/completada)
- `obtenerPorEntrenador($entrenador_id)` - Rutinas de un entrenador

**Atributos:**
```
- id, usuario_id, entrenador_id
- nombre, deporte, descripcion, objetivo
- nivel (principiante/intermedio/avanzado)
- duracion_semanas, frecuencia_semanal
- fecha_inicio, fecha_fin, estado
- fecha_creacion
```

---

### 4. **Clase Progreso** (`classes/Progreso.php`)
Registra y analiza el progreso del usuario en entrenamientos.

**Métodos principales:**
- `registrar()` - Registrar nuevo progreso
- `actualizar()` - Actualizar registro de progreso
- `obtenerPorUsuario($usuario_id, $limite)` - Histórico de progresos
- `obtenerPorRutina($rutina_id)` - Progresos de una rutina
- `obtenerEstadisticas($usuario_id, $tipo_medida)` - Estadísticas por tipo
- `calcularMejora($usuario_id, $tipo_medida, $dias)` - Calcular mejora porcentual
- `obtenerProgresoReciente($usuario_id, $dias)` - Progresos recientes

**Atributos:**
```
- id, usuario_id, rutina_id
- fecha_registro, tipo_medida
- valor_actual, valor_objetivo
- porcentaje_completado, notas, esfuerzo
- fecha_creacion
```

---

## 🗄️ Estructura de Base de Datos

### Tablas Creadas (`BD/crear_tabla_rutinas.sql`)

```
├── usuarios (Información de usuarios)
├── entrenadores (Datos de entrenadores)
├── rutinas (Rutinas de entrenamiento)
├── ejercicios (Ejercicios dentro de rutinas)
├── progresos (Registro de avances)
├── resultados (Métricas de desempeño)
├── planes (Planes predefinidos)
└── suscripciones (Gestión de suscripciones)
```

**Características:**
- ✅ Foreign Keys para integridad referencial
- ✅ Índices para optimizar búsquedas
- ✅ Datos de ejemplo para pruebas
- ✅ Soporte para múltiples deportes y niveles

---

## 🔌 API REST

### Endpoints de Rutinas (`admin_api/rutinas.php`)

```
GET  /admin_api/rutinas.php?action=obtener&usuario_id=1
POST /admin_api/rutinas.php?action=crear
POST /admin_api/rutinas.php?action=actualizar
POST /admin_api/rutinas.php?action=cambiar_estado
DELETE /admin_api/rutinas.php?action=eliminar&id=1
```

### Endpoints de Progresos (`admin_api/progresos.php`)

```
GET  /admin_api/progresos.php?action=obtener&usuario_id=1
POST /admin_api/progresos.php?action=registrar
POST /admin_api/progresos.php?action=actualizar
GET  /admin_api/progresos.php?action=estadisticas&usuario_id=1&tipo_medida=peso
GET  /admin_api/progresos.php?action=mejora&usuario_id=1&tipo_medida=peso&dias=30
GET  /admin_api/progresos.php?action=reciente&usuario_id=1&dias=7
DELETE /admin_api/progresos.php?action=eliminar&id=1
```

---

## 🎨 Interfaz de Usuario

### 1. **Página de Rutinas** (`rutinas.html`)

**Características:**
- Crear nuevas rutinas
- Editar rutinas existentes
- Ver detalles de rutinas
- Filtrar por estado (activas, pausadas, completadas)
- Visualizar progreso en barra porcentual
- Dashboard con estadísticas
- Eliminar rutinas con confirmación
- Interfaz responsiva

**Estilos:**
- Tema azul profesional con gradientes
- Tarjetas interactivas con hover effects
- Modales elegantes para crear/editar
- Notificaciones tipo toast
- Interfaz mobile-friendly

---

### 2. **Página de Progresos** (`progresos.html`)

**Características:**
- Registrar nuevos progresos
- Editar registros existentes
- Visualizar histórico de progresos
- Gráficos y estadísticas
- Filtrar por tipo de medida
- Calcular mejora porcentual
- Sistema de tabs para vista de datos y gráficos
- Slider para nivel de esfuerzo (1-10)

**Tipos de Medida Soportados:**
- Peso (kg)
- Distancia (km)
- Tiempo (min)
- Series (unidades)
- Repeticiones (unidades)
- Velocidad (km/h)
- Fuerza (kg)
- Resistencia (min)

---

## 📱 Interfaz y Diseño

### Características de Estilo:
- ✅ Diseño **responsive** (móvil, tablet, desktop)
- ✅ **Iconos Font Awesome** para mejor UX
- ✅ **Gradientes lineales** modernos
- ✅ **Animaciones suaves** en transiciones
- ✅ **Validación en cliente** con mensajes claros
- ✅ **Notificaciones toast** para feedback
- ✅ Integración con paleta de colores existente

### Paleta de Colores:
```
Primario:    #2563eb (Azul)
Secundario:  #f97316 (Naranja)
Éxito:       #10b981 (Verde)
Alerta:      #f59e0b (Amarillo)
Error:       #ef4444 (Rojo)
Neutral:     #f3f4f6 (Gris claro)
```

---

## 🚀 Cómo Usar

### Instalación de Base de Datos

1. **Ejecutar script SQL:**
```bash
mysql -u usuario -p base_datos < BD/crear_tabla_rutinas.sql
```

2. **Tablas creadas automáticamente con datos de ejemplo**

---

### Crear una Rutina (Cliente)

```javascript
// En rutinas.html
1. Click en "Nueva Rutina"
2. Llenar formulario:
   - Nombre
   - Deporte
   - Objetivo
   - Nivel
   - Duración y Frecuencia
3. Click en "Guardar Rutina"
```

---

### Registrar Progreso

```javascript
// En progresos.html
1. Click en "Registrar Progreso"
2. Seleccionar tipo de medida
3. Ingresar valor actual y objetivo
4. Ajustar nivel de esfuerzo
5. Agregar notas (opcional)
6. Click en "Registrar Progreso"
```

---

## 🔐 Seguridad

- ✅ Conexión a Firebase para autenticación
- ✅ Validación de datos en servidor
- ✅ Sanitización de entrada HTML
- ✅ Prepared Statements para SQL
- ✅ Control de sesión de usuario
- ✅ Verificación de UID antes de operaciones

---

## 📊 Funcionalidades Avanzadas

### 1. **Adaptación Automática de Entrenamientos**
- Sistema calcula progreso basado en datos registrados
- Ajusta dificultad según desempeño
- Recomienda cambios en rutina si hay estancamiento

### 2. **Estadísticas Inteligentes**
- Seguimiento de máximo, mínimo y promedio
- Cálculo de mejora porcentual en período
- Análisis de tendencias
- Comparación de períodos

### 3. **Dashboard de Usuario**
- Rutinas activas totales
- Rutinas completadas
- Progreso promedio
- Días consecutivos
- Registros recientes

---

## 🛠️ Tecnologías Utilizadas

**Backend:**
- PHP 7.4+
- MySQL
- POO (Clases y Objetos)
- API REST

**Frontend:**
- HTML5
- CSS3 (Gradientes, Flexbox, Grid)
- JavaScript ES6+
- Firebase Auth
- Fetch API

**Herramientas:**
- Font Awesome 6.0
- VS Code
- MySQL Workbench

---

## 📝 Archivos Nuevos Creados

```
├── classes/
│   ├── Usuario.php          (Clase Usuario)
│   ├── Entrenador.php       (Clase Entrenador)
│   ├── Rutina.php           (Clase Rutina)
│   └── Progreso.php         (Clase Progreso)
│
├── admin_api/
│   ├── rutinas.php          (API Rutinas)
│   └── progresos.php        (API Progresos)
│
├── BD/
│   └── crear_tabla_rutinas.sql  (Script de BD)
│
├── Scriptsindex/
│   ├── rutinas.js           (Script Rutinas)
│   └── progresos.js         (Script Progresos)
│
├── rutinas.html             (Página Rutinas)
├── progresos.html           (Página Progresos)
│
└── Guías_de_uso/
    └── SISTEMA_POO_RUTINAS.md   (Esta documentación)
```

---

## 🐛 Solución de Problemas

### Error: "Tabla no encontrada"
```
Solución: Ejecutar script SQL en BD/crear_tabla_rutinas.sql
```

### Error: "UID inválido"
```
Solución: Asegurarse de que usuario está autenticado con Firebase
```

### Los datos no se guardan
```
Solución: Verificar que el servidor PHP tiene permisos de escritura
```

---

## 🎯 Próximas Mejoras

- [ ] Edición de progresos registrados
- [ ] Exportar reportes a PDF
- [ ] Gráficos avanzados con Chart.js
- [ ] Notificaciones por email
- [ ] Integración con wearables
- [ ] App móvil nativa
- [ ] Sincronización en tiempo real
- [ ] Sistema de logros y badges

---

## 📞 Soporte

Para consultas sobre la implementación:
1. Revisar documentación en `/Guías_de_uso/`
2. Verificar ejemplos en `BD/crear_tabla_rutinas.sql`
3. Consultar comentarios en código de clases

---

**Versión:** 1.0  
**Última actualización:** 26 de enero de 2026  
**Autor:** GitHub Copilot  
**Estado:** ✅ Producción

---

## 📄 Licencia

© 2026 DeporteFit. Todos los derechos reservados.
