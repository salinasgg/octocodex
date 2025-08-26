$(document).ready(function() {
    $('#perfil-btn').click(function() {
        console.log('Botón perfil clickeado');
        
        // Obtener el user_id del dashboard (que está disponible en la página)
        const userId = window.userId || $('body').data('user-id');
        console.log('User ID obtenido:', userId);
        
        if (!userId) {
            console.error('No se pudo obtener el User ID');
            $('.main-content').html('<div class="alert alert-danger">Error: No se pudo identificar al usuario.</div>');
            return;
        }
        
        $('.main-content').empty();
        $('.main-content').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-3">Cargando perfil...</p></div>');
        
        $.ajax({
            url: '../perfil-content.php',
            method: 'GET',
            data: { user_id: userId },
            success: function(response) {
                console.log('Perfil cargado exitosamente');
                $('.main-content').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error cargando perfil:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                $('.main-content').html('<div class="alert alert-danger">Error al cargar el perfil. Por favor intente nuevamente.</div>');
            }
        });
    });
});