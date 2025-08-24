<?php
// ===== CONFIGURACIÓN DE LA CONEXIÓN A LA BASE DE DATOS =====
/**
 * Archivo de configuración para la conexión a la base de datos MySQL
 * Este archivo contiene todas las configuraciones necesarias para conectar
 * con la base de datos de forma segura usando PDO
 */

// Evitar acceso directo al archivo - Solo puede ser incluido desde otros archivos PHP
if (!defined('DB_CONFIG_LOADED')) {
    define('DB_CONFIG_LOADED', true);
} else {
    die('Acceso directo no permitido');
}

// ==================== CONFIGURACIÓN DE BASE DE DATOS ====================

// Configuración del servidor de base de datos
define('DB_HOST', 'localhost');        // Servidor de la base de datos (localhost para servidor local)
define('DB_NAME', 'u802689289_octocodex_db');    // Nombre de la base de datos que vas a usar
define('DB_USER', 'u802689289_octocodex');             // Usuario de MySQL (cambiar por tu usuario)
define('DB_PASS', 'Cune2024!');                 // Contraseña de MySQL (cambiar por tu contraseña)
define('DB_CHARSET', 'utf8mb4');       // Codificación de caracteres (utf8mb4 soporta emojis)
//define('DB_PORT', 3306);               // Puerto de MySQL (3306 es el puerto por defecto)


// ==================== OPCIONES DE CONFIGURACIÓN PDO ====================

// Array con opciones de configuración para PDO
$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Lanzar excepciones en caso de error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Retornar arrays asociativos por defecto
    PDO::ATTR_EMULATE_PREPARES   => false,                    // Usar prepared statements reales de MySQL
    PDO::ATTR_PERSISTENT         => false,                    // No usar conexiones persistentes
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"       // Establecer codificación al conectar
];

// ==================== CLASE PARA MANEJAR LA CONEXIÓN ====================

class Database {
    // Propiedades privadas de la clase
    private static $instance = null;    // Para implementar patrón Singleton
    private $connection = null;         // Almacena la conexión PDO
    private $host;                      // Host de la base de datos
    private $dbname;                    // Nombre de la base de datos
    private $username;                  // Usuario de la base de datos
    private $password;                  // Contraseña de la base de datos
    private $charset;                   // Codificación de caracteres
    private $port;                      // Puerto de conexión

    /**
     * Constructor privado para implementar patrón Singleton
     * Esto evita que se puedan crear múltiples instancias de la clase
     */
    private function __construct() {
        // Asignar valores de configuración a las propiedades
        $this->host     = DB_HOST;
        $this->dbname   = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->charset  = DB_CHARSET;
        $this->port     = DB_PORT;
        
        // Crear la conexión inmediatamente al instanciar
        $this->connect();
    }

    /**
     * Método para obtener la única instancia de la clase (Patrón Singleton)
     * @return Database - La instancia única de la clase Database
     */
    public static function getInstance() {
        // Si no existe una instancia, crear una nueva
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        // Retornar la instancia única
        return self::$instance;
    }

    /**
     * Método privado para establecer la conexión con la base de datos
     * @throws Exception - Si no se puede conectar a la base de datos
     */
    private function connect() {
        try {
            // Construir el DSN (Data Source Name) para MySQL
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            
            // Obtener las opciones de configuración global
            global $pdo_options;
            
            // Crear la conexión PDO con los parámetros configurados
            $this->connection = new PDO($dsn, $this->username, $this->password, $pdo_options);
            
            // Log exitoso (opcional, solo para desarrollo)
            error_log("Conexión a base de datos establecida exitosamente");
            
        } catch (PDOException $e) {
            // Registrar el error en el log del servidor (sin mostrar detalles al usuario)
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            
            // Lanzar una excepción genérica para el usuario
            throw new Exception("No se pudo conectar a la base de datos. Contacte al administrador.");
        }
    }

    /**
     * Método para obtener la conexión PDO
     * @return PDO - Objeto de conexión PDO
     */
    public function getConnection() {
        // Verificar si la conexión sigue activa
        if ($this->connection === null) {
            $this->connect(); // Reconectar si es necesario
        }
        
        return $this->connection;
    }

?>