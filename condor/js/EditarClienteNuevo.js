/**
 * EditarClienteNuevo.js
 * Manejo de edición de clientes usando formulario independiente
 */

// Función principal para abrir el modal de edición
function abrirModalEditarClienteNuevo(clienteId) {
    // Validar ID
    if (!clienteId || clienteId <= 0) {
        showMessage('ID de cliente inválido', 'error', 3000);
        return;
    }

    // Mostrar loading
    showMessage('Cargando formulario de edición...', 'info', 2000);

    // Realizar petición AJAX para obtener el formulario
    $.ajax({
        url: '../php/editar_cliente_nuevo.php',
        type: 'GET',
        data: { id: clienteId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Insertar el HTML del modal en el body si no existe
                if ($('#modalEditarClienteNuevo').length === 0) {
                    $('body').append(response.html);
                } else {
                    // Actualizar el contenido del modal existente
                    $('#modalEditarClienteNuevo').remove();
                    $('body').append(response.html);
                }

                // Configurar el modal y eventos
                configurarModalEditarClienteNuevo(clienteId);

                // Mostrar el modal
                var modal = new bootstrap.Modal(document.getElementById('modalEditarClienteNuevo'));
                modal.show();

                showMessage('Formulario cargado correctamente', 'success', 2000);
            } else {
                showMessage('Error: ' + response.message, 'error', 5000);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            showMessage('Error al cargar el formulario de edición', 'error', 5000);
        }
    });
}

// Configurar eventos del modal
function configurarModalEditarClienteNuevo(clienteId) {
    // Configurar validación en tiempo real
    $('#cl_nombre_nuevo, #cl_apellido_nuevo, #cl_email_nuevo').on('input', function() {
        validarCampoEnTiempoReal($(this));
    });

    // Configurar envío del formulario
    $('#editarClienteNuevoForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        // Validar formulario antes de enviar
        if (validarFormularioEdicion()) {
            enviarFormularioEdicion();
        }
    });

    // Configurar botón de guardar
    $('#saveEditarClienteNuevoBtn').off('click').on('click', function(e) {
        e.preventDefault();
        $('#editarClienteNuevoForm').trigger('submit');
    });
}

// Validación del formulario
function validarFormularioEdicion() {
    let esValido = true;
    
    // Limpiar errores previos
    $('.error-message').hide();
    $('.form-control').removeClass('is-invalid');
    
    // Validar nombre
    const nombre = $('#cl_nombre_nuevo').val().trim();
    if (!nombre) {
        mostrarErrorCampo('cl_nombre_nuevo', 'nombre-error', '⚠️ El nombre es requerido');
        esValido = false;
    }
    
    // Validar apellido
    const apellido = $('#cl_apellido_nuevo').val().trim();
    if (!apellido) {
        mostrarErrorCampo('cl_apellido_nuevo', 'apellido-error', '⚠️ El apellido es requerido');
        esValido = false;
    }
    
    // Validar email
    const email = $('#cl_email_nuevo').val().trim();
    if (!email) {
        mostrarErrorCampo('cl_email_nuevo', 'email-error', '⚠️ El email es requerido');
        esValido = false;
    } else if (!validarEmail(email)) {
        mostrarErrorCampo('cl_email_nuevo', 'email-error', '⚠️ Ingrese un email válido');
        esValido = false;
    }
    
    // Validar email del contacto si se proporciona
    const emailContacto = $('#cc_email_nuevo').val().trim();
    if (emailContacto && !validarEmail(emailContacto)) {
        showMessage('El email del contacto no es válido', 'error', 3000);
        $('#cc_email_nuevo').addClass('is-invalid');
        esValido = false;
    }
    
    return esValido;
}

// Enviar formulario de edición
function enviarFormularioEdicion() {
    // Deshabilitar botón para evitar envíos duplicados
    $('#saveEditarClienteNuevoBtn').prop('disabled', true).text('Actualizando...');
    
    // Obtener datos del formulario
    const formData = new FormData($('#editarClienteNuevoForm')[0]);
    
    // Agregar datos del contacto al FormData
    formData.append('cc_nombre', $('#cc_nombre_nuevo').val().trim());
    formData.append('cc_apellido', $('#cc_apellido_nuevo').val().trim());
    formData.append('cc_cargo', $('#cc_cargo_nuevo').val().trim());
    formData.append('cc_email', $('#cc_email_nuevo').val().trim());
    formData.append('cc_telefono', $('#cc_telefono_nuevo').val().trim());
    
    // Mostrar mensaje de procesamiento
    showMessage('Actualizando cliente...', 'info', 3000);
    
    // Enviar petición AJAX
    $.ajax({
        url: '../php/editar_cliente_nuevo.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Éxito: mostrar mensaje y cerrar modal
                showMessage(' ' + response.message, 'success', 3000);
                
                // Cerrar modal
                cerrarModalEdicion();
                
                // Actualizar solo la fila específica en la tabla
                actualizarFilaCliente(response.cliente_id, response.cliente_data);
                
            } else {
                // Error: mostrar mensaje de error
                showMessage('❌ ' + response.message, 'error', 5000);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            showMessage('❌ Error al actualizar el cliente', 'error', 5000);
        },
        complete: function() {
            // Rehabilitar botón
            $('#saveEditarClienteNuevoBtn').prop('disabled', false).html('<img src="../icons/16x/guardar16.png" alt="save" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Actualizar Cliente');
        }
    });
}

// Funciones auxiliares
function validarCampoEnTiempoReal(campo) {
    const valor = campo.val().trim();
    const fieldId = campo.attr('id');
    
    // Limpiar error previo
    campo.removeClass('is-invalid');
    
    switch (fieldId) {
        case 'cl_nombre_nuevo':
            if (!valor) {
                mostrarErrorCampo(fieldId, 'nombre-error', '⚠️ El nombre es requerido');
            } else {
                $('#nombre-error').hide();
            }
            break;
            
        case 'cl_apellido_nuevo':
            if (!valor) {
                mostrarErrorCampo(fieldId, 'apellido-error', '⚠️ El apellido es requerido');
            } else {
                $('#apellido-error').hide();
            }
            break;
            
        case 'cl_email_nuevo':
            if (!valor) {
                mostrarErrorCampo(fieldId, 'email-error', '⚠️ El email es requerido');
            } else if (!validarEmail(valor)) {
                mostrarErrorCampo(fieldId, 'email-error', '⚠️ Ingrese un email válido');
            } else {
                $('#email-error').hide();
            }
            break;
    }
}

function mostrarErrorCampo(campoId, errorId, mensaje) {
    $('#' + campoId).addClass('is-invalid');
    $('#' + errorId).text(mensaje).show();
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function cerrarModalEdicion() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarClienteNuevo'));
    if (modal) {
        modal.hide();
    }
    
    // Limpiar el formulario cuando se cierre completamente
    $('#modalEditarClienteNuevo').on('hidden.bs.modal', function () {
        $(this).remove(); // Remover el modal del DOM
    });
}

// Función para actualizar solo la fila específica del cliente en la tabla
function actualizarFilaCliente(clienteId, clienteData) {
    const filaId = '#cliente-row-' + clienteId;
    const fila = $(filaId);
    
    if (fila.length === 0) {
        console.warn('No se encontró la fila del cliente con ID:', clienteId);
        // Como alternativa, recargar la página
        setTimeout(() => {
            location.reload();
        }, 1500);
        return;
    }
    
    // Actualizar cada celda con los nuevos datos
    fila.find('.cl-nombre').text(clienteData.cl_nombre);
    fila.find('.cl-apellido').text(clienteData.cl_apellido);
    fila.find('.cl-empresa').text(clienteData.cl_empresa);
    fila.find('.cl-email').text(clienteData.cl_email);
    fila.find('.cl-telefono').text(clienteData.cl_telefono);
    fila.find('.cl-ciudad').text(clienteData.cl_ciudad);
    fila.find('.cl-pais').text(clienteData.cl_pais);
    fila.find('.cl-tipo').text(clienteData.cl_tipo);
    fila.find('.cl-estado').text(clienteData.cl_estado);
    
    // Agregar efecto visual para indicar que se actualizó
    fila.addClass('table-success');
    setTimeout(() => {
        fila.removeClass('table-success');
    }, 2000);
    
    console.log('✅ Fila del cliente actualizada correctamente:', clienteId);
}

// Función global para ser llamada desde el botón editar
window.abrirModalEditarClienteNuevo = abrirModalEditarClienteNuevo;