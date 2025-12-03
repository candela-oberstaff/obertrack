/**
 * Report Download JavaScript
 * Handles monthly report download logic
 */

function toggleEmployeeDetails(employeeId) {
    const detailsElement = document.getElementById(`employeeDetails_${employeeId}`);
    if (detailsElement) {
        detailsElement.classList.toggle('hidden');
    }
}

function downloadReport(employeeId, employeeName, totalApprovedHours, currentMonthFormat) {
    const certifyCheckbox = document.getElementById(`certifyHours_${employeeId}`);
    if (!certifyCheckbox.checked) {
        Swal.fire({
            title: 'Certificación requerida',
            text: 'Por favor, certifique que las horas son correctas antes de descargar el reporte.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    if (totalApprovedHours < 160) {
        Swal.fire({
            title: 'Horas insuficientes',
            text: 'No se pueden descargar reportes hasta que se hayan aprobado al menos 160 horas.',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    Swal.fire({
        title: `Descargando reporte de ${employeeName}`,
        text: 'Por favor, espere mientras se genera el reporte...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Simular la descarga (reemplazar con la lógica real de descarga)
    setTimeout(() => {
        Swal.fire({
            title: 'Descarga completada',
            text: `El reporte de ${employeeName} se ha descargado con éxito. Se enviará una copia por correo electrónico en breve.`,
            icon: 'success',
            confirmButtonText: 'Genial'
        });
    }, 2000);

    // Iniciar la descarga real
    // Note: window.routes.downloadMonthlyReport must be defined in the main layout or view
    const url = window.routes.downloadMonthlyReport.replace('__MONTH__', currentMonthFormat) + `?employee_id=${employeeId}`;
    window.location.href = url;
}

// Export functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        toggleEmployeeDetails,
        downloadReport
    };
}
