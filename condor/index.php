<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Condor</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style_login.css">

</head>
<body>

    <div class="login-container">
        <!-- Columna izquierda - Imagen/Fondo -->
        <div class="login-image">
            <!-- Iconos flotantes -->
            <i class="fas fa-rocket floating-icon"></i>
            <i class="fas fa-star floating-icon"></i>
            <i class="fas fa-heart floating-icon"></i>
            
            <!-- Contenido de la imagen -->
            <div class="image-content">
                <h1>OctoCodex</h1>
                <p>Bienvenido de vuelta. Inicia sesión para acceder a tu cuenta y descubrir un mundo de posibilidades.</p>
            </div>
        </div>

        <!-- Columna derecha - Formulario -->
        <div class="login-form">
            <div class="form-container">
                <!-- Header del formulario -->
                <div class="form-header">
                    <h2>Iniciar Sesión</h2>
                    <p>Ingresa tus credenciales para continuar</p>
                </div>

                <!-- Alertas -->
                <div class="alert alert-success" id="successAlert">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="successMessage"></span>
                </div>

                <div class="alert alert-danger" id="errorAlert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="errorMessage"></span>
                </div>

                <!-- Formulario -->
                <form id="loginForm">
                    <!-- Campo Usuario -->
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
                        <label for="username">Usuario</label>
                        <i class="fas fa-user input-icon"></i>
                    </div>

                    <!-- Campo Contraseña -->
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                        <label for="password">Contraseña</label>
                        <i class="fas fa-lock input-icon"></i>
                    </div>

                    <!-- Checkbox Recordarme -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Recordarme
                        </label>
                    </div>

                    <!-- Botón de envío -->
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </span>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </form>

                <!-- Separador -->
                <!-- <div class="divider">
                    <span>o continúa con</span>
                </div> -->

                <!-- Botones sociales -->
                <!-- <div class="social-buttons">
                    <a href="#" class="btn-social">
                        <i class="fab fa-google me-2"></i>Google
                    </a>
                    <a href="#" class="btn-social">
                        <i class="fab fa-facebook me-2"></i>Facebook
                    </a>
                </div> -->

                <!-- Enlaces adicionales -->
                <!-- <div class="form-links">
                    <p>
                        ¿No tienes cuenta? 
                        <a href="#" id="registerLink">Regístrate aquí</a>
                    </p>
                    <p>
                        <a href="#" id="forgotPasswordLink">¿Olvidaste tu contraseña?</a>
                    </p>
                </div> -->
            </div>
        </div>
    </div>

    <!-- jQuery (necesario para loginProcess.js) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/loginProcess.js"></script>

</body>
</html>