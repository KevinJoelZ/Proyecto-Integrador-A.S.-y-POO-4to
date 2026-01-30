/**
 * Script para gestionar progresos de entrenamiento
 * Permite registrar, visualizar y analizar progresos
 */

class GestorProgresos {
    constructor() {
        this.progresos = [];
        this.tabActual = 'todos';
        this.inicializarEventos();
        this.establecerFechaHoy();
    }

    /**
     * Inicializar eventos
     */
    inicializarEventos() {
        // Botones
        document.getElementById('btnNuevoProgreso')?.addEventListener('click', () => this.abrirModal());
        document.getElementById('btnCancelarProgreso')?.addEventListener('click', () => this.cerrarModal());

        // Cerrar modal
        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('modalProgreso').style.display = 'none';
            });
        });

        // Formulario
        document.getElementById('formProgreso')?.addEventListener('submit', (e) => this.guardarProgreso(e));

        // Tabs
        document.querySelectorAll('.progreso-tab').forEach(tab => {
            tab.addEventListener('click', (e) => this.cambiarTab(e.target));
        });

        // Slider de esfuerzo
        document.getElementById('esfuerzo')?.addEventListener('input', (e) => {
            document.getElementById('esfuerzoDisplay').textContent = e.target.value;
        });

        // Cerrar al hacer click fuera
        window.addEventListener('click', (e) => {
            if (e.target.id === 'modalProgreso') {
                document.getElementById('modalProgreso').style.display = 'none';
            }
        });
    }

    /**
     * Establecer fecha actual
     */
    establecerFechaHoy() {
        const fechaRegistro = document.getElementById('fechaRegistro');
        if (fechaRegistro) {
            fechaRegistro.valueAsDate = new Date();
        }
    }

    /**
     * Abrir modal
     */
    abrirModal() {
        document.getElementById('formProgreso').reset();
        this.establecerFechaHoy();
        document.getElementById('esfuerzoDisplay').textContent = '5';
        document.getElementById('modalProgreso').style.display = 'block';
    }

    /**
     * Cerrar modal
     */
    cerrarModal() {
        document.getElementById('modalProgreso').style.display = 'none';
    }

    /**
     * Guardar progreso
     */
    async guardarProgreso(e) {
        e.preventDefault();

        const usuarioId = await this.obtenerUsuarioId();
        if (!usuarioId) {
            this.mostrarNotificacion('Debes iniciar sesión', 'error');
            return;
        }

        const progreso = {
            usuario_id: usuarioId,
            rutina_id: null,
            fecha_registro: document.getElementById('fechaRegistro').value,
            tipo_medida: document.getElementById('tipoMedida').value,
            valor_actual: parseFloat(document.getElementById('valorActual').value),
            valor_objetivo: parseFloat(document.getElementById('valorObjetivo').value) || 0,
            notas: document.getElementById('notas').value,
            esfuerzo: parseInt(document.getElementById('esfuerzo').value)
        };

        try {
            const response = await fetch('./admin_api/progresos.php?action=registrar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(progreso)
            });

            const result = await response.json();

            if (result.success) {
                this.mostrarNotificacion('Progreso registrado correctamente', 'success');
                this.cerrarModal();
                await this.cargarProgresos();
            } else {
                this.mostrarNotificacion(result.message || 'Error al registrar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarNotificacion('Error al registrar progreso', 'error');
        }
    }

    /**
     * Cargar progresos
     */
    async cargarProgresos() {
        const usuarioId = await this.obtenerUsuarioId();
        if (!usuarioId) return;

        try {
            const response = await fetch(`./admin_api/progresos.php?action=obtener&usuario_id=${usuarioId}&limite=100`);
            const result = await response.json();

            if (result.success) {
                this.progresos = result.data || [];
                this.actualizarEstadisticas();
                this.renderizarProgresos();
                this.generarGraficos();
            }
        } catch (error) {
            console.error('Error al cargar progresos:', error);
        }
    }

    /**
     * Cambiar tab
     */
    cambiarTab(tab) {
        document.querySelectorAll('.progreso-tab').forEach(t => t.classList.remove('activo'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('activo'));

        tab.classList.add('activo');
        const tabId = `tab-${tab.dataset.tab}`;
        document.getElementById(tabId)?.classList.add('activo');

        this.tabActual = tab.dataset.tab;
    }

    /**
     * Renderizar progresos
     */
    renderizarProgresos() {
        const grid = document.getElementById('registrosGrid');

        if (this.progresos.length === 0) {
            grid.innerHTML = `
                <div class="no-datos">
                    <i class="fas fa-inbox"></i>
                    <h3>Sin registros aún</h3>
                    <p>Comienza a registrar tu progreso de entrenamiento</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = this.progresos.map(p => this.crearTarjetaProgreso(p)).join('');

        // Event listeners
        grid.querySelectorAll('.btn-accion').forEach(btn => {
            if (btn.classList.contains('btn-editar')) {
                btn.addEventListener('click', (e) => this.editarProgreso(e.target.closest('.btn-accion').dataset.id));
            }
            if (btn.classList.contains('btn-eliminar')) {
                btn.addEventListener('click', (e) => this.confirmarEliminar(e.target.closest('.btn-accion').dataset.id));
            }
        });
    }

    /**
     * Crear tarjeta de progreso
     */
    crearTarjetaProgreso(progreso) {
        const fecha = new Date(progreso.fecha_registro);
        const fechaFormato = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
        const esfuerzoColor = progreso.esfuerzo >= 8 ? '#ef4444' : progreso.esfuerzo >= 6 ? '#f59e0b' : '#10b981';

        return `
            <div class="registro-card">
                <div class="registro-header">
                    <div>
                        <div class="registro-tipo">${this.escaparHtml(progreso.tipo_medida)}</div>
                        <div class="registro-fecha">${fechaFormato}</div>
                    </div>
                </div>

                <div class="registro-valores">
                    <div class="valor-item">
                        <span class="valor-label">Valor Actual</span>
                        <span class="valor-numero">${parseFloat(progreso.valor_actual).toFixed(2)}</span>
                    </div>
                    <div class="valor-item">
                        <span class="valor-label">Valor Objetivo</span>
                        <span class="valor-numero">${parseFloat(progreso.valor_objetivo).toFixed(2)}</span>
                    </div>
                </div>

                ${progreso.esfuerzo ? `
                    <div class="registro-esfuerzo">
                        <i class="fas fa-fire" style="color: ${esfuerzoColor};"></i>
                        <span class="esfuerzo-texto">Esfuerzo Percibido</span>
                        <span class="esfuerzo-valor">${progreso.esfuerzo}/10</span>
                    </div>
                ` : ''}

                ${progreso.porcentaje_completado ? `
                    <div class="registro-progreso">
                        <div class="progreso-barra">
                            <div class="progreso-barra-relleno" style="width: ${progreso.porcentaje_completado}%"></div>
                        </div>
                        <span class="progreso-porcentaje">${parseFloat(progreso.porcentaje_completado).toFixed(1)}%</span>
                    </div>
                ` : ''}

                ${progreso.notas ? `
                    <div class="registro-notas">
                        <strong>Notas:</strong> ${this.escaparHtml(progreso.notas)}
                    </div>
                ` : ''}

                <div class="registro-acciones">
                    <button class="btn-accion btn-editar" data-id="${progreso.id}" title="Editar">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-accion btn-eliminar" data-id="${progreso.id}" title="Eliminar">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Actualizar estadísticas
     */
    actualizarEstadisticas() {
        const totalRegistros = this.progresos.length;
        const tiposUnicos = new Set(this.progresos.map(p => p.tipo_medida)).size;
        const ultimoRegistro = this.progresos.length > 0 ? this.progresos[0].fecha_registro : '-';

        document.getElementById('totalRegistros').textContent = totalRegistros;
        document.getElementById('tiposRegistro').textContent = tiposUnicos;

        if (ultimoRegistro !== '-') {
            const fecha = new Date(ultimoRegistro);
            const hace = this.calcularHace(fecha);
            document.getElementById('ultimoRegistro').textContent = hace;
        }

        // Calcular mejora del mes
        const mesActual = new Date();
        const hace30Dias = new Date(mesActual.getTime() - 30 * 24 * 60 * 60 * 1000);

        const progresosMes = this.progresos.filter(p => new Date(p.fecha_registro) >= hace30Dias);
        let mejora = 0;

        if (progresosMes.length > 1) {
            // Calcular mejora promedio
            const mejorasPorTipo = {};
            const tiposOrdenados = {};

            progresosMes.forEach(p => {
                if (!tiposOrdenados[p.tipo_medida]) {
                    tiposOrdenados[p.tipo_medida] = [];
                }
                tiposOrdenados[p.tipo_medida].push(parseFloat(p.valor_actual));
            });

            let totalMejoras = 0;
            let contadores = 0;

            Object.values(tiposOrdenados).forEach(valores => {
                if (valores.length > 1) {
                    const mejoraPorcentual = ((valores[0] - valores[valores.length - 1]) / valores[valores.length - 1]) * 100;
                    totalMejoras += Math.abs(mejoraPorcentual);
                    contadores++;
                }
            });

            mejora = contadores > 0 ? Math.round(totalMejoras / contadores) : 0;
        }

        document.getElementById('mejoraMesActual').textContent = mejora + '%';
    }

    /**
     * Calcular tiempo transcurrido
     */
    calcularHace(fecha) {
        const ahora = new Date();
        const diferencia = ahora - fecha;
        const segundos = Math.floor(diferencia / 1000);

        if (segundos < 60) return 'Hace un momento';
        const minutos = Math.floor(segundos / 60);
        if (minutos < 60) return `Hace ${minutos}m`;
        const horas = Math.floor(minutos / 60);
        if (horas < 24) return `Hace ${horas}h`;
        const dias = Math.floor(horas / 24);
        return `Hace ${dias}d`;
    }

    /**
     * Generar gráficos
     */
    generarGraficos() {
        const container = document.getElementById('graficosContainer');

        if (this.progresos.length === 0) {
            container.innerHTML = `
                <div class="no-datos">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Gráficos no disponibles</h3>
                    <p>Registra más progresos para ver análisis detallados</p>
                </div>
            `;
            return;
        }

        // Agrupar por tipo de medida
        const progresosPorTipo = {};
        this.progresos.forEach(p => {
            if (!progresosPorTipo[p.tipo_medida]) {
                progresosPorTipo[p.tipo_medida] = [];
            }
            progresosPorTipo[p.tipo_medida].push(p);
        });

        // Crear gráficos
        let html = '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">';

        Object.entries(progresosPorTipo).forEach(([tipo, progresos]) => {
            const valores = progresos.map(p => parseFloat(p.valor_actual)).reverse();
            const máximo = Math.max(...valores);
            const mínimo = Math.min(...valores);
            const promedio = (valores.reduce((a, b) => a + b) / valores.length).toFixed(2);

            html += `
                <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                    <h3 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.1rem;">${this.escaparHtml(tipo)}</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="text-align: center; padding: 1rem; background: #f0fdf4; border-radius: 0.5rem;">
                            <div style="font-size: 0.8rem; color: #059669; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Máximo</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #059669;">${máximo.toFixed(2)}</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; background: #eff6ff; border-radius: 0.5rem;">
                            <div style="font-size: 0.8rem; color: #0284c7; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Promedio</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #0284c7;">${promedio}</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; background: #fef2f2; border-radius: 0.5rem;">
                            <div style="font-size: 0.8rem; color: #dc2626; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Mínimo</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #dc2626;">${mínimo.toFixed(2)}</div>
                        </div>
                    </div>
                    <div style="text-align: center; font-size: 0.85rem; color: #6b7280;">
                        Registros: ${progresos.length}
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    /**
     * Editar progreso
     */
    editarProgreso(id) {
        // Implementar lógica de edición
        this.mostrarNotificacion('Funcionalidad de edición en desarrollo', 'info');
    }

    /**
     * Confirmar eliminación
     */
    confirmarEliminar(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
            this.eliminarProgreso(id);
        }
    }

    /**
     * Eliminar progreso
     */
    async eliminarProgreso(id) {
        try {
            const response = await fetch(`./admin_api/progresos.php?action=eliminar&id=${id}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                this.mostrarNotificacion('Progreso eliminado correctamente', 'success');
                await this.cargarProgresos();
            } else {
                this.mostrarNotificacion(result.message || 'Error al eliminar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarNotificacion('Error al eliminar', 'error');
        }
    }

    /**
     * Obtener ID de usuario
     */
    async obtenerUsuarioId() {
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
    window.gestorProgresos = new GestorProgresos();
});

// Función global
async function cargarProgresos() {
    if (window.gestorProgresos) {
        await window.gestorProgresos.cargarProgresos();
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
