var funciones = {
    // Función para mostrar el modal de usuarios
    mostrarModalUsuarios: function () {
        $('#usuarios-btn').click(function () {            

            $.ajax({
                url: '../php/usuarios.php',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    // Aplicar estilos CSS antes de mostrar la tabla
                    funciones.aplicarEstilosTabla();
                    
                    $('.main-content').empty();
                    $('.main-content').html(populateTable(response));
                    console.log('Usuarios cargados exitosamente');
                },
                error: function (xhr, status, error) {
                    $('#usersTableBody').html(`
                        <tr>
                            <td colspan="6" style="text-align: center; color: #ef4444; padding: 40px;">
                                ❌ Error al cargar los datos: ${error}
                            </td>
                        </tr>
                    `);
                    showMessage('Error al cargar usuarios', 'error', 3000);
                }
            });
        });
    },

    // Función para aplicar estilos CSS de la tabla
    aplicarEstilosTabla: function() {
        // Verificar si ya existe el archivo CSS
        let existingLink = document.querySelector('link[href="../admin/css/estilo_tablas.css"]');
        if (!existingLink) {
            // Aplicar estilos CSS de la tabla
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '../admin/css/estilo_tablas.css';
            document.head.appendChild(link);
            console.log('✅ Estilos CSS de tabla aplicados');
        } else {
            console.log('ℹ️ Estilos CSS de tabla ya están cargados');
        }
    },

    // Función para inicializar eventos de la modal
    inicializarModalEvents: function() {
        // Evento cuando se cierra la modal (por cualquier medio)
        $('#myModaleditUser').on('hidden.bs.modal', function () {
            console.log('🔧 Modal cerrada, limpiando backdrop...');
            // Limpiar backdrop residual
            setTimeout(function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            }, 150);
        });
    }
};



function editUser(id) {
    console.log('🔍 ID del usuario a editar:', id);
    
    // Mostrar mensaje de carga
    // showMessage('Cargando datos del usuario...', 'info', 2000);
    
    $.ajax({
        url: '../php/usuarios_info.php',
        method: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function (response) {
            console.log('✅ Datos recibidos en editUser:', response);
            
            if (response.success && response.data) {
                // Llenar el formulario con los datos del usuario
                fillEditForm(response.data);
                
                // Mostrar la modal de Bootstrap
                var modal = new bootstrap.Modal(document.getElementById('myModaleditUser'), {
                    backdrop: 'static', // Evitar que se cierre al hacer clic fuera
                    keyboard: false,    // Evitar que se cierre con ESC
                    scrollable: false   // No permitir scroll interno, que se ajuste al contenido
                });
                modal.show();
                
                // Asegurar que el backdrop se muestre correctamente
                setTimeout(function() {
                    // Verificar que el backdrop esté visible
                    if ($('.modal-backdrop').length === 0) {
                        // Crear backdrop si no existe
                        $('body').append('<div class="modal-backdrop fade show"></div>');
                    }
                    // Asegurar que el body tenga la clase modal-open
                    $('body').addClass('modal-open');
                }, 100);
                
                // showMessage('Datos cargados correctamente', 'success', 2000);
            } else if (response.error) {
                showMessage('Error: ' + response.error, 'error', 3000);
            } else {
                showMessage('Error al cargar datos del usuario', 'error', 3000);
            }
        },
        error: function (xhr, status, error) {
            console.error('❌ Error en la petición:', xhr.status, xhr.statusText);
            console.error('Error completo:', error);
            showMessage('Error al cargar datos del usuario: ' + error, 'error', 3000);
        }
    });
}

// Función para llenar el formulario con los datos del usuario
function fillEditForm(userData) {
    console.log('📝 Llenando formulario con datos:', userData);
    
    // Llenar los campos del formulario
    $('#edit_user_id').val(userData.id);
    $('#us_username').val(userData.us_username || '');
    $('#us_email').val(userData.us_email || '');
    $('#us_nombre').val(userData.us_nombre || '');
    $('#us_fecha_nacimiento').val(userData.us_fecha_nacimiento || '');
    $('#us_rol').val(userData.us_rol || '');
    
    // Configurar el toggle de estado
    const isActive = userData.us_activo == 1;
    $('#us_activo').val(isActive ? '1' : '0');
    $('#statusLabel').text(isActive ? 'Activo' : 'Inactivo');
    
    // Actualizar la apariencia del toggle
    const toggleSwitch = $('#toggleActive');
    if (isActive) {
        toggleSwitch.addClass('active');
    } else {
        toggleSwitch.removeClass('active');
    }
}

// Función para cambiar el estado del usuario
function toggleUserStatus() {
    const toggleSwitch = $('#toggleActive');
    const statusLabel = $('#statusLabel');
    const hiddenInput = $('#us_activo');
    
    if (toggleSwitch.hasClass('active')) {
        // Cambiar a inactivo
        toggleSwitch.removeClass('active');
        statusLabel.text('Inactivo');
        hiddenInput.val('0');
    } else {
        // Cambiar a activo
        toggleSwitch.addClass('active');
        statusLabel.text('Activo');
        hiddenInput.val('1');
    }
}

// Función para guardar los cambios del usuario
function saveUser() {
    console.log('💾 Guardando cambios del usuario...');
    
    // Obtener los datos del formulario
    const formData = {
        user_id: $('#edit_user_id').val(),
        us_username: $('#us_username').val(),
        us_email: $('#us_email').val(),
        us_nombre: $('#us_nombre').val(),
        us_fecha_nacimiento: $('#us_fecha_nacimiento').val(),
        us_rol: $('#us_rol').val(),
        us_activo: $('#us_activo').val()
    };
    
    console.log('📋 Datos a enviar:', formData);
    
    // Validar campos requeridos
    if (!formData.us_username || !formData.us_email || !formData.us_nombre || !formData.us_rol) {
        showMessage('Por favor complete todos los campos requeridos', 'error', 3000);
        return;
    }
    
    // Mostrar mensaje de carga
    showMessage('Guardando cambios...', 'info', 2000);
    
    $.ajax({
        url: '../php/actualizar_usuario.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            console.log('✅ Respuesta del servidor:', response);
            
            if (response.success) {
                showMessage(response.success, 'success', 3000);
                // Cerrar la modal de Bootstrap y limpiar backdrop
                closeModalAndCleanup();
                // Recargar la tabla de usuarios
                $('#usuarios-btn').click();
            } else if (response.error) {
                showMessage('Error: ' + response.error, 'error', 3000);
            } else {
                showMessage('Usuario actualizado exitosamente', 'success', 3000);
                // Cerrar la modal de Bootstrap y limpiar backdrop
                closeModalAndCleanup();
                $('#usuarios-btn').click();
            }
        },
        error: function (xhr, status, error) {
            console.error('❌ Error en la petición:', xhr.status, xhr.statusText);
            console.error('Error completo:', error);
            showMessage('Error al actualizar usuario: ' + error, 'error', 3000);
        }
    });
}

// Función para cerrar la modal y limpiar el backdrop
function closeModalAndCleanup() {
    // Obtener la instancia de la modal
    var modal = bootstrap.Modal.getInstance(document.getElementById('myModaleditUser'));
    
    if (modal) {
        // Cerrar la modal
        modal.hide();
        
        // Limpiar el backdrop después de un pequeño delay
        setTimeout(function() {
            // Remover backdrop residual
            $('.modal-backdrop').remove();
            // Remover clase modal-open del body
            $('body').removeClass('modal-open');
            // Restaurar el padding del body
            $('body').css('padding-right', '');
        }, 150);
    }
}

function deleteUser(id) {
    showConfirm(`¿Estás seguro de querer eliminar este usuario?`, function() {
        console.log('🔍 ID del usuario a eliminar:', id);
        
        $.ajax({
            url: '../php/eliminar_usuario.php',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                console.log('✅ Respuesta del servidor:', response);
                
                if (response.success) {
                    showMessage(response.success, 'success', 3000);
                    // Recargar la tabla de usuarios
                    $('#usuarios-btn').click();
                } else if (response.error) {
                    showMessage(response.error, 'error', 3000);
                } else {
                    showMessage('Usuario eliminado exitosamente', 'success', 3000);
                    // Recargar la tabla de usuarios
                    $('#usuarios-btn').click();
                }
            },
            error: function (xhr, status, error) {
                console.error('❌ Error en la petición:', xhr.status, xhr.statusText);
                console.error('Error completo:', error);
                showMessage('Error al eliminar usuario: ' + error, 'error', 3000);
            }
        });
    });
};


function populateTable(usuarios) {
    console.log('🔍 Datos recibidos en populateTable:', usuarios);
    console.log('📊 Tipo de datos:', typeof usuarios);
    console.log('📋 Es array?', Array.isArray(usuarios));
    
    // Crear el HTML completo de la tabla con contenedor específico
    let tableHTML = `
        <div class="users-table-container">
            <div class="container">
                <div class="header">
                    <h1><img src="../icons/usuarios-white.png" alt="Usuarios" style="vertical-align: middle; margin-right: 10px;"> Gestión de Usuarios</h1>
                    <p>Administra y visualiza todos los usuarios del sistema</p>
                </div>

                <div class="table-container">
                    <table class="users-table" id="usersTable">
                        <thead>
                            <tr>
                                <th> Usuario</th>
                                <th> Email</th>
                                <th> Fecha Registro</th>
                                <th> Rol</th>
                                <th> Estado</th>
                                <th> Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
    `;

    // Verificar si usuarios es un array válido
    if (!usuarios || !Array.isArray(usuarios)) {
        console.error('❌ Error: usuarios no es un array válido');
        tableHTML += `
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #ef4444;">
                                    ❌ Error: Formato de datos inválido
                                    <br><small>Datos recibidos: ${JSON.stringify(usuarios)}</small>
                                </td>
                            </tr>
        `;
    } else if (usuarios.length === 0) {
        tableHTML += `
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">
                                    📭 No hay usuarios registrados
                                </td>
                            </tr>
        `;
    } else {
        // Procesar cada usuario
        usuarios.forEach(function(usuario) {
            console.log('👤 Procesando usuario:', usuario);
            
            // Obtener las iniciales del nombre (usando us_nombre)
            let initials = usuario.us_nombre ? usuario.us_nombre.charAt(0).toUpperCase() : '?';
            
            // Determinar el estado (usando us_activo)
            let statusClass = usuario.us_activo == 1 ? 'status-active' : 'status-inactive';
            let statusText = usuario.us_activo == 1 ? 'Activo' : 'Inactivo';
            
            // Formatear fecha de registro
            let fechaRegistro = usuario.us_fecha_registro ? 
                new Date(usuario.us_fecha_registro).toLocaleDateString('es-ES') : 'No disponible';
            
            tableHTML += `
                            <tr data-user-id="${usuario.id}">
                                <td>
                                    <div class="user-info">
                                        <div class="avatar">${initials}</div>
                                        <div class="user-details">
                                            <div class="user-name">${usuario.us_nombre || 'Sin nombre'}</div>
                                            <div class="user-email">${usuario.us_email || 'Sin email'}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>${usuario.us_email || 'No disponible'}</td>
                                <td>${fechaRegistro}</td>
                                <td>${usuario.us_rol || 'Usuario'}</td>
                                <td>
                                    <span class="status-badge ${statusClass}">${statusText}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn"  data-bs-toggle="modal" data-bs-target="#myModaleditUser" onclick="editUser(${usuario.id})">
                                            <img src="../icons/lapiz.png"  alt="Editar" width="32" height="32" color="red">
                                        </button>
                                        <button class="btn" onclick="deleteUser(${usuario.id})">
                                            <img src="../icons/basura.png" alt="Descripción del ícono" width="32" height="32">
                                        </button>
                                    </div>
                                </td>
                            </tr>
            `;
        });
    }

    // Cerrar la tabla y contenedores
    tableHTML += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    console.log('📋 HTML generado:', tableHTML);
    return tableHTML;
}






