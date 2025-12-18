/**
 * Script mínimo para modo avanzado de subida de fotos
 * Solo maneja la interfaz, sin manipulación de imágenes cliente
 */

document.addEventListener('DOMContentLoaded', function() {
    // Cambio entre modos usando botones radio o tabs
    const btnModes = document.querySelectorAll('.btn-mode');
    const modes = document.querySelectorAll('.upload-mode');
    
    btnModes.forEach(btn => {
        btn.addEventListener('click', function() {
            const mode = this.dataset.mode;
            
            // Actualizar botones
            btnModes.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Actualizar modos
            modes.forEach(m => m.classList.remove('active'));
            document.getElementById('modo-' + mode).classList.add('active');
        });
    });
});
