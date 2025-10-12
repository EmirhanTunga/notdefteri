// Gelen Kutusu JavaScript

// Bildirimleri yükle
async function loadNotifications() {
    try {
        const response = await fetch('notification_list.php');
        const data = await response.json();
        
        const notificationsList = document.getElementById('notificationsList');
        const inboxBadge = document.getElementById('inboxBadge');
        
        // Badge güncelle
        if (data.unread_count > 0) {
            inboxBadge.textContent = data.unread_count;
            inboxBadge.classList.remove('hidden');
        } else {
            inboxBadge.classList.add('hidden');
        }
        
        if (!notificationsList) return;
        
        if (data.notifications && data.notifications.length > 0) {
            notificationsList.innerHTML = data.notifications.map(notif => {
                const typeIcons = {
                    friend_request: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>',
                    group_invite: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>',
                    task_assigned: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>',
                    system: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                };
                
                return `
                    <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-4 ${notif.is_read ? 'opacity-60' : ''} hover:shadow-lg transition-shadow cursor-pointer" onclick="markAsRead(${notif.id})">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    ${typeIcons[notif.type] || typeIcons.system}
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-semibold ${!notif.is_read ? 'text-primary' : ''}">${escapeHtml(notif.title)}</h3>
                                    <span class="text-xs text-subtle-light dark:text-subtle-dark whitespace-nowrap">${formatNotificationDate(notif.created_at)}</span>
                                </div>
                                ${notif.message ? `<p class="text-sm text-subtle-light dark:text-subtle-dark mt-1">${escapeHtml(notif.message)}</p>` : ''}
                                ${!notif.is_read ? '<span class="inline-block mt-2 w-2 h-2 bg-primary rounded-full"></span>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            notificationsList.innerHTML = `
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-subtle-light dark:text-subtle-dark mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-subtle-light dark:text-subtle-dark">Henüz bildiriminiz yok</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Bildirimler yüklenirken hata:', error);
    }
}

// Bildirimi okundu işaretle
async function markAsRead(notificationId) {
    try {
        const response = await fetch('notification_mark_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({notification_id: notificationId})
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadNotifications();
        }
    } catch (error) {
        console.error('Bildirim işaretlenirken hata:', error);
    }
}

// Tümünü okundu işaretle
async function markAllAsRead() {
    try {
        const response = await fetch('notification_mark_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({})
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadNotifications();
        }
    } catch (error) {
        console.error('Bildirimler işaretlenirken hata:', error);
    }
}

// Tarih formatla
function formatNotificationDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Şimdi';
    if (diffMins < 60) return `${diffMins} dk önce`;
    if (diffHours < 24) return `${diffHours} saat önce`;
    if (diffDays < 7) return `${diffDays} gün önce`;
    
    return date.toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' });
}

// HTML escape
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Gelen kutusu sekmesi açıldığında yükle
    const inboxLink = document.querySelector('[data-section="inbox"]');
    if (inboxLink) {
        inboxLink.addEventListener('click', function() {
            setTimeout(loadNotifications, 100);
        });
    }
    
    // Bildirimleri periyodik olarak kontrol et
    setInterval(loadNotifications, 60000); // Her 1 dakikada bir
    
    // İlk yüklemede badge'i güncelle
    loadNotifications();
});
