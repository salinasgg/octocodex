# Documentación de Paginación y Búsqueda - Gestión de Clientes

## 📋 Resumen
Este documento explica cómo funciona la paginación y búsqueda en tiempo real implementada en el sistema de gestión de clientes. La paginación permite mostrar 8 registros por página y la búsqueda permite filtrar por todos los campos de la tabla.

## 🔄 Flujo de Funcionamiento

### 1. **Inicio del Proceso**
- El usuario hace clic en "Gestionar Clientes" en el dashboard
- Se ejecuta el evento click que llama a `obtenerClientes()`

### 2. **Búsqueda en Tiempo Real**
- El usuario escribe en el campo de búsqueda
- Se ejecuta automáticamente después de 500ms de inactividad
- Se busca en todos los campos: nombre, apellido, empresa, email, teléfono, ciudad, país, tipo

### 3. **Petición al Servidor (JavaScript)**
```javascript
// Construye la URL con parámetros de paginación y búsqueda
let url = '../php/abm_clientes.php';
let params = [];

if (pagina > 1) {
    params.push(`pagina=${pagina}`);
}

if (terminoBusqueda && terminoBusqueda.trim() !== '') {
    params.push(`buscar=${encodeURIComponent(terminoBusqueda.trim())}`);
}

if (params.length > 0) {
    url += '?' + params.join('&');
}
```

### 4. **Procesamiento en el Servidor (PHP)**
```php
// Obtiene parámetros de paginación y búsqueda
$registros_por_pagina = 8;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$termino_busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Construye consulta SQL con búsqueda
if (!empty($termino_busqueda)) {
    $campos_busqueda = ['cl_nombre', 'cl_apellido', 'cl_empresa', 'cl_email', 
                       'cl_telefono', 'cl_ciudad', 'cl_pais', 'cl_tipo'];
    
    foreach ($campos_busqueda as $campo) {
        $where_conditions[] = "$campo LIKE :buscar_$campo";
        $params[":buscar_$campo"] = "%$termino_busqueda%";
    }
    
    $where_clause = "WHERE " . implode(" OR ", $where_conditions);
}

$sql = "SELECT * FROM clientes $where_clause ORDER BY cl_nombre ASC LIMIT :limit OFFSET :offset";
```

### 5. **Respuesta JSON**
```json
{
    "tabla_html": "<div>...tabla completa con buscador y paginación...</div>",
    "datos": [...array de clientes filtrados...],
    "paginacion": {
        "pagina_actual": 2,
        "total_paginas": 5,
        "total_registros": 40,
        "registros_por_pagina": 8
    },
    "busqueda": {
        "termino": "juan",
        "resultados": 15
    }
}
```

## 📁 Archivos Involucrados

### **Backend (PHP)**
- `php/abm_clientes.php` - Lógica principal de paginación y búsqueda
- `php/config_bd.php` - Configuración de base de datos

### **Frontend (JavaScript)**
- `js/ABMClientes.js` - Funciones de paginación, búsqueda y AJAX

### **Estilos (CSS)**
- `css/variables.css` - Estilos de paginación, búsqueda y variables CSS

### **Archivos de Prueba**
- `test_buscador.html` - Prueba del buscador en tiempo real

## 🔧 Configuración

### **Registros por Página**
```php
$registros_por_pagina = 8; // Cambiar este valor para modificar registros por página
```

### **Campos de Búsqueda**
```php
$campos_busqueda = [
    'cl_nombre', 'cl_apellido', 'cl_empresa', 'cl_email', 
    'cl_telefono', 'cl_ciudad', 'cl_pais', 'cl_tipo'
];
```

### **Delay de Búsqueda en Tiempo Real**
```javascript
setTimeout(() => {
    // Búsqueda automática
}, 500); // 500ms de delay
```

## 🎨 Estilos CSS

### **Clases del Buscador**
- `.search-container` - Contenedor principal del buscador
- `.search-box` - Contenedor del input y botón
- `.search-input` - Campo de entrada de búsqueda
- `.search-btn` - Botón de búsqueda
- `.search-info` - Información de resultados
- `#resultadosBusqueda` - Contador de resultados

### **Clases de Paginación**
- `.pagination-container` - Contenedor principal
- `.pagination-info` - Información de registros mostrados
- `.pagination-controls` - Contenedor de botones
- `.btn-pagina` - Botones de página normales
- `.btn-pagina-activa` - Botón de página actual

### **Responsive Design**
- En pantallas < 768px: elementos se apilan verticalmente
- Botones más grandes para facilitar el toque en móviles

## 🔍 Funcionalidades del Buscador

### **Búsqueda en Tiempo Real**
- Se ejecuta automáticamente mientras el usuario escribe
- Delay de 500ms para evitar demasiadas peticiones
- Mínimo 2 caracteres para activar la búsqueda

### **Métodos de Búsqueda**
1. **Escribir en el campo** - Búsqueda automática
2. **Presionar Enter** - Búsqueda inmediata
3. **Hacer clic en el botón** - Búsqueda manual

### **Campos de Búsqueda**
- **Nombre** - `cl_nombre`
- **Apellido** - `cl_apellido`
- **Empresa** - `cl_empresa`
- **Email** - `cl_email`
- **Teléfono** - `cl_telefono`
- **Ciudad** - `cl_ciudad`
- **País** - `cl_pais`
- **Tipo** - `cl_tipo` (potencial/actual)

### **Comportamiento de Búsqueda**
- **Búsqueda parcial** - Encuentra coincidencias en cualquier parte del texto
- **Case-insensitive** - No distingue entre mayúsculas y minúsculas
- **Múltiples campos** - Busca en todos los campos simultáneamente
- **Paginación integrada** - Mantiene la búsqueda al cambiar de página

## 🐛 Debugging

### **Logs del Servidor**
```php
error_log("Paginación - Página actual: " . $pagina_actual . ", Offset: " . $offset . ", Búsqueda: " . $termino_busqueda);
```

### **Logs del Cliente**
```javascript
console.log('URL de petición:', url);
console.log('Búsqueda en tiempo real:', terminoBusqueda);
console.log('Datos de búsqueda:', clientes.busqueda);
```

## 📊 Ejemplo de Búsqueda

### **Búsqueda por Nombre**
```
Término: "Juan"
SQL: WHERE cl_nombre LIKE '%Juan%' OR cl_apellido LIKE '%Juan%' OR ...
Resultado: Todos los clientes que contengan "Juan" en cualquier campo
```

### **Búsqueda por Email**
```
Término: "gmail"
SQL: WHERE cl_email LIKE '%gmail%' OR ...
Resultado: Todos los clientes con email que contenga "gmail"
```

### **Búsqueda por País**
```
Término: "Argentina"
SQL: WHERE cl_pais LIKE '%Argentina%' OR ...
Resultado: Todos los clientes de Argentina
```

## 🔍 Verificación de Funcionamiento

### **1. Verificar Consola del Navegador**
- Abrir F12 → Console
- Buscar mensajes de "Búsqueda en tiempo real"
- Verificar que todas las funciones sean 'function'

### **2. Probar Búsquedas**
- Escribir en el campo de búsqueda
- Verificar que aparezcan resultados automáticamente
- Probar con diferentes términos

### **3. Probar URLs Directamente**
- `php/abm_clientes.php` - Sin búsqueda
- `php/abm_clientes.php?buscar=juan` - Búsqueda por "juan"
- `php/abm_clientes.php?buscar=gmail&pagina=2` - Búsqueda con paginación

## 🚨 Solución de Problemas

### **Problema: No funciona la búsqueda en tiempo real**
**Solución:**
1. Verificar que el evento 'input' esté configurado
2. Revisar consola para errores JavaScript
3. Verificar que la función `buscarEnTiempoReal` esté disponible

### **Problema: Búsqueda no encuentra resultados**
**Solución:**
1. Verificar que haya datos en la tabla `clientes`
2. Revisar la consulta SQL generada
3. Verificar que los campos de búsqueda estén correctos

### **Problema: Paginación no mantiene la búsqueda**
**Solución:**
1. Verificar que `cambiarPagina` obtenga el término de búsqueda actual
2. Revisar que la URL incluya el parámetro `buscar`
3. Verificar que el PHP procese ambos parámetros

## 📈 Optimizaciones Implementadas

1. **Debounce** - Evita demasiadas peticiones con timeout de 500ms
2. **Búsqueda parcial** - Encuentra coincidencias en cualquier parte del texto
3. **Múltiples campos** - Busca en todos los campos relevantes
4. **Paginación integrada** - Mantiene la búsqueda al navegar entre páginas
5. **Responsive design** - Funciona en dispositivos móviles
6. **Feedback visual** - Muestra número de resultados encontrados

## 📝 Notas Importantes

- La búsqueda es **server-side** (procesada en el servidor)
- Se usa **PDO** para consultas seguras contra inyección SQL
- La búsqueda es **case-insensitive** y **parcial**
- Los estilos son **responsive** y se adaptan a móviles
- Las funciones JavaScript están **expuestas globalmente** para compatibilidad
- Se incluyen **logs de debugging** para facilitar el mantenimiento
- El **debounce** evita sobrecarga del servidor con búsquedas frecuentes
