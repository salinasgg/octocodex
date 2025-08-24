$(document).ready(function() {
    $("#contactForm").on("submit", function(event) {
        // Evita que el formulario se envíe de la forma tradicional.
        event.preventDefault();

        // Obtiene la URL del script PHP.
        var url = "mail_phpmailer_real.php";

        // Envía los datos del formulario a la URL.
        $.ajax({
            type: "POST",
            url: url,
            data: $(this).serialize(),
            success: function(response) {
                try {
                    // Intentar parsear como JSON
                    var data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        alert(data.message || "¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.");
                        // Limpiar el formulario
                        $("#contactForm")[0].reset();
                    } else {
                        alert(data.error || "Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo.");
                    }
                } catch (e) {
                    // Si no es JSON, tratar como respuesta simple
                    if (response === "success") {
                        alert("¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.");
                        $("#contactForm")[0].reset();
                    } else {
                        alert("Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo.");
                    }
                }
            },
            error: function() {
                // Muestra un mensaje de error si la llamada AJAX falla.
                alert("Ocurrió un error inesperado. Por favor, revisa tu conexión a internet e inténtalo de nuevo.");
            }
        });
    });
});