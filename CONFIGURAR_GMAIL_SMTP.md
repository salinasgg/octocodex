# Configuración de Gmail SMTP para Envío de Correos

## 🎯 **Objetivo**
Configurar XAMPP para enviar correos reales a `salinasgeganb@gmail.com` usando Gmail SMTP.

## 📧 **Configuración Paso a Paso**

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
   - Selecciona "Otra" y nombra como "XAMPP DevStudio"
   - Copia la contraseña generada (16 caracteres)

### **Paso 2: Configurar php.ini**

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

### **Paso 3: Configurar sendmail.ini**

1. **Abre el archivo:** `C:\xampp\sendmail\sendmail.ini`
2. **Busca la sección `[smtp]`**
3. **Reemplaza con:**

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

### **Paso 4: Reiniciar Servicios**

1. **Detén Apache en XAMPP Control Panel**
2. **Inicia Apache nuevamente**
3. **Prueba el formulario**

## 🔧 **Verificación**

### **Para verificar que funciona:**

1. **Envía un mensaje desde el formulario**
2. **Revisa los logs:**
   - `email_log.txt` - Debería mostrar "Email enviado exitosamente"
   - `mensajes_contacto.txt` - Mensajes guardados
3. **Revisa tu bandeja de entrada en:** `salinasgeganb@gmail.com`

### **Para diagnosticar problemas:**

1. **Revisa los logs de XAMPP:**
   - `C:\xampp\apache\logs\error.log`
   - `C:\xampp\sendmail\error.log`

2. **Prueba la función mail():**
   - Abre `phpinfo.php` en tu navegador
   - Busca la sección "mail"

## 📝 **Ejemplo de Configuración**

### **Si tu email es: ejemplo@gmail.com**

**En `C:\xampp\php\php.ini`:**
```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = ejemplo@gmail.com
sendmail_path = "C:\xampp\sendmail\sendmail.exe -t"
```

**En `C:\xampp\sendmail\sendmail.ini`:**
```ini
[smtp]
smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=ejemplo@gmail.com
auth_password=abcd efgh ijkl mnop
force_sender=ejemplo@gmail.com
hostname=localhost
```

## ⚠️ **Notas Importantes**

1. **Usa la contraseña de aplicación** (16 caracteres), NO tu contraseña normal
2. **Habilita verificación en 2 pasos** antes de generar la contraseña
3. **Reinicia Apache** después de cualquier cambio
4. **Verifica los logs** para diagnosticar problemas

## 🚀 **Estado Actual**

✅ **El formulario funciona** - Guarda mensajes en archivo
✅ **Intenta envío real** - Si SMTP está configurado
✅ **Destinatario fijo** - Siempre envía a `salinasgeganb@gmail.com`
✅ **Logs detallados** - Para monitoreo y debugging

## 📞 **Próximos Pasos**

1. **Configura Gmail SMTP** siguiendo las instrucciones
2. **Prueba el formulario** - Debería enviar correos reales
3. **Verifica los logs** - Para confirmar el funcionamiento

¡Una vez configurado, todos los mensajes se enviarán automáticamente a salinasgeganb@gmail.com! 