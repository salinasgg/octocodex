

$(document).ready(function() {
    console.log('logout.js cargado');
    console.log('Document ready en logout.js');
    
    // Verificar si el elemento existe
    if ($('#logout-btn').length > 0 ) {
        console.log('Elemento #logout-btn encontrado');
    } else {
        console.log('Elemento #logout-btn NO encontrado');
        return;
    }
    
    $('#logout-btn').click(function(e) {
        console.log('Logout button clicked - preventDefault');
        // Prevenir la navegación por defecto del enlace
        e.preventDefault();
        
        console.log('Logout button clicked - iniciando AJAX');
        
        // Mostrar indicador visual
        $(this).text('Cerrando sesión...').prop('disabled', true);
        
        $.ajax({
            url: '../php/logout.php',
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                console.log('Enviando petición AJAX a ../php/logout.php');
            },
            success: function(response) {
                console.log('Logout response recibida:', response);
                
                if (response.success) {
                    console.log('Logout exitoso, redirigiendo a:', response.redirect);
                    // Usar la URL de redirección que viene del servidor
                    window.location.href = response.redirect;
                } else {
                    console.log('Logout fallido, redirigiendo directamente');
                    // Si falla, redirigir directamente
                    window.location.href = '../php/logout.php';
                }
            },
            error: function(xhr, status, error) {
                console.log('Logout error:', status, error);
                console.log('Response text:', xhr.responseText);
                // Si hay error en AJAX, redirigir directamente
                window.location.href = '../php/logout.php';
            }
        });
    });
    
    console.log('Evento click registrado en #logout-btn');
});