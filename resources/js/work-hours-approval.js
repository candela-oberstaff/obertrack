/**
 * Work Hours Approval JavaScript
 * Handles approval modal and scroll position management
 */

let currentEmployeeId, currentWeekStart;
let scrollPosition = 0;

/**
 * Show the comment modal for approving with comments
 */
function showCommentModal(employeeId, weekStart) {
    currentEmployeeId = employeeId;
    currentWeekStart = weekStart;
    document.getElementById('commentModal').classList.remove('hidden');
}

/**
 * Close the comment modal
 */
function closeCommentModal() {
    document.getElementById('commentModal').classList.add('hidden');
    document.getElementById('approvalComment').value = '';
}

/**
 * Approve week with comment via AJAX
 */
function approveWithComment() {
    const comment = document.getElementById('approvalComment').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(window.routes.approveWeekWithComment, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            employee_id: currentEmployeeId,
            week_start: currentWeekStart,
            comment: comment
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Semana aprobada con comentarios');
                location.reload();
                window.scrollTo(0, scrollPosition);
            } else {
                alert('Error al aprobar la semana');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al aprobar la semana');
        });

    closeCommentModal();
}

/**
 * Save scroll position before form submission
 */
function saveScrollPosition(form) {
    scrollPosition = window.pageYOffset;
    return true;
}

// Export functions for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showCommentModal,
        closeCommentModal,
        approveWithComment,
        saveScrollPosition
    };
}
