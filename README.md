# DevStudio - Página Web de Desarrollo de Software

Una página web moderna y responsiva para empresas de desarrollo de software web y móvil, construida con HTML5, CSS3, Bootstrap 5, jQuery y PHP.

## 🚀 Características

- **Diseño Moderno**: Interfaz limpia y profesional con gradientes y animaciones
- **Totalmente Responsiva**: Optimizada para dispositivos móviles, tablets y desktop
- **Navegación Suave**: Scroll suave entre secciones
- **Formulario de Contacto**: Funcional con validación y envío de emails
- **Animaciones**: Efectos visuales atractivos con CSS y jQuery
- **SEO Optimizado**: Estructura semántica y meta tags apropiados

## 📁 Estructura del Proyecto

```
emprendimiento/
├── index.html          # Página principal
├── contact.php         # Procesamiento del formulario de contacto
├── css/
│   └── style.css      # Estilos personalizados
├── js/
│   └── script.js      # Funcionalidades JavaScript/jQuery
└── README.md          # Este archivo
```

## 🛠️ Tecnologías Utilizadas

- **HTML5**: Estructura semántica
- **CSS3**: Estilos modernos con gradientes y animaciones
- **Bootstrap 5**: Framework CSS responsivo
- **jQuery**: Interactividad y efectos
- **PHP**: Procesamiento del formulario de contacto
- **Font Awesome**: Iconos vectoriales

## 📋 Requisitos

- Servidor web con soporte para PHP (Apache, Nginx, etc.)
- PHP 7.4 o superior
- Configuración de email en el servidor

## ⚙️ Instalación

1. **Clona o descarga el proyecto** en tu servidor web:
   ```bash
   git clone [url-del-repositorio]
   ```

2. **Configura el servidor web** para que apunte al directorio del proyecto

3. **Configura el email** en `contact.php`:
   - Cambia la línea 47: `$to = 'tu-email@dominio.com';`

4. **Verifica permisos** de escritura para los archivos de log:
   ```bash
   chmod 755 contact.php
   chmod 666 contact_log.txt error_log.txt
   ```

## 🔧 Configuración

### Configuración de Email

Edita el archivo `contact.php` y cambia la línea 47:

```php
$to = 'tu-email@dominio.com'; // Tu dirección de email
```

### Personalización

#### Colores y Estilos
Edita `css/style.css` para cambiar:
- Colores principales
- Gradientes
- Tipografías
- Animaciones

#### Contenido
Edita `index.html` para personalizar:
- Textos y descripciones
- Información de contacto
- Enlaces de redes sociales

#### Funcionalidades
Edita `js/script.js` para modificar:
- Animaciones
- Validaciones
- Efectos interactivos

## 📱 Secciones de la Página

### 1. Navegación
- Logo y nombre de la empresa
- Menú responsive con enlaces a secciones
- Efectos hover y transiciones suaves

### 2. Sección Principal (Hero)
- Título llamativo con efecto de escritura
- Descripción de servicios
- Botones de llamada a la acción
- Iconos animados

### 3. Servicios
- Tres tarjetas con servicios principales
- Iconos de Font Awesome
- Efectos hover y animaciones

### 4. Quienes Somos
- Información sobre la empresa
- Estadísticas y logros
- Tecnologías utilizadas
- Diseño en dos columnas

### 5. Contacto
- Formulario funcional con validación
- Campos: nombre, email, asunto, mensaje
- Envío via AJAX a PHP
- Respuestas automáticas

## 🎨 Personalización

### Cambiar Colores
En `css/style.css`, modifica las variables CSS:

```css
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### Agregar Nuevas Secciones
1. Añade la sección en `index.html`
2. Agrega los estilos en `css/style.css`
3. Incluye funcionalidades en `js/script.js`

### Modificar Formulario
1. Edita los campos en `index.html`
2. Actualiza la validación en `js/script.js`
3. Modifica el procesamiento en `contact.php`

## 🔒 Seguridad

El formulario de contacto incluye:
- Validación del lado cliente y servidor
- Sanitización de datos
- Protección contra spam básica
- Rate limiting
- Logs de errores

## 📊 Rendimiento

- Imágenes optimizadas
- CSS y JS minificados (recomendado para producción)
- Lazy loading de elementos
- Compresión gzip habilitada

## 🚀 Despliegue

### Para Producción

1. **Minifica archivos**:
   ```bash
   # CSS
   npm install -g clean-css-cli
   cleancss css/style.css -o css/style.min.css
   
   # JavaScript
   npm install -g uglify-js
   uglifyjs js/script.js -o js/script.min.js
   ```

2. **Configura HTTPS** para seguridad

3. **Optimiza imágenes** para web

4. **Configura cache** en el servidor

### Hosting Recomendado

- **Netlify**: Para versiones estáticas
- **Vercel**: Despliegue rápido
- **Shared Hosting**: Para versiones con PHP
- **VPS**: Para control total

## 🐛 Solución de Problemas

### Formulario no envía emails
1. Verifica configuración de PHP mail()
2. Revisa logs de error en `error_log.txt`
3. Confirma permisos de escritura

### Estilos no se cargan
1. Verifica rutas de archivos CSS
2. Revisa consola del navegador
3. Confirma que Bootstrap se carga correctamente

### JavaScript no funciona
1. Verifica que jQuery se carga antes que `script.js`
2. Revisa consola del navegador
3. Confirma que no hay errores de sintaxis

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Puedes usarlo libremente para proyectos comerciales y personales.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📞 Soporte

Para soporte técnico o preguntas:
- Email: soporte@devstudio.com
- GitHub Issues: [Crear un issue](https://github.com/tu-usuario/devstudio/issues)

---

**Desarrollado con ❤️ para el desarrollo web moderno** # octocodex
