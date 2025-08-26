// Variable que contiene el HTML del modal de editar perfil
// Este modal se crea dinámicamente cuando se hace clic en "Editar Perfil"
var modalEditarPerfil = `
<!-- Modal para Editar Perfil -->
<div class="modal fade" id="myModalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">

            <div class="modal-body">
                <!-- El contenido se cargará dinámicamente aquí -->
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3">Cargando formulario de edición...</p>
                </div>
            </div>
        </div>
    </div>
</div>
`;


// Variable que contiene el HTML del formulario de edición de perfil
// Este contenido se carga dentro del modal cuando se abre
var ContenidoModalEditarPerfil = `
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="edit-container">
                <!-- Header del formulario -->
                <div class="edit-header">
                    <h1><i class="fas fa-user-edit me-2"></i>Editar Perfil</h1>
                    <p class="mb-0">Actualiza tu información personal</p>
                </div>

                <!-- Contenido del formulario -->
                <div class="edit-content">
    
                        </div>
                    <?php endif; ?>

                    <!-- Formulario principal de edición -->
                    <form id="formEditarPerfil" method="POST" enctype="multipart/form-data">
                        <!-- Sección para mostrar la foto de perfil actual -->
                        <div class="text-center mb-4">
                            <div class="current-avatar">
                                
                                    <!-- Si hay foto de perfil, mostrarla -->
                                    <img src=">" 
                                         alt="Foto de perfil actual" 
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <i class="fas fa-user" style="display: none;"></i>
                             
                                    <!-- Si no hay foto, mostrar ícono por defecto -->
                                    <i class="fas fa-user"></i>
                                
                            </div>
                            <small class="text-muted">Foto de perfil actual</small>
                        </div>

                        <!-- Campo para subir nueva foto de perfil -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-camera me-2"></i>Nueva foto de perfil
                            </label>
                            <div class="file-input-wrapper">
                                <input type="file" name="foto_perfil" id="foto_perfil" class="file-input" accept="image/*">
                                <label for="foto_perfil" class="file-input-label">
                                    <i class="fas fa-cloud-upload-alt me-2"></i>
                                    <span id="file-name">Seleccionar imagen (JPG, PNG, GIF - Máx. 5MB)</span>
                                </label>
                            </div>
                            <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB</small>
                        </div>

                        <!-- Fila con campos de nombre y apellido -->
                        <div class="row">
                            <!-- Campo de nombre -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user me-2"></i>Nombre
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo htmlspecialchars($userData['us_nombre'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <!-- Campo de apellido -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="apellido" class="form-label">
                                        <i class="fas fa-user me-2"></i>Apellido
                                    </label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" 
                                           value="<?php echo htmlspecialchars($userData['us_apellido'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Campo de email -->
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($userData['us_email'] ?? ''); ?>" required>
                        </div>

                        <!-- Campo de biografía -->
                        <div class="form-group">
                            <label for="bio" class="form-label">
                                <i class="fas fa-quote-left me-2"></i>Biografía
                            </label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" 
                                      placeholder="Cuéntanos algo sobre ti..."><?php echo htmlspecialchars($userData['us_bio'] ?? ''); ?></textarea>
                        </div>

                        <!-- Campo de URL de perfil personal -->
                        <div class="form-group">
                            <label for="url_perfil" class="form-label">
                                <i class="fas fa-link me-2"></i>URL de perfil personal
                            </label>
                            <input type="url" class="form-control" id="url_perfil" name="url_perfil" 
                                   value="<?php echo htmlspecialchars($userData['us_url_perfil'] ?? ''); ?>" 
                                   placeholder="https://tu-sitio-web.com">
                            <small class="text-muted">Opcional: Enlace a tu sitio web o perfil social</small>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex gap-3 justify-content-center mt-4">
                            <button type="submit" class="btn btn-primary" id="btnGuardaEditarcambios">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
`;




// Función principal que se ejecuta cuando el documento está listo
// $(document).ready() asegura que el DOM esté completamente cargado antes de ejecutar el código
$(document).ready(function() {
    // Obtener el ID del usuario desde el atributo data del body o desde la URL
    // Primero busca en el atributo data-user-id del body, luego en window.userId, si no encuentra nada usa null
    let userIdPerfil = $('body').attr('data-user-id') || window.userId || null;

    // Evento click para el botón "Editar Perfil"
    // Se ejecuta cuando el usuario hace clic en el botón con ID "btnEditarPerfil"
    $("#btnEditarPerfil").click(function(e) {
        // Prevenir el comportamiento por defecto del botón (evitar recarga de página)
        e.preventDefault();
        console.log("btnEditarPerfil clicked");
        
        // Verificar si el modal ya existe en el DOM (Document Object Model)
        // $('#myModalEditarPerfil').length devuelve 0 si no existe, mayor a 0 si existe
        if (!$('#myModalEditarPerfil').length) {
            // Si el modal no existe, agregarlo al body del documento
            $('body').append(modalEditarPerfil);
            console.log("modalEditarPerfil agregada al body");
        }

        // Mostrar el modal usando el método modal() de Bootstrap
        $('#myModalEditarPerfil').modal('show');

        // Verificar si el archivo CSS de edición ya está cargado en la página
        // document.querySelector('link[href="../css/style_editar.css"]') busca un elemento <link> con ese href
        // El operador ! niega el resultado, así que la condición es verdadera si NO encuentra el CSS
        if (!document.querySelector('link[href="../css/style_editar.css"]')) {
            
            // Crear un nuevo elemento <link> para cargar el archivo CSS
            // document.createElement('link') crea un elemento HTML <link> vacío
            const link = document.createElement('link');
            
            // Establecer el atributo 'rel' del elemento link
            // 'stylesheet' indica que es un archivo CSS
            link.rel = 'stylesheet';
            
            // Establecer la ruta del archivo CSS a cargar
            // '../css/style_editar.css' es la ruta relativa al archivo CSS
            link.href = '../css/style_editar.css';
            
            // Agregar el elemento <link> al <head> del documento HTML
            // document.head obtiene el elemento <head> de la página
            // appendChild() agrega el elemento link como hijo del head
            document.head.appendChild(link);
        }
        
        // Agregar el contenido del formulario al modal-body
        // .html() reemplaza todo el contenido HTML del elemento seleccionado
        $('#myModalEditarPerfil .modal-body').html(ContenidoModalEditarPerfil);
        console.log("ContenidoModalEditarPerfil agregado al modal-body");
        
        // Realizar petición AJAX para obtener los datos del usuario
        $.ajax({
            url: '../obtener.php', // URL del archivo PHP que devuelve los datos del usuario
            method: 'GET', // Método HTTP GET para obtener datos
            data: {
                user_id: userIdPerfil // Enviar el ID del usuario como parámetro
            },
            dataType: 'json', // Especificar que esperamos una respuesta en formato JSON
            success: function(response) {
                // Función que se ejecuta si la petición AJAX es exitosa
                
                // Verificar si hay error en la respuesta del servidor
                if (response.error) {
                    console.error('Error del servidor:', response.error);
                    showMessage('Error al cargar los datos del usuario: ' + response.error, 'error');
                    return; // Salir de la función si hay error
                }
                
                // Mostrar en consola la respuesta completa del servidor para debugging
                console.log('Respuesta del servidor:', response);
                
                // Actualizar los campos del formulario con los datos del usuario
                // .val() establece el valor de los campos de entrada
                $('#nombre').val(response.us_nombre || ''); // Usar el valor de la respuesta o cadena vacía si es null
                $('#apellido').val(response.us_apellido || '');
                $('#email').val(response.us_email || '');
                $('#bio').val(response.us_bio || '');
                $('#url_perfil').val(response.us_url_perfil || '');

                // Actualizar la vista previa de la foto de perfil actual en el modal
                console.log('Foto de perfil del usuario:', response.us_foto_perfil);
                
                // Verificar si el usuario tiene una foto de perfil
                if (response.us_foto_perfil) {
                    // Construir la ruta completa de la imagen
                    const fotoSrc = '../' + response.us_foto_perfil;
                    console.log('Ruta completa de la foto:', fotoSrc);
                    
                    // Actualizar el atributo src de la imagen y mostrarla
                    $('.current-avatar img').attr('src', fotoSrc);
                    $('.current-avatar img').show(); // Mostrar la imagen
                    $('.current-avatar i').hide(); // Ocultar el ícono por defecto
                    
                    console.log('Foto de perfil actualizada en el modal');
                } else {
                    // Si no hay foto de perfil, mostrar el ícono por defecto
                    console.log('No hay foto de perfil, mostrando ícono por defecto');
                    $('.current-avatar img').hide(); // Ocultar la imagen
                    $('.current-avatar i').show(); // Mostrar el ícono
                }
                
                console.log('Datos del usuario cargados correctamente');
            },
            error: function(xhr, status, error) {
                // Función que se ejecuta si hay error en la petición AJAX
                console.error('Error al cargar datos:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                showMessage('Error al cargar los datos del usuario', 'error');
            }
        });

        // Inicializar los eventos del formulario después de cargar el contenido
        // Verificar si la función initializeEditForm existe antes de llamarla
        if (typeof initializeEditForm === 'function') {
            initializeEditForm();
        }
    }); // fin del evento click del botón "Editar Perfil"

    // Función para inicializar el formulario de edición
    // Esta función configura los eventos del formulario cuando se envía
    function initializeEditForm() {
        // Evento submit del formulario (cuando se presiona "Guardar Cambios")
        $('#formEditarPerfil').submit(function(e) {
            // Prevenir el envío tradicional del formulario (evitar recarga de página)
            e.preventDefault();
            
            // Mostrar mensaje de "enviando datos" al usuario
            showMessage('Enviando datos al servidor...', 'info');
            console.log('Enviando datos al servidor...');

            // Crear un objeto FormData con todos los datos del formulario
            // FormData es necesario para enviar archivos junto con otros datos
            const formData = new FormData(this);
            
            // Agregar el user_id al FormData para que el servidor sepa qué usuario actualizar
            formData.append('user_id', userIdPerfil);
            
            console.log('Datos del formulario:', formData);

            // Realizar petición AJAX para enviar los datos al servidor
            $.ajax({
                url: '../php/actualizaperfil.php', // URL del archivo PHP que procesará los datos
                type: 'POST', // Método HTTP POST para enviar datos
                data: formData, // Los datos del formulario a enviar
                processData: false, // No procesar los datos (necesario para FormData)
                contentType: false, // No establecer el tipo de contenido (necesario para FormData)
                dataType: 'json', // Esperar respuesta JSON del servidor
                success: function(response) {
                    // Función que se ejecuta si la petición es exitosa
                    // Con dataType: 'json', jQuery automáticamente parsea la respuesta JSON
                    console.log('Respuesta del servidor:', response);
                    
                    // Mostrar el mensaje de respuesta del servidor al usuario
                    showMessage(response.mensaje, response.tipo);
                    
                    // Si la actualización fue exitosa, cerrar el modal y actualizar solo el perfil
                    if(response.tipo === 'success') {
                        // Cerrar el modal después de un breve delay (1.5 segundos)
                        setTimeout(() => {
                            $('#myModalEditarPerfil').modal('hide'); // Ocultar el modal
                            // Actualizar solo el contenido del perfil sin recargar toda la página
                            $(".main-content").load('perfil-content.php', { user_id: userIdPerfil });
                        }, 1500);
                    }
                },
                error: function(xhr, status, error) {
                    // Función que se ejecuta si hay error en la petición
                    console.error('Error AJAX:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    showMessage('Error al enviar los datos al servidor', 'error');
                }
            }); // fin de la petición AJAX
        }); // fin del evento submit del formulario
    } // fin de la función initializeEditForm

}); // fin de $(document).ready




