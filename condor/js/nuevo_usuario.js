$(document).ready(function() {
    console.log('🔧 nuevo_usuario.js cargado correctamente');

    // Usar delegación de eventos para el botón que se crea dinámicamente
    $(document).on('click', '#nuevoUsuarioBtn', function(e) {
        e.preventDefault();
        console.log('🖱️ Botón nuevo usuario clickeado');
        console.log('📋 Modal ID:', '#myModalNuevoUsuario');
        console.log('🔍 Modal existe:', $('#myModalNuevoUsuario').length > 0);
        
        $('#myModalNuevoUsuario').modal('show');
        console.log('✅ Modal de nuevo usuario abierto');
    });

    // Función para alternar el estado del usuario en el modal de nuevo usuario
    window.toggleNuevoUserStatus = function() {
        console.log('🔄 Alternando estado del nuevo usuario');
        const toggle = document.getElementById('toggleNuevoActive');
        const label = document.getElementById('nuevoStatusLabel');
        const input = document.getElementById('nuevo_us_activo');
        
        if (toggle.classList.contains('active')) {
            toggle.classList.remove('active');
            label.textContent = 'Inactivo';
            input.value = '0';
            console.log('❌ Usuario marcado como inactivo');
        } else {
            toggle.classList.add('active');
            label.textContent = 'Activo';
            input.value = '1';
            console.log('✅ Usuario marcado como activo');
        }
    };

    // Función para guardar el nuevo usuario
    window.saveNuevoUsuario = function() {
        console.log('💾 Guardando nuevo usuario...');
        
        // Obtener los datos del formulario
        const formData = {
            us_username: $('#nuevo_us_username').val(),
            us_nombre: $('#nuevo_us_nombre').val(),
            us_email: $('#nuevo_us_email').val(),
            us_password: $('#nuevo_us_password').val(),
            us_fecha_nacimiento: $('#nuevo_us_fecha_nacimiento').val(),
            us_rol: $('#nuevo_us_rol').val(),
            us_activo: $('#nuevo_us_activo').val(),
            us_puesto: $('#us_puesto').val()
        };
        
        console.log('📊 Datos del formulario:', formData);
        
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
        if (!formData.us_puesto) {
            $('#nuevo-puesto-error').show();
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
            console.log('❌ Validación fallida');
            return;
        }
        
        // Enviar datos al servidor
        $.ajax({
            url: '../php/usuarios_nuevo.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('📡 Respuesta del servidor:', response);
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
                        // Recargar la tabla de usuarios
                        if (typeof funciones !== 'undefined' && funciones.cargarUsuarios) {
                            funciones.cargarUsuarios(); // Actualizar tabla de usuarios
                        } else {
                            // Si no está disponible la función, recargar la página
                            location.reload();
                        }
                    } else {
                        showMessage('❌ Error: ' + (response.message || 'Error al crear usuario'), 'error');
                    }
                } catch (e) {
                    console.error('❌ Error al parsear respuesta:', e);
                    showMessage('❌ Error inesperado al crear usuario', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error en la petición AJAX:', error);
                console.error('❌ Status:', status);
                console.error('❌ Response:', xhr.responseText);
                showMessage('❌ Error de conexión al crear usuario', 'error');
            }
        });
    };

    // Limpiar formulario cuando se cierre el modal
    $('#myModalNuevoUsuario').on('hidden.bs.modal', function() {
        console.log('🔒 Modal cerrado, limpiando formulario');
        $('#nuevoUsuarioForm')[0].reset();
        $('.error-message').hide();
        // Resetear el toggle a activo
        $('#toggleNuevoActive').removeClass('active');
        $('#nuevoStatusLabel').text('Activo');
        $('#nuevo_us_activo').val('1');
    });

    // Verificar que el modal existe cuando se carga la página
    console.log('🔍 Verificando modal:', $('#myModalNuevoUsuario').length > 0 ? '✅ Existe' : '❌ No existe');
    console.log('🔍 Verificando botón:', $('#nuevoUsuarioBtn').length > 0 ? '✅ Existe' : '❌ No existe (se crea dinámicamente)');

    // Inicializar el toggle switch cuando se abre el modal
    $('#myModalNuevoUsuario').on('shown.bs.modal', function() {
        console.log('🎯 Modal abierto, inicializando toggle...');
        // Asegurar que el toggle esté en estado activo por defecto
        $('#toggleNuevoActive').addClass('active');
        $('#nuevoStatusLabel').text('Activo');
        $('#nuevo_us_activo').val('1');
    });

    // Ejecutar prueba automática después de 2 segundos
    setTimeout(function() {
        console.log('🧪 Ejecutando prueba automática...');
        if ($('#myModalNuevoUsuario').length > 0) {
            console.log('✅ Modal encontrado en el DOM');
        } else {
            console.log('❌ Modal NO encontrado en el DOM');
        }
        if ($('#nuevoUsuarioBtn').length > 0) {
            console.log('✅ Botón encontrado en el DOM');
        } else {
            console.log('❌ Botón NO encontrado en el DOM');
        }
    }, 2000);

});