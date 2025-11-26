// Notificaciones tipo Toast
class Toast {
    static show(mensaje, tipo = 'info', duracion = 3000) {
        const container = document.getElementById('toast-container') || this.crearContainer();
        const toast = document.createElement('div');
        toast.className = `toast toast-${tipo}`;
        toast.innerHTML = `
            <div class="toast-contenido">
                <span class="toast-mensaje">${mensaje}</span>
                <button class="toast-cerrar">&times;</button>
            </div>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => toast.classList.add('toast-visible'), 10);
        
        toast.querySelector('.toast-cerrar').addEventListener('click', () => {
            toast.classList.remove('toast-visible');
            setTimeout(() => toast.remove(), 300);
        });
        
        setTimeout(() => {
            toast.classList.remove('toast-visible');
            setTimeout(() => toast.remove(), 300);
        }, duracion);
    }
    
    static crearContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
        return container;
    }
    
    static exito(msg) { this.show(msg, 'exito', 2500); }
    static error(msg) { this.show(msg, 'error', 3500); }
    static advertencia(msg) { this.show(msg, 'advertencia', 3000); }
    static info(msg) { this.show(msg, 'info', 2500); }
}

window.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('loaded');
    document.querySelectorAll('.entrada, .card, .formulario-container').forEach((el, idx) => {
        el.style.animationDelay = `${idx * 0.05}s`;
        el.classList.add('fade-in');
    });
});

window.addEventListener('beforeunload', () => {
    document.body.classList.remove('loaded');
});

// Sistema de temas
const btn = document.getElementById('temaBtn');
const body = document.body;
const temas = ['oscuro', 'claro', 'neon'];
const emojis = ['🌙', '☀️', '✨'];

function aplicar(tema) {
    body.classList.remove('tema-claro', 'tema-neon');
    if (tema === 'claro') body.classList.add('tema-claro');
    if (tema === 'neon') body.classList.add('tema-neon');
    localStorage.setItem('tema', tema);
    
    if (btn) {
        const idx = temas.indexOf(tema);
        btn.textContent = emojis[idx];
        Toast.info('Tema cambiado a: ' + tema.charAt(0).toUpperCase() + tema.slice(1));
    }
}

let actual = localStorage.getItem('tema') || 'oscuro';
aplicar(actual);

if (btn) {
    btn.addEventListener('click', () => {
        const idx = (temas.indexOf(actual) + 1) % temas.length;
        actual = temas[idx];
        aplicar(actual);
    });
}

// Validación de formularios
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.formulario');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim()) {
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                } else {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                }
            });
            
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('invalid');
                } else {
                    this.classList.remove('valid');
                }
            });
        });
        
        form.addEventListener('submit', function(e) {
            let valido = true;
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valido = false;
                    input.classList.add('invalid');
                    input.focus();
                }
            });
            if (!valido) {
                e.preventDefault();
                Toast.error('Por favor completa todos los campos requeridos');
            }
        });
    });
    
    // Contador de caracteres
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const contador = document.createElement('div');
        contador.className = 'contador-caracteres';
        textarea.parentNode.insertBefore(contador, textarea.nextSibling);
        
        function actualizarContador() {
            const max = 5000;
            const actual = textarea.value.length;
            contador.textContent = `${actual}/${max} caracteres`;
            contador.style.color = actual > max * 0.8 ? '#ff5c8a' : 'var(--suave)';
        }
        
        textarea.addEventListener('input', actualizarContador);
        actualizarContador();
    });
    
    // Vista previa de imagen
    const inputImagen = document.querySelector('input[type="file"][accept="image/*"]');
    if (inputImagen) {
        inputImagen.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('.preview-imagen');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'preview-imagen';
                        inputImagen.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
                    Toast.info('Imagen seleccionada correctamente');
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Efectos en tarjetas
document.addEventListener('DOMContentLoaded', function() {
    const entradas = document.querySelectorAll('.entrada');
    
    entradas.forEach((entrada, idx) => {
        entrada.style.animationDelay = `${idx * 0.05}s`;
        entrada.classList.add('fade-in');
        
        entrada.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        entrada.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Confirmación para eliminar
document.addEventListener('DOMContentLoaded', function() {
    const formosEliminar = document.querySelectorAll('form[action*="/eliminar"]');
    
    formosEliminar.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const modal = document.createElement('div');
            modal.className = 'modal-confirmacion';
            modal.innerHTML = `
                <div class="modal-contenido">
                    <h3>⚠️ Confirmar eliminación</h3>
                    <p>¿Estás seguro de que deseas eliminar esto? Esta acción no se puede deshacer.</p>
                    <div class="modal-botones">
                        <button class="btn btn-cancel">Cancelar</button>
                        <button class="btn btn-eliminar">Eliminar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            setTimeout(() => modal.classList.add('modal-visible'), 10);
            
            const btnCancel = modal.querySelector('.btn-cancel');
            const btnEliminar = modal.querySelector('.btn-eliminar');
            
            btnCancel.addEventListener('click', () => {
                modal.classList.remove('modal-visible');
                setTimeout(() => modal.remove(), 300);
            });
            
            btnEliminar.addEventListener('click', () => {
                modal.classList.remove('modal-visible');
                setTimeout(() => {
                    modal.remove();
                    form.submit();
                }, 300);
            });
            
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('modal-visible');
                    setTimeout(() => modal.remove(), 300);
                }
            });
        });
    });
});

// Scroll suave
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.entrada, .formulario-container').forEach(el => {
        observer.observe(el);
    });
});

// Búsqueda
const searchInput = document.querySelector('.search-entrada');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const texto = e.target.value.toLowerCase();
        const entradas = document.querySelectorAll('.entrada');
        
        entradas.forEach(entrada => {
            const titulo = entrada.querySelector('h2')?.textContent.toLowerCase() || '';
            const contenido = entrada.querySelector('.entrada-contenido')?.textContent.toLowerCase() || '';
            
            if (titulo.includes(texto) || contenido.includes(texto)) {
                entrada.style.display = '';
                entrada.classList.add('fade-in');
            } else {
                entrada.style.display = 'none';
            }
        });
        
        const visibles = document.querySelectorAll('.entrada:not([style*="display: none"])').length;
        if (visibles === 0) {
            Toast.info('No se encontraron resultados');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('in');
});

window.addEventListener('beforeunload', function() {
    document.body.classList.remove('in');
});


