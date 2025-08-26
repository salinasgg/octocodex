// Función para mostrar mensajes personalizados
function showMessage(message, type = 'info', duration = 3000) {
    // Crear el elemento del mensaje
    var messageDiv = document.createElement('div');
    messageDiv.className = 'show-message show-message-' + type;
    messageDiv.innerHTML = '<i class="fas fa-' + getIcon(type) + '"></i> ' + message;
    
    // Agregar al body
    document.body.appendChild(messageDiv);
    
    // Mostrar con animación
    setTimeout(function() {
        messageDiv.classList.add('show');
    }, 100);
    
    // Ocultar después del tiempo especificado
    setTimeout(function() {
        messageDiv.classList.remove('show');
        setTimeout(function() {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 300);
    }, duration);
}

// Función para obtener el icono según el tipo de mensaje
function getIcon(type) {
    switch(type) {
        case 'success':
            return 'check-circle';
        case 'error':
            return 'exclamation-circle';
        case 'warning':
            return 'exclamation-triangle';
        case 'info':
        default:
            return 'info-circle';
    }
}

// Función para confirmar acciones
function showConfirm(message, callback) {
    // Crear el modal de confirmación
    var modalDiv = document.createElement('div');
    modalDiv.className = 'show-confirm-overlay';
    modalDiv.innerHTML = `
        <div class="show-confirm-modal">
            <div class="show-confirm-header">
                <i class="fas fa-question-circle"></i>
                <span>Confirmar Acción</span>
            </div>
            <div class="show-confirm-body">
                ${message}
            </div>
            <div class="show-confirm-footer">
                <button class="btn btn-secondary" id="show-confirm-cancel">Cancelar</button>
                <button class="btn btn-danger" id="show-confirm-ok">Confirmar</button>
            </div>
        </div>
    `;
    
    // Agregar al body
    document.body.appendChild(modalDiv);
    
    // Mostrar con animación
    setTimeout(function() {
        modalDiv.classList.add('show');
    }, 100);
    
    // Event listeners
    document.getElementById('show-confirm-cancel').addEventListener('click', function() {
        hideConfirm();
    });
    
    document.getElementById('show-confirm-ok').addEventListener('click', function() {
        hideConfirm();
        if (typeof callback === 'function') {
            callback(true);
        }
    });
    
    // Función para ocultar
    function hideConfirm() {
        modalDiv.classList.remove('show');
        setTimeout(function() {
            if (modalDiv.parentNode) {
                modalDiv.parentNode.removeChild(modalDiv);
            }
        }, 300);
    }
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideConfirm();
        }
    });
}

// Agregar estilos CSS dinámicamente
if (!document.getElementById('show-message-styles')) {
    var style = document.createElement('style');
    style.id = 'show-message-styles';
    style.textContent = `
        .show-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-width: 400px;
            word-wrap: break-word;
        }
        
        .show-message.show {
            transform: translateX(0);
        }
        
        .show-message i {
            margin-right: 8px;
        }
        
        .show-message-success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .show-message-error {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
        }
        
        .show-message-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        .show-message-info {
            background: linear-gradient(135deg, #17a2b8, #6f42c1);
        }
        
        .show-confirm-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .show-confirm-overlay.show {
            opacity: 1;
        }
        
        .show-confirm-modal {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 90%;
            transform: scale(0.8);
            transition: transform 0.3s ease;
        }
        
        .show-confirm-overlay.show .show-confirm-modal {
            transform: scale(1);
        }
        
        .show-confirm-header {
            padding: 20px 20px 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #333;
        }
        
        .show-confirm-header i {
            margin-right: 10px;
            color: #ff6b35;
        }
        
        .show-confirm-body {
            padding: 20px;
            color: #666;
            line-height: 1.5;
        }
        
        .show-confirm-footer {
            padding: 15px 20px 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .show-confirm-footer .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .show-confirm-footer .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .show-confirm-footer .btn-secondary:hover {
            background: #5a6268;
        }
        
        .show-confirm-footer .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .show-confirm-footer .btn-danger:hover {
            background: #c82333;
        }
    `;
    document.head.appendChild(style);
}
