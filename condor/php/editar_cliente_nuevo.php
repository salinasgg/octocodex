<?php
// Incluir el archivo de configuración de la base de datos
require_once 'config_bd.php';

// Crear una instancia de la clase Database para obtener la conexión
$database = Database::getInstance();
// Obtener la conexión PDO a la base de datos
$pdo = $database->getConnection();

// ===== DETECTAR TIPO DE PETICIÓN =====
// Verificar si es una petición GET para mostrar el formulario con datos del cliente
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    mostrarFormularioEdicion($pdo);
    exit;
}

// Verificar si es una petición POST para procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    procesarActualizacionCliente($pdo);
    exit;
}

// ===== FUNCIÓN PARA MOSTRAR FORMULARIO DE EDICIÓN =====
function mostrarFormularioEdicion($pdo) {
    try {
        // Obtener el ID del cliente a editar
        $cliente_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($cliente_id <= 0) {
            throw new Exception('ID de cliente inválido');
        }
        
        // Obtener los datos del cliente
        $sql = "SELECT c.*, cc.* FROM clientes c 
                LEFT JOIN contactos_cliente cc ON c.id = cc.cliente_id 
                WHERE c.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cliente_id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cliente) {
            throw new Exception('Cliente no encontrado');
        }
        
        // Separar nombre y apellido del contacto si existe
        $nombre_contacto = '';
        $apellido_contacto = '';
        if (!empty($cliente['co_nombre'])) {
            $partes_nombre = explode(' ', trim($cliente['co_nombre']), 2);
            $nombre_contacto = $partes_nombre[0] ?? '';
            $apellido_contacto = $partes_nombre[1] ?? '';
        }
        
        // Generar el HTML del formulario con los datos pre-cargados
        $formulario_html = '
<style>
    /* Estilos del modal */
    .modal-content {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border: none;
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    .modal-body {
        padding: 2rem;
        background: #f8f9fa;
    }

    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 1.5rem;
        border-radius: 0 0 12px 12px;
    }

    /* Estilos de formulario */
    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus, .form-select:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        outline: none;
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Botones */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    /* Mensajes de error */
    .error-message {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .modal-body {
            padding: 1rem;
        }
        
        .modal-header {
            padding: 1rem;
        }
        
        .modal-footer {
            padding: 1rem;
        }
    }
</style>

<!-- Modal -->
<div class="modal fade" id="modalEditarClienteNuevo" tabindex="-1" aria-labelledby="modalEditarClienteNuevoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">
            <img src="../icons/editar-usuario.png" alt="Editar Cliente" style="vertical-align: middle; margin-right: 10px; width: 32px; height: 32px;"> 
            Editar Cliente: ' . htmlspecialchars($cliente['cl_nombre'] . ' ' . $cliente['cl_apellido']) . '
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

        <!-- Body -->
        <div class="modal-body">
            <form id="editarClienteNuevoForm">
                <!-- Campo oculto con el ID del cliente -->
                <input type="hidden" id="cliente_id_nuevo" name="cliente_id" value="' . $cliente_id . '">
                
                <!-- Datos del Cliente -->
                <h5 class="mb-3" style="color: #8b5cf6; border-bottom: 2px solid #8b5cf6; padding-bottom: 0.5rem;">
                    <img src="../icons/16x/empresa16.png" alt="Cliente" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                    Datos del Cliente (ID: ' . $cliente_id . ')
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_nombre_nuevo">
                                <img src="../icons/16x/usuario.png" alt="Nombre" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Nombre <span style="color: red;">*</span>
                            </label>
                            <input type="text" id="cl_nombre_nuevo" name="cl_nombre" class="form-control"
                                placeholder="Ingrese el nombre del cliente" 
                                value="' . htmlspecialchars($cliente['cl_nombre']) . '" required>
                            <div class="error-message" id="nombre-error" style="display: none;">
                                ⚠️ El nombre es requerido
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_apellido_nuevo">
                                <img src="../icons/16x/usuario.png" alt="Apellido" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Apellido <span style="color: red;">*</span>
                            </label>
                            <input type="text" id="cl_apellido_nuevo" name="cl_apellido" class="form-control"
                                placeholder="Ingrese el apellido del cliente" 
                                value="' . htmlspecialchars($cliente['cl_apellido']) . '" required>
                            <div class="error-message" id="apellido-error" style="display: none;">
                                ⚠️ El apellido es requerido
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_empresa_nuevo">
                                <img src="../icons/16x/empresa16.png" alt="Empresa" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Empresa
                            </label>
                            <input type="text" id="cl_empresa_nuevo" name="cl_empresa" class="form-control"
                                placeholder="Ingrese el nombre de la empresa"
                                value="' . htmlspecialchars($cliente['cl_empresa']) . '">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_email_nuevo">
                                <img src="../icons/16x/email.png" alt="Email" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Email <span style="color: red;">*</span>
                            </label>
                            <input type="email" id="cl_email_nuevo" name="cl_email" class="form-control"
                                placeholder="cliente@ejemplo.com" 
                                value="' . htmlspecialchars($cliente['cl_email']) . '" required>
                            <div class="error-message" id="email-error" style="display: none;">
                                ⚠️ Ingrese un email válido
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_telefono_nuevo">
                                <img src="../icons/16x/telefono16.png" alt="Teléfono" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Teléfono
                            </label>
                            <input type="tel" id="cl_telefono_nuevo" name="cl_telefono" class="form-control"
                                placeholder="Ingrese el teléfono"
                                value="' . htmlspecialchars($cliente['cl_telefono']) . '">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_ciudad_nuevo">
                                <img src="../icons/16x/ciudad16.png" alt="Ciudad" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Ciudad
                            </label>
                            <input type="text" id="cl_ciudad_nuevo" name="cl_ciudad" class="form-control"
                                placeholder="Ingrese la ciudad"
                                value="' . htmlspecialchars($cliente['cl_ciudad']) . '">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_pais_nuevo">
                                <img src="../icons/16x/bandera16.png" alt="País" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                País
                            </label>
                            <input type="text" id="cl_pais_nuevo" name="cl_pais" class="form-control"
                                placeholder="Ingrese el país"
                                value="' . htmlspecialchars($cliente['cl_pais']) . '">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_tipo_nuevo">
                                <img src="../icons/16x/tipo16.png" alt="Tipo" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Tipo de Cliente
                            </label>
                            <select id="cl_tipo_nuevo" name="cl_tipo" class="form-select">
                                <option value="potencial"' . ($cliente['cl_tipo'] == 'potencial' ? ' selected' : '') . '>🔵 Potencial</option>
                                <option value="actual"' . ($cliente['cl_tipo'] == 'actual' ? ' selected' : '') . '>🟢 Actual</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Datos del Contacto -->
                <h5 class="mb-3 mt-4" style="color: #8b5cf6; border-bottom: 2px solid #8b5cf6; padding-bottom: 0.5rem;">
                    <img src="../icons/16x/infocontacto16.png" alt="Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                    Datos del Contacto Principal
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_nombre_nuevo">
                                <img src="../icons/16x/usuario.png" alt="Nombre Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Nombre del Contacto
                            </label>
                            <input type="text" id="cc_nombre_nuevo" name="cc_nombre" class="form-control"
                                placeholder="Ingrese el nombre del contacto"
                                value="' . htmlspecialchars($nombre_contacto) . '">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_apellido_nuevo">
                                <img src="../icons/16x/usuario.png" alt="Apellido Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Apellido del Contacto
                            </label>
                            <input type="text" id="cc_apellido_nuevo" name="cc_apellido" class="form-control"
                                placeholder="Ingrese el apellido del contacto"
                                value="' . htmlspecialchars($apellido_contacto) . '">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_cargo_nuevo">
                                <img src="../icons/16x/maletin16.png" alt="Cargo" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Cargo
                            </label>
                            <input type="text" id="cc_cargo_nuevo" name="cc_cargo" class="form-control"
                                placeholder="Ingrese el cargo del contacto"
                                value="' . htmlspecialchars($cliente['co_cargo'] ?? '') . '">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_email_nuevo">
                                <img src="../icons/16x/email.png" alt="Email Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Email del Contacto
                            </label>
                            <input type="email" id="cc_email_nuevo" name="cc_email" class="form-control"
                                placeholder="contacto@ejemplo.com"
                                value="' . htmlspecialchars($cliente['co_email'] ?? '') . '">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_telefono_nuevo">
                                <img src="../icons/16x/telefono16.png" alt="Teléfono Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Teléfono del Contacto
                            </label>
                            <input type="tel" id="cc_telefono_nuevo" name="cc_telefono" class="form-control"
                                placeholder="Ingrese el teléfono del contacto"
                                value="' . htmlspecialchars($cliente['co_telefono'] ?? '') . '">
                        </div>
                    </div>
                </div>
            </form>
        </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="cerrarModalEdicion()">
        <img src="../icons/16x/cancel16.png" alt="cancel" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Cancelar
        </button>
        <button type="submit" class="btn btn-primary" id="saveEditarClienteNuevoBtn" form="editarClienteNuevoForm">
        <img src="../icons/16x/guardar16.png" alt="save" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Actualizar Cliente
        </button>
      </div>

    </div>
  </div>
</div>';

        // Preparar respuesta JSON
        $response = array(
            'success' => true,
            'message' => 'Formulario de edición cargado correctamente',
            'html' => $formulario_html,
            'cliente' => $cliente
        );
        
    } catch (Exception $e) {
        // Respuesta de error
        $response = array(
            'success' => false,
            'message' => 'Error al cargar formulario: ' . $e->getMessage()
        );
    }
    
    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}

// ===== FUNCIÓN PARA PROCESAR ACTUALIZACIÓN DE CLIENTE =====
function procesarActualizacionCliente($pdo) {
    try {
        // Obtener datos del formulario
        $cliente_id = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;
        
        if ($cliente_id <= 0) {
            throw new Exception('ID de cliente inválido');
        }
        
        // Validar que el cliente existe y está activo
        $sql_check = "SELECT id FROM clientes WHERE id = ? AND cl_activo = 1";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$cliente_id]);
        if (!$stmt_check->fetch()) {
            throw new Exception('Cliente no encontrado o inactivo');
        }
        
        // Validación de datos requeridos
        $cl_nombre = trim($_POST['cl_nombre'] ?? '');
        $cl_apellido = trim($_POST['cl_apellido'] ?? '');
        $cl_email = trim($_POST['cl_email'] ?? '');
        
        if (empty($cl_nombre)) {
            throw new Exception('El nombre es requerido');
        }
        
        if (empty($cl_apellido)) {
            throw new Exception('El apellido es requerido');
        }
        
        if (empty($cl_email) || !filter_var($cl_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email válido es requerido');
        }
        
        // Verificar que el email no esté en uso por otro cliente
        $sql_email = "SELECT id FROM clientes WHERE cl_email = ? AND id != ? AND cl_activo = 1";
        $stmt_email = $pdo->prepare($sql_email);
        $stmt_email->execute([$cl_email, $cliente_id]);
        if ($stmt_email->fetch()) {
            throw new Exception('El email ya está registrado por otro cliente');
        }
        
        // Obtener otros campos opcionales
        $cl_empresa = trim($_POST['cl_empresa'] ?? '');
        $cl_telefono = trim($_POST['cl_telefono'] ?? '');
        $cl_ciudad = trim($_POST['cl_ciudad'] ?? '');
        $cl_pais = trim($_POST['cl_pais'] ?? '');
        $cl_tipo = $_POST['cl_tipo'] ?? 'potencial';
        
        // Datos del contacto
        $cc_nombre = trim($_POST['cc_nombre'] ?? '');
        $cc_apellido = trim($_POST['cc_apellido'] ?? '');
        $cc_cargo = trim($_POST['cc_cargo'] ?? '');
        $cc_email = trim($_POST['cc_email'] ?? '');
        $cc_telefono = trim($_POST['cc_telefono'] ?? '');
        
        // Validar email del contacto si se proporciona
        if (!empty($cc_email) && !filter_var($cc_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El email del contacto no es válido');
        }
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Actualizar datos del cliente
        $sql_cliente = "UPDATE clientes SET 
                        cl_nombre = ?, 
                        cl_apellido = ?, 
                        cl_empresa = ?, 
                        cl_email = ?, 
                        cl_telefono = ?, 
                        cl_ciudad = ?, 
                        cl_pais = ?, 
                        cl_tipo = ?
                        WHERE id = ?";
        
        $stmt_cliente = $pdo->prepare($sql_cliente);
        $resultado_cliente = $stmt_cliente->execute([
            $cl_nombre,
            $cl_apellido,
            $cl_empresa,
            $cl_email,
            $cl_telefono,
            $cl_ciudad,
            $cl_pais,
            $cl_tipo,
            $cliente_id
        ]);
        
        if (!$resultado_cliente) {
            throw new Exception('Error al actualizar los datos del cliente');
        }
        
        // Manejar datos del contacto
        if (!empty($cc_nombre) || !empty($cc_apellido) || !empty($cc_cargo) || !empty($cc_email) || !empty($cc_telefono)) {
            // Verificar si ya existe un contacto para este cliente
            $sql_check_contact = "SELECT id FROM contactos_cliente WHERE cliente_id = ?";
            $stmt_check_contact = $pdo->prepare($sql_check_contact);
            $stmt_check_contact->execute([$cliente_id]);
            $contacto_existente = $stmt_check_contact->fetch();
            
            // Combinar nombre y apellido del contacto
            $nombre_completo_contacto = trim($cc_nombre . ' ' . $cc_apellido);
            
            if ($contacto_existente) {
                // Actualizar contacto existente
                $sql_contact = "UPDATE contactos_cliente SET 
                                co_nombre = ?, 
                                co_cargo = ?, 
                                co_email = ?, 
                                co_telefono = ?
                                WHERE cliente_id = ?";
                $stmt_contact = $pdo->prepare($sql_contact);
                $resultado_contacto = $stmt_contact->execute([
                    $nombre_completo_contacto,
                    $cc_cargo,
                    $cc_email,
                    $cc_telefono,
                    $cliente_id
                ]);
            } else {
                // Insertar nuevo contacto
                $sql_contact = "INSERT INTO contactos_cliente (cliente_id, co_nombre, co_cargo, co_email, co_telefono) 
                                VALUES (?, ?, ?, ?, ?)";
                $stmt_contact = $pdo->prepare($sql_contact);
                $resultado_contacto = $stmt_contact->execute([
                    $cliente_id,
                    $nombre_completo_contacto,
                    $cc_cargo,
                    $cc_email,
                    $cc_telefono
                ]);
            }
            
            if (!$resultado_contacto) {
                throw new Exception('Error al actualizar los datos del contacto');
            }
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        // Obtener los datos actualizados del cliente para la respuesta
        $sql_updated = "SELECT * FROM clientes WHERE id = ?";
        $stmt_updated = $pdo->prepare($sql_updated);
        $stmt_updated->execute([$cliente_id]);
        $cliente_actualizado = $stmt_updated->fetch(PDO::FETCH_ASSOC);
        
        // Respuesta exitosa con datos actualizados
        $response = array(
            'success' => true,
            'message' => 'Cliente actualizado correctamente',
            'cliente_id' => $cliente_id,
            'cliente_data' => array(
                'cl_nombre' => $cliente_actualizado['cl_nombre'],
                'cl_apellido' => $cliente_actualizado['cl_apellido'],
                'cl_empresa' => $cliente_actualizado['cl_empresa'],
                'cl_email' => $cliente_actualizado['cl_email'],
                'cl_telefono' => $cliente_actualizado['cl_telefono'],
                'cl_ciudad' => $cliente_actualizado['cl_ciudad'],
                'cl_pais' => $cliente_actualizado['cl_pais'],
                'cl_tipo' => $cliente_actualizado['cl_tipo'],
                'cl_estado' => $cliente_actualizado['cl_activo'] ? 'Activo' : 'Inactivo'
            )
        );
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Respuesta de error
        $response = array(
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        );
    }
    
    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>