<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('taskModalLogic', (currentUser) => ({
            isDetailsModalOpen: false,
            selectedTask: null,
            currentTab: 'details',
            
            // Current User Data (required for avatar display)
            currentUser: currentUser,

            // UI State
            isUploadingFile: false,
            newCommentText: '',
            isSubmittingComment: false,
            editingCommentId: null,
            editCommentContent: '',
            
            // Delete Confirmation State
            deleteConfirmation: { isOpen: false, type: null, id: null },

            // Task Edit State (stubbed for now if not used in read-only views, but needed for modal template)
            isEditingTask: false,
            editTaskData: {},
            isSavingTask: false,

            openModal(task, tab = 'details') {
                // Ensure attachments and comments are arrays
                if (!task.attachments) task.attachments = [];
                if (!task.comments) task.comments = [];
                
                this.selectedTask = task;
                this.currentTab = tab;
                this.isDetailsModalOpen = true;
            },

            closeModal() {
                this.isDetailsModalOpen = false;
                this.selectedTask = null;
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
                    user: this.currentUser, // Use the correct user object!
                    task_id: taskId
                };
                
                this.selectedTask.comments.unshift(optimisticComment);
                this.newCommentText = ''; 

                try {
                    // Use generic route
                    const response = await fetch(`/tasks/${taskId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ content: content })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Replace temp comment with real one
                        const index = this.selectedTask.comments.findIndex(c => c.id === tempId);
                        if (index !== -1) this.selectedTask.comments[index] = data.comment;
                    } else {
                        // Revert on failure
                        this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== tempId);
                        console.error('Error al enviar comentario');
                        // Simple alert or toast could go here
                    }
                } catch (error) {
                    this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== tempId);
                    console.error('Error:', error);
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
                    }
                } catch (error) {
                    console.error('Error updating comment:', error);
                }
            },

            confirmDeleteComment(id) {
                this.deleteConfirmation = { isOpen: true, type: 'comment', id: id };
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

            confirmDeleteFile(id) {
                this.deleteConfirmation = { isOpen: true, type: 'file', id: id };
            },

            // --- Global Delete Handler ---

            async performDelete() {
                const { type, id } = this.deleteConfirmation;
                this.deleteConfirmation.isOpen = false;

                if (type === 'file') {
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
                        }
                    } catch (e) { 
                        console.error('Delete error', e); 
                    }
                } else if (type === 'comment') {
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
                        }
                    } catch (e) { 
                        console.error('Delete error', e); 
                    }
                }
            },
            
            // --- Helper / Stub Methods for Template Compatibility ---
            
            startEditingTask() {
                // Read-only in professional dashboard for now
                console.log('Edit task not implemented in this view');
            },
            
            confirmDeleteTask() {
                 console.log('Delete task not implemented in this view');
            },
            
            toggleTaskCompletion(task) {
                 // Implement if professionals can complete tasks from modal
            }
        }));
    });
</script>
