# 🔗 Sistema de Asignaciones de Proyectos

## 📋 Descripción
Sistema completo para gestionar asignaciones de usuarios a proyectos, permitiendo definir roles, horas de trabajo, estados y seguimiento detallado de cada asignación.

## 🏗️ Estructura del Sistema

### 📊 Tablas Principales
- **`asignaciones_proyectos`**: Tabla principal con las asignaciones
- **`historial_asignaciones`**: Auditoría de cambios (opcional)
- **`vista_asignaciones_detalle`**: Vista optimizada para consultas

### 🎭 Roles Disponibles
- **Líder**: Responsable principal del proyecto
- **Desarrollador**: Desarrollador de software
- **Consultor**: Asesor técnico especializado
- **Revisor**: Encargado de revisiones y QA
- **Colaborador**: Participante general

### 📈 Estados de Asignación
- **Activo**: Asignación en curso
- **Completado**: Trabajo finalizado
- **Pausado**: Temporalmente detenido
- **Cancelado**: Asignación cancelada

## 🚀 Instalación

### Opción 1: Instalación Automática (Recomendada)
```
http://localhost/octocodex/condor/php/setup_completo.php
```
Este script ejecuta toda la instalación automáticamente.

### Opción 2: Instalación Manual
1. Ejecutar el script SQL:
   ```sql
   -- Desde phpMyAdmin o cliente MySQL
   SOURCE /path/to/octocodex/condor/sql/crear_asignaciones_proyectos.sql;
   ```

2. Verificar instalación:
   ```
   http://localhost/octocodex/condor/php/verificar_bd.php
   ```

### Opción 3: Paso a Paso
1. **Crear datos base**: `http://localhost/octocodex/condor/php/crear_datos_ejemplo.php`
2. **Instalar sistema**: `http://localhost/octocodex/condor/php/instalar_asignaciones.php`
3. **Verificar**: `http://localhost/octocodex/condor/php/verificar_bd.php`

## 🔧 Scripts Utilitarios

| Script | Descripción | URL |
|--------|-------------|-----|
| `setup_completo.php` | Instalación automática completa | [🚀 Ejecutar](http://localhost/octocodex/condor/php/setup_completo.php) |
| `verificar_bd.php` | Verificar estado de la base de datos | [🔍 Verificar](http://localhost/octocodex/condor/php/verificar_bd.php) |
| `test_asignaciones.php` | Probar API de asignaciones | [🧪 Probar](http://localhost/octocodex/condor/php/test_asignaciones.php) |
| `crear_datos_ejemplo.php` | Crear usuarios y proyectos | [📝 Crear Datos](http://localhost/octocodex/condor/php/crear_datos_ejemplo.php) |
| `instalar_asignaciones.php` | Instalar solo asignaciones | [⚙️ Instalar](http://localhost/octocodex/condor/php/instalar_asignaciones.php) |

## 📊 Dashboard Administrativo

Una vez instalado, el sistema estará disponible en:
- **Dashboard Admin**: `http://localhost/octocodex/condor/admin/dashboard.php`
- **Dashboard Principal**: `http://localhost/octocodex/condor/dashboard.php`

### Funcionalidades del Dashboard:
- ✅ Vista general de estadísticas
- ✅ Gestión por proyecto
- ✅ Gestión por usuario
- ✅ Crear/Editar/Eliminar asignaciones
- ✅ Reportes y gráficos
- ✅ Modal de gestión avanzada

## 🔑 API Endpoints

### Estadísticas
```php
GET /condor/php/asignaciones_proyectos.php?accion=obtener_estadisticas
```

### Listar Proyectos con Asignaciones
```php
GET /condor/php/asignaciones_proyectos.php?accion=listar_proyectos_con_asignaciones
```

### Listar Usuarios con Asignaciones
```php
GET /condor/php/asignaciones_proyectos.php?accion=listar_usuarios_con_asignaciones
```

### Obtener Asignaciones de un Proyecto
```php
GET /condor/php/asignaciones_proyectos.php?accion=obtener_asignaciones&proyecto_id=1
```

### Obtener Asignaciones de un Usuario
```php
GET /condor/php/asignaciones_proyectos.php?accion=obtener_asignaciones_usuario&usuario_id=1
```

### Crear Asignación
```php
POST /condor/php/asignaciones_proyectos.php
{
  "accion": "asignar_usuario",
  "proyecto_id": 1,
  "usuario_id": 2,
  "rol_proyecto": "desarrollador",
  "horas_asignadas": 40.0,
  "fecha_inicio": "2025-09-01",
  "notas": "Desarrollador frontend principal"
}
```

### Actualizar Asignación
```php
POST /condor/php/asignaciones_proyectos.php
{
  "accion": "actualizar_asignacion",
  "asignacion_id": 1,
  "rol_proyecto": "lider",
  "horas_trabajadas": 15.5,
  "estado_asignacion": "activo"
}
```

### Eliminar Asignación
```php
POST /condor/php/asignaciones_proyectos.php
{
  "accion": "eliminar_asignacion",
  "asignacion_id": 1
}
```

## 🎯 Datos de Ejemplo

### Usuarios Creados:
- **admin** / 123456 (Administrador)
- **maria.garcia** / 123456 (Usuario)
- **carlos.rodriguez** / 123456 (Usuario)
- **ana.lopez** / 123456 (Usuario)
- **roberto.hernandez** / 123456 (Usuario)

### Proyectos Creados:
1. Sistema de Gestión Empresarial
2. Aplicación Móvil E-commerce
3. Portal Web Corporativo
4. Sistema de Control de Inventario
5. API de Integración de Servicios

## 🛠️ Troubleshooting

### Error: Tabla no existe
```bash
# Ejecutar instalación completa
http://localhost/octocodex/condor/php/setup_completo.php
```

### Error: 404 en API
```bash
# Verificar rutas en dashboard admin
# Las rutas deben ser absolutas: /octocodex/condor/php/asignaciones_proyectos.php
```

### Error: No autorizado
```bash
# Verificar que la sesión esté activa
# Iniciar sesión en el sistema primero
```

### Error: Sin datos
```bash
# Crear datos de ejemplo
http://localhost/octocodex/condor/php/crear_datos_ejemplo.php
```

## 📊 Base de Datos

### Estructura de `asignaciones_proyectos`
```sql
CREATE TABLE `asignaciones_proyectos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proyecto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `rol_proyecto` enum('lider','desarrollador','consultor','revisor','colaborador'),
  `fecha_asignacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado_asignacion` enum('activo','completado','pausado','cancelado'),
  `notas` text DEFAULT NULL,
  `horas_asignadas` decimal(5,2) DEFAULT NULL,
  `horas_trabajadas` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_assignment` (`proyecto_id`, `usuario_id`),
  -- Foreign Keys
  CONSTRAINT `fk_asignacion_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_asignacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
);
```

### Vista Optimizada
```sql
CREATE VIEW `vista_asignaciones_detalle` AS
SELECT 
    ap.id as asignacion_id,
    ap.proyecto_id,
    ap.usuario_id,
    ap.rol_proyecto,
    ap.estado_asignacion,
    ap.fecha_asignacion,
    ap.horas_asignadas,
    ap.horas_trabajadas,
    p.pr_titulo as proyecto_titulo,
    p.pr_estado as proyecto_estado,
    u.us_nombre as usuario_nombre,
    u.us_apellido as usuario_apellido,
    CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo
FROM asignaciones_proyectos ap
INNER JOIN proyectos p ON ap.proyecto_id = p.id
INNER JOIN usuarios u ON ap.usuario_id = u.id;
```

## ✨ Características

### 🎨 Interfaz de Usuario
- Design moderno con gradiente violeta (#8b5cf6)
- Responsive design para móviles
- Iconos Font Awesome
- Animaciones suaves
- Modal avanzado con pestañas

### 🔧 Funcionalidades Técnicas
- API REST completa
- Validaciones en cliente y servidor
- Manejo de errores robusto
- Logging de auditoría
- Triggers automáticos
- Procedimientos almacenados

### 📈 Reportes y Estadísticas
- Estadísticas generales del sistema
- Métricas por proyecto
- Métricas por usuario
- Distribución de roles
- Horas trabajadas vs asignadas
- Estados de asignaciones

## 🚧 Desarrollo y Mantenimiento

### Agregar Nuevos Roles
```sql
ALTER TABLE asignaciones_proyectos 
MODIFY COLUMN rol_proyecto enum('lider','desarrollador','consultor','revisor','colaborador','nuevo_rol');
```

### Agregar Nuevos Estados
```sql
ALTER TABLE asignaciones_proyectos 
MODIFY COLUMN estado_asignacion enum('activo','completado','pausado','cancelado','nuevo_estado');
```

### Backup de Datos
```sql
-- Backup de asignaciones
SELECT * FROM asignaciones_proyectos INTO OUTFILE '/tmp/asignaciones_backup.csv';

-- Restore
LOAD DATA INFILE '/tmp/asignaciones_backup.csv' INTO TABLE asignaciones_proyectos;
```

---

## 📞 Soporte

Para problemas o preguntas:
1. Verificar logs de PHP en `/xampp/logs/`
2. Ejecutar script de verificación
3. Revisar la consola del navegador para errores JavaScript
4. Verificar permisos de base de datos

---
**Versión**: 1.0  
**Fecha**: 31/08/2025  
**Desarrollado para**: Octocodex System