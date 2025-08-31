<?php
// Incluir el archivo de configuración de la base de datos
require_once 'config_bd.php';

// Crear una instancia de la clase Database para obtener la conexión
$database = Database::getInstance();
// Obtener la conexión PDO a la base de datos
$pdo = $database->getConnection();

// Verificar que la conexión esté funcionando
try {
    $test_query = "SELECT 1 as test";
    $test_stmt = $pdo->prepare($test_query);
    $test_stmt->execute();
    $test_result = $test_stmt->fetch();
    error_log("✅ Conexión a base de datos verificada correctamente en abm_clientes.php");
} catch (Exception $e) {
    error_log("❌ Error en la conexión a la base de datos en abm_clientes.php: " . $e->getMessage());
    // Continuar con la ejecución pero registrar el error
}

// ===== DETECTAR TIPO DE PETICIÓN =====
// Verificar si es una petición POST (para acciones como eliminar, editar, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener la acción solicitada
    $accion = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Debug: Registrar la acción recibida
    error_log("Acción POST recibida: " . $accion);
    
    // ===== MANEJAR DIFERENTES ACCIONES =====
    switch ($accion) {
        case 'eliminarCliente':
            eliminarCliente($pdo);
            break;
            
        case 'editarCliente':
            editarCliente($pdo);
            break;
            
        case 'verCliente':
            verCliente($pdo);
            break;
            
        case 'agregarCliente':
            agregarCliente($pdo);
            break;
            
        case 'actualizarCliente':
            error_log("🔄 Ejecutando función actualizarCliente");
            error_log("🔄 POST data completa: " . print_r($_POST, true));
            error_log("🔄 Headers recibidos: " . print_r(getallheaders(), true));
            actualizarCliente($pdo);
            break;
            
        default:
            // Acción no reconocida
            $response = array(
                'success' => false,
                'message' => 'Acción no reconocida: ' . $accion
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            break;
    }
    exit; // Terminar la ejecución después de procesar la acción POST
}

// ===== FUNCIONES PARA MANEJAR ACCIONES =====

// Función para eliminar un cliente
function eliminarCliente($pdo) {
    try {
        // Obtener el ID del cliente a eliminar
        $cliente_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($cliente_id <= 0) {
            throw new Exception('ID de cliente inválido');
        }
        
        // Verificar que el cliente existe
        $sql_check = "SELECT id, cl_nombre, cl_apellido FROM clientes WHERE id = :id";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindValue(':id', $cliente_id, PDO::PARAM_INT);
        $stmt_check->execute();
        $cliente = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if (!$cliente) {
            throw new Exception('Cliente no encontrado');
        }
        
        // Eliminar el cliente (cambio de estado en lugar de eliminar físicamente)
        $sql_delete = "UPDATE clientes SET cl_activo = 0 WHERE id = :id";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindValue(':id', $cliente_id, PDO::PARAM_INT);
        $stmt_delete->execute();
        
        // Respuesta exitosa
        $response = array(
            'success' => true,
            'message' => 'Cliente ' . $cliente['cl_nombre'] . ' ' . $cliente['cl_apellido'] . ' eliminado correctamente',
            'cliente_id' => $cliente_id
        );
        
        error_log("Cliente eliminado: ID " . $cliente_id . " - " . $cliente['cl_nombre'] . ' ' . $cliente['cl_apellido']);
        
    } catch (Exception $e) {
        // Respuesta de error
        $response = array(
            'success' => false,
            'message' => 'Error al eliminar cliente: ' . $e->getMessage()
        );
        
        error_log("Error al eliminar cliente: " . $e->getMessage());
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Función para editar un cliente
function editarCliente($pdo) {
    try {
        // Obtener el ID del cliente a editar
        $cliente_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($cliente_id <= 0) {
            throw new Exception('ID de cliente inválido');
        }
        
        // Obtener los datos del cliente y sus contactos
        $sql = "SELECT c.*, cc.* FROM clientes c 
                LEFT JOIN contactos_cliente cc ON c.id = cc.cliente_id 
                WHERE c.id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cliente) {
            error_log("❌ Cliente no encontrado en editarCliente - ID: " . $cliente_id);
            // Verificar si hay algún cliente en la base de datos
            $sql_count = "SELECT COUNT(*) as total FROM clientes";
            $stmt_count = $pdo->prepare($sql_count);
            $stmt_count->execute();
            $total_clientes = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
            error_log("🔍 Total de clientes en la base de datos: " . $total_clientes);
            
            // Verificar si hay algún cliente con un ID similar
            $sql_similar = "SELECT id, cl_nombre, cl_apellido FROM clientes WHERE id LIKE '%" . $cliente_id . "%' OR id = " . $cliente_id;
            $stmt_similar = $pdo->prepare($sql_similar);
            $stmt_similar->execute();
            $clientes_similares = $stmt_similar->fetchAll(PDO::FETCH_ASSOC);
            error_log("🔍 Clientes con ID similar: " . print_r($clientes_similares, true));
            
            throw new Exception('Cliente no encontrado');
        }
        
        error_log("✅ Cliente encontrado en editarCliente - ID: " . $cliente_id . " - Nombre: " . $cliente['cl_nombre'] . " " . $cliente['cl_apellido']);
        error_log("✅ Datos completos del cliente: " . print_r($cliente, true));
        
        // Respuesta exitosa con datos del cliente
        $response = array(
            'success' => true,
            'message' => 'Datos del cliente obtenidos correctamente',
            'cliente' => $cliente
        );
        
        error_log("Datos de cliente obtenidos para edición: ID " . $cliente_id);
        
    } catch (Exception $e) {
        // Respuesta de error
        $response = array(
            'success' => false,
            'message' => 'Error al obtener datos del cliente: ' . $e->getMessage()
        );
        
        error_log("Error al obtener datos del cliente: " . $e->getMessage());
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Función para ver detalles de un cliente
function verCliente($pdo) {
    try {
        // Obtener el ID del cliente a ver
        $cliente_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($cliente_id <= 0) {
            throw new Exception('ID de cliente inválido');
        }
        
        // Obtener los datos completos del cliente con información de contactos
        $sql = "SELECT c.*, cc.* FROM clientes c 
                LEFT JOIN contactos_cliente cc ON c.id = cc.cliente_id 
                WHERE c.id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cliente) {
            throw new Exception('Cliente no encontrado');
        }

        // Generar el HTML del cliente
        $cliente_html = '<div class="client-info-container" id="clientInfoContent">
        <div class="client-profile">
            <!-- Header con información principal -->
            <div class="client-header">
                <div class="client-actions" style="position: absolute; top: 10px; right: 10px;">
                    <img src="../icons/volver-white.png" alt="Editar" style="cursor: pointer; margin-right: 10px;" id="volver-a-clientes" onclick="volverAClientes()" class="volver">                    
                </div>
                <div class="client-avatar" id="clientAvatar">'.strtoupper(substr($cliente['cl_nombre'], 0, 1) . substr($cliente['cl_apellido'], 0, 1)).'</div>
                <div class="client-main-info">
                    <h2 class="client-name" id="clientName">'.$cliente['cl_nombre'].' '.$cliente['cl_apellido'].'</h2>
                    <p class="client-id" id="clientId">ID: '.$cliente['id'].'</p>
                    <span class="client-status status-active" id="clientStatus">✅ '.($cliente['cl_activo'] ? 'Activo' : 'Inactivo').'</span>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
           <!-- <div class="info-section">
                <h3 class="section-title">
                    📊 Estadísticas
                </h3>
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-number" id="totalOrders">47</div>
                        <div class="stat-label">Pedidos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalSpent">$12,450</div>
                        <div class="stat-label">Total Gastado</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="loyaltyPoints">2,340</div>
                        <div class="stat-label">Puntos</div>
                    </div>
                </div>
            </div>-->

            <!-- Información personal -->
            <div class="info-section">
                <h3 class="section-title">
                    <img src="../icons/16x/ver-usuario16.png" alt="Información Personal" style="vertical-align: middle; margin-right: 10px;"> Información Personal
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/email.png" alt="Email" style="vertical-align: middle; margin-right: 10px;"> Email</span>
                        <span class="info-value" id="clientEmail">'.$cliente['cl_email'].'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/telefono16.png" alt="Teléfono" style="vertical-align: middle; margin-right: 10px;"> Teléfono</span>
                        <span class="info-value" id="clientPhone">'.$cliente['cl_telefono'].'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/empresa16.png" alt="Empresa" style="vertical-align: middle; margin-right: 10px;"> Empresa</span>
                        <span class="info-value" id="clientCompany">'.$cliente['cl_empresa'].'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/tipo16.png" alt="Tipo" style="vertical-align: middle; margin-right: 10px;"> Tipo</span>
                        <span class="info-value" id="clientType">'.$cliente['cl_tipo'].'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/calendario.png" alt="Fecha Registro" style="vertical-align: middle; margin-right: 10px;"> Fecha Registro</span>
                        <span class="info-value" id="clientBirthdate">'.($cliente['cl_fecha_registro'] ?? 'No disponible').'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/dni16.png" alt="Documento" style="vertical-align: middle; margin-right: 10px;"> Documento</span>
                        <span class="info-value" id="clientDocument">'.($cliente['co_documento'] ?? 'No disponible').'</span>
                    </div>
                </div>
            </div>

            <!-- Información de contacto -->
            <div class="info-section">
                <h3 class="section-title">
                    <img src="../icons/16x/infocontacto16.png" alt="Información de Contacto" style="vertical-align: middle; margin-right: 10px;"> Información de Contacto
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/direccion16.png" alt="Dirección" style="vertical-align: middle; margin-right: 10px;"> Dirección</span>
                        <span class="info-value" id="clientAddress">'.($cliente['cl_direccion'] ?? 'No disponible').'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/ciudad16.png" alt="Ciudad" style="vertical-align: middle; margin-right: 10px;"> Ciudad</span>
                        <span class="info-value" id="clientCity">'.$cliente['cl_ciudad'].'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/bandera16.png" alt="País" style="vertical-align: middle; margin-right: 10px;"> País</span>
                        <span class="info-value" id="clientCountry">'.$cliente['cl_pais'].'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/codigo16.png" alt="Código Postal" style="vertical-align: middle; margin-right: 10px;"> Código Postal</span>
                        <span class="info-value" id="clientPostal">'.($cliente['co_codigo_postal'] ?? 'No disponible').'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/telefono16.png" alt="Teléfono Secundario" style="vertical-align: middle; margin-right: 10px;"> Teléfono Secundario</span>
                        <span class="info-value" id="clientPhone2">'.($cliente['co_telefono_secundario'] ?? 'No disponible').'</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/email.png" alt="Email Secundario" style="vertical-align: middle; margin-right: 10px;"> Email Secundario</span>
                        <span class="info-value" id="clientEmail2">'.($cliente['co_email_secundario'] ?? 'No disponible').'</span>
                    </div>
                </div>
            </div>

            <!-- Tags y categorías -->
            <div class="info-section">
                <h3 class="section-title">
                    <img src="../icons/16x/cate16.png" alt="Categorías y Prioridad" style="vertical-align: middle; margin-right: 10px;"> Categorías y Prioridad
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/categoria16.png" alt="Categoría" style="vertical-align: middle; margin-right: 10px;"> Categoría</span>
                        <div class="tags-container" id="clientCategories">
                            <span class="tag">'.$cliente['cl_tipo'].'</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><img src="../icons/16x/estado16.png" alt="Estado" style="vertical-align: middle; margin-right: 10px;"> Estado</span>
                        <div class="tags-container">
                            <span class="tag priority-'.($cliente['cl_activo'] ? 'high' : 'low').'" id="clientPriority">'.($cliente['cl_activo'] ? '✅ Activo' : '❌ Inactivo').'</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad reciente -->
          <!--  <div class="info-section">
                <h3 class="section-title">
                    📈 Actividad Reciente
                </h3>
                <div class="activity-timeline" id="activityTimeline">
                    <div class="activity-item">
                        <div class="activity-date">Hace 2 días</div>
                        <div class="activity-description">🛒 Realizó compra por $250.00</div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-date">Hace 1 semana</div>
                        <div class="activity-description">📧 Abrió email promocional</div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-date">Hace 2 semanas</div>
                        <div class="activity-description">👤 Actualizó información de perfil</div>
                    </div>
                </div>
            </div>-->
        </div>
    </div>';
        
        // Respuesta exitosa con datos completos del cliente
        $response = array(
            'success' => true,
            'message' => 'Detalles del cliente obtenidos correctamente',
            'cliente' => $cliente,
            'cliente_html' => $cliente_html
        );
        
        error_log("Detalles de cliente obtenidos: ID " . $cliente_id);
        
    } catch (Exception $e) {
        // Respuesta de error
        $response = array(
            'success' => false,
            'message' => 'Error al obtener detalles del cliente: ' . $e->getMessage()
        );
        
        error_log("Error al obtener detalles del cliente: " . $e->getMessage());
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Función para agregar un nuevo cliente
function agregarCliente($pdo) {
    try {
        // Obtener los datos del nuevo cliente
        $datos = array(
            'cl_nombre' => isset($_POST['cl_nombre']) ? trim($_POST['cl_nombre']) : '',
            'cl_apellido' => isset($_POST['cl_apellido']) ? trim($_POST['cl_apellido']) : '',
            'cl_empresa' => isset($_POST['cl_empresa']) ? trim($_POST['cl_empresa']) : '',
            'cl_email' => isset($_POST['cl_email']) ? trim($_POST['cl_email']) : '',
            'cl_telefono' => isset($_POST['cl_telefono']) ? trim($_POST['cl_telefono']) : '',
            'cl_ciudad' => isset($_POST['cl_ciudad']) ? trim($_POST['cl_ciudad']) : '',
            'cl_pais' => isset($_POST['cl_pais']) ? trim($_POST['cl_pais']) : '',
            'cl_tipo' => isset($_POST['cl_tipo']) ? trim($_POST['cl_tipo']) : 'potencial'
        );
        
        // Validar campos obligatorios
        if (empty($datos['cl_nombre']) || empty($datos['cl_apellido']) || empty($datos['cl_email'])) {
            throw new Exception('Los campos nombre, apellido y email son obligatorios');
        }
        
        // Verificar que el email no exista
        $sql_check = "SELECT id FROM clientes WHERE cl_email = :email";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindValue(':email', $datos['cl_email']);
        $stmt_check->execute();
        
        if ($stmt_check->fetch()) {
            throw new Exception('Ya existe un cliente con ese email');
        }


        // Obtener los datos de contacto del cliente
        $nombre_contacto = isset($_POST['cc_nombre']) ? trim($_POST['cc_nombre']) : '';
        $apellido_contacto = isset($_POST['cc_apellido']) ? trim($_POST['cc_apellido']) : '';
        
        $datos_contacto = array(
            'co_nombre' => trim($nombre_contacto . ' ' . $apellido_contacto),
            'co_cargo' => isset($_POST['cc_cargo']) ? trim($_POST['cc_cargo']) : '',
            'co_email' => isset($_POST['cc_email']) ? trim($_POST['cc_email']) : '',
            'co_telefono' => isset($_POST['cc_telefono']) ? trim($_POST['cc_telefono']) : '',
            'co_principal' => 1 // Por defecto es el contacto principal
        );

        // Validar campos obligatorios de contacto
        if (!empty($datos_contacto['co_nombre']) || !empty($datos_contacto['co_email'])) {
            // Verificar que el email de contacto no exista
            $sql_check_contact = "SELECT id FROM contactos_cliente WHERE co_email = :email";
            $stmt_check_contact = $pdo->prepare($sql_check_contact);
            $stmt_check_contact->bindValue(':email', $datos_contacto['co_email']);
            $stmt_check_contact->execute();

            if ($stmt_check_contact->fetch()) {
                throw new Exception('Ya existe un contacto con ese email');
            }
        }
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Insertar el nuevo cliente
        $sql_insert = "INSERT INTO clientes (cl_nombre, cl_apellido, cl_empresa, cl_email, cl_telefono, cl_ciudad, cl_pais, cl_tipo, cl_activo) 
                       VALUES (:nombre, :apellido, :empresa, :email, :telefono, :ciudad, :pais, :tipo, 1)";
        
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->bindValue(':nombre', $datos['cl_nombre']);
        $stmt_insert->bindValue(':apellido', $datos['cl_apellido']);
        $stmt_insert->bindValue(':empresa', $datos['cl_empresa']);
        $stmt_insert->bindValue(':email', $datos['cl_email']);
        $stmt_insert->bindValue(':telefono', $datos['cl_telefono']);
        $stmt_insert->bindValue(':ciudad', $datos['cl_ciudad']);
        $stmt_insert->bindValue(':pais', $datos['cl_pais']);
        $stmt_insert->bindValue(':tipo', $datos['cl_tipo']);
        $stmt_insert->execute();
        
        $nuevo_id = $pdo->lastInsertId();
        
        // Insertar contacto si se proporcionaron datos
        if (!empty($datos_contacto['co_nombre']) || !empty($datos_contacto['co_email'])) {
            $sql_insert_contact = "INSERT INTO contactos_cliente (cliente_id, co_nombre, co_cargo, co_email, co_telefono, co_principal) 
                                  VALUES (:cliente_id, :nombre, :cargo, :email, :telefono, :principal)";
            
            $stmt_insert_contact = $pdo->prepare($sql_insert_contact);
            $stmt_insert_contact->bindValue(':cliente_id', $nuevo_id);
            $stmt_insert_contact->bindValue(':nombre', $datos_contacto['co_nombre']);
            $stmt_insert_contact->bindValue(':cargo', $datos_contacto['co_cargo']);
            $stmt_insert_contact->bindValue(':email', $datos_contacto['co_email']);
            $stmt_insert_contact->bindValue(':telefono', $datos_contacto['co_telefono']);
            $stmt_insert_contact->bindValue(':principal', $datos_contacto['co_principal']);
            $stmt_insert_contact->execute();
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        // Respuesta exitosa
        $response = array(
            'success' => true,
            'message' => 'Cliente agregado correctamente',
            'cliente_id' => $nuevo_id,
            'cliente' => $datos,
            'contacto' => $datos_contacto
        );
        
        error_log("Nuevo cliente agregado: ID " . $nuevo_id . " - " . $datos['cl_nombre'] . ' ' . $datos['cl_apellido']);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Respuesta de error
        $response = array(
            'success' => false,
            'message' => 'Error al agregar cliente: ' . $e->getMessage()
        );
        
        error_log("Error al agregar cliente: " . $e->getMessage());
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Función para actualizar un cliente existente
function actualizarCliente($pdo) {
    try {
        error_log("🚀 Actualizando cliente - ID: " . ($_POST['cliente_id'] ?? $_POST['id'] ?? 'NO_ID'));
        
        // LÓGICA SIMPLIFICADA: igual que el test que funciona
        $cliente_id = 0;
        
        // Primero verificar cliente_id
        if (isset($_POST['cliente_id']) && is_numeric($_POST['cliente_id'])) {
            $cliente_id = (int)$_POST['cliente_id'];
        } 
        // Luego verificar id  
        elseif (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $cliente_id = (int)$_POST['id'];
        }
        
        error_log("🔍 ID recibido: " . $cliente_id . " de POST: " . print_r([$_POST['cliente_id'] ?? 'null', $_POST['id'] ?? 'null'], true));
        
        
        if ($cliente_id <= 0) {
            error_log("❌ ID de cliente inválido: " . $cliente_id);
            error_log("❌ POST completo: " . print_r($_POST, true));
            throw new Exception('ID de cliente inválido. Recibido: ' . $cliente_id);
        }
        
        // CONSULTA SIMPLE que incluya clientes activos e inactivos
        error_log("🔍 Buscando cliente con ID: " . $cliente_id);
        $sql_check = "SELECT * FROM clientes WHERE id = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$cliente_id]);
        $cliente_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        error_log("🔍 Cliente encontrado: " . ($cliente_existente ? "SÍ" : "NO"));
        
        if (!$cliente_existente) {
            // Debug: mostrar qué clientes existen realmente
            $sql_debug = "SELECT id, cl_nombre, cl_activo FROM clientes WHERE id IN ($cliente_id-2, $cliente_id-1, $cliente_id, $cliente_id+1, $cliente_id+2)";
            $stmt_debug = $pdo->prepare($sql_debug);
            $stmt_debug->execute();
            $clientes_debug = $stmt_debug->fetchAll(PDO::FETCH_ASSOC);
            error_log("🔍 Clientes cerca del ID $cliente_id: " . print_r($clientes_debug, true));
            
            // También verificar si existe pero está inactivo
            $sql_inactivo = "SELECT * FROM clientes WHERE id = ? AND cl_activo = 0";
            $stmt_inactivo = $pdo->prepare($sql_inactivo);
            $stmt_inactivo->execute([$cliente_id]);
            $cliente_inactivo = $stmt_inactivo->fetch(PDO::FETCH_ASSOC);
            
            if ($cliente_inactivo) {
                error_log("⚠️ Cliente $cliente_id existe pero está INACTIVO");
                throw new Exception('Cliente inactivo, no se puede editar');
            }
            
            throw new Exception('Cliente no encontrado con ID: ' . $cliente_id);
        }
        
        error_log("✅ Cliente encontrado: " . $cliente_existente['cl_nombre'] . ' ' . $cliente_existente['cl_apellido']);
        
        // Obtener los datos del cliente actualizado
        $datos = array(
            'cl_nombre' => isset($_POST['cl_nombre']) ? trim($_POST['cl_nombre']) : '',
            'cl_apellido' => isset($_POST['cl_apellido']) ? trim($_POST['cl_apellido']) : '',
            'cl_empresa' => isset($_POST['cl_empresa']) ? trim($_POST['cl_empresa']) : '',
            'cl_email' => isset($_POST['cl_email']) ? trim($_POST['cl_email']) : '',
            'cl_telefono' => isset($_POST['cl_telefono']) ? trim($_POST['cl_telefono']) : '',
            'cl_ciudad' => isset($_POST['cl_ciudad']) ? trim($_POST['cl_ciudad']) : '',
            'cl_pais' => isset($_POST['cl_pais']) ? trim($_POST['cl_pais']) : '',
            'cl_tipo' => isset($_POST['cl_tipo']) ? trim($_POST['cl_tipo']) : 'potencial'
        );
        
        // Validar campos obligatorios
        if (empty($datos['cl_nombre']) || empty($datos['cl_apellido']) || empty($datos['cl_email'])) {
            throw new Exception('Los campos nombre, apellido y email son obligatorios');
        }
        
        // Obtener el email actual del cliente para comparar
        $sql_email_actual = "SELECT cl_email FROM clientes WHERE id = :id";
        $stmt_email_actual = $pdo->prepare($sql_email_actual);
        $stmt_email_actual->bindValue(':id', $cliente_id, PDO::PARAM_INT);
        $stmt_email_actual->execute();
        $email_actual = $stmt_email_actual->fetch(PDO::FETCH_ASSOC);
        
        // Solo validar email único si se está cambiando el email
        if ($email_actual && $email_actual['cl_email'] !== $datos['cl_email']) {
            error_log("Email cambiado para cliente ID: " . $cliente_id . " - Email anterior: " . $email_actual['cl_email'] . " - Email nuevo: " . $datos['cl_email']);
            
            // Verificar que el nuevo email no exista en otro cliente
            $sql_check_email = "SELECT id FROM clientes WHERE cl_email = :email AND id != :id";
            $stmt_check_email = $pdo->prepare($sql_check_email);
            $stmt_check_email->bindValue(':email', $datos['cl_email']);
            $stmt_check_email->bindValue(':id', $cliente_id, PDO::PARAM_INT);
            $stmt_check_email->execute();
            
            $email_existente = $stmt_check_email->fetch();
            if ($email_existente) {
                error_log("Email ya existe en cliente ID: " . $email_existente['id']);
                throw new Exception('Ya existe otro cliente con ese email');
            }
            
            error_log("Email validado correctamente para cliente ID: " . $cliente_id);
        } else {
            error_log("Email no cambiado para cliente ID: " . $cliente_id . " - Email: " . $datos['cl_email']);
        }
        
        // Obtener los datos de contacto del cliente
        $nombre_contacto = isset($_POST['cc_nombre']) ? trim($_POST['cc_nombre']) : '';
        $apellido_contacto = isset($_POST['cc_apellido']) ? trim($_POST['cc_apellido']) : '';
        
        $datos_contacto = array(
            'co_nombre' => trim($nombre_contacto . ' ' . $apellido_contacto),
            'co_cargo' => isset($_POST['cc_cargo']) ? trim($_POST['cc_cargo']) : '',
            'co_email' => isset($_POST['cc_email']) ? trim($_POST['cc_email']) : '',
            'co_telefono' => isset($_POST['cc_telefono']) ? trim($_POST['cc_telefono']) : ''
        );
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Actualizar el cliente
        $sql_update = "UPDATE clientes SET 
                       cl_nombre = :nombre, 
                       cl_apellido = :apellido, 
                       cl_empresa = :empresa, 
                       cl_email = :email, 
                       cl_telefono = :telefono, 
                       cl_ciudad = :ciudad, 
                       cl_pais = :pais, 
                       cl_tipo = :tipo 
                       WHERE id = :id";
        
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindValue(':nombre', $datos['cl_nombre']);
        $stmt_update->bindValue(':apellido', $datos['cl_apellido']);
        $stmt_update->bindValue(':empresa', $datos['cl_empresa']);
        $stmt_update->bindValue(':email', $datos['cl_email']);
        $stmt_update->bindValue(':telefono', $datos['cl_telefono']);
        $stmt_update->bindValue(':ciudad', $datos['cl_ciudad']);
        $stmt_update->bindValue(':pais', $datos['cl_pais']);
        $stmt_update->bindValue(':tipo', $datos['cl_tipo']);
        $stmt_update->bindValue(':id', $cliente_id, PDO::PARAM_INT);
        $stmt_update->execute();
        
        // Actualizar o insertar contacto si se proporcionaron datos
        if (!empty($datos_contacto['co_nombre']) || !empty($datos_contacto['co_email'])) {
            // Verificar si ya existe un contacto para este cliente
            $sql_check_contact = "SELECT id FROM contactos_cliente WHERE cliente_id = :cliente_id";
            $stmt_check_contact = $pdo->prepare($sql_check_contact);
            $stmt_check_contact->bindValue(':cliente_id', $cliente_id, PDO::PARAM_INT);
            $stmt_check_contact->execute();
            $contacto_existente = $stmt_check_contact->fetch();
            
            if ($contacto_existente) {
                // Actualizar contacto existente
                $sql_update_contact = "UPDATE contactos_cliente SET 
                                      co_nombre = :nombre, 
                                      co_cargo = :cargo, 
                                      co_email = :email, 
                                      co_telefono = :telefono 
                                      WHERE cliente_id = :cliente_id";
                
                $stmt_update_contact = $pdo->prepare($sql_update_contact);
                $stmt_update_contact->bindValue(':nombre', $datos_contacto['co_nombre']);
                $stmt_update_contact->bindValue(':cargo', $datos_contacto['co_cargo']);
                $stmt_update_contact->bindValue(':email', $datos_contacto['co_email']);
                $stmt_update_contact->bindValue(':telefono', $datos_contacto['co_telefono']);
                $stmt_update_contact->bindValue(':cliente_id', $cliente_id, PDO::PARAM_INT);
                $stmt_update_contact->execute();
            } else {
                // Insertar nuevo contacto
                $sql_insert_contact = "INSERT INTO contactos_cliente (cliente_id, co_nombre, co_cargo, co_email, co_telefono, co_principal) 
                                      VALUES (:cliente_id, :nombre, :cargo, :email, :telefono, 1)";
                
                $stmt_insert_contact = $pdo->prepare($sql_insert_contact);
                $stmt_insert_contact->bindValue(':cliente_id', $cliente_id, PDO::PARAM_INT);
                $stmt_insert_contact->bindValue(':nombre', $datos_contacto['co_nombre']);
                $stmt_insert_contact->bindValue(':cargo', $datos_contacto['co_cargo']);
                $stmt_insert_contact->bindValue(':email', $datos_contacto['co_email']);
                $stmt_insert_contact->bindValue(':telefono', $datos_contacto['co_telefono']);
                $stmt_insert_contact->execute();
            }
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        // Respuesta exitosa
        $response = array(
            'success' => true,
            'message' => 'Cliente actualizado correctamente',
            'cliente_id' => $cliente_id
        );
        
        error_log("Cliente actualizado: ID " . $cliente_id . " - " . $datos['cl_nombre'] . ' ' . $datos['cl_apellido']);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Respuesta de error
        $response = array(
            'success' => false,
            'message' => 'Error al actualizar cliente: ' . $e->getMessage()
        );
        
        error_log("Error al actualizar cliente: " . $e->getMessage());
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

// ===== PETICIÓN GET - MOSTRAR TABLA DE CLIENTES =====
// Si llegamos aquí, es una petición GET para mostrar la tabla

// ===== CONFIGURACIÓN DE PAGINACIÓN =====
// Definir cuántos registros se mostrarán por página
$registros_por_pagina = 8;
// Obtener el número de página actual desde la URL (GET), si no existe, usar página 1
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
// Calcular el offset (desplazamiento) para la consulta SQL
// Ejemplo: página 1 = offset 0, página 2 = offset 8, página 3 = offset 16
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// ===== CONFIGURACIÓN DE BÚSQUEDA =====
// Obtener el término de búsqueda desde la URL (GET)
$termino_busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Debug: Registrar en el log del servidor los parámetros de paginación y búsqueda
error_log("Petición GET - Página actual: " . $pagina_actual . ", Offset: " . $offset . ", Búsqueda: " . $termino_busqueda);

// ===== CONSTRUIR CONSULTAS SQL CON BÚSQUEDA =====
// Preparar las condiciones WHERE para la búsqueda
$where_conditions = [];
$params = [];

// Si hay un término de búsqueda, agregar condiciones para todos los campos
if (!empty($termino_busqueda)) {
    // Buscar en todos los campos de texto de la tabla clientes
    $campos_busqueda = [
        'cl_nombre', 'cl_apellido', 'cl_empresa', 'cl_email', 
        'cl_telefono', 'cl_ciudad', 'cl_pais', 'cl_tipo'
    ];
    
    // Construir condiciones OR para cada campo
    foreach ($campos_busqueda as $campo) {
        $where_conditions[] = "$campo LIKE :buscar_$campo";
        $params[":buscar_$campo"] = "%$termino_busqueda%";
    }
    
    // Unir todas las condiciones con OR
    $where_clause = "WHERE " . implode(" OR ", $where_conditions);
} else {
    // Si no hay búsqueda, no agregar condiciones WHERE
    $where_clause = "";
}

// ===== OBTENER TOTAL DE REGISTROS CON BÚSQUEDA =====
// Consulta SQL para contar el total de registros que coinciden con la búsqueda
$sql_count = "SELECT COUNT(*) as total FROM clientes WHERE cl_activo = 1";
if (!empty($where_clause)) {
    $sql_count = "SELECT COUNT(*) as total FROM clientes WHERE cl_activo = 1 AND " . substr($where_clause, 6); // Remover "WHERE " del where_clause
}
$stmt_count = $pdo->prepare($sql_count);

// Vincular parámetros de búsqueda si existen
if (!empty($params)) {
    foreach ($params as $key => $value) {
        $stmt_count->bindValue($key, $value);
    }
}

// Ejecutar la consulta de conteo
$stmt_count->execute();
// Obtener el resultado del conteo
$total_registros = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
// Calcular el total de páginas necesarias (redondeando hacia arriba)
$total_paginas = ceil($total_registros / $registros_por_pagina);

// ===== OBTENER CLIENTES CON PAGINACIÓN Y BÚSQUEDA =====
// Consulta SQL para obtener los clientes con LIMIT, OFFSET y búsqueda
$sql = "SELECT * FROM clientes WHERE cl_activo = 1 ORDER BY cl_nombre ASC LIMIT :limit OFFSET :offset";
if (!empty($where_clause)) {
    $sql = "SELECT * FROM clientes WHERE cl_activo = 1 AND " . substr($where_clause, 6) . " ORDER BY cl_nombre ASC LIMIT :limit OFFSET :offset";
}
$stmt = $pdo->prepare($sql);

// Vincular parámetros de búsqueda si existen
if (!empty($params)) {
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
}

// Vincular el parámetro :limit con el número de registros por página
$stmt->bindValue(':limit', $registros_por_pagina, PDO::PARAM_INT);
// Vincular el parámetro :offset con el desplazamiento calculado
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
// Ejecutar la consulta
$stmt->execute();
// Obtener todos los registros como un array asociativo
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== GENERAR TABLA HTML =====
// Iniciar la construcción del HTML de la tabla
$tabla_html = '<div class="users-table-container">
    <div class="container-fluid">
        <div class="header">                
            <h1><img src="../icons/usuarios-white.png" alt="Clientes" style="vertical-align: middle; margin-right: 10px;"> Gestión de Clientes</h1>
            <p>Administra y visualiza todos los clientes del sistema</p>                    
        </div>
        
        <!-- ===== BUSCADOR EN TIEMPO REAL ===== -->
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="buscadorClientes" placeholder="Buscar clientes..." 
                       value="' . htmlspecialchars($termino_busqueda) . '"  style="width: 50%; padding: 10px; margin-top: 10px; margin-bottom: 10px;    ">
                <!-- <button type="button" class="search-btn" onclick="buscarClientes()">
                    <i class="fas fa-search"></i>
                </button>-->
                <button class="btn btn-agregar" id="nuevoCliente" onclick="mostrarModalAgregarCliente()"><img src="../icons/plus-white.png" alt="Agregar" width="32" height="32"> AGREGAR CLIENTE</button>
            </div>
            <div class="search-info">
                <span id="resultadosBusqueda">' . $total_registros . ' cliente(s) encontrado(s)</span>
            </div>
        </div>
        
        <div class="table-container">
            <table class="users-table" id="clientesTable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Empresa</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Ciudad</th>
                        <th>País</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>';

// ===== GENERAR FILAS DE LA TABLA =====
// Recorrer cada cliente obtenido de la base de datos
foreach($clientes as $cliente) {
    // Determinar el estado del cliente (1 = Activo, 0 = Inactivo)
    $estado = $cliente['cl_activo'] ? 'Activo' : 'Inactivo';
    // Agregar una fila HTML para cada cliente
    $tabla_html .= '<tr id="cliente-row-'.$cliente['id'].'">
        <td class="cl-nombre">'.$cliente['cl_nombre'].'</td>
        <td class="cl-apellido">'.$cliente['cl_apellido'].'</td>
        <td class="cl-empresa">'.$cliente['cl_empresa'].'</td>
        <td class="cl-email">'.$cliente['cl_email'].'</td>
        <td class="cl-telefono">'.$cliente['cl_telefono'].'</td>
        <td class="cl-ciudad">'.$cliente['cl_ciudad'].'</td>
        <td class="cl-pais">'.$cliente['cl_pais'].'</td>
        <td class="cl-tipo">'.$cliente['cl_tipo'].'</td>
        <td class="cl-estado">'.$estado.'</td>
        <td>
            <button class="btn btn-editar-nuevo" onclick="abrirModalEditarClienteNuevo('.$cliente['id'].')"><img src="../icons/lapiz.png"  alt="Editar" width="25" height="25" ></button>
            <button class="btn btn-eliminar" data-id="'.$cliente['id'].'"><img src="../icons/basura.png" alt="Descripción del ícono" width="25" height="25"></button>
            <button class="btn btn-ver" data-id="'.$cliente['id'].'"><img src="../icons/ver-violeta.png" alt="Descripción del ícono" width="25" height="25"></button>
        </td>
    </tr>';
}

// Cerrar la tabla HTML
$tabla_html .= '</tbody></table>';

// ===== AGREGAR CONTROLES DE PAGINACIÓN =====
// Solo mostrar controles de paginación si hay más de una página
if ($total_paginas > 1) {
    // Iniciar el contenedor de paginación
    $tabla_html .= '<div class="pagination-container">
        <div class="pagination-info">
            Mostrando ' . ($offset + 1) . ' - ' . min($offset + $registros_por_pagina, $total_registros) . ' de ' . $total_registros . ' registros
        </div>
        <div class="pagination-controls">';
    
    // ===== BOTÓN ANTERIOR =====
    // Mostrar botón "Anterior" solo si no estamos en la primera página
    if ($pagina_actual > 1) {
        $tabla_html .= '<button class="btn-pagina" onclick="cambiarPagina(' . ($pagina_actual - 1) . ')">&laquo; Anterior</button>';
    }
    
    // ===== NÚMEROS DE PÁGINA =====
    // Calcular el rango de páginas a mostrar (5 páginas máximo: actual ± 2)
    $inicio = max(1, $pagina_actual - 2);
    $fin = min($total_paginas, $pagina_actual + 2);
    
    // Si el inicio es mayor que 1, mostrar el botón de la página 1
    if ($inicio > 1) {
        $tabla_html .= '<button class="btn-pagina" onclick="cambiarPagina(1)">1</button>';
        // Si hay un gap, mostrar puntos suspensivos
        if ($inicio > 2) {
            $tabla_html .= '<span class="pagination-ellipsis">...</span>';
        }
    }
    
    // Generar botones para cada página en el rango
    for ($i = $inicio; $i <= $fin; $i++) {
        // Determinar si es la página activa para aplicar estilos diferentes
        $clase_activa = ($i == $pagina_actual) ? 'btn-pagina-activa' : 'btn-pagina';
        $tabla_html .= '<button class="' . $clase_activa . '" onclick="cambiarPagina(' . $i . ')">' . $i . '</button>';
    }
    
    // Si el fin es menor que el total de páginas, mostrar el botón de la última página
    if ($fin < $total_paginas) {
        // Si hay un gap, mostrar puntos suspensivos
        if ($fin < $total_paginas - 1) {
            $tabla_html .= '<span class="pagination-ellipsis">...</span>';
        }
        $tabla_html .= '<button class="btn-pagina" onclick="cambiarPagina(' . $total_paginas . ')">' . $total_paginas . '</button>';
    }
    
    // ===== BOTÓN SIGUIENTE =====
    // Mostrar botón "Siguiente" solo si no estamos en la última página
    if ($pagina_actual < $total_paginas) {
        $tabla_html .= '<button class="btn-pagina" onclick="cambiarPagina(' . ($pagina_actual + 1) . ')">Siguiente &raquo;</button>';
    }
    
    // Cerrar los contenedores de paginación
    $tabla_html .= '</div></div>';
}

// Cerrar todos los contenedores HTML
$tabla_html .= '</div></div></div>';

// ===== PREPARAR RESPUESTA JSON =====
// Crear un array con toda la información necesaria
$clientes = array(
    'tabla_html' => $tabla_html,  // El HTML completo de la tabla con paginación
    'datos' => $clientes,         // Los datos de los clientes de la página actual
    'paginacion' => array(        // Información de paginación para el frontend
        'pagina_actual' => $pagina_actual,      // Página actual
        'total_paginas' => $total_paginas,      // Total de páginas
        'total_registros' => $total_registros,  // Total de registros
        'registros_por_pagina' => $registros_por_pagina  // Registros por página
    ),
    'busqueda' => array(          // Información de búsqueda
        'termino' => $termino_busqueda,         // Término de búsqueda actual
        'resultados' => $total_registros        // Número de resultados encontrados
    )
);

// ===== ENVIAR RESPUESTA =====
// Establecer el header para indicar que la respuesta es JSON
header('Content-Type: application/json');
// Convertir el array a JSON y enviarlo al cliente
echo json_encode($clientes);

?>