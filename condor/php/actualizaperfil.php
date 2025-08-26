<?php
// Establecer el tipo de contenido de la respuesta como JSON
// Esto le dice al navegador que la respuesta será en formato JSON
header('Content-Type: application/json');

// Permitir acceso desde cualquier origen (CORS)
// Esto permite que el archivo sea llamado desde diferentes dominios
header('Access-Control-Allow-Origin: *');

// Permitir solo el método POST para las peticiones
// Solo acepta peticiones POST, no GET, PUT, etc.
header('Access-Control-Allow-Methods: POST');

// Permitir el encabezado Content-Type en las peticiones
// Necesario para que el navegador pueda enviar datos con FormData
header('Access-Control-Allow-Headers: Content-Type');

// Incluir archivo de configuración de base de datos
// Este archivo contiene la clase Database y configuración de conexión
require_once 'config_bd.php';

// Iniciar un bloque try-catch para manejar errores
try {
    // Conexión a la base de datos usando la clase Database
    // Obtiene una instancia única de la clase Database
    $database = Database::getInstance();
    
    // Obtiene la conexión PDO desde la instancia de Database
    $pdo = $database->getConnection();
    
    // Verificar que la petición sea de tipo POST
    // $_SERVER['REQUEST_METHOD'] contiene el método HTTP usado (GET, POST, etc.)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Obtener el user_id de la sesión o de la petición
        // ?? es el operador de fusión null, usa el primer valor que no sea null
        // Primero busca en $_POST, luego en $_SESSION, si no encuentra nada usa null
        $user_id = $_POST['user_id'] ?? $_SESSION['user_id'] ?? null;
        
        // Verificar si se proporcionó un user_id válido
        if (!$user_id) {
            // Si no hay user_id, devolver error en formato JSON
            echo json_encode(['tipo' => 'error', 'mensaje' => 'No se proporcionó un ID de usuario válido']);
            exit; // Terminar la ejecución del script
        }
        
        // Validar que los campos requeridos estén presentes
        // empty() verifica si la variable está vacía (null, "", 0, false, array vacío)
        if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['email'])) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Por favor, completa todos los campos requeridos']);
            exit;
        }
        
        // Obtener y limpiar los datos del formulario
        // trim() elimina espacios en blanco al inicio y final del texto
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $email = trim($_POST['email']);
        
        // Los campos bio y url_perfil son opcionales, usar operador ?? para valor por defecto
        $bio = trim($_POST['bio'] ?? '');
        $url_perfil = trim($_POST['url_perfil'] ?? '');
        
        // Validar que el email tenga un formato válido
        // filter_var() con FILTER_VALIDATE_EMAIL verifica el formato del email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'El email no tiene un formato válido']);
            exit;
        }
        
        // Verificar si el email ya existe para otro usuario
        // Preparar consulta SQL para buscar emails duplicados
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE us_email = ? AND id != ?");
        
        // Ejecutar la consulta con el email y el user_id actual
        // Esto busca si existe otro usuario con el mismo email
        $stmt->execute([$email, $user_id]);
        
        // Si se encontró al menos un registro (rowCount() > 0)
        if ($stmt->rowCount() > 0) {
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Este email ya está en uso por otro usuario']);
            exit;
        }
        
        // Obtener datos actuales del usuario para la foto de perfil
        // Consultar la foto de perfil actual del usuario
        $stmt = $pdo->prepare("SELECT us_foto_perfil FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // Obtener el resultado como array asociativo
        $usuarioActual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener la foto de perfil actual, si no existe usar null
        $foto_perfil = $usuarioActual['us_foto_perfil'] ?? null;
        
        // Procesar imagen de perfil si se subió
        // isset() verifica si la variable existe y no es null
        // $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK significa que no hubo errores
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            
            // Obtener información del archivo subido
            $archivoTemporal = $_FILES['foto_perfil']['tmp_name']; // Ruta temporal del archivo
            $nombreOriginal = $_FILES['foto_perfil']['name']; // Nombre original del archivo
            $tipoArchivo = $_FILES['foto_perfil']['type']; // Tipo MIME del archivo
            $tamaño = $_FILES['foto_perfil']['size']; // Tamaño en bytes
            
            // Definir los tipos de archivo permitidos (solo imágenes)
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            
            // Verificar si el tipo de archivo está permitido
            if (!in_array($tipoArchivo, $tiposPermitidos)) {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'Solo se permiten archivos JPG, PNG o GIF']);
                exit;
            }
            
            // Validar tamaño máximo (5MB = 5 * 1024 * 1024 bytes)
            if ($tamaño > 5 * 1024 * 1024) {
                echo json_encode(['tipo' => 'error', 'mensaje' => 'La imagen no puede ser mayor a 5MB']);
                exit;
            }
            
            // Crear directorio de uploads si no existe
            $directorioUploads = '../uploads/perfiles/';
            
            // is_dir() verifica si el directorio existe
            if (!is_dir($directorioUploads)) {
                // mkdir() crea el directorio con permisos 755 (lectura/escritura/ejecución para propietario, lectura/ejecución para grupo y otros)
                // true permite crear directorios anidados si no existen
                mkdir($directorioUploads, 0755, true);
            }
            
            // Generar nombre único para la imagen
            // pathinfo() extrae información del archivo, PATHINFO_EXTENSION obtiene la extensión
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
            
            // Crear nombre único con prefijo, user_id y timestamp
            $nombreImagen = 'perfil_' . $user_id . '_' . time() . '.' . $extension;
            
            // Ruta completa donde se guardará la imagen
            $rutaDestino = $directorioUploads . $nombreImagen;
            
            // Mover el archivo desde la ubicación temporal a la ubicación final
            if (move_uploaded_file($archivoTemporal, $rutaDestino)) {
                // Si se movió correctamente, eliminar imagen anterior si existe
                if ($foto_perfil && file_exists('../' . $foto_perfil)) {
                    // unlink() elimina el archivo
                    unlink('../' . $foto_perfil);
                }
                
                // Actualizar la variable con la nueva ruta de la imagen
                $foto_perfil = 'uploads/perfiles/' . $nombreImagen;
            } else {
                // Si no se pudo mover el archivo, devolver error
                echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al subir la imagen']);
                exit;
            }
        }
        
        // Actualizar datos en la tabla usuarios
        // Preparar consulta SQL para actualizar los datos del usuario
        $sql = "UPDATE usuarios SET 
                us_nombre = ?, 
                us_apellido = ?, 
                us_email = ?, 
                us_bio = ?, 
                us_foto_perfil = ?, 
                us_url_perfil = ?, 
                fecha_actualizacion = NOW() 
                WHERE id = ?";
        
        // Preparar la consulta para evitar inyección SQL
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta con los datos del formulario
        // Los parámetros se pasan en el mismo orden que los ? en la consulta SQL
        if ($stmt->execute([$nombre, $apellido, $email, $bio, $foto_perfil, $url_perfil, $user_id])) {
            // Si la actualización fue exitosa, actualizar datos de sesión
            $_SESSION['email'] = $email;
            $_SESSION['nombre_completo'] = $nombre . ' ' . $apellido;
            
            // Devolver mensaje de éxito en formato JSON
            echo json_encode(['tipo' => 'success', 'mensaje' => 'Perfil actualizado exitosamente']);
        } else {
            // Si la actualización falló, devolver error
            echo json_encode(['tipo' => 'error', 'mensaje' => 'Error al actualizar el perfil']);
        }
        
    } else {
        // Si el método no es POST, devolver error
        echo json_encode(['tipo' => 'error', 'mensaje' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    // Capturar errores específicos de la base de datos
    // PDOException se lanza cuando hay problemas con la base de datos
    echo json_encode(['tipo' => 'error', 'mensaje' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Capturar cualquier otro error del servidor
    // Exception es la clase base para todos los errores
    echo json_encode(['tipo' => 'error', 'mensaje' => 'Error del servidor: ' . $e->getMessage()]);
}
?>
