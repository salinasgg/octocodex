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
    },

    // Función para recargar la tabla de usuarios
    recargarTablaUsuarios: function() {
        console.log('🔄 Recargando tabla de usuarios...');
        
        $.ajax({
            url: '../php/usuarios.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                // Aplicar estilos CSS antes de mostrar la tabla
                funciones.aplicarEstilosTabla();
                
                // Solo actualizar la tabla si estamos en la vista de usuarios
                if ($('.users-table-container').length > 0) {
                    $('.main-content').html(populateTable(response));
                    console.log('✅ Tabla de usuarios actualizada');
                } else {
                    console.log('ℹ️ No estamos en la vista de usuarios, no se actualiza la tabla');
                }
            },
            error: function (xhr, status, error) {
                console.error('❌ Error al recargar tabla de usuarios:', error);
                showMessage('Error al actualizar la tabla de usuarios', 'error', 3000);
            }
        });
    },

    // Función para actualizar solo los datos de búsqueda sin recargar toda la tabla
    actualizarDatosBusqueda: function(usuarios) {
        if (typeof initializeUserSearch === 'function' && $('.users-table-container').length > 0) {
            // Actualizar los datos de búsqueda sin perder el estado de búsqueda actual
            const currentSearchTerm = $('#searchUsers').val();
            initializeUserSearch(usuarios);
            
            // Mantener el término de búsqueda actual si había uno
            if (currentSearchTerm) {
                $('#searchUsers').val(currentSearchTerm).trigger('input');
            }
            
            console.log('🔍 Datos de búsqueda actualizados');
        }
    }
};



function editUser(id) {
    console.log('🔍 ID del usuario a editar:', id);
    
    // Mostrar mensaje de carga
    //  showMessage('Cargando datos del usuario...', 'info', 2000);
    
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
                setTimeout(() => {
                    funciones.recargarTablaUsuarios();
                }, 200);
            } else if (response.error) {
                showMessage('Error: ' + response.error, 'error', 3000);
            } else {
                showMessage('Usuario actualizado exitosamente', 'success', 3000);
                // Cerrar la modal de Bootstrap y limpiar backdrop
                closeModalAndCleanup();
                setTimeout(() => {
                    funciones.recargarTablaUsuarios();
                }, 200);
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
                    funciones.recargarTablaUsuarios();
                } else if (response.error) {
                    showMessage(response.error, 'error', 3000);
                } else {
                    showMessage('Usuario eliminado exitosamente', 'success', 3000);
                    // Recargar la tabla de usuarios
                    funciones.recargarTablaUsuarios();
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
    
    // Crear el HTML completo de la tabla con contenedor específico y buscador
    let tableHTML = `
        <div class="users-table-container">
            <div class="container">
                <!-- Header con gradiente violeta -->
                <div class="header">                
                    <h1>
                        <img src="../icons/usuarios-white.png" alt="Usuarios" style="vertical-align: middle; margin-right: 10px;"> 
                        Gestión de Usuarios
                    </h1>
                    <p>Administra y visualiza todos los usuarios del sistema</p>                    
                </div>
                
                <!-- Controles superiores con buscador y botón nuevo -->
                <div class="controls-container" style="
                    background: var(--gradiente-violeta);
                    padding: 20px 30px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 15px;
                    border-bottom: 1px solid rgba(255,255,255,0.2);
                ">
                    <!-- Buscador en tiempo real -->
                    <div class="search-container" style="
                        display: flex;
                        align-items: center;
                        background: rgba(255, 255, 255, 0.15);
                        border-radius: 25px;
                        padding: 8px 20px;
                        min-width: 300px;
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.2);
                    ">
                        <i class="fas fa-search" style="color: rgba(255, 255, 255, 0.7); margin-right: 10px;"></i>
                        <input 
                            type="text" 
                            id="searchUsers" 
                            placeholder="Buscar usuarios por nombre, email o rol..." 
                            style="
                                background: transparent;
                                border: none;
                                color: white;
                                flex: 1;
                                font-size: 14px;
                                outline: none;
                            "
                        >
                        <div id="searchResults" style="
                            color: rgba(255, 255, 255, 0.8);
                            font-size: 12px;
                            margin-left: 10px;
                        "></div>
                    </div>
                    
                    <!-- Botón nuevo usuario -->
                    <button id="nuevoUsuarioBtn" class="btn-agregar" style="
                        background: rgba(255, 255, 255, 0.2);
                        border: 1px solid rgba(255, 255, 255, 0.3);
                        color: white;
                        padding: 10px 20px;
                        border-radius: 25px;
                        font-weight: 600;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        transition: all 0.3s ease;
                        cursor: pointer;
                    " onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-2px)'" 
                       onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'">
                        <img src="../icons/16x/agregar-usuario16.png" alt="Nuevo" style="vertical-align: middle; filter: brightness(0) invert(1);">
                        Nuevo Usuario
                    </button>
                </div>
                
                <!-- Información de búsqueda -->
                <div id="searchInfo" class="search-info" style="display: none; padding: 15px; background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                    <span id="searchInfoText"></span>
                    <button id="clearSearch" style="
                        margin-left: 10px;
                        background: #6c757d;
                        color: white;
                        border: none;
                        border-radius: 15px;
                        padding: 4px 12px;
                        font-size: 12px;
                        cursor: pointer;
                    ">Limpiar</button>
                </div>
                
                <div class="table-container">
                    <table class="users-table" id="usersTable">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>Usuario</th>
                                <th><i class="fas fa-envelope me-2"></i>Email</th>
                                <th><i class="fas fa-calendar me-2"></i>Fecha Registro</th>
                                <th><i class="fas fa-user-tag me-2"></i>Rol</th>
                                <th><i class="fas fa-toggle-on me-2"></i>Estado</th>
                                <th><i class="fas fa-cogs me-2"></i>Acciones</th>
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
                
                <!-- Estadísticas de la tabla -->
                <div class="table-stats" style="
                    padding: 15px 30px;
                    background: #f8f9fa;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-top: 1px solid #e9ecef;
                    font-size: 14px;
                    color: #6c757d;
                ">
                    <div>
                        <i class="fas fa-users me-2"></i>
                        Total de usuarios: <span id="totalUsers" class="fw-bold">${Array.isArray(usuarios) ? usuarios.length : 0}</span>
                    </div>
                    <div id="filteredStats" style="display: none;">
                        Mostrando: <span id="filteredCount" class="fw-bold">0</span> de <span id="totalCount" class="fw-bold">0</span>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            // Inicializar búsqueda en tiempo real
            $(document).ready(function() {
                initializeUserSearch(${JSON.stringify(usuarios)});
            });
        </script>
    `;

    console.log('📋 HTML generado:', tableHTML);
    return tableHTML;
}

/**
 * Inicializar funcionalidad de búsqueda en tiempo real para usuarios
 */
function initializeUserSearch(allUsers) {
    console.log('🔍 Inicializando búsqueda de usuarios con:', allUsers);
    
    let currentUsers = allUsers || [];
    
    // Evento de búsqueda en tiempo real
    $('#searchUsers').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        
        if (searchTerm === '') {
            // Mostrar todos los usuarios
            showAllUsers();
        } else {
            // Filtrar usuarios
            const filteredUsers = filterUsers(currentUsers, searchTerm);
            displayFilteredUsers(filteredUsers, searchTerm);
        }
    });
    
    // Evento para limpiar búsqueda
    $('#clearSearch').on('click', function() {
        $('#searchUsers').val('');
        showAllUsers();
    });
    
    // Tecla Escape para limpiar
    $('#searchUsers').on('keydown', function(e) {
        if (e.key === 'Escape') {
            $(this).val('');
            showAllUsers();
        }
    });
    
    function filterUsers(users, searchTerm) {
        return users.filter(user => {
            const nombre = (user.us_nombre || '').toLowerCase();
            const email = (user.us_email || '').toLowerCase();
            const rol = (user.us_rol || '').toLowerCase();
            const username = (user.us_username || '').toLowerCase();
            
            return nombre.includes(searchTerm) || 
                   email.includes(searchTerm) || 
                   rol.includes(searchTerm) ||
                   username.includes(searchTerm);
        });
    }
    
    function displayFilteredUsers(filteredUsers, searchTerm) {
        const tableBody = $('#usersTableBody');
        tableBody.empty();
        
        // Mostrar información de búsqueda
        $('#searchInfo').show();
        $('#searchInfoText').html(`
            <i class="fas fa-search me-2"></i>
            Búsqueda: "<strong>${searchTerm}</strong>" - 
            <span class="text-primary fw-bold">${filteredUsers.length}</span> resultados encontrados
        `);
        $('#filteredStats').show();
        $('#filteredCount').text(filteredUsers.length);
        $('#totalCount').text(currentUsers.length);
        
        // Mostrar resultados en tiempo real
        $('#searchResults').text(`${filteredUsers.length} de ${currentUsers.length}`);
        
        if (filteredUsers.length === 0) {
            tableBody.html(`
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">
                        <i class="fas fa-search-minus" style="font-size: 2em; margin-bottom: 10px; opacity: 0.5;"></i>
                        <br>No se encontraron usuarios que coincidan con "<strong>${searchTerm}</strong>"
                        <br><small class="text-muted">Intenta con otros términos de búsqueda</small>
                    </td>
                </tr>
            `);
        } else {
            // Renderizar usuarios filtrados
            filteredUsers.forEach(function(usuario) {
                const row = createUserRow(usuario, searchTerm);
                tableBody.append(row);
            });
        }
    }
    
    function showAllUsers() {
        $('#searchInfo').hide();
        $('#filteredStats').hide();
        $('#searchResults').text('');
        
        const tableBody = $('#usersTableBody');
        tableBody.empty();
        
        if (currentUsers.length === 0) {
            tableBody.html(`
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">
                        📭 No hay usuarios registrados
                    </td>
                </tr>
            `);
        } else {
            currentUsers.forEach(function(usuario) {
                const row = createUserRow(usuario);
                tableBody.append(row);
            });
        }
    }
    
    function createUserRow(usuario, highlightTerm = '') {
        // Obtener las iniciales del nombre
        let initials = usuario.us_nombre ? usuario.us_nombre.charAt(0).toUpperCase() : '?';
        
        // Determinar el estado
        let statusClass = usuario.us_activo == 1 ? 'status-active' : 'status-inactive';
        let statusText = usuario.us_activo == 1 ? 'Activo' : 'Inactivo';
        
        // Formatear fecha de registro
        let fechaRegistro = usuario.us_fecha_registro ? 
            new Date(usuario.us_fecha_registro).toLocaleDateString('es-ES') : 'No disponible';
        
        // Función para destacar texto de búsqueda
        function highlightText(text, term) {
            if (!term || !text) return text;
            const regex = new RegExp(`(${term})`, 'gi');
            return text.replace(regex, '<mark style="background: #fff3cd; padding: 1px 3px; border-radius: 3px;">$1</mark>');
        }
        
        // Aplicar destacado si hay término de búsqueda
        const nombre = highlightTerm ? highlightText(usuario.us_nombre || 'Sin nombre', highlightTerm) : (usuario.us_nombre || 'Sin nombre');
        const email = highlightTerm ? highlightText(usuario.us_email || 'Sin email', highlightTerm) : (usuario.us_email || 'Sin email');
        const rol = highlightTerm ? highlightText(usuario.us_rol || 'Usuario', highlightTerm) : (usuario.us_rol || 'Usuario');
        
        return `
            <tr data-user-id="${usuario.id}" style="transition: all 0.3s ease;">
                <td>
                    <div class="user-info">
                        <div class="avatar">${initials}</div>
                        <div class="user-details">
                            <div class="user-name">${nombre}</div>
                            <div class="user-email">${email}</div>
                        </div>
                    </div>
                </td>
                <td>${email}</td>
                <td>${fechaRegistro}</td>
                <td>${rol}</td>
                <td>
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#myModaleditUser" onclick="editUser(${usuario.id})" title="Editar usuario">
                            <img src="../icons/lapiz.png" alt="Editar" width="32" height="32">
                        </button>
                        <button class="btn btn-delete" onclick="deleteUser(${usuario.id})" title="Eliminar usuario">
                            <img src="../icons/basura.png" alt="Eliminar" width="32" height="32">
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }
}






