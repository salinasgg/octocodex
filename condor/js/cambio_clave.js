/**
 * cambio_clave.js
 * Manejo del cambio de contraseña de usuarios
 */

$(document).ready(function() {
    // Inicializar eventos cuando el DOM esté listo
    inicializarEventosCambioClave();
});

// ===== INICIALIZACIÓN DE EVENTOS =====
function inicializarEventosCambioClave() {
    // Evento para abrir el modal desde el menú
    $('#cambiar-password-btn').off('click').on('click', function(e) {
        e.preventDefault();
        abrirModalCambioClave();
    });

    // Eventos para mostrar/ocultar contraseñas
    $('.toggle-password').off('click').on('click', function() {
        togglePasswordVisibility($(this));
    });

    // Evento para el envío del formulario
    $('#formCambiarPassword').off('submit').on('submit', function(e) {
        e.preventDefault();
        procesarCambioClave();
    });

    // Validación en tiempo real
    $('#passwordNueva, #passwordConfirmar').on('input', function() {
        validarPasswordsEnTiempoReal();
    });

    // Limpiar formulario al cerrar modal
    $('#modalCambiarPassword').on('hidden.bs.modal', function() {
        limpiarFormularioCambioClave();
    });
}

// ===== FUNCIÓN PARA ABRIR EL MODAL =====
function abrirModalCambioClave() {
    // Limpiar formulario antes de abrir
    limpiarFormularioCambioClave();
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalCambiarPassword'));
    modal.show();
    
    // Enfocar en el primer campo
    setTimeout(() => {
        $('#passwordActual').focus();
    }, 500);
}

// ===== FUNCIÓN PARA MOSTRAR/OCULTAR CONTRASEÑAS =====
function togglePasswordVisibility(button) {
    const targetId = button.data('target');
    const input = $('#' + targetId);
    const icon = button.find('i');
    
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
        button.attr('title', 'Ocultar contraseña');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
        button.attr('title', 'Mostrar contraseña');
    }
}

// ===== VALIDACIÓN EN TIEMPO REAL =====
function validarPasswordsEnTiempoReal() {
    const passwordNueva = $('#passwordNueva').val();
    const passwordConfirmar = $('#passwordConfirmar').val();
    
    // Validar longitud de nueva contraseña
    if (passwordNueva.length > 0 && passwordNueva.length < 6) {
        mostrarError('passwordNueva', 'La nueva contraseña debe tener al menos 6 caracteres');
    } else {
        ocultarError('passwordNueva');
    }
    
    // Validar coincidencia de contraseñas
    if (passwordConfirmar.length > 0) {
        if (passwordNueva !== passwordConfirmar) {
            mostrarError('passwordConfirmar', 'Las contraseñas no coinciden');
        } else {
            ocultarError('passwordConfirmar');
        }
    }
}

// ===== VALIDACIÓN COMPLETA DEL FORMULARIO =====
function validarFormularioCambioClave() {
    let esValido = true;
    
    // Limpiar errores previos
    ocultarTodosLosErrores();
    
    // Obtener valores
    const passwordActual = $('#passwordActual').val().trim();
    const passwordNueva = $('#passwordNueva').val().trim();
    const passwordConfirmar = $('#passwordConfirmar').val().trim();
    
    // Validar contraseña actual
    if (!passwordActual) {
        mostrarError('passwordActual', 'La contraseña actual es requerida');
        esValido = false;
    }
    
    // Validar nueva contraseña
    if (!passwordNueva) {
        mostrarError('passwordNueva', 'La nueva contraseña es requerida');
        esValido = false;
    } else if (passwordNueva.length < 6) {
        mostrarError('passwordNueva', 'La nueva contraseña debe tener al menos 6 caracteres');
        esValido = false;
    }
    
    // Validar confirmación
    if (!passwordConfirmar) {
        mostrarError('passwordConfirmar', 'Debe confirmar la nueva contraseña');
        esValido = false;
    } else if (passwordNueva !== passwordConfirmar) {
        mostrarError('passwordConfirmar', 'Las contraseñas no coinciden');
        esValido = false;
    }
    
    // Validar que las contraseñas sean diferentes
    if (passwordActual && passwordNueva && passwordActual === passwordNueva) {
        mostrarError('passwordNueva', 'La nueva contraseña debe ser diferente a la actual');
        esValido = false;
    }
    
    return esValido;
}

// ===== PROCESAR CAMBIO DE CONTRASEÑA =====
function procesarCambioClave() {
    // Validar formulario
    if (!validarFormularioCambioClave()) {
        return;
    }
    
    // Deshabilitar botón y mostrar estado de carga
    const botonSubmit = $('#btnCambiarPassword');
    botonSubmit.prop('disabled', true);
    const textoOriginal = botonSubmit.html();
    botonSubmit.html('<i class="fas fa-spinner fa-spin me-2"></i>Cambiando...');
    
    // Obtener datos del formulario
    const formData = new FormData();
    formData.append('passwordActual', $('#passwordActual').val().trim());
    formData.append('passwordNueva', $('#passwordNueva').val().trim());
    formData.append('passwordConfirmar', $('#passwordConfirmar').val().trim());
    
    // Mostrar mensaje de procesamiento
    if (typeof showMessage === 'function') {
        showMessage('Procesando cambio de contraseña...', 'info', 3000);
    }
    
    // Enviar petición AJAX
    $.ajax({
        url: '../php/cambio_clave.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Éxito: mostrar mensaje y cerrar modal
                if (typeof showMessage === 'function') {
                    showMessage('✅ ' + response.message, 'success', 5000);
                } else {
                    alert('✅ ' + response.message);
                }
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalCambiarPassword'));
                modal.hide();
                
                // Opcionalmente, cerrar sesión automáticamente después de cambiar contraseña
                if (response.logout) {
                    setTimeout(() => {
                        if (typeof showMessage === 'function') {
                            showMessage('Redirigiendo al login por seguridad...', 'info', 3000);
                        }
                        // Redirigir al login después de 3 segundos
                        setTimeout(() => {
                            window.location.href = '../index.php';
                        }, 3000);
                    }, 2000);
                }
                
            } else {
                // Error: mostrar mensaje de error
                if (typeof showMessage === 'function') {
                    showMessage('❌ ' + response.message, 'error', 5000);
                } else {
                    alert('❌ ' + response.message);
                }
                
                // Si es error de contraseña actual, enfocar en ese campo
                if (response.message.toLowerCase().includes('actual') || response.message.toLowerCase().includes('incorrec')) {
                    $('#passwordActual').focus().select();
                    mostrarError('passwordActual', response.message);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            
            const mensaje = 'Error al procesar la solicitud. Inténtelo de nuevo.';
            if (typeof showMessage === 'function') {
                showMessage('❌ ' + mensaje, 'error', 5000);
            } else {
                alert('❌ ' + mensaje);
            }
        },
        complete: function() {
            // Rehabilitar botón
            botonSubmit.prop('disabled', false);
            botonSubmit.html(textoOriginal);
        }
    });
}

// ===== FUNCIONES DE UTILIDAD PARA ERRORES =====
function mostrarError(campoId, mensaje) {
    const campo = $('#' + campoId);
    const errorDiv = $('#error-' + campoId);
    
    // Marcar campo como inválido
    campo.addClass('is-invalid');
    
    // Mostrar mensaje de error
    errorDiv.html('<i class="fas fa-exclamation-triangle me-1"></i>' + mensaje).show();
}

function ocultarError(campoId) {
    const campo = $('#' + campoId);
    const errorDiv = $('#error-' + campoId);
    
    // Remover marca de inválido
    campo.removeClass('is-invalid');
    
    // Ocultar mensaje de error
    errorDiv.hide();
}

function ocultarTodosLosErrores() {
    $('.form-control').removeClass('is-invalid');
    $('.error-message').hide();
}

// ===== LIMPIAR FORMULARIO =====
function limpiarFormularioCambioClave() {
    // Limpiar campos
    $('#formCambiarPassword')[0].reset();
    
    // Ocultar errores
    ocultarTodosLosErrores();
    
    // Restaurar iconos de visibilidad
    $('.toggle-password i').removeClass('fa-eye-slash').addClass('fa-eye');
    $('.toggle-password').attr('title', 'Mostrar contraseña');
    
    // Asegurar que todos los campos sean de tipo password
    $('#passwordActual, #passwordNueva, #passwordConfirmar').attr('type', 'password');
}

// ===== FUNCIÓN GLOBAL PARA COMPATIBILIDAD =====
window.abrirModalCambioClave = abrirModalCambioClave;