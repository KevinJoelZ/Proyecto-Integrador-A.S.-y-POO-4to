# 🔄 DIAGRAMAS DE FLUJO - SISTEMA POO DEPORTIVO

## 📊 Diagrama General del Sistema

```
┌─────────────────────────────────────────────────────────────────┐
│                    USUARIO FINAL (Browser)                       │
│                   cliente.php → Firebase Login                   │
└────────────────────────────────┬────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │                         │
            ┌───────▼────────┐      ┌────────▼────────┐
            │   rutinas.html │      │ progresos.html  │
            │ (Interfaces)   │      │  (Interfaces)   │
            └───────┬────────┘      └────────┬────────┘
                    │                         │
            ┌───────▼────────┐      ┌────────▼────────┐
            │   rutinas.js   │      │  progresos.js   │
            │ (GestorRutinas)│      │(GestorProgresos)│
            └───────┬────────┘      └────────┬────────┘
                    │                         │
            ┌───────▼─────────────────────────▼──────────┐
            │         API REST (Fetch POST/GET)          │
            └───────┬─────────────────────────┬──────────┘
                    │                         │
        ┌───────────▼───────────┐   ┌────────▼──────────┐
        │  admin_api/           │   │  admin_api/       │
        │  rutinas.php          │   │  progresos.php    │
        │  (CRUD + Validation)  │   │  (Stats + Analysis)
        └───────────┬───────────┘   └────────┬──────────┘
                    │                         │
        ┌───────────▼───────────┐   ┌────────▼──────────┐
        │ Rutina class          │   │  Progreso class   │
        │ (Business Logic)      │   │  (Analytics)      │
        └───────────┬───────────┘   └────────┬──────────┘
                    │                         │
                    └───────────────┬─────────┘
                                    │
                    ┌───────────────▼─────────────┐
                    │    MySQLi Connection        │
                    │    (conexion.php)           │
                    └───────────────┬─────────────┘
                                    │
                    ┌───────────────▼─────────────┐
                    │    MySQL Database           │
                    │ (8 Tablas normalizadas)    │
                    └─────────────────────────────┘
```

---

## 🔄 Flujo: Crear Nueva Rutina

```
USUARIO                         NAVEGADOR              SERVIDOR
   │                                │                      │
   │                                │                      │
   ├─── Click "Nueva Rutina" ──────►│                      │
   │                                │                      │
   │◄────── Modal Abierto ──────────┤                      │
   │                                │                      │
   ├─── Completa Formulario ───────►│                      │
   │  (Nombre, Deporte, Nivel...)   │                      │
   │                                │                      │
   ├─── Click "Guardar Rutina" ────►│                      │
   │                                │ Validación Cliente    │
   │                                ├─ Campos requeridos    │
   │                                ├─ Formato correcto     │
   │                                │                      │
   │                                │ POST /admin_api/    │
   │                                │ rutinas.php         │
   │                                ├───────────────────►│
   │                                │                    │
   │                                │               Validación
   │                                │               Servidor
   │                                │                    │
   │                                │              Crear Rutina
   │                                │              object
   │                                │                    │
   │                                │           Prepared
   │                                │           Statement
   │                                │                    │
   │                                │          INSERT
   │                                │          INTO BD
   │                                │                    │
   │                                │◄─── JSON Response ─┤
   │                                │  {"success": true}  │
   │                                │                    │
   │◄───── Notificación "OK" ───────┤                    │
   │                                │                    │
   │◄────── Actualizar Grid ────────┤                    │
   │    (Nueva rutina visible)       │                    │
   │                                │                    │
   ▼                                ▼                    ▼
```

---

## 📊 Flujo: Registrar Progreso

```
USUARIO                    NAVEGADOR                 SERVIDOR
   │                          │                          │
   ├─ Click Registrar ──────►│                          │
   │                          │                          │
   │◄── Modal Abierto ────────┤                          │
   │                          │                          │
   ├─ Selecciona Medida ─────►│ (peso, distancia...)   │
   ├─ Ingresa Valor ────────►│                          │
   ├─ Ajusta Esfuerzo ──────►│ (Slider 1-10)           │
   ├─ Agrega Notas ────────►│                          │
   │                          │                          │
   ├─ Click Registrar ──────►│ Validación              │
   │                          ├─ Tipo válido           │
   │                          ├─ Valores numéricos     │
   │                          ├─ Rango esfuerzo        │
   │                          │                        │
   │                          │ POST /admin_api/     │
   │                          │ progresos.php        │
   │                          ├───────────────────►│
   │                          │                    │
   │                          │            Crear objeto
   │                          │            Progreso
   │                          │                    │
   │                          │         Calcular %
   │                          │         completado
   │                          │                    │
   │                          │         INSERT
   │                          │         BD
   │                          │                    │
   │                          │    Obtener stats
   │                          │    actualizadas
   │                          │                    │
   │                          │◄─── JSON Response
   │                          │  {success, id,
   │                          │   mejora, stats}
   │                          │                    │
   │◄─ Notificación OK ──────┤                    │
   │                          │                    │
   │◄─ Actualizar Dashboard ─┤                    │
   │  (Stats + último)        │                    │
   │                          │                    │
   ▼                          ▼                    ▼
```

---

## 🗄️ Flujo: Base de Datos

```
USUARIO REGISTRA DATOS
    │
    ▼
┌──────────────────────┐
│   TABLA: usuarios    │
│ ─────────────────── │
│ • id (PK)           │
│ • nombre            │
│ • email (UNIQUE)    │
│ • uid_firebase (UK) │
│ • deporte_favorito  │
│ • nivel             │
│ • fecha_registro    │
└────────┬─────────────┘
         │
         ├──────────────┐
         │              │
         ▼              ▼
    ┌──────────────┐  ┌──────────────┐
    │ TABLA:       │  │ TABLA:       │
    │ rutinas      │  │ progresos    │
    │ ─────────── │  │ ─────────── │
    │ • id (PK)   │  │ • id (PK)   │
    │ • usuario_id│  │ • usuario_id│
    │   (FK)      │  │   (FK)      │
    │ • nombre    │  │ • tipo_med  │
    │ • deporte   │  │ • valor     │
    │ • nivel     │  │ • esfuerzo  │
    │ • estado    │  │ • fecha     │
    │ • fecha_ini │  │ • notas     │
    │ • fecha_fin │  │             │
    │ • progreso% │  │ ↓ ANÁLISIS  │
    │             │  │ • min/max   │
    │ ↓EJERCICIOS │  │ • avg       │
    │ • id (FK)   │  │ • mejora%   │
    │ • nombre    │  │             │
    │ • series    │  └─────────────┘
    │ • reps      │
    │ • descanso  │
    │             │
    └─────────────┘

RELACIONES:
━━━━━━━━━
usuarios ──────┬──────► rutinas
               ├──────► progresos
               └──────► resultados

rutinas ──────┬──────► ejercicios
              └──────► progresos
```

---

## 🔐 Flujo: Autenticación y Seguridad

```
┌─ INICIO ─┐
    │
    ├─ Usuario abre cliente.php
    │
    ▼
┌───────────────────────┐
│ Firebase Auth SDK     │
│ (Firebase SDK JS)     │
│                       │
│ google.com login      │
└───────┬───────────────┘
        │
        ├─ Usuario se autentica
        │
        ▼
┌───────────────────────┐
│ Firebase retorna:     │
│ • UID                 │
│ • Email               │
│ • Foto                │
└───────┬───────────────┘
        │
        ├─ JavaScript guarda datos
        │
        ▼
┌───────────────────────┐
│ rutinas.js / JS envía │
│ GET rutinas.php       │
│ + UID en header/body  │
│                       │
│ Prepared Statement:   │
│ SELECT * FROM usuarios│
│ WHERE uid = ?         │
│ (? = parámetro)       │
│                       │
│ ↓ SQL Injection ✓     │
│   Prevenido           │
└───────┬───────────────┘
        │
        ├─ Valida UID en BD
        │
        ▼
┌───────────────────────┐
│ Retorna datos SOLO si │
│ UID existe            │
│                       │
│ Si UID no existe:     │
│ → Crear usuario       │
│ → Guardar UID         │
└───────┬───────────────┘
        │
        ├─ Operaciones permitidas
        │
        ▼
   ÉXITO ✓
```

---

## 📈 Flujo: Cálculo de Estadísticas

```
USUARIO REGISTRA PROGRESO
    │
    ▼
╔═══════════════════════════╗
║ POST /progresos.php       ║
║ ├─ usuario_id: 1          ║
║ ├─ rutina_id: 5           ║
║ ├─ tipo_medida: "peso"    ║
║ ├─ valor_actual: 75.5 kg  ║
║ ├─ valor_objetivo: 73 kg  ║
║ ├─ esfuerzo: 7            ║
║ └─ fecha: 2026-01-26      ║
╚═════════┬═════════════════╝
          │
          ▼
   CLASE PROGRESO
   ├─ Validar datos
   ├─ Sanitizar entrada
   │
   └─ Calcular % completado
      │
      ├─ FÓRMULA:
      │  % = ((valor_actual - valor_objetivo) 
      │       / (valor_objetivo * 100))
      │
      │  Ejemplo:
      │  75.5 - 73 = 2.5 kg conseguidos
      │  2.5 / 73 = 0.0342
      │  0.0342 * 100 = 3.42% completado
      │
      ▼
   INSERT INTO progresos
   ├─ id: auto
   ├─ usuario_id: 1
   ├─ valor_actual: 75.5
   ├─ porcentaje_completado: 3.42
   └─ fecha: 2026-01-26
      │
      ▼
   ESTADÍSTICAS AUTOMÁTICAS
   │
   ├─ MIN
   │  SELECT MIN(valor_actual) 
   │  FROM progresos WHERE usuario_id = 1 
   │  AND tipo_medida = 'peso'
   │  → 72 kg
   │
   ├─ MAX
   │  SELECT MAX(valor_actual)
   │  FROM progresos WHERE usuario_id = 1 
   │  AND tipo_medida = 'peso'
   │  → 78 kg
   │
   ├─ PROMEDIO
   │  SELECT AVG(valor_actual)
   │  FROM progresos WHERE usuario_id = 1 
   │  AND tipo_medida = 'peso'
   │  → 74.5 kg
   │
   ├─ MEJORA PORCENTUAL
   │  Primera medida: 80 kg (2026-01-01)
   │  Última medida: 75.5 kg (2026-01-26)
   │  Mejora = (80 - 75.5) / 80 * 100
   │        = 4.5 / 80 * 100
   │        = 5.625% de mejora
   │
   └─ TENDENCIA
      Últimos 7 días promedio
      vs 30 días promedio
      → Aceleración/desaceleración

      ▼
   API RESPONSE JSON
   {
     "success": true,
     "id": 45,
     "porcentaje_completado": 3.42,
     "estadisticas": {
       "min": 72,
       "max": 78,
       "promedio": 74.5,
       "mejora_porcentual": 5.625,
       "tendencia": "aceleración"
     }
   }
      │
      ▼
   FRONTEND ACTUALIZA
   ├─ Tarjeta de progreso
   ├─ Dashboard stats
   ├─ Gráfico (si aplica)
   └─ Notificación "Registrado"
```

---

## 🔄 Ciclo Completo: Crear → Registrar → Analizar

```
SEMANA 1
┌──────────────┐
│ CREAR RUTINA │  Usuario crea "Rutina Fitness 4x"
│ "Fitness 4x" │  - Objetivo: Bajar de peso
│ Duración: 4  │  - 4 semanas
│ semanas      │  - Nivel: Intermedio
└──────┬───────┘
       │
SEMANA 1-4
       │
       ├─ DÍA 1 (2026-01-02)
       │ └─ Pesa: 80 kg → Progreso 0%
       │
       ├─ DÍA 7 (2026-01-09)
       │ └─ Pesa: 78.5 kg → Progreso 3.75%
       │
       ├─ DÍA 14 (2026-01-16)
       │ └─ Pesa: 76.5 kg → Progreso 7.5%
       │
       ├─ DÍA 21 (2026-01-23)
       │ └─ Pesa: 75 kg → Progreso 11.25%
       │
       └─ DÍA 28 (2026-01-30)
          └─ Pesa: 74 kg → Progreso 13.75%
             ✓ COMPLETADA (pero puede continuar)

ANALIZAR PROGRESO
       │
       ├─ Mejora total: (80-74)/80 = 7.5%
       ├─ Promedio: 76.8 kg
       ├─ Mínimo: 74 kg
       ├─ Máximo: 80 kg
       │
       ├─ Tendencia: 
       │  Semana 1-2: 1.5 kg
       │  Semana 2-3: 2 kg
       │  Semana 3-4: 1 kg
       │  ↓ Desaceleración (menos pérdida)
       │
       └─ RECOMENDACIONES
         ├─ Aumentar intensidad
         ├─ Aumentar frecuencia
         ├─ Revisar alimentación
         └─ Crear nueva rutina

PRÓXIMA ACCIÓN
       │
       └─ NUEVA RUTINA
          "Fitness 4x Avanzado"
          - Nivel: Avanzado
          - Objetivo: Pérdida de grasa
          - Duración: 4 semanas
          
          ↓ CICLO REPETIDO
```

---

## 🎯 Matriz de Flujos por Rol

```
┌────────────────────────────────────────────────────────────┐
│                      FLUJOS DE USUARIO                      │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  USUARIO PRINCIPIANTE                                      │
│  ┌─────────────┐  ┌──────────┐  ┌──────────────┐          │
│  │ Login       │→ │ Primera  │→ │ Registrar    │          │
│  │ Google      │  │ Rutina   │  │ Progreso 1x  │          │
│  └─────────────┘  └──────────┘  │ por semana   │          │
│                                 └──────────────┘          │
│                                                             │
│  USUARIO INTERMEDIO                                        │
│  ┌─────────────┐  ┌──────────┐  ┌──────────────┐          │
│  │ Login       │→ │ 2-3      │→ │ Registrar    │          │
│  │ Google      │  │ Rutinas  │  │ Progreso 3x  │          │
│  └─────────────┘  │ Distintos│  │ por semana   │          │
│                   │ Deportes │  └──────────────┘          │
│                   └──────────┘                             │
│                                                             │
│  USUARIO AVANZADO                                          │
│  ┌─────────────┐  ┌──────────┐  ┌──────────────┐          │
│  │ Login       │→ │ 4+       │→ │ Registrar    │          │
│  │ Google      │  │ Rutinas  │  │ Progreso     │          │
│  └─────────────┘  │ Personal │  │ diario +     │          │
│                   │ izado    │  │ Análisis IA  │          │
│                   └──────────┘  └──────────────┘          │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

---

## 📱 Flujo Mobile vs Desktop

```
DESKTOP (1920x1080)              MOBILE (375x812)
┌────────────────────┐           ┌──────────────┐
│  Mis Rutinas       │           │ Mis Rutinas  │
│ ┌────┬────┬────┐   │           │ ┌──────────┐ │
│ │Fit │Run │Nat │   │           │ │Fitness   │ │
│ ├────┼────┼────┤   │           │ ├──────────┤ │
│ │Yo  │Cic │Fut │   │           │ │Running   │ │
│ └────┴────┴────┘   │           │ ├──────────┤ │
│                    │           │ │Natación  │ │
│ ┌─────────────┐    │           │ ├──────────┤ │
│ │Grid 3 col   │    │           │ │Ciclismo  │ │
│ │3 rutinas    │    │           │ └──────────┘ │
│ │por fila     │    │           │ (scroll v)   │
│ └─────────────┘    │           └──────────────┘
│                    │
└────────────────────┘
```

---

## 🔗 Conexión entre Componentes

```
┌─────────────────────────────────────────────────────┐
│  COMPONENTES CONECTADOS                             │
└─────────────────────────────────────────────────────┘

usuario.php              rutina.php
    │                        │
    ├─────────────┬──────────┤
                  │
              progreso.php
                  │
    ┌─────────────┴──────────┐
    │                        │
rutinas.html              progresos.html
    │                        │
    │                    ┌───┴───┐
    └─────► Mismo CSS ◄──┘       │
                             mismo JS
                            (patrón)
```

---

## ⏱️ Cronograma de Ejecución

```
USUARIO ABRE PÁGINA
    │ 0ms
    ▼
    Cargar HTML (100ms)
    ├─ CSS inline
    ├─ Firebase SDK
    └─ rutinas.js
    
    ▼ 100ms
    Verificar Auth Firebase (200ms)
    ├─ Si no autenticado → Redirigir
    └─ Si autenticado → UID guardado
    
    ▼ 300ms
    Cargar rutinas.html desde servidor
    
    ▼ 350ms
    Ejecutar rutinas.js:
    ├─ Constructor (50ms)
    └─ cargarRutinas() (500ms)
         ├─ GET admin_api/rutinas.php
         ├─ Esperar respuesta (400ms)
         └─ Procesar JSON (100ms)
    
    ▼ 850ms
    Renderizar UI
    ├─ renderizarRutinas() (200ms)
    ├─ Pintar DOM (100ms)
    └─ Animaciones CSS (300ms)
    
    ▼ 1150ms
    ✓ PÁGINA LISTA PARA USAR
    (Target: < 2 segundos)
```

---

## 🎓 Conclusión de Diagramas

Todos los flujos representan:

✅ **Flujo Usuario:** Acciones de usuario en interfaz  
✅ **Flujo Datos:** Movimiento de información  
✅ **Flujo Seguridad:** Validación en todos los niveles  
✅ **Flujo Cálculo:** Estadísticas automáticas  
✅ **Flujo Bases de Datos:** Relaciones y operaciones  

**Todo integrado en un sistema cohesivo y funcional.**

---

*Diagrama creado: 26 de enero de 2026*  
*Versión: 1.0*  
*Sistema: DeporteFit - POO Rutinas*
