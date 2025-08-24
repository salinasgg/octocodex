# Instalación y Configuración de PHPMailer

## 🎯 **Objetivo**
Instalar PHPMailer para envío confiable de correos electrónicos a `salinasgeganb@gmail.com`.

## 📦 **Instalación de PHPMailer**

### **Opción 1: Descarga Manual (Recomendado)**

1. **Descarga PHPMailer desde GitHub:**
   - Ve a: https://github.com/PHPMailer/PHPMailer
   - Haz clic en "Code" > "Download ZIP"
   - O usa este enlace directo: https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip

2. **Extrae los archivos:**
   - Extrae el archivo ZIP descargado
   - Copia la carpeta `src` del archivo extraído
   - Renómbrala como `PHPMailer`
   - Colócala en tu proyecto: `C:\xampp\htdocs\emprendimiento\PHPMailer\`

3. **Estructura de archivos:**
   ```
   emprendimiento/
   ├── PHPMailer/
   │   ├── PHPMailer.php
   │   ├── SMTP.php
   │   └── Exception.php
   ├── mail_phpmailer_real.php
   ├── js/
   └── ...
   ```

### **Opción 2: Usando Composer**

1. **Instala Composer** (si no lo tienes):
   - Descarga desde: https://getcomposer.org/download/

2. **Ejecuta en tu proyecto:**
   ```bash
   composer require phpmailer/phpmailer
   ```

## 🔧 **Configuración de Gmail**

### **Paso 1: Configurar Gmail**

1. **Ve a tu cuenta de Gmail** (la que usarás para enviar)
2. **Habilita la verificación en 2 pasos:**
   - Ve a Configuración de Google
   - Seguridad > Verificación en 2 pasos
   - Sigue las instrucciones para activarla

3. **Genera una contraseña de aplicación:**
   - Ve a Configuración de Google
   - Seguridad > Verificación en 2 pasos
   - Contraseñas de aplicación
   - Selecciona "Otra" y nombra como "PHPMailer DevStudio"
   - Copia la contraseña generada (16 caracteres)

### **Paso 2: Configurar el Archivo**

1. **Edita `mail_phpmailer_real.php`**
2. **Cambia estas líneas:**
   ```php
   $mail->Username = 'tu-email@gmail.com'; // Cambiar por tu email
   $mail->Password = 'tu-contraseña-de-aplicación'; // Cambiar por tu contraseña de aplicación
   ```

3. **Ejemplo:**
   ```php
   $mail->Username = 'ejemplo@gmail.com';
   $mail->Password = 'abcd efgh ijkl mnop';
   ```

## 🔄 **Actualizar el Formulario**

### **Opción 1: Usar el archivo con PHPMailer**

1. **Edita `js/sendmail.js`**
2. **Cambia la línea:**
   ```javascript
   var url = "mail.php";
   ```
   **Por:**
   ```javascript
   var url = "mail_phpmailer_real.php";
   ```

### **Opción 2: Mantener compatibilidad**

El archivo `mail_phpmailer_real.php` tiene fallback automático:
- Si PHPMailer está disponible → Lo usa
- Si no está disponible → Usa la función `mail()` nativa
- Siempre guarda mensajes en archivo

## ✅ **Verificación**

### **Para verificar que funciona:**

1. **Envía un mensaje desde el formulario**
2. **Revisa los logs:**
   - `email_log.txt` - Debería mostrar "Email enviado exitosamente"
   - `mensajes_contacto.txt` - Mensajes guardados
3. **Revisa tu bandeja de entrada en:** `salinasgeganb@gmail.com`

### **Para diagnosticar problemas:**

1. **Verifica que PHPMailer esté instalado:**
   - Confirma que existe: `PHPMailer/PHPMailer.php`

2. **Revisa los logs:**
   - `email_log.txt` - Para ver errores específicos
   - `email_error_log.txt` - Para errores generales

3. **Verifica la configuración:**
   - Email y contraseña correctos
   - Verificación en 2 pasos habilitada
   - Contraseña de aplicación generada

## 📝 **Ejemplo de Configuración Completa**

### **Si tu email es: ejemplo@gmail.com**

**En `mail_phpmailer_real.php`:**
```php
$mail->Username = 'ejemplo@gmail.com';
$mail->Password = 'abcd efgh ijkl mnop';
```

**En `js/sendmail.js`:**
```javascript
var url = "mail_phpmailer_real.php";
```

## ⚠️ **Notas Importantes**

1. **Usa la contraseña de aplicación** (16 caracteres), NO tu contraseña normal
2. **Habilita verificación en 2 pasos** antes de generar la contraseña
3. **Verifica que PHPMailer esté en la carpeta correcta**
4. **Revisa los logs** para diagnosticar problemas

## 🚀 **Ventajas de PHPMailer**

✅ **Más confiable** - Mejor manejo de errores
✅ **Soporte SMTP completo** - Configuración avanzada
✅ **HTML y texto plano** - Correos profesionales
✅ **Archivos adjuntos** - Si los necesitas en el futuro
✅ **Logs detallados** - Mejor debugging

## 📞 **Próximos Pasos**

1. **Instala PHPMailer** siguiendo las instrucciones
2. **Configura Gmail** con contraseña de aplicación
3. **Actualiza el archivo** con tus credenciales
4. **Prueba el formulario** - Debería enviar correos reales

¡Una vez configurado, PHPMailer enviará correos de forma confiable a salinasgeganb@gmail.com! 