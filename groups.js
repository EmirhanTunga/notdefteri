// Grup Çalışması JavaScript

let currentGroupId = null;
let currentGroupMembers = [];

// Grupları yükle
async function loadGroups() {
    try {
        const response = await fetch('group_list.php');
        const data = await response.json();
        
        const groupsList = document.getElementById('groupsList');
        if (!groupsList) return;
        
        if (data.groups && data.groups.length > 0) {
            groupsList.innerHTML = data.groups.map(group => `
                <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="openGroupDetail(${group.id})">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-xl font-bold">${escapeHtml(group.name)}</h3>
                        <span class="px-2 py-1 text-xs rounded-full ${group.role === 'admin' ? 'bg-primary/20 text-primary' : 'bg-subtle-light/20 text-subtle-light dark:text-subtle-dark'}">
                            ${group.role === 'admin' ? 'Admin' : 'Üye'}
                        </span>
                    </div>
                    <p class="text-sm text-subtle-light dark:text-subtle-dark mb-4 line-clamp-2">${escapeHtml(group.description || 'Açıklama yok')}</p>
                    <div class="flex items-center gap-4 text-sm text-subtle-light dark:text-subtle-dark">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>${group.member_count} üye</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span>${group.task_count} görev</span>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            groupsList.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-subtle-light dark:text-subtle-dark mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-subtle-light dark:text-subtle-dark">Henüz hiç grubunuz yok. Yeni bir grup oluşturun!</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Gruplar yüklenirken hata:', error);
    }
}

// Yeni grup oluştur
async function createGroup() {
    const name = document.getElementById('newGroupName').value.trim();
    const description = document.getElementById('newGroupDescription').value.trim();
    
    if (!name) {
        alert('Grup adı gerekli');
        return;
    }
    
    try {
        const response = await fetch('group_create.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({name, description})
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('newGroupName').value = '';
            document.getElementById('newGroupDescription').value = '';
            closeCreateGroupModal();
            loadGroups();
        } else {
            alert(data.error || 'Grup oluşturulamadı');
        }
    } catch (error) {
        console.error('Grup oluşturulurken hata:', error);
        alert('Bir hata oluştu');
    }
}

// Grup detayını aç
async function openGroupDetail(groupId) {
    currentGroupId = groupId;
    
    try {
        // Grup üyelerini yükle
        const membersResponse = await fetch(`group_members.php?group_id=${groupId}`);
        const membersData = await membersResponse.json();
        currentGroupMembers = membersData.members || [];
        
        // Görevleri yükle
        const tasksResponse = await fetch(`group_task_list.php?group_id=${groupId}`);
        const tasksData = await tasksResponse.json();
        
        // Grup bilgilerini göster
        const groupsList = document.getElementById('groupsList');
        const groupCard = groupsList.querySelector(`[onclick="openGroupDetail(${groupId})"]`);
        if (groupCard) {
            const groupName = groupCard.querySelector('h3').textContent;
            const groupDesc = groupCard.querySelector('p').textContent;
            document.getElementById('groupDetailName').textContent = groupName;
            document.getElementById('groupDetailDescription').textContent = groupDesc;
        }
        
        // Üyeleri göster
        renderGroupMembers(currentGroupMembers);
        
        // Görevleri göster
        renderGroupTasks(tasksData.tasks || []);
        
        // Görev atama seçeneklerini güncelle
        updateTaskAssigneeOptions();
        
        // Modalı göster
        document.getElementById('groupDetailModal').classList.remove('hidden');
    } catch (error) {
        console.error('Grup detayı yüklenirken hata:', error);
    }
}

// Grup detayını kapat
function closeGroupDetail() {
    document.getElementById('groupDetailModal').classList.add('hidden');
    currentGroupId = null;
    currentGroupMembers = [];
}

// Üyeleri render et
function renderGroupMembers(members) {
    const membersList = document.getElementById('groupMembersList');
    const memberCountBadge = document.getElementById('memberCountBadge');
    
    if (memberCountBadge) {
        memberCountBadge.textContent = `(${members.length}/5)`;
    }
    
    membersList.innerHTML = members.map(member => `
        <div class="flex items-center gap-3 p-3 bg-accent-light dark:bg-accent-dark rounded-lg">
            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">
                ${member.username.charAt(0).toUpperCase()}
            </div>
            <div class="flex-1">
                <div class="font-medium">${escapeHtml(member.username)}</div>
                <div class="text-xs text-subtle-light dark:text-subtle-dark">${member.role === 'admin' ? 'Admin' : 'Üye'}</div>
            </div>
        </div>
    `).join('');
}

// Görevleri render et
function renderGroupTasks(tasks) {
    const tasksList = document.getElementById('groupTasksList');
    
    if (tasks.length === 0) {
        tasksList.innerHTML = `
            <div class="text-center py-8 text-subtle-light dark:text-subtle-dark">
                Henüz görev yok. Yeni görev oluşturun!
            </div>
        `;
        return;
    }
    
    tasksList.innerHTML = tasks.map(task => {
        const priorityColors = {
            low: 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
            medium: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
            high: 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'
        };
        
        const statusColors = {
            todo: 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200',
            in_progress: 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
            completed: 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
        };
        
        const statusLabels = {
            todo: 'Yapılacak',
            in_progress: 'Devam Ediyor',
            completed: 'Tamamlandı'
        };
        
        return `
            <div class="p-4 bg-accent-light dark:bg-accent-dark rounded-lg">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="font-semibold flex-1">${escapeHtml(task.title)}</h4>
                    <span class="px-2 py-1 text-xs rounded-full ${priorityColors[task.priority]}">
                        ${task.priority === 'high' ? 'Yüksek' : task.priority === 'medium' ? 'Orta' : 'Düşük'}
                    </span>
                </div>
                ${task.description ? `<p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">${escapeHtml(task.description)}</p>` : ''}
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded-full ${statusColors[task.status]}">
                            ${statusLabels[task.status]}
                        </span>
                        ${task.assigned_to_username ? `<span class="text-subtle-light dark:text-subtle-dark">→ ${escapeHtml(task.assigned_to_username)}</span>` : ''}
                    </div>
                    <select onchange="updateTaskStatus(${task.id}, this.value)" class="px-2 py-1 text-xs rounded bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark">
                        <option value="todo" ${task.status === 'todo' ? 'selected' : ''}>Yapılacak</option>
                        <option value="in_progress" ${task.status === 'in_progress' ? 'selected' : ''}>Devam Ediyor</option>
                        <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Tamamlandı</option>
                    </select>
                </div>
                ${task.due_date ? `<div class="text-xs text-subtle-light dark:text-subtle-dark mt-2">Bitiş: ${new Date(task.due_date).toLocaleDateString('tr-TR')}</div>` : ''}
            </div>
        `;
    }).join('');
}

// Görev durumunu güncelle
async function updateTaskStatus(taskId, status) {
    try {
        const response = await fetch('group_task_update.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({task_id: taskId, status})
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Görevleri yeniden yükle
            const tasksResponse = await fetch(`group_task_list.php?group_id=${currentGroupId}`);
            const tasksData = await tasksResponse.json();
            renderGroupTasks(tasksData.tasks || []);
        } else {
            alert(data.error || 'Görev güncellenemedi');
        }
    } catch (error) {
        console.error('Görev güncellenirken hata:', error);
    }
}

// Üye ekleme formunu göster
async function showAddMemberForm() {
    // Üye sayısı kontrolü
    if (currentGroupMembers.length >= 5) {
        alert('Grup maksimum 5 üye alabilir');
        return;
    }
    
    // Arkadaşları yükle
    try {
        const response = await fetch('friend_list.php');
        const data = await response.json();
        const friends = data.friends || [];
        
        // Zaten grupta olan arkadaşları filtrele
        const groupMemberIds = currentGroupMembers.map(m => m.id);
        const availableFriends = friends.filter(f => !groupMemberIds.includes(f.id));
        
        const friendSelector = document.getElementById('friendSelector');
        friendSelector.innerHTML = '<option value="">Arkadaş seçin...</option>' + 
            availableFriends.map(friend => 
                `<option value="${friend.id}">${escapeHtml(friend.username)}</option>`
            ).join('');
        
        if (availableFriends.length === 0) {
            alert('Gruba eklenebilecek arkadaşınız yok');
            return;
        }
        
        document.getElementById('addMemberForm').classList.remove('hidden');
    } catch (error) {
        console.error('Arkadaşlar yüklenirken hata:', error);
        alert('Arkadaşlar yüklenemedi');
    }
}

function hideAddMemberForm() {
    document.getElementById('addMemberForm').classList.add('hidden');
    document.getElementById('friendSelector').value = '';
}

// Arkadaştan gruba üye ekle
async function addGroupMemberFromFriend() {
    const userId = document.getElementById('friendSelector').value;
    
    if (!userId) {
        alert('Lütfen bir arkadaş seçin');
        return;
    }
    
    try {
        const response = await fetch('group_add_member.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({group_id: currentGroupId, user_id: parseInt(userId)})
        });
        
        const data = await response.json();
        
        if (data.success) {
            hideAddMemberForm();
            // Üyeleri yeniden yükle
            const membersResponse = await fetch(`group_members.php?group_id=${currentGroupId}`);
            const membersData = await membersResponse.json();
            currentGroupMembers = membersData.members || [];
            renderGroupMembers(currentGroupMembers);
            updateTaskAssigneeOptions();
        } else {
            alert(data.error || 'Üye eklenemedi');
        }
    } catch (error) {
        console.error('Üye eklenirken hata:', error);
        alert('Bir hata oluştu');
    }
}

// Görev oluşturma formunu göster
function showCreateTaskForm() {
    document.getElementById('createTaskForm').classList.remove('hidden');
}

function hideCreateTaskForm() {
    document.getElementById('createTaskForm').classList.add('hidden');
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskDescription').value = '';
    document.getElementById('taskAssignee').value = '';
    document.getElementById('taskPriority').value = 'medium';
    document.getElementById('taskDueDate').value = '';
}

// Görev atama seçeneklerini güncelle
function updateTaskAssigneeOptions() {
    const select = document.getElementById('taskAssignee');
    select.innerHTML = '<option value="">Atanacak kişi seç</option>' + 
        currentGroupMembers.map(member => 
            `<option value="${member.id}">${escapeHtml(member.username)}</option>`
        ).join('');
}

// Grup görevi oluştur
async function createGroupTask() {
    const title = document.getElementById('taskTitle').value.trim();
    const description = document.getElementById('taskDescription').value.trim();
    const assignedTo = document.getElementById('taskAssignee').value;
    const priority = document.getElementById('taskPriority').value;
    const dueDate = document.getElementById('taskDueDate').value;
    
    if (!title) {
        alert('Görev başlığı gerekli');
        return;
    }
    
    try {
        const response = await fetch('group_task_create.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                group_id: currentGroupId,
                title,
                description,
                assigned_to: assignedTo || null,
                priority,
                due_date: dueDate || null
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            hideCreateTaskForm();
            // Görevleri yeniden yükle
            const tasksResponse = await fetch(`group_task_list.php?group_id=${currentGroupId}`);
            const tasksData = await tasksResponse.json();
            renderGroupTasks(tasksData.tasks || []);
        } else {
            alert(data.error || 'Görev oluşturulamadı');
        }
    } catch (error) {
        console.error('Görev oluşturulurken hata:', error);
    }
}

// Grup oluşturma modalını aç
function openCreateGroupModal() {
    const modal = document.createElement('div');
    modal.id = 'createGroupModal';
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h2 class="text-2xl font-bold mb-4">Yeni Grup Oluştur</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Grup Adı</label>
                    <input type="text" id="newGroupName" class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark" placeholder="Örn: Proje Ekibi">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Açıklama</label>
                    <textarea id="newGroupDescription" rows="3" class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark" placeholder="Grup hakkında kısa açıklama"></textarea>
                </div>
                <div class="flex gap-2">
                    <button onclick="createGroup()" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">Oluştur</button>
                    <button onclick="closeCreateGroupModal()" class="px-4 py-2 bg-subtle-light/20 rounded-lg">İptal</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeCreateGroupModal() {
    const modal = document.getElementById('createGroupModal');
    if (modal) modal.remove();
}

// HTML escape
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    const createGroupBtn = document.getElementById('createGroupBtn');
    if (createGroupBtn) {
        createGroupBtn.addEventListener('click', openCreateGroupModal);
    }
    
    // Gruplar sekmesi açıldığında yükle
    const groupsLink = document.querySelector('[data-section="groups"]');
    if (groupsLink) {
        groupsLink.addEventListener('click', function() {
            setTimeout(loadGroups, 100);
        });
    }
});
