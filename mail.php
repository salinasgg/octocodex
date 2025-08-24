<?php
// Define la dirección de correo a la que se enviarán los mensajes.
$destinatario = "salinasgeganb@gmail.com";

// Asegúrate de que los datos se han enviado por el método POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recopila los datos del formulario.
    $nombre = htmlspecialchars(trim($_POST["nombre"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $asunto = htmlspecialchars(trim($_POST["asunto"]));
    $mensaje = htmlspecialchars(trim($_POST["mensaje"]));

    // Construye el asunto y el cuerpo del correo.
    $asunto_mail = "Mensaje desde el formulario de contacto: " . $asunto;
    $cuerpo_mail = "Nombre: " . $nombre . "\n";
    $cuerpo_mail .= "Correo: " . $email . "\n";
    $cuerpo_mail .= "Asunto: " . $asunto . "\n";
    $cuerpo_mail .= "Mensaje:\n" . $mensaje;

    // Define los encabezados del correo para evitar que se marque como spam.
    $encabezados = "From: " . $email . "\r\n";
    $encabezados .= "Reply-To: " . $email . "\r\n";
    $encabezados .= "X-Mailer: PHP/" . phpversion();

    // Intenta enviar el correo.
    if (mail($destinatario, $asunto_mail, $cuerpo_mail, $encabezados)) {
        // Envía una respuesta de éxito a la llamada AJAX.
        echo "success";
    } else {
        // Envía una respuesta de error.
        echo "error";
    }
} else {
    // Si la solicitud no es POST, envía un mensaje de error.
    http_response_code(403);
    echo "error";
}
?>