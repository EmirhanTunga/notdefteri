// Kanban Board JavaScript
// Global instance kontrolü
if (window.kanbanBoardInstance) {
    console.warn('KanbanBoard already exists, skipping initialization');
} else {

class KanbanBoard {
    constructor() {
        this.activeTasksContainer = document.getElementById('active-tasks-container');
        this.completedTasksContainer = document.getElementById('completed-tasks-container');
        this.init();
    }

    init() {
        // Kanban section gösterildiğinde görevleri yükle
        document.addEventListener('sectionChanged', (e) => {
            if (e.detail.section === 'kanban') {
                this.loadTasks();
            }
        });

        // Yeni görev ekleme butonu
        const newTaskButton = document.getElementById('kanbanNewTaskBtn');
        if (newTaskButton && !newTaskButton.dataset.initialized) {
            newTaskButton.dataset.initialized = 'true';
            newTaskButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.showTaskModal();
            });
        }

        this.loadTasks();
    }

    async loadTasks() {
        try {
            const response = await fetch('get_kanban_tasks.php');
            const data = await response.json();

            if (data.success) {
                this.renderTasks(data.tasks);
                this.updateTaskCounts(data.tasks);
            } else {
                console.error('Görevler yüklenirken hata:', data.message);
            }
        } catch (error) {
            console.error('Görevler yüklenirken hata:', error);
        }
    }

    renderTasks(tasks) {
        const activeTasks = tasks.filter(task => task.status !== 'completed');
        const completedTasks = tasks.filter(task => task.status === 'completed');

        // Aktif görevleri render et
        this.activeTasksContainer.innerHTML = '';
        if (activeTasks.length === 0) {
            this.activeTasksContainer.innerHTML = this.getEmptyStateHTML('active');
        } else {
            activeTasks.forEach(task => {
                this.activeTasksContainer.appendChild(this.createTaskCard(task));
            });
        }

        // Tamamlanan görevleri render et
        this.completedTasksContainer.innerHTML = '';
        if (completedTasks.length === 0) {
            this.completedTasksContainer.innerHTML = this.getEmptyStateHTML('completed');
        } else {
            completedTasks.forEach(task => {
                this.completedTasksContainer.appendChild(this.createTaskCard(task, true));
            });
        }
    }

    createTaskCard(task, isCompleted = false) {
        const card = document.createElement('div');
        card.className = `kanban-task-card bg-card-light dark:bg-card-dark rounded-xl p-4 border border-border-light dark:border-border-dark hover:shadow-lg transition-all duration-200 ${isCompleted ? 'opacity-75' : ''}`;
        card.dataset.taskId = task.id;

        const priorityColors = {
            'low': 'bg-green-500',
            'medium': 'bg-yellow-500', 
            'high': 'bg-red-500'
        };

        const priorityLabels = {
            'low': 'Düşük',
            'medium': 'Orta',
            'high': 'Yüksek'
        };

        card.innerHTML = `
            <div class="flex items-start justify-between mb-3">
                <h4 class="font-semibold text-foreground-light dark:text-foreground-dark flex-1 mr-2">${this.escapeHtml(task.title)}</h4>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 ${priorityColors[task.priority]} rounded-full" title="${priorityLabels[task.priority]} Öncelik"></span>
                    ${!isCompleted ? `
                        <button data-task-id="${task.id}" data-action="complete" 
                                class="task-toggle-btn w-6 h-6 rounded-full border-2 border-green-500 hover:bg-green-500 transition-colors flex items-center justify-center group"
                                title="Tamamlandı olarak işaretle">
                            <svg class="w-3 h-3 text-green-500 group-hover:text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    ` : `
                        <button data-task-id="${task.id}" data-action="uncomplete" 
                                class="task-toggle-btn w-6 h-6 rounded-full bg-green-500 hover:bg-gray-400 transition-colors flex items-center justify-center group"
                                title="Aktif görevlere geri al">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    `}
                </div>
            </div>
            
            ${task.description ? `
                <p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">${this.escapeHtml(task.description)}</p>
            ` : ''}
            
            <div class="flex items-center justify-between text-xs text-subtle-light dark:text-subtle-dark">
                <span class="px-2 py-1 bg-accent-light dark:bg-accent-dark rounded-full">${priorityLabels[task.priority]}</span>
                ${task.due_date ? `<span class="text-orange-600 dark:text-orange-400">${this.formatDate(task.due_date)}</span>` : ''}
            </div>
            
            <div class="mt-3 pt-2 border-t border-border-light dark:border-border-dark text-xs text-subtle-light dark:text-subtle-dark">
                Oluşturulma: ${this.formatDateTime(task.created_at)}
            </div>
        `;

        // Event listener ekle
        const toggleBtn = card.querySelector('.task-toggle-btn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const taskId = parseInt(toggleBtn.dataset.taskId);
                const action = toggleBtn.dataset.action;
                const newStatus = action === 'complete' ? 'completed' : 'todo';
                
                console.log('Toggle button clicked:', { taskId, action, newStatus });
                this.toggleTaskStatus(taskId, newStatus);
            });
        }

        return card;
    }

    getEmptyStateHTML(type) {
        if (type === 'active') {
            return `
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-subtle-light dark:text-subtle-dark mb-1">Henüz aktif görev yok</p>
                    <p class="text-sm text-subtle-light dark:text-subtle-dark">Yeni görev ekleyerek başlayın</p>
                </div>
            `;
        } else {
            return `
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-subtle-light dark:text-subtle-dark">Tamamlanan görev yok</p>
                </div>
            `;
        }
    }

    updateTaskCounts(tasks) {
        const activeTasks = tasks.filter(task => task.status !== 'completed');
        const completedTasks = tasks.filter(task => task.status === 'completed');

        // Aktif görev sayısını güncelle - ID ile spesifik selector
        const activeCountElement = document.getElementById('active-tasks-count');
        if (activeCountElement) {
            activeCountElement.textContent = activeTasks.length;
        }

        // Tamamlanan görev sayısını güncelle - ID ile spesifik selector
        const completedCountElement = document.getElementById('completed-tasks-count');
        if (completedCountElement) {
            completedCountElement.textContent = completedTasks.length;
        }
    }

    async toggleTaskStatus(taskId, newStatus) {
        try {
            console.log('toggleTaskStatus called:', { taskId, newStatus });
            
            const formData = new FormData();
            formData.append('id', taskId);
            formData.append('status', newStatus);

            console.log('Sending request to kanban_task_update.php');
            const response = await fetch('kanban_task_update.php', {
                method: 'POST',
                body: formData
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                console.log('Task status updated successfully');
                // Görevleri yeniden yükle
                await this.loadTasks();
                this.showNotification(data.message, 'success');
            } else {
                console.error('Task update failed:', data.message);
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Görev durumu güncellenirken hata:', error);
            this.showNotification('Bir hata oluştu', 'error');
        }
    }

    showTaskModal() {
        // Eğer modal zaten açıksa, kapat
        const existingModal = document.getElementById('kanbanTaskModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Modal HTML'i oluştur
        const modalHTML = `
            <div id="kanbanTaskModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-xl max-w-md w-full mx-4">
                    <div class="p-6 border-b border-border-light dark:border-border-dark">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Yeni Görev Ekle</h3>
                            <button id="closeModalBtn" class="text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <form id="kanbanTaskForm" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Görev Başlığı *</label>
                            <input type="text" name="title" required 
                                   class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Açıklama</label>
                            <textarea name="description" rows="3" 
                                     class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Öncelik</label>
                                <select name="priority" 
                                       class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary focus:border-primary">
                                    <option value="low">Düşük</option>
                                    <option value="medium" selected>Orta</option>
                                    <option value="high">Yüksek</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-2">Son Tarih</label>
                                <input type="date" name="due_date" 
                                       class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="submit" 
                                   class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                Görev Ekle
                            </button>
                            <button type="button" id="cancelModalBtn" 
                                   class="px-4 py-2 bg-subtle-light/20 rounded-lg hover:bg-subtle-light/30 transition-colors">
                                İptal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        // Modal'ı sayfaya ekle
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Form submit event'i
        document.getElementById('kanbanTaskForm').addEventListener('submit', this.handleTaskSubmit.bind(this));
        
        // Modal kapatma butonları
        document.getElementById('closeModalBtn').addEventListener('click', () => this.closeTaskModal());
        document.getElementById('cancelModalBtn').addEventListener('click', () => this.closeTaskModal());

        // ESC ile kapat
        document.addEventListener('keydown', this.handleEscKey.bind(this));
    }

    closeTaskModal() {
        const modal = document.getElementById('kanbanTaskModal');
        if (modal) {
            // Animasyonlu kapatma
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.remove();
            }, 200);
        }
        document.removeEventListener('keydown', this.handleEscKey.bind(this));
    }

    handleEscKey(e) {
        if (e.key === 'Escape') {
            this.closeTaskModal();
        }
    }

    async handleTaskSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('kanban_task_add.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.closeTaskModal();
                await this.loadTasks();
                this.showNotification('Görev başarıyla eklendi!', 'success');
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Görev eklenirken hata:', error);
            this.showNotification('Görev eklenirken hata oluştu', 'error');
        }
    }

    showNotification(message, type = 'info') {
        // Basit bildirim gösterme (toast notification)
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // 3 saniye sonra kaldır
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('tr-TR');
    }

    formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('tr-TR');
    }
}

// Kanban Board'ı başlat
let kanbanBoard;
document.addEventListener('DOMContentLoaded', () => {
    if (!window.kanbanBoardInstance) {
        kanbanBoard = new KanbanBoard();
        window.kanbanBoardInstance = kanbanBoard;
        window.kanbanBoard = kanbanBoard; // Global access için
    } else {
        kanbanBoard = window.kanbanBoardInstance;
        window.kanbanBoard = kanbanBoard; // Global access için
    }
});

} // Global instance kontrolü sonu
