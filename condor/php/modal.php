<!-- The Modal -->
<div class="modal fade" id="myModalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">
            <img src="../icons/agregar-usuario.png" alt="Agregar Usuario" style="vertical-align: middle; margin-right: 10px; width: 32px; height: 32px;"> 
            Agregar Nuevo Usuario
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

        <!-- Body -->
        <div class="modal-body">

        </div>
            
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <img src="../icons/16x/cancel16.png" alt="cancel" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Cancelar
        </button>
        <button type="button" class="btn btn-primary" onclick="saveNuevoUsuario()" id="saveNuevoBtn">
        <img src="../icons/16x/guardar16.png" alt="save" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;"> Crear Usuario
        </button>
      </div>

    </div>
  </div>
</div>


