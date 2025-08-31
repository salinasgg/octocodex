<?php
// Manejar peticiones GET para cargar el modal
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $modal_html = file_get_contents(__FILE__);
    $lines = explode("\n", $modal_html);
    $html_content = implode("\n", array_slice($lines, 1));
    $response = array(
        'success' => true,
        'message' => 'Modal de edición cargado correctamente',
        'html' => $html_content
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!-- Modal para Editar Cliente -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-labelledby="modalEditarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarClienteLabel">
                    <img src="../icons/editar-usuario.png" alt="Editar Cliente" style="vertical-align: middle; margin-right: 10px; width: 24px; height: 24px;">
                    Editar Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarClienteForm">
                    <!-- Información del Cliente -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cl_nombre" class="form-label">
                                    <img src="../icons/16x/nombre16.png" alt="Nombre" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Nombre *
                                </label>
                                <input type="text" class="form-control" id="cl_nombre" name="cl_nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cl_apellido" class="form-label">
                                    <img src="../icons/16x/apellido16.png" alt="Apellido" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Apellido *
                                </label>
                                <input type="text" class="form-control" id="cl_apellido" name="cl_apellido" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cl_empresa" class="form-label">
                                    <img src="../icons/16x/empresa16.png" alt="Empresa" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Empresa
                                </label>
                                <input type="text" class="form-control" id="cl_empresa" name="cl_empresa">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cl_email" class="form-label">
                                    <img src="../icons/16x/email.png" alt="Email" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Email *
                                </label>
                                <input type="email" class="form-control" id="cl_email" name="cl_email" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cl_telefono" class="form-label">
                                    <img src="../icons/16x/telefono16.png" alt="Teléfono" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Teléfono
                                </label>
                                <input type="tel" class="form-control" id="cl_telefono" name="cl_telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cl_tipo" class="form-label">
                                    <img src="../icons/16x/tipo16.png" alt="Tipo" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Tipo
                                </label>
                                <select class="form-select" id="cl_tipo" name="cl_tipo">
                                    <option value="potencial">Potencial</option>
                                    <option value="actual">Actual</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cl_ciudad" class="form-label">
                                    <img src="../icons/16x/ciudad16.png" alt="Ciudad" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Ciudad
                                </label>
                                <input type="text" class="form-control" id="cl_ciudad" name="cl_ciudad">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cl_pais" class="form-label">
                                    <img src="../icons/16x/bandera16.png" alt="País" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    País
                                </label>
                                <input type="text" class="form-control" id="cl_pais" name="cl_pais">
                            </div>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <hr>
                    <h6 class="mb-3">
                        <img src="../icons/16x/infocontacto16.png" alt="Información de Contacto" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                        Información de Contacto
                    </h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cc_nombre" class="form-label">
                                    <img src="../icons/16x/nombre16.png" alt="Nombre Contacto" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Nombre del Contacto
                                </label>
                                <input type="text" class="form-control" id="cc_nombre" name="cc_nombre">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cc_apellido" class="form-label">
                                    <img src="../icons/16x/apellido16.png" alt="Apellido Contacto" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Apellido del Contacto
                                </label>
                                <input type="text" class="form-control" id="cc_apellido" name="cc_apellido">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cc_cargo" class="form-label">
                                    <img src="../icons/16x/cargo16.png" alt="Cargo" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Cargo
                                </label>
                                <input type="text" class="form-control" id="cc_cargo" name="cc_cargo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cc_email" class="form-label">
                                    <img src="../icons/16x/email.png" alt="Email Contacto" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Email del Contacto
                                </label>
                                <input type="email" class="form-control" id="cc_email" name="cc_email">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cc_telefono" class="form-label">
                                    <img src="../icons/16x/telefono16.png" alt="Teléfono Contacto" style="vertical-align: middle; margin-right: 5px; width: 16px; height: 16px;">
                                    Teléfono del Contacto
                                </label>
                                <input type="tel" class="form-control" id="cc_telefono" name="cc_telefono">
                            </div>
                        </div>
                    </div>

                    <!-- Campo oculto para el ID del cliente -->
                    <input type="hidden" id="cliente_id_edit" name="cliente_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <img src="../icons/16x/cancel16.png" alt="cancel" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="saveEditarClienteBtn" form="editarClienteForm">
                    <img src="../icons/16x/guardar16.png" alt="save" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Actualizar Cliente
                </button>
            </div>
        </div>
    </div>
</div>
