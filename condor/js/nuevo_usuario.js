$(document).ready(function() {

    // Usar delegación de eventos para el botón que se crea dinámicamente
    $(document).on('click', '#nuevoUsuarioBtn', function(e) {
        e.preventDefault();
        $('#myModalNuevoUsuario').modal('show');
    });

    // Función para alternar el estado del usuario en el modal de nuevo usuario
    window.toggleNuevoUserStatus = function() {
        const toggle = document.getElementById('toggleNuevoActive');
        const label = document.getElementById('nuevoStatusLabel');
        const input = document.getElementById('nuevo_us_activo');
        
        if (toggle.classList.contains('active')) {
            toggle.classList.remove('active');
            label.textContent = 'Inactivo';
            input.value = '0';
        } else {
            toggle.classList.add('active');
            label.textContent = 'Activo';
            input.value = '1';
        }
    };

    // Función para guardar el nuevo usuario
    window.saveNuevoUsuario = function() {
        // Obtener los datos del formulario
        const formData = {
            us_username: $('#nuevo_us_username').val(),
            us_nombre: $('#nuevo_us_nombre').val(),
            us_email: $('#nuevo_us_email').val(),
            us_password: $('#nuevo_us_password').val(),
            us_fecha_nacimiento: $('#nuevo_us_fecha_nacimiento').val(),
            us_rol: $('#nuevo_us_rol').val(),
            us_activo: $('#nuevo_us_activo').val()
        };
        
        // Validar campos requeridos
        let isValid = true;
        $('.error-message').hide();
        
        if (!formData.us_username) {
            $('#nuevo-username-error').show();
            isValid = false;
        }
        if (!formData.us_nombre) {
            $('#nuevo-nombre-error').show();
            isValid = false;
        }
        if (!formData.us_email) {
            $('#nuevo-email-error').show();
            isValid = false;
        }
        if (!formData.us_password) {
            $('#nuevo-password-error').show();
            isValid = false;
        }
        if (!formData.us_fecha_nacimiento) {
            $('#nuevo-fecha-error').show();
            isValid = false;
        }
        if (!formData.us_rol) {
            $('#nuevo-rol-error').show();
            isValid = false;
        }
        
        if (!isValid) {
            return;
        }
        
        // Enviar datos al servidor
        $.ajax({
            url: '../php/usuarios_nuevo.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                try {
                    console.log(response);
                    // const result = JSON.parse(response);
                    if (response.success) {
                        // Mostrar mensaje de éxito
                        showMessage('✅ Usuario creado exitosamente', 'success');
                        // Cerrar modal
                        $('#myModalNuevoUsuario').modal('hide');
                        // Limpiar formulario
                        $('#nuevoUsuarioForm')[0].reset();
                        // Recargar la tabla de usuarios
                        if (typeof funciones !== 'undefined' && funciones.cargarUsuarios) {
                            funciones.cargarUsuarios();
                        }
                    } else {
                        showMessage('❌ Error: ' + (result.message || 'Error al crear usuario'), 'error');
                    }
                } catch (e) {
                    showMessage('❌ Error inesperado al crear usuario', 'error');
                }
            },
            error: function(xhr, status, error) {
                showMessage('❌ Error de conexión al crear usuario', 'error');
            }
        });
    };

    // Limpiar formulario cuando se cierre el modal
    $('#myModalNuevoUsuario').on('hidden.bs.modal', function() {
        $('#nuevoUsuarioForm')[0].reset();
        $('.error-message').hide();
        // Resetear el toggle a activo
        $('#toggleNuevoActive').removeClass('active');
        $('#nuevoStatusLabel').text('Activo');
        $('#nuevo_us_activo').val('1');
    });

    // Inicializar el toggle switch cuando se abre el modal
    $('#myModalNuevoUsuario').on('shown.bs.modal', function() {
        // Asegurar que el toggle esté en estado activo por defecto
        $('#toggleNuevoActive').addClass('active');
        $('#nuevoStatusLabel').text('Activo');
        $('#nuevo_us_activo').val('1');
    });

});