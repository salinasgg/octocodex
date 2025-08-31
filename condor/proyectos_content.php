<?php
// ===== CONTENIDO DE PROYECTOS PARA ADMIN =====
/**
 * Versión del contenido de proyectos optimizada para cargar desde admin/dashboard.php
 * Incluye las rutas correctas para CSS y JS
 */
?>

<style>
    /* ===== ESTILOS PARA GESTIÓN DE PROYECTOS ===== */
    /* body {
        font-family: 'Inter', sans-serif;
        background: var(--secondary-color);
        overflow-x: hidden;
    } */

    .text-primary{
        color: #8b5cf6 !important;
    }

    /* Estilo para el botón de guardar en el modal */
    .btn-primary {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        border: none !important;
        color: white !important;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        color: white !important;
    }

    .btn-primary:focus {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.3);
        color: white !important;
    }

    .btn-primary:active {
        background: linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%) !important;
        transform: translateY(0);
        color: white !important;
    }
    /* Header con gradiente violeta como gestión de clientes */
    .header-controls {
        background: var(--gradiente-violeta);
        padding: 30px;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        color: white;
    }

    .header-controls h2 {
        color: white;
        font-weight: 700;
        font-size: 28px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-controls h2 img {
        width: 40px;
        height: 40px;
        filter: brightness(0) invert(1);
    }

    /* Contenedor de búsqueda con estilo mejorado */
    .search-box {
        display: flex;
        gap: 12px;
        flex: 1;
        min-width: 300px;
        max-width: 500px;
    }

    .search-input {
        flex: 1;
        padding: 12px 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .search-input:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
    }

    /* Botón principal con estilo mejorado */
    .btn-primary {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 12px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    /* Board estilo Trello mejorado */
    .trello-board {
        display: flex;
        gap: 25px;
        padding: 20px 0;
        overflow-x: auto;
        min-height: calc(100vh - 250px);
    }

    .trello-column {
        min-width: 350px;
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 250px);
        border: 1px solid var(--border-color);
        position: relative;
        z-index: 1;
    }

    .column-header {
        padding: 25px;
        background: var(--gradiente-violeta);
        color: white;
        border-radius: 16px 16px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .column-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .project-count {
        background: rgba(255, 255, 255, 0.25);
        padding: 6px 12px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    .projects-container {
        padding: 25px;
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .project-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        
    }

    .project-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradiente-violeta);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .project-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-color);
    }

    .project-card:hover::before {
        opacity: 1;
    }

    .project-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0 0 12px 0;
        line-height: 1.4;
    }

    .project-client {
        font-size: 14px;
        color: var(--text-light);
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .project-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
    }

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

    .progress-container {
        margin-bottom: 15px;
    }

    .progress-label {
        font-size: 13px;
        color: var(--text-light);
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
        font-weight: 500;
    }

    .progress {
        height: 8px;
        border-radius: 4px;
        background: var(--border-color);
        overflow: hidden;
    }

    .progress-bar {
        background: var(--gradiente-violeta);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .project-dates {
        font-size: 12px;
        color: var(--text-light);
        display: flex;
        justify-content: space-between;
        font-weight: 500;
    }

    .add-project-btn {
        background: rgba(139, 92, 246, 0.1);
        border: 2px dashed var(--primary-color);
        color: var(--primary-color);
        padding: 25px;
        text-align: center;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .add-project-btn:hover {
        background: rgba(139, 92, 246, 0.2);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        border-color: var(--primary-dark);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .header-controls {
            flex-direction: column;
            align-items: stretch;
            padding: 20px;
        }
        
        .header-controls h2 {
            font-size: 24px;
            justify-content: center;
        }
        
        .search-box {
            min-width: 100%;
            max-width: none;
        }
        
        .trello-board {
            flex-direction: column;
            gap: 20px;
        }
        
        .trello-column {
            min-width: 100%;
            max-height: none;
        }
    }

    /* Drag & Drop mejorado */
    .project-card.dragging {
        opacity: 0.7;
        transform: rotate(3deg) scale(1.02);
        box-shadow: var(--shadow-lg);
    }

    .trello-column.drag-over {
        background: rgba(139, 92, 246, 0.05);
        border: 2px dashed var(--primary-color);
    }

    /* Dropdown mejorado */
    .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        padding: 8px;
        position: absolute;
        z-index: 9999 !important;
        min-width: 180px;
        background: white;
        margin-top: 5px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .dropdown-item {
        border-radius: 8px;
        padding: 10px 15px;
        font-weight: 500;
        transition: all 0.2s ease;
        color: var(--text-dark);
        text-decoration: none;
        display: block;
        white-space: nowrap;
    }

    .dropdown-item:hover {
        background: var(--primary-color);
        color: white;
        transform: translateX(5px);
        text-decoration: none;
    }

    .dropdown-item:focus {
        background: var(--primary-color);
        color: white;
    }

    /* Posicionamiento específico para dropdowns en project-card */
    .project-card .dropdown {
        position: relative;
        z-index: 1000;
    }

    .project-card .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
        margin-top: 5px !important;
        z-index: 9999 !important;
    }

    /* Asegurar que el dropdown aparezca por encima de otros elementos */
    .project-card {
        position: relative;
        z-index: 1;
        overflow: visible;
    }

    .project-card:hover {
        z-index: 1000;
    }

    /* Asegurar que el dropdown siempre esté visible */
    .dropdown.show .dropdown-menu {
        z-index: 9999 !important;
        position: absolute !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .dropdown.show {
        z-index: 9999 !important;
    }

    /* Nuevo estilo para las descripciones de proyecto */
    .project-description {
        margin-bottom: 12px;
        padding: 8px;
        background: rgba(139, 92, 246, 0.05);
        border-radius: 8px;
        border-left: 3px solid var(--primary-color);
    }

    /* Estilos para la información de deadline */
    .project-deadline {
        padding: 6px 10px;
        border-radius: 8px;
        background: rgba(248, 249, 250, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Estilos para información extra del proyecto */
    .project-info-extra {
        border-top: 1px solid var(--border-color);
        padding-top: 10px;
    }

    .project-timestamps {
        opacity: 0.7;
    }

    /* Asegurar que todos los dropdowns estén por encima */
    .projects-container {
        overflow: visible;
        z-index: 1;
    }

    .trello-column {
        overflow: visible;
    }

    /* Badges mejorados */
    .badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
    }

    .bg-success {
        background: linear-gradient(135deg, #10b981, #059669) !important;
    }

    /* Estilo específico para badge de propuesta */
    .bg-primary {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
        color: white !important;
    }

    .bg-desarrollo {
        background: linear-gradient(135deg,rgb(71, 81, 230),rgb(58, 70, 237)) !important;
        color: white !important;
    }


    /* Estilo especial para botón cancelar en confirmación de eliminación */
    .btn-cancel-delete {
        background: linear-gradient(135deg, var(--accent-color), #2980b9) !important;
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cancel-delete:hover {
        background: linear-gradient(135deg, #2980b9, var(--accent-color)) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(41, 128, 185, 0.3);
        color: white;
    }

    /* Estilos adicionales para el modal de detalle */
    .modal-xl .modal-content {
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
    }

    .modal-xl .card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .modal-xl .card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .modal-xl .progress-bar {
        border-radius: 4px;
        transition: width 0.5s ease;
    }
</style>

<div class="main-container">
    <!-- Header con controles -->
    <div class="header-controls">
        <div class="d-flex align-items-center gap-3">
            <h2 class="mb-0">
                <img src="../icons/rama.png" alt="Proyectos" style="width: 40px; height: 40px;">
                Gestión de Proyectos
            </h2>
        </div>
        
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Buscar proyectos..." id="searchInput">
            <button class="btn-primary" onclick="buscarProyectos()">
                <i class="fas fa-search"></i>
            </button>
        </div>
        
        <button class="btn-primary" onclick="abrirModalNuevoProyecto()">
            <i class="fas fa-plus"></i>
            Nuevo Proyecto
        </button>
    </div>

    <!-- Board estilo Trello -->
    <div class="trello-board" id="trelloBoard">
        
        <!-- Columna Propuestas -->
        <div class="trello-column" data-estado="propuesta">
            <div class="column-header" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <h5 class="column-title">
                    <i class="fas fa-lightbulb"></i>Propuestas
                </h5>
                <span class="project-count">0</span>
            </div>
            <div class="projects-container" id="propuesta-container">
                <div class="add-project-btn" onclick="abrirModalNuevoProyecto('propuesta')">
                    <i class="fas fa-plus"></i>Agregar Propuesta
                </div>
            </div>
        </div>

        <!-- Columna En Desarrollo -->
        <div class="trello-column" data-estado="en_desarrollo">
            <div class="column-header" style="background:linear-gradient(to top right,rgb(26, 67, 100),rgb(62, 77, 207),rgb(58, 123, 207));">
                <h5 class="column-title">
                    <i class="fas fa-code"></i>En Desarrollo
                </h5>
                <span class="project-count">0</span>
            </div>
            <div class="projects-container" id="en_desarrollo-container">
                <div class="add-project-btn" onclick="abrirModalNuevoProyecto('en_desarrollo')">
                    <i class="fas fa-plus"></i>Iniciar Desarrollo
                </div>
            </div>
        </div>

        <!-- Columna En Revisión -->
        <div class="trello-column" data-estado="en_revision">
            <div class="column-header" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <h5 class="column-title">
                    <i class="fas fa-eye"></i>En Revisión
                </h5>
                <span class="project-count">0</span>
            </div>
            <div class="projects-container" id="en_revision-container">
                <div class="add-project-btn" onclick="abrirModalNuevoProyecto('en_revision')">
                    <i class="fas fa-plus"></i>Enviar a Revisión
                </div>
            </div>
        </div>

        <!-- Columna Finalizados -->
        <div class="trello-column" data-estado="finalizado">
            <div class="column-header" style="background: linear-gradient(135deg, #059669, #047857);">
                <h5 class="column-title">
                    <i class="fas fa-check-circle"></i>Finalizados
                </h5>
                <span class="project-count">0</span>
            </div>
            <div class="projects-container" id="finalizado-container">
                <!-- Los proyectos finalizados no necesitan botón de agregar -->
            </div>
        </div>

    </div>
</div>

<!-- Modal Nuevo/Editar Proyecto -->
<div class="modal fade" id="modalProyecto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--gradiente-violeta); color: white;">
                <h5 class="modal-title" id="modalProyectoTitle">
                    <i class="fas fa-project-diagram me-2"></i>Nuevo Proyecto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formProyecto" method="POST">
                    <input type="hidden" id="proyecto_id" name="proyecto_id">
                    <input type="hidden" id="accion" name="accion" value="crear">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">
                                <i class="fas fa-heading me-1 text-primary"></i>Título del Proyecto
                            </label>
                            <input type="text" class="form-control" id="pr_titulo" name="pr_titulo" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-flag me-1 text-primary"></i>Prioridad
                            </label>
                            <select class="form-select" id="pr_prioridad" name="pr_prioridad">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="critica">Crítica</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-align-left me-1 text-primary"></i>Descripción
                        </label>
                        <textarea class="form-control" id="pr_descripcion" name="pr_descripcion" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-user-tie me-1 text-primary"></i>Cliente
                            </label>
                            <select class="form-select" id="cliente_id" name="cliente_id">
                                <option value="">Seleccionar cliente...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-tasks me-1 text-primary"></i>Estado
                            </label>
                            <select class="form-select" id="pr_estado" name="pr_estado">
                                <option value="propuesta">Propuesta</option>
                                <option value="en_desarrollo">En Desarrollo</option>
                                <option value="en_revision">En Revisión</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="pausado">Pausado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-1 text-primary"></i>Fecha Inicio
                            </label>
                            <input type="date" class="form-control" id="pr_fecha_inicio" name="pr_fecha_inicio">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-calendar-check me-1 text-primary"></i>Fecha Estimada
                            </label>
                            <input type="date" class="form-control" id="pr_fecha_estimada" name="pr_fecha_estimada">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-dollar-sign me-1 text-primary"></i>Presupuesto
                            </label>
                            <input type="number" step="0.01" class="form-control" id="pr_presupuesto" name="pr_presupuesto" placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-chart-line me-1 text-primary"></i>Progreso (%)
                        </label>
                        <input type="range" class="form-range" id="pr_progreso" name="pr_progreso" min="0" max="100" value="0" oninput="actualizarProgreso(this.value)">
                        <div class="d-flex justify-content-between">
                            <span>0%</span>
                            <span id="progreso-actual" class="fw-bold text-primary">0%</span>
                            <span>100%</span>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarProyecto()">
                    <i class="fas fa-save me-1"></i>Guardar Proyecto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle del Proyecto -->
<div class="modal fade" id="modalDetalleProyecto" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--gradiente-violeta); color: white;">
                <h5 class="modal-title" id="modalDetalleTitle">
                    <i class="fas fa-eye me-2"></i>Detalle del Proyecto
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
                                            <div class="progress-bar" style="background: var(--gradiente-violeta);" id="detalle-progress-bar"></div>
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

                        <!-- Acciones rápidas -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary btn-sm" onclick="editarProyectoDesdeDetalle()">
                                        <i class="fas fa-edit me-2"></i>Editar Proyecto
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="duplicarProyectoDesdeDetalle()">
                                        <i class="fas fa-copy me-2"></i>Duplicar Proyecto
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="eliminarProyectoDesdeDetalle()">
                                        <i class="fas fa-trash me-2"></i>Eliminar Proyecto
                                    </button>
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
                <button type="button" class="btn btn-primary" onclick="editarProyectoDesdeDetalle()">
                    <i class="fas fa-edit me-1"></i>Editar Proyecto
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para actualizar progreso
    function actualizarProgreso(valor) {
        const elemento = document.getElementById('progreso-actual');
        if (elemento) {
            elemento.textContent = valor + '%';
        }
    }
    
    // Inicialización cuando se carga desde el dashboard
    console.log('Contenido de proyectos cargado, esperando inicialización desde ABMProyectos.js');
</script>
