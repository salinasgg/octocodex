<?php
// Incluir el archivo de configuración de la base de datos
require_once 'config_bd.php';

// Crear una instancia de la clase Database para obtener la conexión
$database = Database::getInstance();
// Obtener la conexión PDO a la base de datos
$pdo = $database->getConnection();

// ===== DETECTAR TIPO DE PETICIÓN =====
// Verificar si es una petición POST (para procesar el formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    procesarNuevoCliente($pdo);
    exit;
}

// ===== FUNCIÓN PARA PROCESAR EL NUEVO CLIENTE =====
function procesarNuevoCliente($pdo) {
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

        // Verificar que el email de contacto no exista si se proporcionó
        if (!empty($datos_contacto['co_email'])) {
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
        
        $nuevo_cliente_id = $pdo->lastInsertId();
        
        // Insertar contacto si se proporcionaron datos
        if (!empty($datos_contacto['co_nombre']) || !empty($datos_contacto['co_email'])) {
            $sql_insert_contact = "INSERT INTO contactos_cliente (cliente_id, co_nombre, co_cargo, co_email, co_telefono, co_principal) 
                                  VALUES (:cliente_id, :nombre, :cargo, :email, :telefono, :principal)";
            
            $stmt_insert_contact = $pdo->prepare($sql_insert_contact);
            $stmt_insert_contact->bindValue(':cliente_id', $nuevo_cliente_id);
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
            'cliente_id' => $nuevo_cliente_id,
            'cliente' => $datos,
            'contacto' => $datos_contacto
        );
        
        error_log("Nuevo cliente agregado: ID " . $nuevo_cliente_id . " - " . $datos['cl_nombre'] . ' ' . $datos['cl_apellido']);
        
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

// ===== PETICIÓN GET - MOSTRAR FORMULARIO =====
// Si llegamos aquí, es una petición GET para mostrar el formulario

// Generar el HTML del formulario
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

    /* Toggle switch para estado */
    .toggle-container {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 10px;
    }

    .toggle-switch {
        width: 60px;
        height: 30px;
        background: #e5e7eb;
        border-radius: 15px;
        position: relative;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .toggle-switch.active {
        background: #10b981;
    }

    .toggle-slider {
        width: 26px;
        height: 26px;
        background: white;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: transform 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch.active .toggle-slider {
        transform: translateX(30px);
    }

    .toggle-label {
        font-weight: 600;
        color: #374151;
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
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">
            <img src="../icons/agregar-usuario.png" alt="Agregar Cliente" style="vertical-align: middle; margin-right: 10px; width: 32px; height: 32px;"> 
            Agregar Nuevo Cliente
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

        <!-- Body -->
        <div class="modal-body">
            <form id="nuevoClienteForm">
                
                <!-- Datos del Cliente -->
                <h5 class="mb-3" style="color: #8b5cf6; border-bottom: 2px solid #8b5cf6; padding-bottom: 0.5rem;">
                    <img src="../icons/16x/empresa16.png" alt="Cliente" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                    Datos del Cliente
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_nombre">
                                <img src="../icons/16x/usuario.png" alt="Nombre" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Nombre <span style="color: red;">*</span>
                            </label>
                            <input type="text" id="cl_nombre" name="cl_nombre" class="form-control"
                                placeholder="Ingrese el nombre del cliente" required>
                            <div class="error-message" id="nombre-error" style="display: none;">
                                ⚠️ El nombre es requerido
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_apellido">
                                <img src="../icons/16x/usuario.png" alt="Apellido" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Apellido <span style="color: red;">*</span>
                            </label>
                            <input type="text" id="cl_apellido" name="cl_apellido" class="form-control"
                                placeholder="Ingrese el apellido del cliente" required>
                            <div class="error-message" id="apellido-error" style="display: none;">
                                ⚠️ El apellido es requerido
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_empresa">
                                <img src="../icons/16x/empresa16.png" alt="Empresa" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Empresa
                            </label>
                            <input type="text" id="cl_empresa" name="cl_empresa" class="form-control"
                                placeholder="Ingrese el nombre de la empresa">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_email">
                                <img src="../icons/16x/email.png" alt="Email" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Email <span style="color: red;">*</span>
                            </label>
                            <input type="email" id="cl_email" name="cl_email" class="form-control"
                                placeholder="cliente@ejemplo.com" required>
                            <div class="error-message" id="email-error" style="display: none;">
                                ⚠️ Ingrese un email válido
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_telefono">
                                <img src="../icons/16x/telefono16.png" alt="Teléfono" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Teléfono
                            </label>
                            <input type="tel" id="cl_telefono" name="cl_telefono" class="form-control"
                                placeholder="Ingrese el teléfono">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_ciudad">
                                <img src="../icons/16x/ciudad16.png" alt="Ciudad" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Ciudad
                            </label>
                            <input type="text" id="cl_ciudad" name="cl_ciudad" class="form-control"
                                placeholder="Ingrese la ciudad">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_pais">
                                <img src="../icons/16x/bandera16.png" alt="País" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                País
                            </label>
                            <input type="text" id="cl_pais" name="cl_pais" class="form-control"
                                placeholder="Ingrese el país">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cl_tipo">
                                <img src="../icons/16x/tipo16.png" alt="Tipo" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Tipo de Cliente
                            </label>
                            <select id="cl_tipo" name="cl_tipo" class="form-select">
                                <option value="potencial">🔵 Potencial</option>
                                <option value="activo">🟢 Activo</option>
                                <option value="inactivo">🔴 Inactivo</option>
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
                            <label class="form-label" for="cc_nombre">
                                <img src="../icons/16x/usuario.png" alt="Nombre Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Nombre del Contacto
                            </label>
                            <input type="text" id="cc_nombre" name="cc_nombre" class="form-control"
                                placeholder="Ingrese el nombre del contacto">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_apellido">
                                <img src="../icons/16x/usuario.png" alt="Apellido Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Apellido del Contacto
                            </label>
                            <input type="text" id="cc_apellido" name="cc_apellido" class="form-control"
                                placeholder="Ingrese el apellido del contacto">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_cargo">
                                <img src="../icons/16x/maletin16.png" alt="Cargo" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Cargo
                            </label>
                            <input type="text" id="cc_cargo" name="cc_cargo" class="form-control"
                                placeholder="Ingrese el cargo del contacto">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_email">
                                <img src="../icons/16x/email.png" alt="Email Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Email del Contacto
                            </label>
                            <input type="email" id="cc_email" name="cc_email" class="form-control"
                                placeholder="contacto@ejemplo.com">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="cc_telefono">
                                <img src="../icons/16x/telefono16.png" alt="Teléfono Contacto" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Teléfono del Contacto
                            </label>
                            <input type="tel" id="cc_telefono" name="cc_telefono" class="form-control"
                                placeholder="Ingrese el teléfono del contacto">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label" for="cc_notas">
                        <img src="../icons/16x/notas16.png" alt="Notas" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                        Notas Adicionales
                    </label>
                    <textarea id="cc_notas" name="cc_notas" class="form-control"
                        placeholder="Ingrese notas adicionales sobre el cliente o contacto"></textarea>
                </div>
            </form>
        </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="volverATabla()">
        <img src="../icons/16x/cancel16.png" alt="cancel" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Cancelar
        </button>
        <button type="submit" class="btn btn-primary" id="saveNuevoClienteBtn" form="nuevoClienteForm">
        <img src="../icons/16x/guardar16.png" alt="save" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Crear Cliente
        </button>
      </div>

    </div>
  </div>
</div>';

// Preparar respuesta JSON
$response = array(
    'success' => true,
    'message' => 'Formulario cargado correctamente',
    'html' => $formulario_html
);

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
