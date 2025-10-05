// Gemini AI Chat Yönetimi
let geminiContext = 'general';
let conversationHistory = [];

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    initGeminiChat();
});

function initGeminiChat() {
    const chatForm = document.getElementById('geminiChatForm');
    const chatInput = document.getElementById('geminiChatInput');
    
    if (chatForm) {
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (message) {
                await sendGeminiMessage(message);
                chatInput.value = '';
            }
        });
    }
    
    // Context butonları
    const contextButtons = {
        'geminiContextGeneral': 'general',
        'geminiContextPlanner': 'planner',
        'geminiContextNotes': 'notes',
        'geminiContextTasks': 'tasks'
    };
    
    Object.keys(contextButtons).forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.addEventListener('click', () => {
                setGeminiContext(contextButtons[btnId]);
                updateContextButtons(btnId);
            });
        }
    });
}

function setGeminiContext(context) {
    geminiContext = context;
    
    // Context değiştiğinde kullanıcıya bilgi ver
    const contextNames = {
        'general': 'Genel',
        'planner': 'Planlayıcı',
        'notes': 'Notlar',
        'tasks': 'Görevler'
    };
    
    addGeminiMessage('system', `Bağlam "${contextNames[context]}" olarak değiştirildi. Bu alanda size nasıl yardımcı olabilirim?`);
}

function updateContextButtons(activeId) {
    const buttons = ['geminiContextGeneral', 'geminiContextPlanner', 'geminiContextNotes', 'geminiContextTasks'];
    buttons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            if (btnId === activeId) {
                btn.className = 'px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors';
            } else {
                btn.className = 'px-4 py-2 rounded-lg bg-accent-light dark:bg-accent-dark text-foreground-light dark:text-foreground-dark text-sm font-medium hover:bg-primary hover:text-white transition-colors';
            }
        }
    });
}

async function sendGeminiMessage(message) {
    // Kullanıcı mesajını göster
    addGeminiMessage('user', message);
    
    // Yükleniyor göstergesi
    const loadingId = showGeminiLoading();
    
    try {
        const response = await fetch('gemini_chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                context: geminiContext,
                history: conversationHistory
            })
        });
        
        const data = await response.json();
        
        // Yükleniyor göstergesini kaldır
        removeGeminiLoading(loadingId);
        
        if (data.success) {
            // AI yanıtını göster
            addGeminiMessage('ai', data.response);
            
            // Konuşma geçmişine ekle
            conversationHistory.push({
                role: 'user',
                content: message
            });
            conversationHistory.push({
                role: 'assistant',
                content: data.response
            });
            
            // Geçmişi sınırla (son 10 mesaj çifti)
            if (conversationHistory.length > 20) {
                conversationHistory = conversationHistory.slice(-20);
            }
        } else {
            addGeminiMessage('error', data.error || 'Bir hata oluştu. Lütfen tekrar deneyin.');
        }
    } catch (error) {
        removeGeminiLoading(loadingId);
        addGeminiMessage('error', 'Bağlantı hatası. Lütfen internet bağlantınızı kontrol edin ve tekrar deneyin.');
        console.error('Gemini API Error:', error);
    }
}

function addGeminiMessage(type, content) {
    const messagesContainer = document.getElementById('geminiChatMessages');
    if (!messagesContainer) return;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'flex gap-3';
    
    if (type === 'user') {
        messageDiv.className += ' flex-row-reverse';
        messageDiv.innerHTML = `
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="flex-1 bg-primary/10 rounded-xl p-4 max-w-2xl">
                <p class="text-foreground-light dark:text-foreground-dark">${escapeHtml(content)}</p>
            </div>
        `;
    } else if (type === 'ai') {
        messageDiv.innerHTML = `
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                    <path d="M187.58,144.84l-32-80a8,8,0,0,0-15.16,0l-32,80a8,8,0,0,0,15.16,6.06L130.34,136h43.32l6.76,14.9a8,8,0,0,0,15.16-6.06ZM136.34,120,152,80.94,167.66,120Z"></path>
                </svg>
            </div>
            <div class="flex-1 bg-accent-light dark:bg-accent-dark rounded-xl p-4 max-w-3xl">
                <p class="text-sm font-medium text-primary mb-2">Gemini AI</p>
                <div class="text-foreground-light dark:text-foreground-dark prose prose-sm max-w-none">
                    ${formatAIResponse(content)}
                </div>
            </div>
        `;
    } else if (type === 'system') {
        messageDiv.innerHTML = `
            <div class="w-full flex justify-center">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-4 py-2 text-sm text-blue-700 dark:text-blue-300">
                    ${escapeHtml(content)}
                </div>
            </div>
        `;
    } else if (type === 'error') {
        messageDiv.innerHTML = `
            <div class="w-full flex justify-center">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-4 py-2 text-sm text-red-700 dark:text-red-300">
                    ⚠️ ${escapeHtml(content)}
                </div>
            </div>
        `;
    }
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function showGeminiLoading() {
    const messagesContainer = document.getElementById('geminiChatMessages');
    if (!messagesContainer) return null;
    
    const loadingId = 'loading-' + Date.now();
    const loadingDiv = document.createElement('div');
    loadingDiv.id = loadingId;
    loadingDiv.className = 'flex gap-3';
    loadingDiv.innerHTML = `
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </div>
        <div class="flex-1 bg-accent-light dark:bg-accent-dark rounded-xl p-4 max-w-3xl">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 300ms"></div>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(loadingDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    return loadingId;
}

function removeGeminiLoading(loadingId) {
    if (loadingId) {
        const loadingDiv = document.getElementById(loadingId);
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }
}

function formatAIResponse(text) {
    // Markdown benzeri formatlamalar
    let formatted = escapeHtml(text);
    
    // Başlıklar (### Başlık)
    formatted = formatted.replace(/###\s+(.+?)(\n|$)/g, '<h3 class="font-bold text-lg mt-4 mb-2">$1</h3>');
    formatted = formatted.replace(/##\s+(.+?)(\n|$)/g, '<h2 class="font-bold text-xl mt-4 mb-2">$1</h2>');
    
    // Bold (**text**)
    formatted = formatted.replace(/\*\*(.+?)\*\*/g, '<strong class="font-bold">$1</strong>');
    
    // Italic (*text*)
    formatted = formatted.replace(/\*(.+?)\*/g, '<em class="italic">$1</em>');
    
    // Liste öğeleri (- item veya * item)
    formatted = formatted.replace(/^[-*]\s+(.+?)$/gm, '<li class="ml-4">• $1</li>');
    
    // Numaralı liste (1. item)
    formatted = formatted.replace(/^\d+\.\s+(.+?)$/gm, '<li class="ml-4">$1</li>');
    
    // Satır sonları
    formatted = formatted.replace(/\n\n/g, '</p><p class="mb-2">');
    formatted = formatted.replace(/\n/g, '<br>');
    
    // Kod blokları (`code`)
    formatted = formatted.replace(/`(.+?)`/g, '<code class="bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-sm font-mono">$1</code>');
    
    return '<p class="mb-2">' + formatted + '</p>';
}

// Hızlı prompt doldurma
function fillGeminiPrompt(prompt) {
    const input = document.getElementById('geminiChatInput');
    if (input) {
        input.value = prompt;
        input.focus();
    }
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Planlayıcı için Gemini yardımcısını aç
function openGeminiPlannerHelper() {
    // Gemini bölümüne geç
    const geminiLink = document.querySelector('.nav-link[data-section="gemini"]');
    if (geminiLink) {
        geminiLink.click();
    }
    
    // Context'i planlayıcı olarak ayarla
    setTimeout(() => {
        setGeminiContext('planner');
        updateContextButtons('geminiContextPlanner');
        
        // Örnek bir prompt öner
        const input = document.getElementById('geminiChatInput');
        if (input) {
            input.value = 'Bir proje için detaylı bir eylem planı oluşturmama yardım et. Adım adım ne yapmam gerektiğini belirt.';
            input.focus();
        }
    }, 100);
}
