// ===== SCRIPT DE PROCESAMIENTO DE LOGIN =====
// Esperar a que el documento esté completamente cargado
$(document).ready(function () {
    console.log('loginProcess.js cargado correctamente');

    // ===== VARIABLES GLOBALES =====
    const loginForm = $('#loginForm');
    const loginBtn = $('#loginBtn');
    const successAlert = $('#successAlert');
    const errorAlert = $('#errorAlert');
    const successMessage = $('#successMessage');
    const errorMessage = $('#errorMessage');

    // ===== FUNCIÓN PARA MOSTRAR ALERTAS =====
    function showAlert(type, message) {
        // Ocultar todas las alertas primero
        successAlert.hide();
        errorAlert.hide();

        // Mostrar la alerta correspondiente
        if (type === 'success') {
            successMessage.text(message);
            successAlert.fadeIn();
        } else {
            errorMessage.text(message);
            errorAlert.fadeIn();
        }

        // Ocultar automáticamente después de 5 segundos
        setTimeout(() => {
            successAlert.fadeOut();
            errorAlert.fadeOut();
        }, 5000);
    }

    // ===== FUNCIÓN PARA VALIDAR FORMULARIO =====
    function validateForm() {
        const username = $('#username').val().trim();
        const password = $('#password').val();

        // Validar que el usuario no esté vacío
        if (!username) {
            showAlert('error', 'Por favor ingresa tu usuario');
            $('#username').focus();
            return false;
        }

        // Validar que la contraseña no esté vacía
        if (!password) {
            showAlert('error', 'Por favor ingresa tu contraseña');
            $('#password').focus();
            return false;
        }

        // Validar longitud mínima de contraseña
        if (password.length < 6) {
            showAlert('error', 'La contraseña debe tener al menos 6 caracteres');
            $('#password').focus();
            return false;
        }

        return true;
    }

    // ===== FUNCIÓN PARA MOSTRAR ESTADO DE CARGA =====
    function setLoadingState(loading) {
        const btnText = loginBtn.find('.btn-text');
        const spinner = loginBtn.find('.spinner-border');

        if (loading) {
            // Estado de carga: deshabilitar botón y mostrar spinner
            loginBtn.prop('disabled', true);
            loginBtn.addClass('loading');
            btnText.html('<i class="fas fa-spinner fa-spin me-2"></i>Iniciando sesión...');
            spinner.removeClass('d-none');
        } else {
            // Estado normal: habilitar botón y ocultar spinner
            loginBtn.prop('disabled', false);
            loginBtn.removeClass('loading');
            btnText.html('<i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión');
            spinner.addClass('d-none');
        }
    }

    // ===== MANEJADOR DEL ENVÍO DEL FORMULARIO =====
    loginForm.on('submit', function (event) {
        event.preventDefault(); // Evitar la recarga de la página

        console.log('Formulario enviado');

        // Validar el formulario antes de enviar
        if (!validateForm()) {
            return;
        }

        // Obtener los datos del formulario
        const formData = {
            username: $('#username').val().trim(),
            password: $('#password').val(),
            remember: $('#rememberMe').is(':checked')
        };

        console.log('Datos del formulario:'+ JSON.stringify(formData));

        // Mostrar estado de carga
        setLoadingState(true);

        // ===== SIMULACIÓN DE ENVÍO AJAX =====
        // (Reemplazar con tu lógica real de envío al servidor)
        // setTimeout(() => {
        //     // Simular validación de credenciales
        //     if (formData.username === 'admin' && formData.password === '123456') {
        //         // Login exitoso
        //         showAlert('success', '¡Inicio de sesión exitoso! Redirigiendo...');
                
        //         // Simular redirección después de 2 segundos
        //         setTimeout(() => {
        //             window.location.href = 'dashboard.html'; // Cambiar por tu URL de destino
        //         }, 2000);
        //     } else {
        //         // Login fallido
        //         showAlert('error', 'Usuario o contraseña incorrectos');
        //         setLoadingState(false);
        //     }
        // }, 2000);

        // ===== CÓDIGO AJAX REAL (DESCOMENTAR Y MODIFICAR SEGÚN NECESITES) =====
        
        $.ajax({
            type: 'POST',
            url: 'php/loginprocess.php', // URL de tu archivo PHP
            data: formData,
            dataType: 'json',
            timeout: 10000, // 10 segundos de timeout
            beforeSend: function() {
                setLoadingState(true);
            },
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    // Login exitoso
                    showAlert('success', response.message || 'Inicio de sesión exitoso');
                    
                    // Redirigir según el rol del usuario
                    setTimeout(function() {
                        if (response.data && response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = 'dashboard.php';
                        }
                    }, 1500);
                    
                } else {
                    // Login fallido
                    showAlert('error', response.message || 'Error de inicio de sesión');
                    setLoadingState(false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                
                let errorMessage = 'Error de conexión. Inténtalo de nuevo.';
                
                if (status === 'timeout') {
                    errorMessage = 'La solicitud tardó demasiado. Verifica tu conexión.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Error interno del servidor.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Archivo de procesamiento no encontrado.';
                }
                
                showAlert('error', errorMessage);
                setLoadingState(false);
            }
        });
        
    });

    // ===== EVENT LISTENERS ADICIONALES =====
    
    // Navegación con Enter en los campos
    $('#username').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $('#password').focus();
        }
    });

    $('#password').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            loginForm.submit();
        }
    });

    // Enlaces adicionales
    $('#registerLink').on('click', function(e) {
        e.preventDefault();
        showAlert('info', 'Funcionalidad de registro próximamente disponible');
    });

    $('#forgotPasswordLink').on('click', function(e) {
        e.preventDefault();
        showAlert('info', 'Funcionalidad de recuperación de contraseña próximamente disponible');
    });

    // Botones sociales
    $('.btn-social').on('click', function(e) {
        e.preventDefault();
        showAlert('info', 'Inicio de sesión con redes sociales próximamente disponible');
    });

    // ===== INICIALIZACIÓN =====
    // Poner foco en el primer campo al cargar
    $('#username').focus();

    // Limpiar cualquier mensaje de error al cargar
    successAlert.hide();
    errorAlert.hide();

    console.log('loginProcess.js inicializado correctamente');
});