# Configuración de Correos en XAMPP

## 🎯 **Objetivo**
Configurar XAMPP para que el formulario de contacto envíe correos reales a `salinasgeganb@gmail.com`.

## 📧 **Configuración Rápida con Gmail**

### **Paso 1: Configurar php.ini**

1. **Abre el archivo:** `C:\xampp\php\php.ini`
2. **Busca la sección `[mail function]`**
3. **Reemplaza o agrega estas líneas:**

```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = tu-email@gmail.com
sendmail_path = "C:\xampp\sendmail\sendmail.exe -t"
```

### **Paso 2: Configurar sendmail.ini**

1. **Abre el archivo:** `C:\xampp\sendmail\sendmail.ini`
2. **Busca la sección `[smtp]`**
3. **Configura:**

```ini
[smtp]
smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=tu-email@gmail.com
auth_password=tu-contraseña-de-aplicación
force_sender=tu-email@gmail.com
hostname=localhost
```

### **Paso 3: Configurar Gmail**

1. **Ve a tu cuenta de Gmail**
2. **Habilita la verificación en 2 pasos**
3. **Genera una contraseña de aplicación:**
   - Ve a Configuración > Cuentas e importación
   - En "Acceso a la cuenta" > "Contraseñas de aplicación"
   - Selecciona "Otra" y nombra como "XAMPP"
   - Copia la contraseña generada (16 caracteres)

### **Paso 4: Reiniciar Servicios**

1. **Detén Apache en XAMPP Control Panel**
2. **Inicia Apache nuevamente**
3. **Prueba el formulario**

## 🔧 **Configuración Alternativa con Mercury**

### **Opción 1: Usar Mercury (Incluido en XAMPP)**

1. **Descarga Mercury desde:** https://www.pmail.com/
2. **Instálalo en:** `C:\xampp\Mercury\`
3. **Configura Mercury:**
   - Dominio: `localhost`
   - Puerto SMTP: `25`
4. **Configura php.ini:**
   ```ini
   [mail function]
   SMTP = localhost
   smtp_port = 25
   sendmail_from = noreply@devstudio.com
   ```

### **Opción 2: Configuración Manual**

1. **Edita `C:\xampp\php\php.ini`:**
   ```ini
   [mail function]
   SMTP = localhost
   smtp_port = 25
   sendmail_from = noreply@devstudio.com
   ```

2. **Instala un servidor SMTP local:**
   - **hMailServer** (gratis)
   - **Postfix** (para Windows)
   - **Mercury** (recomendado)

## 📝 **Verificación**

### **Para verificar que funciona:**

1. **Envía un mensaje desde el formulario**
2. **Revisa los logs:**
   - `email_log.txt` - Envíos exitosos
   - `email_error_log.txt` - Errores
   - `mensajes_contacto.txt` - Mensajes guardados

3. **Revisa tu bandeja de entrada en:** `salinasgeganb@gmail.com`

### **Para diagnosticar problemas:**

1. **Revisa los logs de XAMPP:**
   - `C:\xampp\apache\logs\error.log`
   - `C:\xampp\sendmail\error.log`

2. **Prueba la función mail():**
   - Abre `phpinfo.php` en tu navegador
   - Busca la sección "mail"

## 🚀 **Solución Temporal (Ya Funciona)**

Si no puedes configurar SMTP ahora, el formulario:

✅ **Guarda mensajes en `mensajes_contacto.txt`**
✅ **Muestra confirmación al usuario**
✅ **Registra logs detallados**
✅ **Intenta enviar correo real (si está configurado)**

## 📞 **Próximos Pasos**

1. **Prueba el formulario ahora** - Funciona guardando en archivo
2. **Configura Gmail SMTP** - Para envío real de correos
3. **Verifica los logs** - Para monitorear el funcionamiento

¡El formulario ya está funcionando! Los mensajes se guardan y también intenta enviar correos reales si tienes SMTP configurado. 