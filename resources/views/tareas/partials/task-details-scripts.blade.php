<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('taskModalLogic', (currentUser) => ({
            isDetailsModalOpen: false,
            selectedTask: null,
            currentTab: 'details',
            
            // Current User Data (required for avatar display)
            currentUser: currentUser,

            // State tracking for consistency
            hasChanges: false,

            // UI State
            isUploadingFile: false,
            newCommentText: '',
            isSubmittingComment: false,
            editingCommentId: null,
            editCommentContent: '',
            deletingCommentId: null, // Track which comment is being deleted
            deletingFileId: null, // Track which file is being deleted
            updatingCommentId: null, // Track which comment is being updated
            searchAssignee: '', // Search query for assignee list
            
            // Delete Confirmation State
            deleteConfirmation: { isOpen: false, type: null, id: null },

            // Task Edit State
            isEditingTask: false,
            editTaskData: {
                assignees: []
            },
            isSavingTask: false,
            isDeletingTask: false,
            isDeleting: false, // Unified loading state for modal

            openModal(task, tab = 'details') {
                // Ensure attachments and comments are arrays
                if (!task.attachments) task.attachments = [];
                if (!task.comments) task.comments = [];
                
                // Set initial data for immediate render
                this.selectedTask = task;
                this.currentTab = tab;
                this.isDetailsModalOpen = true;
                this.hasChanges = false; // Reset change tracking on open

                // Fetch fresh data (comments, attachments, etc.)
                this.fetchTaskDetails(task.id);
            },

            async fetchTaskDetails(id) {
                try {
                    // Cache busting param
                    const response = await fetch(`/tasks/${id}?t=${new Date().getTime()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        // Verify we are still looking at the same task
                        if (this.selectedTask && this.selectedTask.id === id) {
                            // Preserve optimistic comments (those with temp_ IDs)
                            const optimisticComments = (this.selectedTask.comments || []).filter(c => c.id && String(c.id).startsWith('temp_'));
                            
                            // Merge fresh data but keep optimistic ones at the top
                            this.selectedTask = { 
                                ...this.selectedTask, 
                                ...data.task,
                                comments: [...optimisticComments, ...(data.task.comments || [])]
                            };
                        }
                    }
                } catch (error) {
                    console.error('Error fetching task details:', error);
                }
            },
            
            closeModal() {
                // Always close modal first
                this.isDetailsModalOpen = false;
                this.selectedTask = null;
                
                // Then reload if needed (delayed to allow modal to close visually)
                if (this.hasChanges) {
                    setTimeout(() => {
                        location.reload(true);
                    }, 100);
                }
            },

            formatDate(d) {
                if (!d) return '--/--/----';
                const parts = d.split('T')[0].split('-');
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            },

            // --- Comment Logic ---

            async submitComment() {
                if (!this.newCommentText.trim()) return;
                this.isSubmittingComment = true;
                
                const taskId = this.selectedTask.id;
                const content = this.newCommentText;
                
                // Optimistic Update
                const tempId = 'temp_' + Date.now();
                const optimisticComment = {
                    id: tempId,
                    content: content,
                    created_at: new Date().toISOString(),
                    user: this.currentUser, 
                    task_id: taskId
                };
                
                // Add correctly to reactive array
                if (!this.selectedTask.comments) this.selectedTask.comments = [];
                this.selectedTask.comments.unshift(optimisticComment);
                this.newCommentText = ''; 

                try {
                    const response = await fetch(`/tasks/${taskId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ 
                            content: content,
                            task_id: taskId
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        
                        // Find the temp comment again (index might have changed if fetch occurred)
                        const index = this.selectedTask.comments.findIndex(c => c.id === tempId);
                        
                        if (index !== -1) {
                            // Update properties instead of replacing object to minimize flicker if possible, 
                            // though ID change forces re-render if keyed by ID.
                            // We replace it to ensure all server-generated fields are present.
                            this.selectedTask.comments[index] = data.comment;
                        }
                        this.hasChanges = true;
                    } else {
                        throw new Error('Failed to post');
                    }
                } catch (error) {
                    // Revert on failure
                    this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== tempId);
                    console.error('Error al enviar comentario:', error);
                    alert('Error al enviar el comentario.');
                } finally {
                    this.isSubmittingComment = false;
                }
            },

            startEditingComment(comment) {
                this.editingCommentId = comment.id;
                this.editCommentContent = comment.content;
            },

            async updateComment(commentId) {
                if (!this.editCommentContent.trim()) return;
                this.updatingCommentId = commentId;

                try {
                    const response = await fetch(`/tasks/comments/${commentId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ content: this.editCommentContent })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        const index = this.selectedTask.comments.findIndex(c => c.id === commentId);
                        if (index !== -1) this.selectedTask.comments[index] = data.comment;
                        this.editingCommentId = null;
                        this.hasChanges = true;
                    } else {
                         throw new Error('Update failed');
                    }
                } catch (error) {
                    console.error('Error updating comment:', error);
                    alert('Error al actualizar el comentario');
                } finally {
                    this.updatingCommentId = null;
                }
            },

            async deleteComment(id) {
                if (!confirm('¿Estás seguro de eliminar este comentario?')) return;
                this.deletingCommentId = id;
                
                try {
                    const response = await fetch(`/tasks/comments/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== id);
                        this.hasChanges = true;
                    } else {
                        throw new Error('Failed');
                    }
                } catch (error) {
                    console.error('Error deleting comment:', error);
                    alert('Error al eliminar el comentario');
                } finally {
                    this.deletingCommentId = null;
                }
            },

            // --- File Upload Logic ---

            async uploadFile(file) {
                if (!file || this.isUploadingFile) return;
                this.isUploadingFile = true;

                const formData = new FormData();
                formData.append('file', file);
                
                try {
                    const response = await fetch(`/tasks/${this.selectedTask.id}/attachments`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (!this.selectedTask.attachments) this.selectedTask.attachments = [];
                        this.selectedTask.attachments.unshift(data.attachment);
                        this.hasChanges = true;
                    } else {
                        const data = await response.json();
                        console.error(data.message || 'Error uploading file');
                    }
                } catch (error) {
                    console.error('Connection error:', error);
                } finally {
                    this.isUploadingFile = false;
                }
            },

            async deleteFile(id) {
                if (!confirm('¿Estás seguro de eliminar este archivo?')) return;
                this.deletingFileId = id;

                try {
                    const response = await fetch(`/tasks/attachments/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        this.selectedTask.attachments = this.selectedTask.attachments.filter(a => a.id !== id);
                        this.hasChanges = true;
                    } else {
                        throw new Error('Failed');
                    }
                } catch (error) {
                    console.error('Error deleting file:', error);
                    alert('Error al eliminar el archivo');
                } finally {
                    this.deletingFileId = null;
                }
            },

            // --- Global Delete Handler ---

            async performDelete() {
                const { type, id } = this.deleteConfirmation;
                
                if (type === 'task') {
                    // Task deletion - show loading state
                    this.isDeletingTask = true;
                    
                    try {
                         const response = await fetch(`/tareas/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        
                        // Reload page after successful delete
                        window.location.reload(); 
                        
                    } catch (e) {
                        console.error('Delete error', e);
                        this.isDeletingTask = false;
                        this.deleteConfirmation.isOpen = false;
                        alert('Error al eliminar la tarea');
                    }
                }
            },
            
            // --- Task Edit Logic ---
            
            startEditingTask() {
                this.isEditingTask = true;
                this.searchAssignee = '';
                // Clone task data for editing
                this.editTaskData = {
                    title: this.selectedTask.title,
                    description: this.selectedTask.description,
                    priority: this.selectedTask.priority,
                    end_date: this.selectedTask.end_date ? this.selectedTask.end_date.split('T')[0] : '',
                    assignees: this.selectedTask.assignees.map(a => a.id)
                };
            },
            
            async saveTask() {
                if (!this.editTaskData.title.trim()) return;
                this.isSavingTask = true;

                try {
                    const response = await fetch(`/tareas/${this.selectedTask.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.editTaskData)
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Merge updated fields from server
                        this.selectedTask = { ...this.selectedTask, ...data.task };
                        this.isEditingTask = false;
                        this.hasChanges = true;
                    } else {
                        throw new Error('Failed to update');
                    }
                } catch (error) {
                    console.error('Error updating task:', error);
                    alert('Error al guardar los cambios.');
                } finally {
                    this.isSavingTask = false;
                }
            },
            
            confirmDeleteTask() {
                this.deleteConfirmation = { isOpen: true, type: 'task', id: this.selectedTask.id };
            },
            
            async toggleTaskCompletion(taskId) {
                // Optimistic UI
                const originalStatus = this.selectedTask.completed;
                this.selectedTask.completed = !originalStatus;

                try {
                    const response = await fetch(`/tasks/${taskId}/toggle-completion`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed');
                    }
                    const data = await response.json();
                    // Sync with server state just in case
                    this.selectedTask.completed = data.completed;
                    this.hasChanges = true;
                } catch (error) {
                    console.error('Error toggling completion:', error);
                    this.selectedTask.completed = originalStatus; // Revert
                }
            }
        }));
    });
</script>
