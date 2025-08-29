
<!-- The Modal -->
<div class="modal fade" id="myModaleditUser" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">
            <img src="../icons/editar-usuario.png" alt="Usuarios" style="vertical-align: middle; margin-right: 10px; width: 32px; height: 32px;"> 
            Modificar Usuario
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

        <!-- Body -->
        <div class="modal-body">
            <form id="editUserForm">
                <input type="hidden" id="edit_user_id" name="user_id">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="us_username">
                                <img src="../icons/16x/usuario.png" alt="Usuario" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Nombre de Usuario
                            </label>
                            <input type="text" id="us_username" name="us_username" class="form-control"
                                placeholder="Ingrese el nombre de usuario" required>
                            <div class="error-message" id="username-error" style="display: none;">
                                ⚠️ El nombre de usuario es requerido
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="us_nombre">
                                <img src="../icons/16x/usuario.png" alt="Nombre" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Nombre Completo
                            </label>
                            <input type="text" id="us_nombre" name="us_nombre" class="form-control"
                                placeholder="Ingrese el nombre completo" required>
                            <div class="error-message" id="nombre-error" style="display: none;">
                                ⚠️ El nombre es requerido
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="us_email">
                                <img src="../icons/16x/email.png" alt="Email" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Correo Electrónico
                            </label>
                            <input type="email" id="us_email" name="us_email" class="form-control"
                                placeholder="usuario@ejemplo.com" required>
                            <div class="error-message" id="email-error" style="display: none;">
                                ⚠️ Ingrese un correo válido
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="us_fecha_nacimiento">
                                <img src="../icons/16x/calendario.png" alt="Fecha" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Fecha de Nacimiento
                            </label>
                            <input type="date" id="us_fecha_nacimiento" name="us_fecha_nacimiento" class="form-control" required>
                            <div class="error-message" id="fecha-error" style="display: none;">
                                ⚠️ La fecha de nacimiento es requerida
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="us_rol">
                                <img src="../icons/16x/rol.png" alt="Rol" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Rol del Usuario
                            </label>
                            <select id="us_rol" name="us_rol" class="form-select" required>
                                <option value="">Seleccione un rol</option>
                                <option value="administrador">🔧 Administrador</option>
                                <option value="usuario">👤 Usuario</option>
                            </select>
                            <div class="error-message" id="rol-error" style="display: none;">
                                ⚠️ Debe seleccionar un rol
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">
                                <img src="../icons/16x/estado.png" alt="Estado" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> 
                                Estado del Usuario
                            </label>
                            <div class="toggle-container">
                                <div class="toggle-switch" id="toggleActive" onclick="toggleUserStatus()">
                                    <div class="toggle-slider"></div>
                                </div>
                                <span class="toggle-label" id="statusLabel">Inactivo</span>
                                <input type="hidden" id="us_activo" name="us_activo" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <img src="../icons/16x/cancel16.png" alt="cancel" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Cancelar
        </button>
        <button type="button" class="btn btn-primary" onclick="saveUser()" id="saveBtn">
        <img src="../icons/16x/guardar16.png" alt="save" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Guardar Cambios
        </button>
      </div>

    </div>
  </div>
</div>


