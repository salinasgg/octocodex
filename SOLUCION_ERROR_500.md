# Solución para Error 500 en Envío de Correos

## 🔍 **Diagnóstico del Problema**

El error 500 (Internal Server Error) indica un problema en el servidor PHP. Las causas más comunes son:

### 1. **Configuración de PHP Mail**
- La función `mail()` no está habilitada
- Configuración incorrecta de SMTP
- Permisos de escritura insuficientes

### 2. **Errores de Sintaxis**
- Funciones definidas después de su uso
- Variables no definidas
- Headers enviados después de contenido

## 🛠️ **Soluciones Paso a Paso**

### **Paso 1: Verificar Configuración PHP**

1. **Accede a `http://localhost/emprendimiento/test.php`**
   - Verifica que PHP esté funcionando
   - Revisa si la función `mail()` está disponible

2. **Revisa los logs de error**
   - Abre `C:\xampp\apache\logs\error.log`
   - Busca errores relacionados con tu proyecto

### **Paso 2: Configurar XAMPP para Correos**

1. **Edita el archivo `php.ini`**
   - Ubicación: `C:\xampp\php\php.ini`
   - Busca la sección `[mail function]`

2. **Configuración recomendada para desarrollo:**
   ```ini
   [mail function]
   sendmail_path = "C:\xampp\sendmail\sendmail.exe -t"
   SMTP = localhost
   smtp_port = 25
   ```

3. **Reinicia Apache**
   - Detén Apache en XAMPP Control Panel
   - Inicia Apache nuevamente

### **Paso 3: Configurar Sendmail (Opcional)**

1. **Descarga sendmail para Windows**
   - Busca "sendmail for Windows"
   - Instala en `C:\xampp\sendmail\`

2. **Configura `sendmail.ini`**
   ```ini
   [sendmail]
   smtp_server=smtp.gmail.com
   smtp_port=587
   auth_username=tu-email@gmail.com
   auth_password=tu-contraseña-de-aplicación
   force_sender=tu-email@gmail.com
   ```

### **Paso 4: Usar Versión Simplificada**

El archivo `mail_simple.php` está configurado para pruebas:

1. **Verifica que funcione:**
   - Envía un mensaje desde el formulario
   - Revisa los logs en `email_log.txt` y `email_error_log.txt`

2. **Si funciona, vuelve a `mail.php`:**
   - Cambia en `js/sendmail.js` la URL de `mail_simple.php` a `mail.php`

### **Paso 5: Configuración de Gmail (Recomendado)**

Para usar Gmail como servidor SMTP:

1. **Habilita autenticación de 2 factores en Gmail**

2. **Genera contraseña de aplicación:**
   - Ve a Configuración de Google Account
   - Seguridad → Contraseñas de aplicación
   - Genera una para "Correo"

3. **Configura `php.ini`:**
   ```ini
   [mail function]
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = tu-email@gmail.com
   ```

## 🔧 **Archivos de Prueba**

### **test.php**
- Verifica configuración del servidor
- Accede a: `http://localhost/emprendimiento/test.php`

### **mail_simple.php**
- Versión simplificada para pruebas
- Menos validaciones, más fácil de debuggear

## 📝 **Logs de Error**

### **Archivos de log creados:**
- `email_log.txt` - Envíos exitosos
- `email_error_log.txt` - Errores de envío
- `rate_limit_*.txt` - Control de rate limiting

### **Ubicación de logs del sistema:**
- Apache: `C:\xampp\apache\logs\error.log`
- PHP: `C:\xampp\php\logs\php_error.log`

## 🚀 **Solución Rápida**

Si necesitas una solución inmediata:

1. **Usa la versión simplificada:**
   - El formulario ya está configurado para usar `mail_simple.php`

2. **Verifica permisos:**
   ```bash
   # En la carpeta del proyecto
   chmod 666 *.txt
   ```

3. **Reinicia servicios:**
   - Detén Apache y MySQL en XAMPP
   - Inicia Apache y MySQL nuevamente

## 📞 **Soporte**

Si el problema persiste:

1. **Revisa los logs de error**
2. **Verifica la configuración de XAMPP**
3. **Prueba con diferentes configuraciones de correo**

## ✅ **Verificación Final**

Para confirmar que todo funciona:

1. Envía un mensaje desde el formulario
2. Verifica que aparezca en `email_log.txt`
3. Revisa tu bandeja de entrada en `salinasgeganb@gmail.com`
4. Confirma que no hay errores en la consola del navegador 