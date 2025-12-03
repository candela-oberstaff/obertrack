/**
 * Task Management JavaScript
 * Handles task details, completion toggling, comments, and editing
 */

function toggleTaskDetails(taskId) {
    const detailsElement = document.getElementById(`taskDetails-${taskId}`);
    const chevronElement = document.getElementById(`chevron-${taskId}`);
    if (detailsElement && chevronElement) {
        detailsElement.classList.toggle('hidden');
        chevronElement.classList.toggle('rotate-180');
    }
}

function toggleEmployerTaskCompletion(taskId) {
    const toggleButton = document.getElementById(`toggle-button-${taskId}`);
    const statusBadge = document.getElementById(`status-badge-${taskId}`);

    if (toggleButton && statusBadge) {
        // Check current state based on button text or class
        const isCurrentlyCompleted = toggleButton.textContent.includes('Marcar En Progreso') ||
            toggleButton.textContent.includes('Marcar como En Progreso');

        // We want to toggle to the opposite state
        const newCompletedState = !isCurrentlyCompleted;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/empleador/tareas/${taskId}/toggle-completion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ completed: newCompletedState })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (newCompletedState) {
                        // Changed to Completed
                        toggleButton.innerHTML = '<i class="fas fa-undo mr-1"></i> Marcar En Progreso';
                        toggleButton.classList.remove('bg-green-500', 'hover:bg-green-600');
                        toggleButton.classList.add('bg-yellow-500', 'hover:bg-yellow-600');

                        statusBadge.textContent = 'Completada';
                        statusBadge.classList.remove('bg-yellow-100', 'text-yellow-800');
                        statusBadge.classList.add('bg-green-100', 'text-green-800');
                    } else {
                        // Changed to In Progress
                        toggleButton.innerHTML = '<i class="fas fa-check mr-1"></i> Marcar Completada';
                        toggleButton.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                        toggleButton.classList.add('bg-green-500', 'hover:bg-green-600');

                        statusBadge.textContent = 'En Progreso';
                        statusBadge.classList.remove('bg-green-100', 'text-green-800');
                        statusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                    }

                    // Optional: Show alert if SweetAlert2 is available
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Estado actualizado',
                            text: 'El estado de la tarea ha sido actualizado correctamente.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'No se pudo actualizar el estado de la tarea', 'error');
                    } else {
                        alert('Error al actualizar el estado de la tarea');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
                } else {
                    alert('Error al procesar la solicitud');
                }
            });
    }
}

function showEmployerEditFields(taskId) {
    const editForm = document.getElementById(`editForm${taskId}`);
    if (editForm) {
        editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
    }
}

function toggleEmployerComments(taskId) {
    const commentsSection = document.getElementById(`commentsSection-${taskId}`);
    if (commentsSection) {
        commentsSection.classList.toggle('hidden');
    }
}

function addEmployerTaskComment(event, taskId) {
    event.preventDefault();
    const newCommentTextarea = document.getElementById(`newComment-${taskId}`);

    if (newCommentTextarea) {
        const commentContent = newCommentTextarea.value;

        if (commentContent.trim() === '') {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Por favor, escribe un comentario antes de enviarlo.', 'warning');
            } else {
                alert('Por favor, escribe un comentario antes de enviarlo.');
            }
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/empleador/tareas/${taskId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: commentContent })
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Reload page to show new comment (simple approach)
                // Ideally we would append the comment to the DOM dynamically
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'No se pudo agregar el comentario', 'error');
                } else {
                    alert('Error al agregar el comentario');
                }
            });
    }
}

function editEmployerComment(commentId) {
    // Placeholder for edit functionality
    // This would typically open a modal or replace the comment text with a textarea
    console.log('Edit comment', commentId);
}

function deleteEmployerComment(commentId, taskId) {
    if (!confirm('¿Estás seguro de querer eliminar este comentario?')) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/empleador/tareas/${taskId}/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const commentElement = document.getElementById(`comment-${commentId}`);
            if (commentElement) {
                commentElement.remove();

                // Update count
                const countSpan = document.getElementById(`commentCount-${taskId}`);
                if (countSpan) {
                    let count = parseInt(countSpan.innerText);
                    countSpan.innerText = Math.max(0, count - 1);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el comentario');
        });
}

// Export functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        toggleTaskDetails,
        toggleEmployerTaskCompletion,
        showEmployerEditFields,
        toggleEmployerComments,
        addEmployerTaskComment,
        editEmployerComment,
        deleteEmployerComment
    };
}
