// Etiket Sistemi JavaScript

// Etiketleri yükle
async function loadTags() {
    try {
        const response = await fetch('tag_list.php');
        const data = await response.json();
        
        const tagsList = document.getElementById('tagsList');
        if (!tagsList) return;
        
        if (data.tags && data.tags.length > 0) {
            tagsList.innerHTML = data.tags.map(tag => `
                <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="w-4 h-4 rounded-full" style="background-color: ${tag.color}"></div>
                            <h3 class="text-lg font-semibold">${escapeHtml(tag.name)}</h3>
                        </div>
                        <button onclick="deleteTag(${tag.id})" class="text-red-500 hover:text-red-600 p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm text-subtle-light dark:text-subtle-dark">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span>${tag.note_count} not</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>${formatDate(tag.created_at)}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            tagsList.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-subtle-light dark:text-subtle-dark mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <p class="text-subtle-light dark:text-subtle-dark">Henüz etiket oluşturmadınız. Yeni etiket ekleyin!</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Etiketler yüklenirken hata:', error);
    }
}

// Etiket oluşturma modalını aç
function openCreateTagModal() {
    const modal = document.createElement('div');
    modal.id = 'createTagModal';
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h2 class="text-2xl font-bold mb-4">Yeni Etiket Oluştur</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Etiket Adı</label>
                    <input type="text" id="newTagName" class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark" placeholder="Örn: İş, Kişisel, Önemli">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Renk</label>
                    <div class="flex gap-2 flex-wrap">
                        <button type="button" onclick="selectTagColor('#4a90e2')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-primary transition-colors" style="background-color: #4a90e2" data-color="#4a90e2"></button>
                        <button type="button" onclick="selectTagColor('#50e3c2')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-primary transition-colors" style="background-color: #50e3c2" data-color="#50e3c2"></button>
                        <button type="button" onclick="selectTagColor('#f5a623')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-primary transition-colors" style="background-color: #f5a623" data-color="#f5a623"></button>
                        <button type="button" onclick="selectTagColor('#e74c3c')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-primary transition-colors" style="background-color: #e74c3c" data-color="#e74c3c"></button>
                        <button type="button" onclick="selectTagColor('#9b59b6')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-primary transition-colors" style="background-color: #9b59b6" data-color="#9b59b6"></button>
                        <button type="button" onclick="selectTagColor('#2ecc71')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-primary transition-colors" style="background-color: #2ecc71" data-color="#2ecc71"></button>
                        <button type="button" onclick="selectTagColor('#34495e')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-primary transition-colors" style="background-color: #34495e" data-color="#34495e"></button>
                        <button type="button" onclick="selectTagColor('#e91e63')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-primary transition-colors" style="background-color: #e91e63" data-color="#e91e63"></button>
                    </div>
                    <input type="hidden" id="selectedTagColor" value="#4a90e2">
                </div>
                <div class="flex gap-2">
                    <button onclick="createTag()" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">Oluştur</button>
                    <button onclick="closeCreateTagModal()" class="px-4 py-2 bg-subtle-light/20 rounded-lg">İptal</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    document.getElementById('newTagName').focus();
    
    // İlk rengi seçili yap
    document.querySelector('[data-color="#4a90e2"]').classList.add('border-primary');
}

function closeCreateTagModal() {
    const modal = document.getElementById('createTagModal');
    if (modal) modal.remove();
}

// Renk seçimi
function selectTagColor(color) {
    document.getElementById('selectedTagColor').value = color;
    
    // Tüm renk butonlarının border'ını kaldır
    document.querySelectorAll('[data-color]').forEach(btn => {
        btn.classList.remove('border-primary');
    });
    
    // Seçili rengin border'ını ekle
    document.querySelector(`[data-color="${color}"]`).classList.add('border-primary');
}

// Etiket oluştur
async function createTag() {
    const name = document.getElementById('newTagName').value.trim();
    const color = document.getElementById('selectedTagColor').value;
    
    if (!name) {
        alert('Etiket adı gerekli');
        return;
    }
    
    try {
        const response = await fetch('tag_create.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({name, color})
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeCreateTagModal();
            loadTags();
        } else {
            alert(data.error || 'Etiket oluşturulamadı');
        }
    } catch (error) {
        console.error('Etiket oluşturulurken hata:', error);
        alert('Bir hata oluştu');
    }
}

// Etiket sil
async function deleteTag(tagId) {
    if (!confirm('Bu etiketi silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    try {
        const response = await fetch('tag_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({tag_id: tagId})
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadTags();
        } else {
            alert(data.error || 'Etiket silinemedi');
        }
    } catch (error) {
        console.error('Etiket silinirken hata:', error);
        alert('Bir hata oluştu');
    }
}

// Tarih formatla
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        return 'Bugün';
    } else if (diffDays === 1) {
        return 'Dün';
    } else if (diffDays < 7) {
        return `${diffDays} gün önce`;
    } else {
        return date.toLocaleDateString('tr-TR', { day: 'numeric', month: 'short', year: 'numeric' });
    }
}

// HTML escape
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    const createTagBtn = document.getElementById('createTagBtn');
    if (createTagBtn) {
        createTagBtn.addEventListener('click', openCreateTagModal);
    }
    
    // Etiketler sekmesi açıldığında yükle
    const tagsLink = document.querySelector('[data-section="tags"]');
    if (tagsLink) {
        tagsLink.addEventListener('click', function() {
            setTimeout(loadTags, 100);
        });
    }
});
