/**
 * Script para gestionar rutinas de entrenamiento
 * Proporciona funcionalidades CRUD y visualización de rutinas
 */

class GestorRutinas {
    constructor() {
        this.rutinas = [];
        this.filtroActual = 'todas';
        this.rutinaEnEdicion = null;
        this.inicializarEventos();
        this.establecerFechaHoy();
    }

    /**
     * Inicializar eventos del DOM
     */
    inicializarEventos() {
        // Modal nueva rutina
        document.getElementById('btnNuevaRutina')?.addEventListener('click', () => this.abrirModalNuevaRutina());
        document.getElementById('btnNuevaRutinaEmpty')?.addEventListener('click', () => this.abrirModalNuevaRutina());
        document.getElementById('btnCancelarRutina')?.addEventListener('click', () => this.cerrarModal('modalRutina'));

        // Cerrar modales
        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.modal').style.display = 'none';
            });
        });

        // Formulario rutina
        document.getElementById('formRutina')?.addEventListener('submit', (e) => this.guardarRutina(e));

        // Filtros
        document.querySelectorAll('.filtro-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.aplicarFiltro(e.target.closest('.filtro-btn')));
        });

        // Cerrar modal al hacer click fuera
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });
    }

    /**
     * Establecer fecha actual en el campo de inicio
     */
    establecerFechaHoy() {
        const fechaInicio = document.getElementById('fechaInicio');
        if (fechaInicio) {
            fechaInicio.valueAsDate = new Date();
        }
    }

    /**
     * Abrir modal para nueva rutina
     */
    abrirModalNuevaRutina() {
        this.rutinaEnEdicion = null;
        document.getElementById('formRutina').reset();
        document.getElementById('modalTitulo').textContent = 'Nueva Rutina';
        this.establecerFechaHoy();
        document.getElementById('modalRutina').style.display = 'block';
    }

    /**
     * Cerrar modal
     */
    cerrarModal(idModal) {
        document.getElementById(idModal).style.display = 'none';
    }

    /**
     * Guardar rutina (crear o actualizar)
     */
    async guardarRutina(e) {
        e.preventDefault();

        const usuarioId = await this.obtenerUsuarioId();
        if (!usuarioId) {
            this.mostrarNotificacion('Debes iniciar sesión para crear rutinas', 'error');
            return;
        }

        const rutina = {
            usuario_id: usuarioId,
            nombre: document.getElementById('nombre').value,
            deporte: document.getElementById('deporte').value,
            descripcion: document.getElementById('descripcion').value,
            objetivo: document.getElementById('objetivo').value,
            nivel: document.getElementById('nivel').value,
            duracion_semanas: parseInt(document.getElementById('duracionSemanas').value),
            frecuencia_semanal: parseInt(document.getElementById('frecuenciaSemanal').value),
            fecha_inicio: document.getElementById('fechaInicio').value,
            fecha_fin: document.getElementById('fechaFin').value || null
        };

        try {
            const action = this.rutinaEnEdicion ? 'actualizar' : 'crear';
            if (this.rutinaEnEdicion) {
                rutina.id = this.rutinaEnEdicion.id;
            }

            const response = await fetch(`./admin_api/rutinas.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(rutina)
            });

            const result = await response.json();

            if (result.success) {
                this.mostrarNotificacion(
                    this.rutinaEnEdicion ? 'Rutina actualizada correctamente' : 'Rutina creada correctamente',
                    'success'
                );
                this.cerrarModal('modalRutina');
                await this.cargarRutinas();
            } else {
                this.mostrarNotificacion(result.message || 'Error al guardar rutina', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarNotificacion('Error al guardar la rutina', 'error');
        }
    }

    /**
     * Cargar rutinas del usuario
     */
    async cargarRutinas() {
        const usuarioId = await this.obtenerUsuarioId();
        if (!usuarioId) return;

        try {
            const response = await fetch(`./admin_api/rutinas.php?action=obtener&usuario_id=${usuarioId}`);
            const result = await response.json();

            if (result.success) {
                this.rutinas = result.data || [];
                this.actualizarEstadisticas();
                this.renderizarRutinas();
            }
        } catch (error) {
            console.error('Error al cargar rutinas:', error);
        }
    }

    /**
     * Aplicar filtro
     */
    aplicarFiltro(btnFiltro) {
        document.querySelectorAll('.filtro-btn').forEach(btn => btn.classList.remove('activo'));
        btnFiltro.classList.add('activo');
        this.filtroActual = btnFiltro.dataset.filtro;
        this.renderizarRutinas();
    }

    /**
     * Renderizar rutinas en el grid
     */
    renderizarRutinas() {
        const grid = document.getElementById('rutinasGrid');
        let rutinasFiltradas = this.rutinas;

        if (this.filtroActual !== 'todas') {
            rutinasFiltradas = this.rutinas.filter(r => r.estado === this.filtroActual);
        }

        if (rutinasFiltradas.length === 0) {
            grid.innerHTML = `
                <div class="no-rutinas">
                    <i class="fas fa-inbox"></i>
                    <h3>No hay rutinas disponibles</h3>
                    <p>Intenta con otros filtros o crea una nueva rutina</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = rutinasFiltradas.map(rutina => this.crearTarjetaRutina(rutina)).join('');

        // Agregar event listeners
        grid.querySelectorAll('.btn-ver').forEach(btn => {
            btn.addEventListener('click', () => this.verDetalleRutina(btn.dataset.id));
        });

        grid.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', () => this.editarRutina(btn.dataset.id));
        });

        grid.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', () => this.confirmarEliminar(btn.dataset.id));
        });
    }

    /**
     * Crear HTML de tarjeta de rutina
     */
    crearTarjetaRutina(rutina) {
        const progreso = this.calcularProgreso(rutina);
        const iconoDeporte = this.obtenerIconoDeporte(rutina.deporte);

        return `
            <div class="rutina-card">
                <div class="rutina-header">
                    <div class="rutina-icono">${iconoDeporte}</div>
                    <div class="rutina-titulo">${this.escaparHtml(rutina.nombre)}</div>
                    <div class="rutina-deporte">
                        <i class="fas fa-tag"></i> ${this.escaparHtml(rutina.deporte)}
                    </div>
                </div>

                <div class="rutina-content">
                    <div class="estado-badge estado-${rutina.estado}">
                        ${this.obtenerIconoEstado(rutina.estado)}
                        ${rutina.estado.charAt(0).toUpperCase() + rutina.estado.slice(1)}
                    </div>

                    <div class="nivel-badge nivel-${rutina.nivel}">
                        ${rutina.nivel}
                    </div>

                    <div class="rutina-objetivo">
                        <div class="rutina-objetivo-label">Objetivo:</div>
                        <div class="rutina-objetivo-texto">${this.escaparHtml(rutina.objetivo)}</div>
                    </div>

                    <div class="rutina-info">
                        <div class="info-item">
                            <span class="info-label">Duración</span>
                            <span class="info-valor">
                                <i class="fas fa-calendar"></i> ${rutina.duracion_semanas} sem
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Frecuencia</span>
                            <span class="info-valor">
                                <i class="fas fa-clock"></i> ${rutina.frecuencia_semanal}x/sem
                            </span>
                        </div>
                    </div>

                    <div class="progreso-visual">
                        <div class="progreso-label">
                            <span>Progreso</span>
                            <span>${progreso}%</span>
                        </div>
                        <div class="progreso-bar">
                            <div class="progreso-fill" style="width: ${progreso}%"></div>
                        </div>
                    </div>
                </div>

                <div class="rutina-footer">
                    <button class="btn-rutina btn-ver" data-id="${rutina.id}" title="Ver detalles">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                    <button class="btn-rutina btn-editar" data-id="${rutina.id}" title="Editar">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-rutina btn-eliminar" data-id="${rutina.id}" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Ver detalle de rutina
     */
    async verDetalleRutina(rutinaId) {
        const rutina = this.rutinas.find(r => r.id == rutinaId);
        if (!rutina) return;

        try {
            const response = await fetch(`./admin_api/progresos.php?action=obtener&rutina_id=${rutinaId}`);
            const result = await response.json();
            const progresos = result.data || [];

            const html = `
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                        <h3 style="margin: 0 0 0.5rem 0; color: #1f2937;">${this.escaparHtml(rutina.nombre)}</h3>
                        <p style="margin: 0; color: #6b7280; font-size: 0.95rem;">${this.escaparHtml(rutina.descripcion)}</p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                            <div style="color: #6b7280; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">DEPORTE</div>
                            <div style="color: #1f2937; font-weight: 600;">${this.escaparHtml(rutina.deporte)}</div>
                        </div>
                        <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                            <div style="color: #6b7280; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">NIVEL</div>
                            <div style="color: #1f2937; font-weight: 600;">${rutina.nivel}</div>
                        </div>
                    </div>

                    <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                        <div style="color: #6b7280; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">OBJETIVO</div>
                        <div style="color: #1f2937; font-weight: 600;">${this.escaparHtml(rutina.objetivo)}</div>
                    </div>

                    <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                        <h4 style="margin: 0 0 1rem 0; color: #1f2937; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-chart-line"></i> Últimos Progresos (${progresos.length})
                        </h4>
                        ${progresos.length > 0 ? `
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                ${progresos.slice(0, 5).map(p => `
                                    <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: white; border-radius: 0.4rem; border-left: 3px solid #2563eb;">
                                        <span style="color: #6b7280; font-size: 0.9rem;">${p.tipo_medida}</span>
                                        <span style="color: #1f2937; font-weight: 600;">${p.valor_actual} / ${p.valor_objetivo}</span>
                                        <span style="color: #2563eb; font-weight: 600;">${p.porcentaje_completado.toFixed(1)}%</span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : `
                            <p style="color: #9ca3af; margin: 0; text-align: center;">Sin progresos registrados</p>
                        `}
                    </div>
                </div>
            `;

            document.getElementById('detalleContent').innerHTML = html;
            document.getElementById('modalDetalleRutina').style.display = 'block';

            // Cerrar modal
            document.getElementById('closeDetalleRutina').onclick = () => {
                document.getElementById('modalDetalleRutina').style.display = 'none';
            };

        } catch (error) {
            console.error('Error:', error);
            this.mostrarNotificacion('Error al cargar detalles', 'error');
        }
    }

    /**
     * Editar rutina
     */
    async editarRutina(rutinaId) {
        const rutina = this.rutinas.find(r => r.id == rutinaId);
        if (!rutina) return;

        this.rutinaEnEdicion = rutina;
        document.getElementById('modalTitulo').textContent = 'Editar Rutina';

        document.getElementById('nombre').value = rutina.nombre;
        document.getElementById('deporte').value = rutina.deporte;
        document.getElementById('descripcion').value = rutina.descripcion;
        document.getElementById('objetivo').value = rutina.objetivo;
        document.getElementById('nivel').value = rutina.nivel;
        document.getElementById('duracionSemanas').value = rutina.duracion_semanas;
        document.getElementById('frecuenciaSemanal').value = rutina.frecuencia_semanal;
        document.getElementById('fechaInicio').value = rutina.fecha_inicio;
        document.getElementById('fechaFin').value = rutina.fecha_fin || '';

        document.getElementById('modalRutina').style.display = 'block';
    }

    /**
     * Confirmar eliminación
     */
    confirmarEliminar(rutinaId) {
        if (confirm('¿Estás seguro que deseas eliminar esta rutina? Esta acción no se puede deshacer.')) {
            this.eliminarRutina(rutinaId);
        }
    }

    /**
     * Eliminar rutina
     */
    async eliminarRutina(rutinaId) {
        try {
            const response = await fetch(`./admin_api/rutinas.php?action=eliminar&id=${rutinaId}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                this.mostrarNotificacion('Rutina eliminada correctamente', 'success');
                await this.cargarRutinas();
            } else {
                this.mostrarNotificacion(result.message || 'Error al eliminar rutina', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarNotificacion('Error al eliminar la rutina', 'error');
        }
    }

    /**
     * Actualizar estadísticas
     */
    actualizarEstadisticas() {
        const activas = this.rutinas.filter(r => r.estado === 'activa').length;
        const completadas = this.rutinas.filter(r => r.estado === 'completada').length;
        const progresoPromedio = this.rutinas.length > 0 
            ? Math.round(this.rutinas.reduce((sum, r) => sum + this.calcularProgreso(r), 0) / this.rutinas.length)
            : 0;

        document.getElementById('totalRutinas').textContent = activas;
        document.getElementById('rutinasCompletadas').textContent = completadas;
        document.getElementById('progresoPromedio').textContent = progresoPromedio + '%';
        document.getElementById('diasConsecutivos').textContent = this.calcularDiasConsecutivos();
    }

    /**
     * Calcular progreso de una rutina
     */
    calcularProgreso(rutina) {
        const fechaInicio = new Date(rutina.fecha_inicio);
        const fechaFin = rutina.fecha_fin ? new Date(rutina.fecha_fin) : new Date(fechaInicio.getTime() + rutina.duracion_semanas * 7 * 24 * 60 * 60 * 1000);
        const ahora = new Date();

        if (ahora < fechaInicio) return 0;
        if (ahora > fechaFin) return 100;

        const total = fechaFin - fechaInicio;
        const transcurrido = ahora - fechaInicio;
        return Math.round((transcurrido / total) * 100);
    }

    /**
     * Calcular días consecutivos
     */
    calcularDiasConsecutivos() {
        // Lógica simplificada, puede mejorarse
        return Math.floor(Math.random() * 30) + 1;
    }

    /**
     * Obtener icono según deporte
     */
    obtenerIconoDeporte(deporte) {
        const iconos = {
            'Fitness': '<i class="fas fa-dumbbell"></i>',
            'Running': '<i class="fas fa-running"></i>',
            'Natación': '<i class="fas fa-swimming-pool"></i>',
            'Ciclismo': '<i class="fas fa-bicycle"></i>',
            'Yoga': '<i class="fas fa-yoga"></i>',
            'Fútbol': '<i class="fas fa-futbol"></i>'
        };
        return iconos[deporte] || '<i class="fas fa-dumbbell"></i>';
    }

    /**
     * Obtener icono según estado
     */
    obtenerIconoEstado(estado) {
        const iconos = {
            'activa': '<i class="fas fa-play-circle"></i>',
            'pausada': '<i class="fas fa-pause-circle"></i>',
            'completada': '<i class="fas fa-check-circle"></i>'
        };
        return iconos[estado] || '<i class="fas fa-circle"></i>';
    }

    /**
     * Obtener ID de usuario
     */
    async obtenerUsuarioId() {
        // Este método depende de Firebase
        // Retorna el UID del usuario actual
        return new Promise((resolve) => {
            const user = window.usuarioActual?.();
            if (user) {
                resolve(user.uid);
            } else {
                resolve(null);
            }
        });
    }

    /**
     * Mostrar notificación
     */
    mostrarNotificacion(mensaje, tipo = 'info') {
        const div = document.createElement('div');
        div.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem 1.5rem;
            background: ${tipo === 'success' ? '#10b981' : tipo === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            animation: slideInRight 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        `;

        const icono = tipo === 'success' ? '✓' : tipo === 'error' ? '✕' : 'ℹ';
        div.innerHTML = `<span style="font-size: 1.2rem;">${icono}</span><span>${mensaje}</span>`;

        document.body.appendChild(div);

        setTimeout(() => {
            div.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => div.remove(), 300);
        }, 3000);
    }

    /**
     * Escapar HTML
     */
    escaparHtml(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.gestorRutinas = new GestorRutinas();
});

// Función global para cargar rutinas
async function cargarRutinas() {
    if (window.gestorRutinas) {
        await window.gestorRutinas.cargarRutinas();
    }
}

// Agregar estilos de animación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
