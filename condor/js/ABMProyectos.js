// ===== ABM PROYECTOS - JAVASCRIPT =====
/**
 * Funcionalidades JavaScript para el sistema de gestión de proyectos
 * Incluye CRUD, drag & drop, búsqueda y actualización en tiempo real
 */

// Variables globales
let proyectos = [];
let clientes = [];
let draggedProject = null;

/**
 * Función helper para determinar la URL base correcta según el contexto
 */
function getBaseUrl() {
    const pathname = window.location.pathname;
    
    if (pathname.includes('/admin/')) {
        return '../php/';
    } else if (pathname.includes('/condor/')) {
        return 'php/';
    } else {
        return 'php/';
    }
}

// ==================== FUNCIONES DE INICIALIZACIÓN ====================

// Función para cargar proyectos desde el dashboard (evita conflictos con navbar)
function cargarProyectosDesdeDashboard() {
    console.log('🔄 Cargando proyectos desde dashboard...');
    
    // Cargar el contenido de proyectos optimizado para admin
    $.ajax({
        url: '../proyectos_content.php',
        method: 'GET',
        success: function(response) {
            console.log('✅ Contenido de proyectos cargado exitosamente');
            $('.main-content').html(response);
            
            // Inicializar funcionalidades después de cargar el contenido
            setTimeout(function() {
                if (typeof cargarProyectos === 'function') {
                    cargarProyectos();
                }
                if (typeof cargarClientes === 'function') {
                    cargarClientes();
                }
            }, 100);
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar proyectos_content.php:', error);
            $('.main-content').html('<div class="alert alert-danger">Error al cargar el contenido de proyectos</div>');
        }
    });
}

// Event listener para el botón de proyectos (solo si existe)
$(document).ready(function() {
    // Usar event delegation para evitar conflictos
    $(document).on('click', '#proyectos-btn', function(e) {
        e.preventDefault();
        console.log('🔄 Botón proyectos clickeado');
        cargarProyectosDesdeDashboard();
    });
});

/**
 * Cargar todos los proyectos desde el servidor
 */
function cargarProyectos() {
    // Determinar la ruta correcta según el contexto
    const baseUrl = getBaseUrl();
    
    console.log('🔍 Cargando proyectos desde:', baseUrl + 'abm_proyectos.php');
    
    $.ajax({
        url: baseUrl + 'abm_proyectos.php',
        method: 'GET',
        data: { accion: 'listar' },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                proyectos = response.proyectos;
                
                renderizarProyectos();
                actualizarContadores();
            } else {
                mostrarAlerta('Error al cargar proyectos: ' + response.mensaje, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            mostrarAlerta('Error de conexión al cargar proyectos', 'error');
        }
    });
}

/**
 * Cargar lista de clientes para el select
 */
function cargarClientes() {
    // Determinar la ruta correcta según el contexto
    const baseUrl = getBaseUrl();
    
    $.ajax({
        url: baseUrl + 'abm_proyectos.php',
        method: 'GET',
        data: { accion: 'listar_clientes' },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                clientes = response.clientes;
                llenarSelectClientes();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar clientes:', error);
        }
    });
}

/**
 * Llenar el select de clientes en el modal
 */
function llenarSelectClientes() {
    const select = document.getElementById('cliente_id');
    if (!select) return;
    
    select.innerHTML = '<option value="">Seleccionar cliente...</option>';
    
    clientes.forEach(cliente => {
        const option = document.createElement('option');
        option.value = cliente.id;
        option.textContent = cliente.nombre_display;
        select.appendChild(option);
    });
}

// ==================== FUNCIONES DE RENDERIZADO ====================

/**
 * Renderizar proyectos en las columnas estilo Trello
 */
function renderizarProyectos() {
    // Limpiar contenedores
    const estados = ['propuesta', 'en_desarrollo', 'en_revision', 'finalizado'];
    estados.forEach(estado => {
        const container = document.getElementById(estado + '-container');
        if (container) {
            // Mantener el botón de agregar, solo limpiar las tarjetas
            const addBtn = container.querySelector('.add-project-btn');
            container.innerHTML = '';
            if (addBtn && estado !== 'finalizado') {
                container.appendChild(addBtn);
            }
        }
    });
    
    // Agrupar proyectos por estado
    const proyectosPorEstado = {};
    estados.forEach(estado => {
        proyectosPorEstado[estado] = proyectos.filter(p => p.pr_estado === estado);
    });
    
    // Renderizar cada grupo
    Object.keys(proyectosPorEstado).forEach(estado => {
        const container = document.getElementById(estado + '-container');
        if (container) {
            proyectosPorEstado[estado].forEach(proyecto => {
                const card = crearTarjetaProyecto(proyecto);
                if (estado === 'finalizado') {
                    container.appendChild(card);
                } else {
                    container.insertBefore(card, container.lastElementChild);
                }
            });
        }
    });
    
    // Configurar drag & drop
    configurarDragAndDrop();
}

/**
 * Crear una tarjeta de proyecto
 */
function crearTarjetaProyecto(proyecto) {
    const card = document.createElement('div');
    card.className = 'project-card';
    card.draggable = true;
    card.dataset.projectId = proyecto.id;
    card.dataset.estado = proyecto.pr_estado;
    
    // Determinar color de prioridad
    const prioridadClass = `priority-${proyecto.pr_prioridad}`;
    
    // Formatear fechas
    const fechaInicio = proyecto.pr_fecha_inicio ? 
        new Date(proyecto.pr_fecha_inicio).toLocaleDateString('es-AR') : 'Sin definir';
    const fechaEstimada = proyecto.pr_fecha_estimada ? 
        new Date(proyecto.pr_fecha_estimada).toLocaleDateString('es-AR') : 'Sin definir';
    
    // Calcular días restantes si hay fecha estimada
    let diasRestantes = '';
    let estadoDias = '';
    if (proyecto.pr_fecha_estimada) {
        const hoy = new Date();
        const fechaEst = new Date(proyecto.pr_fecha_estimada);
        const diffTime = fechaEst - hoy;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays > 0) {
            diasRestantes = `${diffDays} días restantes`;
            estadoDias = 'text-success';
        } else if (diffDays === 0) {
            diasRestantes = 'Vence hoy';
            estadoDias = 'text-warning fw-bold';
        } else {
            diasRestantes = `${Math.abs(diffDays)} días vencido`;
            estadoDias = 'text-danger fw-bold';
        }
    }
    
    // Crear contenido de la tarjeta con más información
    card.innerHTML = `
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="project-title">${proyecto.pr_titulo}</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle-custom" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="editarProyecto(${proyecto.id}); event.preventDefault();">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="verDetalleProyecto(${proyecto.id}); event.preventDefault();">
                        <img src="../icons/16x/ver-violeta16.png" alt="ver-violeta" style="margin-right: 0.5rem;">Ver Detalle
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="duplicarProyecto(${proyecto.id}); event.preventDefault();">
                        <i class="fas fa-copy me-2"></i>Duplicar
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="eliminarProyecto(${proyecto.id}); event.preventDefault();">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </a></li>
                </ul>
            </div>
        </div>
        
        ${proyecto.cliente_empresa ? `
            <div class="project-client">
                <i class="fas fa-building me-1"></i>
                ${proyecto.cliente_empresa}
            </div>
        ` : ''}
        
        ${proyecto.pr_descripcion ? `
            <div class="project-description">
                <small class="text-muted">
                    <i class="fas fa-align-left me-1"></i>
                    ${proyecto.pr_descripcion.length > 80 ? proyecto.pr_descripcion.substring(0, 80) + '...' : proyecto.pr_descripcion}
                </small>
            </div>
        ` : ''}
        
        <div class="project-meta">
            <span class="priority-badge ${prioridadClass}">
                <i class="fas fa-flag me-1"></i>
                ${proyecto.pr_prioridad.charAt(0).toUpperCase() + proyecto.pr_prioridad.slice(1)}
            </span>
            ${proyecto.pr_presupuesto ? `
                <span class="badge bg-success">
                    <i class="fas fa-dollar-sign me-1"></i>$${parseFloat(proyecto.pr_presupuesto).toLocaleString()}
                </span>
            ` : ''}
        </div>
        
        <div class="progress-container">
            <div class="progress-label">
                <span><i class="fas fa-chart-line me-1"></i>Progreso</span>
                <span class="fw-bold">${proyecto.pr_progreso}%</span>
            </div>
            <div class="progress">
                <div class="progress-bar" style="width: ${proyecto.pr_progreso}%"></div>
            </div>
        </div>
        
        <div class="project-dates">
            <small>
                <i class="fas fa-calendar-alt me-1"></i>Inicio: ${fechaInicio}
            </small>
            <small>
                <i class="fas fa-calendar-check me-1"></i>Est.: ${fechaEstimada}
            </small>
        </div>
        
        ${diasRestantes ? `
            <div class="project-deadline mt-2">
                <small class="${estadoDias}">
                    <i class="fas fa-clock me-1"></i>${diasRestantes}
                </small>
            </div>
        ` : ''}
        
        <div class="project-info-extra mt-2">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-user me-1"></i>ID: ${proyecto.id}
                </small>
                ${proyecto.total_tareas ? `
                    <small class="text-muted">
                        <i class="fas fa-tasks me-1"></i>
                        ${proyecto.tareas_completadas}/${proyecto.total_tareas} tareas
                    </small>
                ` : ''}
            </div>
        </div>
        
        <div class="project-timestamps mt-1">
            <small class="text-muted">
                <i class="fas fa-plus-circle me-1"></i>
                Creado: ${proyecto.pr_fecha_creacion ? new Date(proyecto.pr_fecha_creacion).toLocaleDateString('es-AR') : 'N/A'}
            </small>
        </div>
        
        <!-- Indicadores de usuarios asignados -->
        <div class="assigned-users mt-2" id="assigned-users-${proyecto.id}">
            <div class="d-flex align-items-center">
                <small class="text-muted me-2">
                    <i class="fas fa-users me-1"></i>Equipo:
                </small>
                <div class="assigned-avatars" id="avatars-${proyecto.id}">
                    <div class="loading-avatars">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar asignaciones para mostrar avatares (asíncrono)
    setTimeout(() => cargarAvatares(proyecto.id), 100);
    
    // Eventos de drag
    card.addEventListener('dragstart', function(e) {
        draggedProject = {
            id: proyecto.id,
            element: this,
            originalState: proyecto.pr_estado
        };
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    });
    
    card.addEventListener('dragend', function() {
        this.classList.remove('dragging');
        draggedProject = null;
    });
    
    return card;
}

/**
 * Actualizar contadores de proyectos en cada columna
 */
function actualizarContadores() {
    const estados = ['propuesta', 'en_desarrollo', 'en_revision', 'finalizado'];
    
    estados.forEach(estado => {
        const count = proyectos.filter(p => p.pr_estado === estado).length;
        const column = document.querySelector(`[data-estado="${estado}"] .project-count`);
        if (column) {
            column.textContent = count;
        }
    });
}

// ==================== DRAG & DROP ====================

/**
 * Configurar funcionalidad drag and drop
 */
function configurarDragAndDrop() {
    const columns = document.querySelectorAll('.trello-column');
    
    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });
        
        column.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        
        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (draggedProject) {
                const nuevoEstado = this.dataset.estado;
                const estadoAnterior = draggedProject.originalState;
                
                if (nuevoEstado !== estadoAnterior) {
                    cambiarEstadoProyecto(draggedProject.id, nuevoEstado);
                }
            }
        });
    });
}

/**
 * Cambiar estado de proyecto via AJAX
 */
function cambiarEstadoProyecto(proyectoId, nuevoEstado) {
    // Determinar la ruta correcta según el contexto
    const baseUrl = getBaseUrl();
    
    $.ajax({
        url: baseUrl + 'abm_proyectos.php',
        method: 'POST',
        data: {
            accion: 'cambiar_estado',
            id: proyectoId,
            estado: nuevoEstado
        },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                showMessage('Estado del proyecto actualizado a ' + nuevoEstado, 'success', 3000);
                cargarProyectos(); // Recargar para reflejar cambios
            } else {
                mostrarAlerta('Error: ' + response.mensaje, 'error');
                cargarProyectos(); // Recargar para revertir cambio visual
            }
        },
        error: function() {
            mostrarAlerta('Error de conexión al cambiar estado', 'error');
            cargarProyectos(); // Recargar para revertir cambio visual
        }
    });
}

// ==================== FUNCIONES DEL MODAL ====================

/**
 * Abrir modal para nuevo proyecto
 */
function abrirModalNuevoProyecto(estadoInicial = 'propuesta') {
    console.log('🔧 Abriendo modal para nuevo proyecto con estado:', estadoInicial);
    
    try {
        document.getElementById('modalProyectoTitle').innerHTML = 
            '<i class="fas fa-project-diagram me-2"></i>Nuevo Proyecto';
        document.getElementById('formProyecto').reset();
        document.getElementById('proyecto_id').value = '';
        document.getElementById('accion').value = 'crear';
        document.getElementById('pr_estado').value = estadoInicial;
        document.getElementById('progreso-actual').textContent = '0%';
        
        const modal = new bootstrap.Modal(document.getElementById('modalProyecto'));
        modal.show();
        console.log('✅ Modal abierto correctamente');
    } catch (error) {
        console.error('❌ Error al abrir modal:', error);
        mostrarAlerta('Error al abrir el modal: ' + error.message, 'error');
    }
}

/**
 * Editar proyecto existente
 */
function editarProyecto(id) {
    console.log('🔧 Editando proyecto con ID:', id);
    
    const proyecto = proyectos.find(p => p.id == id);
    if (!proyecto) {
        console.error('❌ Proyecto no encontrado con ID:', id);
        mostrarAlerta('Proyecto no encontrado', 'error');
        return;
    }
    
    try {
        // Llenar formulario
        document.getElementById('modalProyectoTitle').innerHTML = 
            '<i class="fas fa-edit me-2"></i>Editar Proyecto';
        document.getElementById('proyecto_id').value = proyecto.id;
        document.getElementById('accion').value = 'actualizar';
        document.getElementById('pr_titulo').value = proyecto.pr_titulo;
        document.getElementById('pr_descripcion').value = proyecto.pr_descripcion || '';
        document.getElementById('pr_estado').value = proyecto.pr_estado;
        document.getElementById('pr_fecha_inicio').value = proyecto.pr_fecha_inicio || '';
        document.getElementById('pr_fecha_estimada').value = proyecto.pr_fecha_estimada || '';
        document.getElementById('pr_presupuesto').value = proyecto.pr_presupuesto || '';
        document.getElementById('pr_prioridad').value = proyecto.pr_prioridad;
        document.getElementById('pr_progreso').value = proyecto.pr_progreso;
        document.getElementById('cliente_id').value = proyecto.cliente_id || '';
        
        actualizarProgreso(proyecto.pr_progreso);
        
        const modal = new bootstrap.Modal(document.getElementById('modalProyecto'));
        modal.show();
        
        // Cargar asignaciones existentes después de abrir el modal
        setTimeout(() => {
            cargarAsignacionesProyecto(proyecto.id);
        }, 500);
        
        console.log('✅ Modal de edición abierto correctamente');
    } catch (error) {
        console.error('❌ Error al abrir modal de edición:', error);
        mostrarAlerta('Error al abrir el modal de edición: ' + error.message, 'error');
    }
}

/**
 * Guardar proyecto (crear o actualizar)
 */
function guardarProyecto() {
    const form = document.getElementById('formProyecto');
    const formData = new FormData(form);
    
    // Validaciones básicas
    if (!formData.get('pr_titulo').trim()) {
        mostrarAlerta('El título del proyecto es requerido', 'error');
        return;
    }
    
    // Determinar la ruta correcta según el contexto
    const baseUrl = getBaseUrl();
    
    console.log('🔍 Guardando proyecto en:', baseUrl + 'abm_proyectos.php');
    console.log('📤 Datos del formulario:', Object.fromEntries(formData));
    console.log('📤 Acción:', formData.get('accion'));
    console.log('📤 ID del proyecto:', formData.get('proyecto_id'));
    
    $.ajax({
        url: baseUrl + 'abm_proyectos.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta exitosa:', response);
            if (response.exito) {
                showMessage(response.mensaje, 'success', 3000);
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalProyecto'));
                modal.hide();
                cargarProyectos(); // Recargar lista
            } else {
                console.error('❌ Error en respuesta:', response);
                mostrarAlerta('Error: ' + response.mensaje, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX al guardar proyecto:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusText: xhr.statusText,
                readyState: xhr.readyState
            });
            
            // Intentar parsear la respuesta de error
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                console.error('❌ Error parseado:', errorResponse);
                showMessage('Error: ' + (errorResponse.mensaje || error), 'error', 3000);
            } catch (e) {
                console.error('❌ No se pudo parsear la respuesta de error');
                showMessage('Error de conexión al guardar proyecto: ' + error, 'error', 3000);
            }
        }
    });
}

/**
 * Eliminar proyecto
 */
function eliminarProyecto(id) {
    // Convertir ID a número para asegurar consistencia
    const proyectoId = parseInt(id);
    console.log('🔧 Eliminando proyecto con ID:', proyectoId);
    console.log('🔧 ID original:', id, 'Tipo:', typeof id);
    console.log('🔧 ID convertido:', proyectoId, 'Tipo:', typeof proyectoId);
    
    const proyecto = proyectos.find(p => p.id == proyectoId);
    if (!proyecto) {
        console.error('❌ Proyecto no encontrado con ID:', proyectoId);
        mostrarAlerta('Proyecto no encontrado', 'error');
        return;
    }
    mostrarConfirmacionEliminar(`¿Está seguro de eliminar el proyecto "${proyecto.pr_titulo}"?`, function() {
        // Determinar la ruta correcta según el contexto
        const baseUrl = getBaseUrl();
        
        console.log('🔍 Eliminando proyecto desde:', baseUrl + 'abm_proyectos.php');
        console.log('📤 ID del proyecto:', proyectoId);
        console.log('📤 Tipo de ID:', typeof proyectoId);
        console.log('📤 Datos a enviar:', { accion: 'eliminar', id: proyectoId });
        
        const formData = {
            accion: 'eliminar',
            id: proyectoId
        };
        
        console.log('📤 FormData final:', formData);
        console.log('📤 FormData.id:', formData.id);
        console.log('📤 FormData.id tipo:', typeof formData.id);
        console.log('📤 FormData.accion:', formData.accion);
        
        // Convertir a FormData para asegurar que se envíe correctamente
        const formDataObj = new FormData();
        formDataObj.append('accion', formData.accion);
        formDataObj.append('id', formData.id);
        
        console.log('📤 FormDataObj creado:', formDataObj);
        console.log('📤 FormDataObj.get("id"):', formDataObj.get('id'));

        $.ajax({
            url: baseUrl + 'abm_proyectos.php',
            method: 'POST',
            data: formDataObj,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('✅ Respuesta exitosa:', response);
                if (response.exito) {
                    showMessage(response.mensaje, 'success', 3000);
                    cargarProyectos(); // Recargar lista
                } else {
                    console.error('❌ Error en respuesta:', response);
                    showMessage('Error: ' + response.mensaje, 'error', 3000);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al eliminar proyecto:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusText: xhr.statusText,
                    readyState: xhr.readyState
                });
                
                // Intentar parsear la respuesta de error
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    console.error('❌ Error parseado:', errorResponse);
                    showMessage('Error: ' + (errorResponse.mensaje || error), 'error', 3000);
                } catch (e) {
                    showMessage('Error de conexión al eliminar proyecto', 'error', 3000);
                }
            }
        });
    });
}   

/**
 * Ver detalle completo del proyecto
 */
function verDetalleProyecto(id) {
    console.log('🔧 Mostrando detalle del proyecto con ID:', id);
    
    const proyecto = proyectos.find(p => p.id == id);
    if (!proyecto) {
        console.error('❌ Proyecto no encontrado con ID:', id);
        mostrarAlerta('Proyecto no encontrado', 'error');
        return;
    }
    
    try {
        // Verificar que el modal existe antes de intentar usarlo
        const modalElement = document.getElementById('modalDetalleProyecto');
        if (!modalElement) {
            console.error('❌ Modal modalDetalleProyecto no encontrado en el DOM');
            mostrarAlerta('Error: Modal de detalle no disponible. Recarga la página.', 'error');
            return;
        }
        
        // Verificar elementos críticos del modal
        const elementosCriticos = [
            'modalDetalleTitle', 'detalle-titulo', 'detalle-descripcion', 
            'detalle-cliente', 'detalle-estado', 'detalle-progreso', 
            'detalle-progress-bar', 'detalle-fecha-inicio', 'detalle-fecha-estimada',
            'detalle-estado-plazo', 'detalle-prioridad', 'detalle-presupuesto',
            'detalle-id', 'detalle-tareas', 'detalle-actualizacion', 'detalle-fecha-creacion'
        ];
        
        const elementosFaltantes = [];
        elementosCriticos.forEach(elementoId => {
            if (!document.getElementById(elementoId)) {
                elementosFaltantes.push(elementoId);
            }
        });
        
        if (elementosFaltantes.length > 0) {
            console.error('❌ Elementos faltantes en modal:', elementosFaltantes);
            mostrarAlerta('Error: Modal de detalle incompleto. Elementos faltantes: ' + elementosFaltantes.join(', '), 'error');
            return;
        }
        
        // Actualizar título del modal
        document.getElementById('modalDetalleTitle').innerHTML = 
            `<img src="../icons/16x/ver-violeta16.png" alt="ver-violeta" style="margin-right: 0.5rem;">Detalle del Proyecto: ${proyecto.pr_titulo}`;
        
        // Llenar información general
        document.getElementById('detalle-titulo').textContent = proyecto.pr_titulo;
        document.getElementById('detalle-descripcion').textContent = proyecto.pr_descripcion || 'Sin descripción';
        document.getElementById('detalle-cliente').textContent = proyecto.cliente_empresa || 'Sin asignar';
        
        // Estado con badge apropiado
        const estadoBadge = document.getElementById('detalle-estado');
        estadoBadge.textContent = proyecto.pr_estado.replace('_', ' ').toUpperCase();
        estadoBadge.className = `badge fs-6 ${getEstadoBadgeClass(proyecto.pr_estado)}`;
        
        // Progreso
        const progreso = parseInt(proyecto.pr_progreso) || 0;
        document.getElementById('detalle-progreso').textContent = `${progreso}%`;
        document.getElementById('detalle-progress-bar').style.width = `${progreso}%`;
        
        // Fechas
        const fechaInicio = proyecto.pr_fecha_inicio ? 
            new Date(proyecto.pr_fecha_inicio).toLocaleDateString('es-AR') : 'Sin definir';
        const fechaEstimada = proyecto.pr_fecha_estimada ? 
            new Date(proyecto.pr_fecha_estimada).toLocaleDateString('es-AR') : 'Sin definir';
        const fechaCreacion = proyecto.pr_fecha_creacion ? 
            new Date(proyecto.pr_fecha_creacion).toLocaleDateString('es-AR') : 'N/A';
            
        document.getElementById('detalle-fecha-inicio').textContent = fechaInicio;
        document.getElementById('detalle-fecha-estimada').textContent = fechaEstimada;
        document.getElementById('detalle-fecha-creacion').textContent = fechaCreacion;
        
        // Estado del plazo
        let estadoPlazo = '-';
        let claseEstadoPlazo = '';
        if (proyecto.pr_fecha_estimada) {
            const hoy = new Date();
            const fechaEst = new Date(proyecto.pr_fecha_estimada);
            const diffTime = fechaEst - hoy;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 7) {
                estadoPlazo = `A tiempo (${diffDays} días restantes)`;
                claseEstadoPlazo = 'text-success';
            } else if (diffDays > 0) {
                estadoPlazo = `Próximo a vencer (${diffDays} días)`;
                claseEstadoPlazo = 'text-warning fw-bold';
            } else if (diffDays === 0) {
                estadoPlazo = 'Vence hoy';
                claseEstadoPlazo = 'text-warning fw-bold';
            } else {
                estadoPlazo = `Vencido (${Math.abs(diffDays)} días)`;
                claseEstadoPlazo = 'text-danger fw-bold';
            }
        }
        const estadoPlazoElement = document.getElementById('detalle-estado-plazo');
        estadoPlazoElement.textContent = estadoPlazo;
        estadoPlazoElement.className = `mb-0 ${claseEstadoPlazo}`;
        
        // Prioridad
        const prioridadBadge = document.getElementById('detalle-prioridad');
        prioridadBadge.textContent = proyecto.pr_prioridad.charAt(0).toUpperCase() + proyecto.pr_prioridad.slice(1);
        prioridadBadge.className = `priority-badge fs-6 priority-${proyecto.pr_prioridad}`;
        
        // Presupuesto
        const presupuesto = proyecto.pr_presupuesto ? 
            `$${parseFloat(proyecto.pr_presupuesto).toLocaleString('es-AR')}` : 'Sin definir';
        document.getElementById('detalle-presupuesto').textContent = presupuesto;
        
        // Información técnica
        document.getElementById('detalle-id').textContent = `#${proyecto.id}`;
        
        const tareas = proyecto.total_tareas ? 
            `${proyecto.tareas_completadas || 0}/${proyecto.total_tareas} completadas` : 'Sin tareas asignadas';
        document.getElementById('detalle-tareas').textContent = tareas;
        
        document.getElementById('detalle-actualizacion').textContent = 'Hace pocos minutos'; // Placeholder
        
        // Almacenar ID para las acciones
        window.currentProjectDetailId = proyecto.id;
        
        // Mostrar modal con verificación adicional
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('✅ Modal de detalle abierto correctamente');
        
    } catch (error) {
        console.error('❌ Error al abrir modal de detalle:', error);
        console.error('Stack trace:', error.stack);
        mostrarAlerta('Error al mostrar el detalle del proyecto: ' + error.message, 'error');
    }
}
 
/**
 * Obtener clase CSS para el badge de estado
 */
function getEstadoBadgeClass(estado) {
    switch (estado) {
        case 'propuesta': return 'bg-primary text-white';
        case 'en_desarrollo': return 'bg-desarrollo';
        case 'en_revision': return 'bg-warning';
        case 'finalizado': return 'bg-success';
        case 'pausado': return 'bg-secondary';
        case 'cancelado': return 'bg-danger';
        default: return 'bg-secondary';
    }
}


/**
 * Editar proyecto desde el modal de detalle
 */
function editarProyectoDesdeDetalle() {
    if (window.currentProjectDetailId) {
        // Cerrar modal de detalle
        const modalDetalle = bootstrap.Modal.getInstance(document.getElementById('modalDetalleProyecto'));
        if (modalDetalle) {
            modalDetalle.hide();
        }
        
        // Abrir modal de edición
        setTimeout(() => {
            editarProyecto(window.currentProjectDetailId);
        }, 300);
    }
}

/**
 * Duplicar proyecto desde el modal de detalle
 */
function duplicarProyectoDesdeDetalle() {
    if (window.currentProjectDetailId) {
        // Cerrar modal de detalle
        const modalDetalle = bootstrap.Modal.getInstance(document.getElementById('modalDetalleProyecto'));
        if (modalDetalle) {
            modalDetalle.hide();
        }
        
        // Abrir modal de duplicación
        setTimeout(() => {
            duplicarProyecto(window.currentProjectDetailId);
        }, 300);
    }
}

/**
 * Eliminar proyecto desde el modal de detalle
 */
function eliminarProyectoDesdeDetalle() {
    if (window.currentProjectDetailId) {
        // Cerrar modal de detalle
        const modalDetalle = bootstrap.Modal.getInstance(document.getElementById('modalDetalleProyecto'));
        if (modalDetalle) {
            modalDetalle.hide();
        }
        
        // Ejecutar eliminación
        setTimeout(() => {
            eliminarProyecto(window.currentProjectDetailId);
        }, 300);
    }
}

/**
 * Duplicar proyecto existente
 */
function duplicarProyecto(id) {
    console.log('🔧 Duplicando proyecto con ID:', id);
    
    const proyecto = proyectos.find(p => p.id == id);
    if (!proyecto) {
        console.error('❌ Proyecto no encontrado con ID:', id);
        mostrarAlerta('Proyecto no encontrado', 'error');
        return;
    }
    
    showConfirm(`¿Está seguro de duplicar el proyecto "${proyecto.pr_titulo}"?`, function() {
        try {
            // Llenar formulario con datos del proyecto original
            document.getElementById('modalProyectoTitle').innerHTML = 
                '<i class="fas fa-copy me-2"></i>Duplicar Proyecto';
            document.getElementById('proyecto_id').value = '';
            document.getElementById('accion').value = 'crear';
            document.getElementById('pr_titulo').value = proyecto.pr_titulo + ' (Copia)';
            document.getElementById('pr_descripcion').value = proyecto.pr_descripcion || '';
            document.getElementById('pr_estado').value = 'propuesta'; // Los duplicados inician como propuesta
            document.getElementById('pr_fecha_inicio').value = ''; // Fechas vacías para el nuevo proyecto
            document.getElementById('pr_fecha_estimada').value = '';
            document.getElementById('pr_presupuesto').value = proyecto.pr_presupuesto || '';
            document.getElementById('pr_prioridad').value = proyecto.pr_prioridad;
            document.getElementById('pr_progreso').value = 0; // Progreso inicia en 0
            document.getElementById('cliente_id').value = proyecto.cliente_id || '';
            
            actualizarProgreso(0);
            
            const modal = new bootstrap.Modal(document.getElementById('modalProyecto'));
            modal.show();
            console.log('✅ Modal de duplicación abierto correctamente');
        } catch (error) {
            console.error('❌ Error al abrir modal de duplicación:', error);
            mostrarAlerta('Error al abrir el modal de duplicación: ' + error.message, 'error');
        }
    });
}

// ==================== FUNCIÓN DE BÚSQUEDA ====================

/**
 * Buscar proyectos en tiempo real
 */
function buscarProyectos() {
    const termino = document.getElementById('searchInput').value.toLowerCase();
    
    if (termino.trim() === '') {
        // Si no hay término de búsqueda, mostrar todos los proyectos
        renderizarProyectos();
        return;
    }
    
    // Filtrar proyectos
    const proyectosFiltrados = proyectos.filter(proyecto => {
        return proyecto.pr_titulo.toLowerCase().includes(termino) ||
               (proyecto.pr_descripcion && proyecto.pr_descripcion.toLowerCase().includes(termino)) ||
               (proyecto.cliente_empresa && proyecto.cliente_empresa.toLowerCase().includes(termino));
    });
    
    // Renderizar proyectos filtrados
    renderizarProyectosFiltrados(proyectosFiltrados);
}

/**
 * Renderizar proyectos filtrados por búsqueda
 */
function renderizarProyectosFiltrados(proyectosFiltrados) {
    // Limpiar todas las columnas
    const estados = ['propuesta', 'en_desarrollo', 'en_revision', 'finalizado'];
    estados.forEach(estado => {
        const container = document.getElementById(estado + '-container');
        if (container) {
            const addBtn = container.querySelector('.add-project-btn');
            container.innerHTML = '';
            if (addBtn && estado !== 'finalizado') {
                container.appendChild(addBtn);
            }
        }
    });
    
    // Agrupar proyectos filtrados por estado
    const proyectosPorEstado = {};
    estados.forEach(estado => {
        proyectosPorEstado[estado] = proyectosFiltrados.filter(p => p.pr_estado === estado);
    });
    
    // Renderizar proyectos filtrados
    Object.keys(proyectosPorEstado).forEach(estado => {
        const container = document.getElementById(estado + '-container');
        if (container) {
            proyectosPorEstado[estado].forEach(proyecto => {
                const card = crearTarjetaProyecto(proyecto);
                if (estado === 'finalizado') {
                    container.appendChild(card);
                } else {
                    container.insertBefore(card, container.lastElementChild);
                }
            });
        }
    });
    
    // Actualizar contadores
    estados.forEach(estado => {
        const count = proyectosPorEstado[estado].length;
        const column = document.querySelector(`[data-estado="${estado}"] .project-count`);
        if (column) {
            column.textContent = count;
        }
    });
}

// ==================== FUNCIONES DE UTILIDAD ====================

/**
 * Mostrar alertas al usuario
 */
function mostrarAlerta(mensaje, tipo) {
    // Crear elemento de alerta
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alerta.style.cssText = 'top: 100px; right: 20px; z-index: 9999; min-width: 300px;';
    
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alerta);
    
    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (alerta.parentNode) {
            alerta.remove();
        }
    }, 5000);
}

/**
 * Mostrar confirmación personalizada para eliminación con botón cancelar estilizado
 */
function mostrarConfirmacionEliminar(mensaje, callback) {
    // Crear modal de confirmación dinámicamente
    const modalHtml = `
        <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--gradiente-violeta); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 48px;"></i>
                            <p class="fs-5 mb-3">${mensaje}</p>
                            <p class="text-muted">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel-delete" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmarEliminarBtn">
                            <i class="fas fa-trash me-1"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal previo si existe
    const modalPrevio = document.getElementById('modalConfirmarEliminar');
    if (modalPrevio) {
        modalPrevio.remove();
    }
    
    // Agregar modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Configurar evento del botón confirmar
    document.getElementById('confirmarEliminarBtn').addEventListener('click', function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar'));
        modal.hide();
        
        // Ejecutar callback después de cerrar modal
        setTimeout(() => {
            callback();
            // Remover modal del DOM
            const modalElement = document.getElementById('modalConfirmarEliminar');
            if (modalElement) {
                modalElement.remove();
            }
        }, 300);
    });
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    modal.show();
    
    // Remover modal del DOM al cerrarse
    document.getElementById('modalConfirmarEliminar').addEventListener('hidden.bs.modal', function() {
        setTimeout(() => {
            const modalElement = document.getElementById('modalConfirmarEliminar');
            if (modalElement) {
                modalElement.remove();
            }
        }, 100);
    });
}

/**
 * Actualizar indicador de progreso en el modal
 */
function actualizarProgreso(valor) {
    const elemento = document.getElementById('progreso-actual');
    if (elemento) {
        elemento.textContent = valor + '%';
    }
}

// ==================== EVENT LISTENERS ====================

// Búsqueda en tiempo real
$(document).ready(function() {
    // Usar event delegation para evitar conflictos
    $(document).on('input', '#searchInput', function() {
        // Usar debounce para optimizar
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(buscarProyectos, 300);
    });
    
    // Limpiar búsqueda con Escape
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                buscarProyectos();
            }
        }
    });
});

// ==================== FUNCIONES ADICIONALES ====================

/**
 * Refrescar datos cada 30 segundos
 */
setInterval(function() {
    // Solo refrescar si no hay modales abiertos y si estamos en la página de proyectos
    if (!document.querySelector('.modal.show') && document.querySelector('.trello-board')) {
        cargarProyectos();
    }
}, 30000);

/**
 * Función para exportar proyectos a CSV
 */
function exportarProyectos() {
    let csv = 'Título,Cliente,Estado,Prioridad,Progreso,Fecha Inicio,Fecha Estimada,Presupuesto\n';
    
    proyectos.forEach(proyecto => {
        csv += `"${proyecto.pr_titulo}","${proyecto.cliente_empresa || ''}","${proyecto.pr_estado}","${proyecto.pr_prioridad}","${proyecto.pr_progreso}%","${proyecto.pr_fecha_inicio || ''}","${proyecto.pr_fecha_estimada || ''}","${proyecto.pr_presupuesto || ''}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `proyectos_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// ==================== FUNCIONES DE ASIGNACIÓN DE PROYECTOS ====================

/**
 * Abrir modal para asignar usuario
 */
function abrirModalAsignarUsuario() {
    const proyectoId = document.getElementById('proyecto_id').value;
    
    if (!proyectoId) {
        mostrarAlerta('Debe guardar el proyecto primero antes de asignar usuarios', 'error');
        return;
    }
    
    // Cargar usuarios disponibles
    cargarUsuariosDisponibles(proyectoId);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalAsignarUsuario'));
    modal.show();
}

/**
 * Cargar usuarios disponibles para asignar
 */
function cargarUsuariosDisponibles(proyectoId) {
    console.log('🔍 Cargando usuarios disponibles para proyecto:', proyectoId);
    
    // Determinar la ruta correcta según el contexto
    const baseUrl = getBaseUrl();
    
    $.ajax({
        url: baseUrl + 'asignaciones_proyectos.php',
        method: 'GET',
        data: {
            accion: 'listar_usuarios_disponibles',
            proyecto_id: proyectoId
        },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                const select = document.getElementById('asignar_usuario_id');
                select.innerHTML = '<option value="">Seleccionar usuario...</option>';
                
                response.usuarios.forEach(usuario => {
                    const option = document.createElement('option');
                    option.value = usuario.id;
                    option.textContent = `${usuario.nombre_completo} (${usuario.us_email})`;
                    
                    // Marcar si ya está asignado
                    if (usuario.estado_asignacion === 'asignado') {
                        option.textContent += ' - YA ASIGNADO';
                        option.disabled = true;
                        option.style.color = '#6c757d';
                    }
                    
                    select.appendChild(option);
                });
                
                console.log('✅ Usuarios cargados:', response.usuarios.length);
            } else {
                console.error('❌ Error al cargar usuarios:', response.mensaje);
                mostrarAlerta('Error al cargar usuarios disponibles', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error en petición de usuarios:', error);
            mostrarAlerta('Error de conexión al cargar usuarios', 'error');
        }
    });
}

/**
 * Confirmar asignación de usuario
 */
function confirmarAsignacion() {
    const proyectoId = document.getElementById('proyecto_id').value;
    const usuarioId = document.getElementById('asignar_usuario_id').value;
    const rolProyecto = document.getElementById('asignar_rol_proyecto').value;
    const horasAsignadas = document.getElementById('asignar_horas_asignadas').value;
    const fechaInicio = document.getElementById('asignar_fecha_inicio').value;
    const notas = document.getElementById('asignar_notas').value;
    
    // Validaciones
    if (!usuarioId) {
        mostrarAlerta('Debe seleccionar un usuario', 'error');
        return;
    }
    
    if (!rolProyecto) {
        mostrarAlerta('Debe seleccionar un rol para el proyecto', 'error');
        return;
    }
    
    console.log('🔧 Asignando usuario al proyecto...');
    console.log('Datos a enviar:', {
        proyecto_id: proyectoId,
        usuario_id: usuarioId,
        rol_proyecto: rolProyecto,
        horas_asignadas: horasAsignadas,
        fecha_inicio: fechaInicio,
        notas: notas
    });
    
    // Determinar la ruta correcta según el contexto
    const baseUrl = getBaseUrl();
    
    console.log('Pathname:', window.location.pathname);
    console.log('Base URL calculada:', baseUrl);
    console.log('URL de destino:', baseUrl + 'asignaciones_proyectos.php');
    
    $.ajax({
        url: baseUrl + 'asignaciones_proyectos.php',
        method: 'POST',
        data: {
            accion: 'asignar_usuario',
            proyecto_id: proyectoId,
            usuario_id: usuarioId,
            rol_proyecto: rolProyecto,
            horas_asignadas: horasAsignadas,
            fecha_inicio: fechaInicio,
            notas: notas
        },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                mostrarAlerta('Usuario asignado exitosamente al proyecto', 'success');
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAsignarUsuario'));
                modal.hide();
                
                // Limpiar formulario
                document.getElementById('formAsignarUsuario').reset();
                
                // Recargar asignaciones
                cargarAsignacionesProyecto(proyectoId);
                
            } else {
                console.error('❌ Error en asignación:', response.mensaje);
                mostrarAlerta(response.mensaje || 'Error al asignar usuario', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error en petición de asignación:', error);
            mostrarAlerta('Error de conexión al asignar usuario', 'error');
        }
    });
}

/**
 * Cargar asignaciones de un proyecto
 */
function cargarAsignacionesProyecto(proyectoId) {
    if (!proyectoId) return;
    
    console.log('🔍 Cargando asignaciones del proyecto:', proyectoId);
    
    // Determinar la ruta correcta según el contexto
    const baseUrl = getBaseUrl();
    
    $.ajax({
        url: baseUrl + 'asignaciones_proyectos.php',
        method: 'GET',
        data: {
            accion: 'obtener_asignaciones',
            proyecto_id: proyectoId
        },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                mostrarAsignacionesEnModal(response.asignaciones, response.estadisticas);
                console.log('✅ Asignaciones cargadas:', response.asignaciones.length);
            } else {
                console.error('❌ Error al cargar asignaciones:', response.mensaje);
                // Mostrar mensaje informativo en el modal
                const container = document.getElementById('lista-asignaciones');
                if (container) {
                    container.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                            <p class="mb-0">Error al cargar asignaciones</p>
                            <small>${response.mensaje}</small>
                        </div>`;
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error en petición de asignaciones:', error);
            console.error('Status:', status, 'XHR:', xhr);
            
            // Mostrar mensaje de error en el modal
            const container = document.getElementById('lista-asignaciones');
            if (container) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-wifi fa-2x mb-2 text-danger"></i>
                        <p class="mb-0">Error de conexión</p>
                        <small>No se pudieron cargar las asignaciones</small>
                    </div>`;
            }
        }
    });
}

/**
 * Mostrar asignaciones en el modal
 */
function mostrarAsignacionesEnModal(asignaciones, estadisticas) {
    const container = document.getElementById('lista-asignaciones');
    const btnAsignar = document.getElementById('btn-asignar-usuario');
    
    // Habilitar botón de asignar
    btnAsignar.disabled = false;
    
    // Actualizar contador
    document.getElementById('asignados-count').textContent = asignaciones.length;
    
    if (asignaciones.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-user-slash fa-2x mb-2"></i>
                <p class="mb-0">No hay usuarios asignados</p>
                <small>Haz clic en "Asignar Usuario" para comenzar</small>
            </div>
        `;
    } else {
        container.innerHTML = '';
        
        asignaciones.forEach(asignacion => {
            const item = document.createElement('div');
            item.className = 'list-group-item border-0 shadow-sm mb-2';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="background: var(--gradiente-violeta); color: white; width: 40px; height: 40px;">
                            ${asignacion.usuario_nombre.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h6 class="mb-1">${asignacion.nombre_completo}</h6>
                            <small class="text-muted">
                                <span class="badge bg-primary">${getRolDisplayName(asignacion.rol_proyecto)}</span>
                                ${asignacion.horas_asignadas ? `• ${asignacion.horas_asignadas}h asignadas` : ''}
                                ${asignacion.fecha_inicio ? `• Inicia: ${new Date(asignacion.fecha_inicio).toLocaleDateString('es-AR')}` : ''}
                            </small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="editarAsignacion(${asignacion.asignacion_id}); event.preventDefault();">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="eliminarAsignacion(${asignacion.asignacion_id}); event.preventDefault();">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </a></li>
                        </ul>
                    </div>
                </div>
                ${asignacion.notas ? `<div class="mt-2"><small class="text-muted"><i class="fas fa-sticky-note me-1"></i>${asignacion.notas}</small></div>` : ''}
            `;
            
            container.appendChild(item);
        });
    }
    
    // Actualizar estadísticas
    actualizarEstadisticasAsignaciones(asignaciones, estadisticas);
}

/**
 * Actualizar estadísticas de asignaciones
 */
function actualizarEstadisticasAsignaciones(asignaciones, estadisticas) {
    document.getElementById('stat-total-usuarios').textContent = estadisticas.total_asignados || 0;
    document.getElementById('stat-horas-asignadas').textContent = estadisticas.total_horas_asignadas || 0;
    
    // Contar roles
    const roles = {
        lider: 0,
        desarrollador: 0,
        consultor: 0,
        otros: 0
    };
    
    asignaciones.forEach(asignacion => {
        const rol = asignacion.rol_proyecto;
        if (roles.hasOwnProperty(rol)) {
            roles[rol]++;
        } else {
            roles.otros++;
        }
    });
    
    const total = asignaciones.length || 1;
    
    // Actualizar contadores
    document.getElementById('count-lideres').textContent = roles.lider;
    document.getElementById('count-desarrolladores').textContent = roles.desarrollador;
    document.getElementById('count-consultores').textContent = roles.consultor;
    
    // Actualizar barras de progreso
    document.getElementById('progress-lideres').style.width = `${(roles.lider / total) * 100}%`;
    document.getElementById('progress-desarrolladores').style.width = `${(roles.desarrollador / total) * 100}%`;
    document.getElementById('progress-consultores').style.width = `${(roles.consultor / total) * 100}%`;
    document.getElementById('progress-otros').style.width = `${(roles.otros / total) * 100}%`;
}

/**
 * Obtener nombre para mostrar del rol
 */
function getRolDisplayName(rol) {
    const roles = {
        'lider': 'Líder',
        'desarrollador': 'Desarrollador',
        'consultor': 'Consultor',
        'revisor': 'Revisor',
        'colaborador': 'Colaborador'
    };
    return roles[rol] || rol;
}

/**
 * Eliminar asignación
 */
function eliminarAsignacion(asignacionId) {
    mostrarConfirmacionEliminar('¿Está seguro de eliminar esta asignación?', function() {
        // Determinar la ruta correcta según el contexto
        const baseUrl = getBaseUrl();
        
        $.ajax({
            url: baseUrl + 'asignaciones_proyectos.php',
            method: 'POST',
            data: {
                accion: 'eliminar_asignacion',
                asignacion_id: asignacionId
            },
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    mostrarAlerta('Asignación eliminada exitosamente', 'success');
                    const proyectoId = document.getElementById('proyecto_id').value;
                    cargarAsignacionesProyecto(proyectoId);
                } else {
                    mostrarAlerta(response.mensaje || 'Error al eliminar asignación', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al eliminar asignación:', error);
                mostrarAlerta('Error de conexión al eliminar asignación', 'error');
            }
        });
    });
}

/**
 * Cargar avatares de usuarios asignados para mostrar en las tarjetas
 */
function cargarAvatares(proyectoId) {
    $.ajax({
        url: 'php/asignaciones_proyectos.php',
        method: 'GET',
        data: {
            accion: 'obtener_asignaciones',
            proyecto_id: proyectoId
        },
        dataType: 'json',
        success: function(response) {
            const avatarContainer = document.getElementById(`avatars-${proyectoId}`);
            if (!avatarContainer) return;
            
            if (response.exito && response.asignaciones.length > 0) {
                const maxAvatars = 4; // Máximo de avatares a mostrar
                const asignaciones = response.asignaciones.slice(0, maxAvatars);
                const restantes = response.asignaciones.length - maxAvatars;
                
                let avatarsHtml = '';
                
                asignaciones.forEach((asignacion, index) => {
                    const inicial = asignacion.usuario_nombre.charAt(0).toUpperCase();
                    const rolClass = getRolColorClass(asignacion.rol_proyecto);
                    
                    avatarsHtml += `
                        <div class="avatar-mini ${rolClass}" 
                             title="${asignacion.nombre_completo} - ${getRolDisplayName(asignacion.rol_proyecto)}"
                             style="
                                width: 24px; 
                                height: 24px; 
                                border-radius: 50%; 
                                color: white; 
                                font-size: 10px; 
                                font-weight: bold;
                                display: inline-flex; 
                                align-items: center; 
                                justify-content: center;
                                margin-left: ${index > 0 ? '-4px' : '0'};
                                border: 2px solid white;
                                z-index: ${10 - index};
                                position: relative;
                             ">
                            ${inicial}
                        </div>
                    `;
                });
                
                // Mostrar contador si hay más usuarios
                if (restantes > 0) {
                    avatarsHtml += `
                        <div class="avatar-mini bg-secondary" 
                             title="+${restantes} más usuarios asignados"
                             style="
                                width: 24px; 
                                height: 24px; 
                                border-radius: 50%; 
                                background: #6c757d;
                                color: white; 
                                font-size: 9px; 
                                font-weight: bold;
                                display: inline-flex; 
                                align-items: center; 
                                justify-content: center;
                                margin-left: -4px;
                                border: 2px solid white;
                                position: relative;
                             ">
                            +${restantes}
                        </div>
                    `;
                }
                
                avatarContainer.innerHTML = avatarsHtml;
                
            } else {
                avatarContainer.innerHTML = '<small class="text-muted">Sin asignar</small>';
            }
        },
        error: function() {
            const avatarContainer = document.getElementById(`avatars-${proyectoId}`);
            if (avatarContainer) {
                avatarContainer.innerHTML = '<small class="text-muted">Error</small>';
            }
        }
    });
}

/**
 * Obtener clase de color según el rol
 */
function getRolColorClass(rol) {
    const colores = {
        'lider': 'bg-warning',
        'desarrollador': 'bg-primary', 
        'consultor': 'bg-info',
        'revisor': 'bg-success',
        'colaborador': 'bg-secondary'
    };
    return colores[rol] || 'bg-secondary';
}

// Hacer las funciones disponibles globalmente
window.abrirModalNuevoProyecto = abrirModalNuevoProyecto;
window.editarProyecto = editarProyecto;
window.duplicarProyecto = duplicarProyecto;
window.guardarProyecto = guardarProyecto;
window.eliminarProyecto = eliminarProyecto;
window.verDetalleProyecto = verDetalleProyecto;
window.editarProyectoDesdeDetalle = editarProyectoDesdeDetalle;
window.duplicarProyectoDesdeDetalle = duplicarProyectoDesdeDetalle;
window.eliminarProyectoDesdeDetalle = eliminarProyectoDesdeDetalle;
window.buscarProyectos = buscarProyectos;
window.actualizarProgreso = actualizarProgreso;
window.cargarProyectosDesdeDashboard = cargarProyectosDesdeDashboard;
window.mostrarConfirmacionEliminar = mostrarConfirmacionEliminar;
window.abrirModalAsignarUsuario = abrirModalAsignarUsuario;
window.confirmarAsignacion = confirmarAsignacion;
window.cargarAsignacionesProyecto = cargarAsignacionesProyecto;
window.eliminarAsignacion = eliminarAsignacion;

/**
 * Ver detalle de proyecto desde contexto de asignaciones
 * Esta función carga los datos del proyecto y abre el modal desde cualquier contexto
 */
function verDetalleProyectoDesdeAsignaciones(proyectoId) {
    console.log('🔧 Abriendo detalle de proyecto desde asignaciones:', proyectoId);
    
    // Verificar si el modal existe en el DOM actual
    let modalElement = document.getElementById('modalDetalleProyecto');
    console.log('🔍 Modal existente encontrado:', !!modalElement);
    
    if (!modalElement) {
        console.log('⚠️ Modal no encontrado, creando modal dinámico...');
        try {
            crearModalDetalleProyectoDinamico();
            modalElement = document.getElementById('modalDetalleProyecto');
            console.log('✅ Modal dinámico creado:', !!modalElement);
        } catch (error) {
            console.error('❌ Error al crear modal dinámico:', error);
            alert('Error al crear modal: ' + error.message);
            return;
        }
    }
    
    // Determinar la ruta correcta según el contexto
    const baseUrl = getBaseUrl();
    const fullUrl = baseUrl + 'abm_proyectos.php';
    console.log('🌐 URL para AJAX:', fullUrl);
    console.log('📋 Datos enviados:', { accion: 'obtener', id: proyectoId });
    
    // Cargar datos específicos del proyecto
    $.ajax({
        url: fullUrl,
        method: 'GET',
        data: { 
            accion: 'obtener', 
            id: proyectoId 
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('📡 Enviando petición AJAX...');
        },
        success: function(response) {
            console.log('📥 Respuesta recibida:', response);
            
            if (response.exito && response.proyecto) {
                const proyecto = response.proyecto;
                console.log('✅ Proyecto cargado exitosamente:', proyecto.pr_titulo);
                console.log('📊 Datos del proyecto:', proyecto);
                
                // Agregar temporalmente a la lista global si no existe
                const proyectoExistente = proyectos.find(p => p.id == proyectoId);
                if (!proyectoExistente) {
                    console.log('➕ Agregando proyecto a lista global...');
                    proyectos.push(proyecto);
                } else {
                    console.log('♻️ Actualizando proyecto existente en lista global...');
                    const index = proyectos.findIndex(p => p.id == proyectoId);
                    proyectos[index] = proyecto;
                }
                
                // Abrir modal directamente sin llamar a verDetalleProyecto (evitar loop)
                console.log('🎭 Abriendo modal directamente...');
                try {
                    abrirModalDetalleDirecto(proyecto);
                    console.log('✅ Modal abierto correctamente');
                } catch (error) {
                    console.error('❌ Error al abrir modal:', error);
                    alert('Error al abrir modal: ' + error.message);
                }
                
            } else {
                console.error('❌ Error en respuesta del servidor:', response);
                mostrarAlerta('Error al cargar los datos del proyecto: ' + (response.mensaje || 'Proyecto no encontrado'), 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX completo:', {xhr: xhr, status: status, error: error});
            console.error('❌ Texto de respuesta:', xhr.responseText);
            console.error('❌ Status HTTP:', xhr.status);
            mostrarAlerta('Error de conexión al cargar el proyecto: ' + error, 'error');
        }
    });
}

/**
 * Crear modal de detalle dinámicamente si no existe
 */
function crearModalDetalleProyectoDinamico() {
    const modalHtml = `
    <!-- Modal Ver Detalle del Proyecto (Dinámico) -->
    <div class="modal fade" id="modalDetalleProyecto" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                    <h5 class="modal-title" id="modalDetalleTitle">
                        <img src="../icons/16x/ver-violeta16.png" alt="ver-violeta" style="margin-right: 0.5rem;">Detalle del Proyecto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Información principal -->
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-info-circle me-2"></i>Información General
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-heading me-1"></i>Título
                                            </label>
                                            <p class="fs-5 fw-bold text-dark mb-0" id="detalle-titulo">-</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-align-left me-1"></i>Descripción
                                            </label>
                                            <p class="text-muted" id="detalle-descripcion">Sin descripción</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-building me-1"></i>Cliente
                                            </label>
                                            <p class="mb-0" id="detalle-cliente">Sin asignar</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-tasks me-1"></i>Estado
                                            </label>
                                            <span class="badge fs-6" id="detalle-estado">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progreso y fechas -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-chart-line me-2"></i>Progreso y Fechas
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-percentage me-1"></i>Progreso del Proyecto
                                            </label>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);" id="detalle-progress-bar"></div>
                                            </div>
                                            <div class="text-center mt-2">
                                                <span class="fs-5 fw-bold text-primary" id="detalle-progreso">0%</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>Fecha de Inicio
                                            </label>
                                            <p class="mb-0" id="detalle-fecha-inicio">Sin definir</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-calendar-check me-1"></i>Fecha Estimada
                                            </label>
                                            <p class="mb-0" id="detalle-fecha-estimada">Sin definir</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-clock me-1"></i>Estado del Plazo
                                            </label>
                                            <p class="mb-0" id="detalle-estado-plazo">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-plus-circle me-1"></i>Fecha de Creación
                                            </label>
                                            <p class="mb-0" id="detalle-fecha-creacion">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información lateral -->
                        <div class="col-md-4">
                            <!-- Prioridad y presupuesto -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-flag me-2"></i>Prioridad y Presupuesto
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-muted d-block">Prioridad</label>
                                        <span class="priority-badge fs-6" id="detalle-prioridad">-</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted d-block">
                                            <i class="fas fa-dollar-sign me-1"></i>Presupuesto
                                        </label>
                                        <p class="fs-4 fw-bold text-success mb-0" id="detalle-presupuesto">Sin definir</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información técnica -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-cog me-2"></i>Información Técnica
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">ID del Proyecto</label>
                                        <p class="mb-0 font-monospace" id="detalle-id">-</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Total de Tareas</label>
                                        <p class="mb-0" id="detalle-tareas">Sin tareas</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Última Actualización</label>
                                        <p class="mb-0" id="detalle-actualizacion">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .priority-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .priority-alta { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .priority-media { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .priority-baja { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .priority-critica { background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; }
    </style>
    `;
    
    // Agregar el modal al body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    console.log('✅ Modal dinámico creado');
}

/**
 * Abrir modal de detalle directamente con los datos del proyecto (sin recursión)
 */
function abrirModalDetalleDirecto(proyecto) {
    console.log('🔧 Mostrando detalle directo del proyecto:', proyecto.pr_titulo);
    
    try {
        // Verificar que el modal existe
        const modalElement = document.getElementById('modalDetalleProyecto');
        if (!modalElement) {
            throw new Error('Modal modalDetalleProyecto no encontrado en el DOM');
        }
        
        // Verificar elementos críticos del modal
        const elementosCriticos = [
            'modalDetalleTitle', 'detalle-titulo', 'detalle-descripcion', 
            'detalle-cliente', 'detalle-estado', 'detalle-progreso', 
            'detalle-progress-bar', 'detalle-fecha-inicio', 'detalle-fecha-estimada',
            'detalle-estado-plazo', 'detalle-prioridad', 'detalle-presupuesto',
            'detalle-id', 'detalle-tareas', 'detalle-actualizacion', 'detalle-fecha-creacion'
        ];
        
        const elementosFaltantes = [];
        elementosCriticos.forEach(elementoId => {
            if (!document.getElementById(elementoId)) {
                elementosFaltantes.push(elementoId);
            }
        });
        
        if (elementosFaltantes.length > 0) {
            console.error('❌ Elementos faltantes en modal:', elementosFaltantes);
            throw new Error('Modal de detalle incompleto. Elementos faltantes: ' + elementosFaltantes.join(', '));
        }
        
        // Llenar datos del modal
        llenarDatosModal(proyecto);
        
        // Mostrar modal
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('✅ Modal de detalle abierto correctamente');
        
    } catch (error) {
        console.error('❌ Error al abrir modal de detalle:', error);
        throw error;
    }
}

/**
 * Llenar los datos del modal con la información del proyecto
 */
function llenarDatosModal(proyecto) {
    console.log('📝 Llenando datos del modal...');
    
    // Actualizar título del modal
    document.getElementById('modalDetalleTitle').innerHTML = 
        `<i class="fas fa-eye me-2"></i>Detalle del Proyecto: ${proyecto.pr_titulo}`;
    
    // Llenar información general
    document.getElementById('detalle-titulo').textContent = proyecto.pr_titulo;
    document.getElementById('detalle-descripcion').textContent = proyecto.pr_descripcion || 'Sin descripción';
    
    // Cliente (verificar diferentes formatos posibles)
    let clienteNombre = 'Sin asignar';
    if (proyecto.cliente_empresa || proyecto.cl_empresa) {
        clienteNombre = proyecto.cliente_empresa || proyecto.cl_empresa;
    } else if (proyecto.cl_nombre && proyecto.cl_apellido) {
        clienteNombre = `${proyecto.cl_nombre} ${proyecto.cl_apellido}`;
    }
    document.getElementById('detalle-cliente').textContent = clienteNombre;
    
    // Estado con badge apropiado
    const estadoBadge = document.getElementById('detalle-estado');
    const estadoTexto = proyecto.pr_estado.replace('_', ' ').toUpperCase();
    estadoBadge.textContent = estadoTexto;
    estadoBadge.className = `badge fs-6 ${getEstadoBadgeClass(proyecto.pr_estado)}`;
    
    // Progreso
    const progreso = parseInt(proyecto.pr_progreso) || 0;
    document.getElementById('detalle-progreso').textContent = `${progreso}%`;
    document.getElementById('detalle-progress-bar').style.width = `${progreso}%`;
    
    // Fechas
    const fechaInicio = proyecto.pr_fecha_inicio ? 
        new Date(proyecto.pr_fecha_inicio).toLocaleDateString('es-AR') : 'Sin definir';
    const fechaEstimada = proyecto.pr_fecha_estimada ? 
        new Date(proyecto.pr_fecha_estimada).toLocaleDateString('es-AR') : 'Sin definir';
    const fechaCreacion = proyecto.pr_fecha_creacion ? 
        new Date(proyecto.pr_fecha_creacion).toLocaleDateString('es-AR') : 'N/A';
        
    document.getElementById('detalle-fecha-inicio').textContent = fechaInicio;
    document.getElementById('detalle-fecha-estimada').textContent = fechaEstimada;
    document.getElementById('detalle-fecha-creacion').textContent = fechaCreacion;
    
    // Estado del plazo
    let estadoPlazo = '-';
    let claseEstadoPlazo = '';
    if (proyecto.pr_fecha_estimada) {
        const hoy = new Date();
        const fechaEst = new Date(proyecto.pr_fecha_estimada);
        const diffTime = fechaEst - hoy;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays > 7) {
            estadoPlazo = `A tiempo (${diffDays} días restantes)`;
            claseEstadoPlazo = 'text-success';
        } else if (diffDays > 0) {
            estadoPlazo = `Próximo a vencer (${diffDays} días)`;
            claseEstadoPlazo = 'text-warning fw-bold';
        } else if (diffDays === 0) {
            estadoPlazo = 'Vence hoy';
            claseEstadoPlazo = 'text-warning fw-bold';
        } else {
            estadoPlazo = `Vencido (${Math.abs(diffDays)} días)`;
            claseEstadoPlazo = 'text-danger fw-bold';
        }
    }
    const estadoPlazoElement = document.getElementById('detalle-estado-plazo');
    estadoPlazoElement.textContent = estadoPlazo;
    estadoPlazoElement.className = `mb-0 ${claseEstadoPlazo}`;
    
    // Prioridad
    const prioridadBadge = document.getElementById('detalle-prioridad');
    const prioridadTexto = proyecto.pr_prioridad.charAt(0).toUpperCase() + proyecto.pr_prioridad.slice(1);
    prioridadBadge.textContent = prioridadTexto;
    prioridadBadge.className = `priority-badge fs-6 priority-${proyecto.pr_prioridad}`;
    
    // Presupuesto
    const presupuesto = proyecto.pr_presupuesto ? 
        `$${parseFloat(proyecto.pr_presupuesto).toLocaleString('es-AR')}` : 'Sin definir';
    document.getElementById('detalle-presupuesto').textContent = presupuesto;
    
    // Información técnica
    document.getElementById('detalle-id').textContent = `#${proyecto.id}`;
    
    const tareas = proyecto.total_tareas ? 
        `${proyecto.tareas_completadas || 0}/${proyecto.total_tareas} completadas` : 'Sin tareas asignadas';
    document.getElementById('detalle-tareas').textContent = tareas;
    
    document.getElementById('detalle-actualizacion').textContent = 'Hace pocos minutos';
    
    console.log('✅ Datos del modal llenados correctamente');
}

// Exponer función para uso desde HTML
window.verDetalleProyectoDesdeAsignaciones = verDetalleProyectoDesdeAsignaciones;

console.log('ABM Proyectos cargado correctamente');