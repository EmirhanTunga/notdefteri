// Arkadaşlık Sistemi JavaScript

let userFriends = [];

// Arkadaşları yükle
async function loadFriends() {
    try {
        const response = await fetch('friend_list.php');
        const data = await response.json();
        
        userFriends = data.friends || [];
        const requests = data.requests || [];
        const sent = data.sent || [];
        
        // Arkadaşları göster
        renderFriendsList(userFriends);
        
        // Gelen istekleri göster
        if (requests.length > 0) {
            document.getElementById('friendRequestsSection').style.display = 'block';
            renderFriendRequests(requests);
        } else {
            document.getElementById('friendRequestsSection').style.display = 'none';
        }
    } catch (error) {
        console.error('Arkadaşlar yüklenirken hata:', error);
    }
}

// Arkadaşları render et
function renderFriendsList(friends) {
    const friendsList = document.getElementById('friendsList');
    
    if (friends.length === 0) {
        friendsList.innerHTML = `
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 mx-auto text-subtle-light dark:text-subtle-dark mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-subtle-light dark:text-subtle-dark">Henüz arkadaşınız yok. Arkadaş ekleyin!</p>
            </div>
        `;
        return;
    }
    
    friendsList.innerHTML = friends.map(friend => `
        <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xl">
                    ${friend.username.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold">${escapeHtml(friend.username)}</h3>
                    <p class="text-sm text-subtle-light dark:text-subtle-dark">${escapeHtml(friend.email || '')}</p>
                </div>
            </div>
        </div>
    `).join('');
}

// Arkadaşlık isteklerini render et
function renderFriendRequests(requests) {
    const requestsList = document.getElementById('friendRequestsList');
    
    requestsList.innerHTML = requests.map(request => `
        <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xl">
                    ${request.username.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold">${escapeHtml(request.username)}</h3>
                    <p class="text-xs text-subtle-light dark:text-subtle-dark">${new Date(request.requested_at).toLocaleDateString('tr-TR')}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="respondToFriendRequest(${request.request_id}, 'accept')" class="flex-1 px-3 py-2 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600">
                    Kabul Et
                </button>
                <button onclick="respondToFriendRequest(${request.request_id}, 'reject')" class="flex-1 px-3 py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600">
                    Reddet
                </button>
            </div>
        </div>
    `).join('');
}

// Arkadaşlık isteğine yanıt ver
async function respondToFriendRequest(requestId, action) {
    try {
        const response = await fetch('friend_respond.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({request_id: requestId, action})
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadFriends();
        } else {
            alert(data.error || 'İşlem başarısız');
        }
    } catch (error) {
        console.error('Yanıt verilirken hata:', error);
    }
}

// Arkadaş ekleme modalını aç
function openAddFriendModal() {
    const modal = document.createElement('div');
    modal.id = 'addFriendModal';
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h2 class="text-2xl font-bold mb-4">Arkadaş Ekle</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Kullanıcı Adı</label>
                    <input type="text" id="friendUsername" class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark" placeholder="Kullanıcı adını girin">
                </div>
                <div class="flex gap-2">
                    <button onclick="sendFriendRequest()" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">İstek Gönder</button>
                    <button onclick="closeAddFriendModal()" class="px-4 py-2 bg-subtle-light/20 rounded-lg">İptal</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    document.getElementById('friendUsername').focus();
}

function closeAddFriendModal() {
    const modal = document.getElementById('addFriendModal');
    if (modal) modal.remove();
}

// Arkadaşlık isteği gönder
async function sendFriendRequest() {
    const username = document.getElementById('friendUsername').value.trim();
    
    if (!username) {
        alert('Kullanıcı adı gerekli');
        return;
    }
    
    try {
        const response = await fetch('friend_request.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({username})
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeAddFriendModal();
            alert('Arkadaşlık isteği gönderildi');
            loadFriends();
        } else {
            alert(data.error || 'İstek gönderilemedi');
        }
    } catch (error) {
        console.error('İstek gönderilirken hata:', error);
        alert('Bir hata oluştu');
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
    const addFriendBtn = document.getElementById('addFriendBtn');
    if (addFriendBtn) {
        addFriendBtn.addEventListener('click', openAddFriendModal);
    }
    
    // Arkadaşlar sekmesi açıldığında yükle
    const friendsLink = document.querySelector('[data-section="friends"]');
    if (friendsLink) {
        friendsLink.addEventListener('click', function() {
            setTimeout(loadFriends, 100);
        });
    }
});
