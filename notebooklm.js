// NotebookLM Integration
let currentFile = null;

// Dosya yükleme işlemi
function handleNotebookFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    currentFile = file;
    const fileInfo = document.getElementById('notebookFileInfo');
    fileInfo.innerHTML = `
        <div class="flex items-center justify-center gap-2 text-green-600 dark:text-green-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${file.name} (${(file.size / 1024).toFixed(2)} KB) seçildi</span>
        </div>
    `;
}

// NotebookLM ile işleme
async function processWithNotebookLM() {
    if (!currentFile) {
        showToast('Lütfen önce bir dosya seçin', 'error');
        return;
    }
    
    const fileInfo = document.getElementById('notebookFileInfo');
    fileInfo.innerHTML = `
        <div class="flex items-center justify-center gap-2 text-blue-600 dark:text-blue-400">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Dosya işleniyor...</span>
        </div>
    `;
    
    try {
        // Dosyayı oku
        const fileContent = await readFileContent(currentFile);
        
        // İçeriği küçük parçalara böl
        const contentChunks = splitContent(fileContent, 2000);
        
        // Gemini API ile özet oluştur
        const summary = await generateSummary(contentChunks[0]);
        
        // Şablon oluştur
        const template = await generateTemplate(contentChunks[0], summary);
        
        // Öneriler oluştur
        const suggestions = await generateSuggestions(contentChunks[0], summary);
        
        // Sonuçları göster
        document.getElementById('notebookSummary').innerHTML = `
            <div class="prose dark:prose-invert max-w-none">
                ${summary}
            </div>
        `;
        
        document.getElementById('notebookTemplate').innerHTML = template;
        
        // Önerileri göster
        document.getElementById('notebookSuggestions').innerHTML = suggestions;
        
        // İşlem geçmişine ekle
        addToHistory(currentFile.name, summary, template);
        
        fileInfo.innerHTML = `
            <div class="flex items-center justify-center gap-2 text-green-600 dark:text-green-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Dosya başarıyla işlendi!</span>
            </div>
        `;
        
        showToast('Dosya başarıyla işlendi!', 'success');
        
    } catch (error) {
        console.error('İşlem hatası:', error);
        fileInfo.innerHTML = `
            <div class="flex items-center justify-center gap-2 text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Hata: ${error.message}</span>
            </div>
        `;
        showToast('Dosya işlenirken hata oluştu: ' + error.message, 'error');
    }
}

// Dosya içeriğini oku
function readFileContent(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            resolve(e.target.result);
        };
        
        reader.onerror = function(e) {
            reject(new Error('Dosya okunamadı'));
        };
        
        // Dosya tipine göre okuma
        if (file.type === 'text/plain') {
            reader.readAsText(file);
        } else if (file.type === 'application/pdf') {
            reader.readAsDataURL(file);
        } else {
            reader.readAsText(file); // Diğer dosya tipleri için varsayılan
        }
    });
}

// İçeriği küçük parçalara böl
function splitContent(content, maxLength) {
    if (content.length <= maxLength) {
        return [content];
    }
    
    const chunks = [];
    let currentChunk = '';
    const lines = content.split('\n');
    
    for (const line of lines) {
        if (currentChunk.length + line.length > maxLength && currentChunk.length > 0) {
            chunks.push(currentChunk.trim());
            currentChunk = line;
        } else {
            currentChunk += (currentChunk ? '\n' : '') + line;
        }
    }
    
    if (currentChunk.trim()) {
        chunks.push(currentChunk.trim());
    }
    
    return chunks;
}

// Gemini API ile özet oluştur
async function generateSummary(content) {
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 45000); // 45 saniye timeout
        
        const response = await fetch('gemini_chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message: `Aşağıdaki metni özetle. Net, anlaşılır ve yapılandırılmış bir özet oluştur:\n\n${content.substring(0, 3000)}`,
                history: [],
                context: 'notes'
            }),
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Özet oluşturulamadı');
        }
        
        return data.response;
        
    } catch (error) {
        console.error('Özet oluşturma hatası:', error);
        if (error.name === 'AbortError') {
            return `<p class="text-red-500">Özet oluşturma zaman aşımına uğradı. Lütfen tekrar deneyin.</p>`;
        }
        return `<p class="text-red-500">Özet oluşturulamadı: ${error.message}</p>`;
    }
}

// Şablon oluştur (Görsel Tablo Formatında)
async function generateTemplate(content, summary) {
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 45000); // 45 saniye timeout
        
        const response = await fetch('gemini_chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message: `Aşağıdaki metin ve özete dayanarak, HTML tablo formatında görsel bir şablon oluştur. Tablolar, kartlar ve renkli kutular kullanarak bilgileri organize et. Her bölüm için farklı renkler ve ikonlar kullan. Responsive tasarım için Tailwind CSS sınıfları kullan:\n\nMETİN:\n${content.substring(0, 2000)}\n\nÖZET:\n${summary}`,
                history: [],
                context: 'notes'
            }),
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Şablon oluşturulamadı');
        }
        
        // Eğer HTML formatında değilse, basit bir tablo oluştur
        if (!data.response.includes('<table') && !data.response.includes('<div class=')) {
            return createVisualTemplate(content, summary);
        }
        
        return data.response;
        
    } catch (error) {
        console.error('Şablon oluşturma hatası:', error);
        if (error.name === 'AbortError') {
            return createVisualTemplate(content, summary);
        }
        return createVisualTemplate(content, summary);
    }
}

// Öneriler oluştur
async function generateSuggestions(content, summary) {
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 45000); // 45 saniye timeout
        
        const response = await fetch('gemini_chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message: `Aşağıdaki dosya içeriği ve özetine dayanarak, kullanıcıya 5-7 adet pratik öneri ver. Her öneri kısa, net ve uygulanabilir olsun. Öneriler şu kategorilerde olabilir: öğrenme, uygulama, geliştirme, takip, organizasyon:\n\nMETİN:\n${content.substring(0, 1500)}\n\nÖZET:\n${summary}`,
                history: [],
                context: 'notes'
            }),
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Öneriler oluşturulamadı');
        }
        
        return formatSuggestions(data.response);
        
    } catch (error) {
        console.error('Öneriler oluşturma hatası:', error);
        if (error.name === 'AbortError') {
            return formatSuggestions('Öneriler oluşturma zaman aşımına uğradı. Lütfen tekrar deneyin.');
        }
        return formatSuggestions('Öneriler oluşturulamadı: ' + error.message);
    }
}

// Görsel şablon oluştur
function createVisualTemplate(content, summary) {
    const lines = content.split('\n').filter(line => line.trim().length > 0);
    const sections = [];
    
    // İçeriği bölümlere ayır
    let currentSection = { title: 'Genel Bilgiler', items: [] };
    
    lines.forEach(line => {
        const trimmedLine = line.trim();
        if (trimmedLine.length > 50 && (trimmedLine.includes(':') || trimmedLine.includes('•') || trimmedLine.includes('-'))) {
            if (currentSection.items.length > 0) {
                sections.push(currentSection);
            }
            currentSection = { title: trimmedLine.substring(0, 50) + '...', items: [] };
        } else if (trimmedLine.length > 10) {
            currentSection.items.push(trimmedLine);
        }
    });
    
    if (currentSection.items.length > 0) {
        sections.push(currentSection);
    }
    
    // HTML tablo oluştur
    let html = '<div class="space-y-4">';
    
    sections.forEach((section, index) => {
        const colors = [
            'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
            'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
            'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800',
            'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800',
            'bg-pink-50 dark:bg-pink-900/20 border-pink-200 dark:border-pink-800'
        ];
        
        const icons = [
            '📋', '📝', '📊', '💡', '🎯', '📈', '🔍', '⭐'
        ];
        
        html += `
            <div class="rounded-lg border-2 ${colors[index % colors.length]} p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">${icons[index % icons.length]}</span>
                    <h3 class="font-semibold text-lg">${section.title}</h3>
                </div>
                <div class="space-y-2">
        `;
        
        section.items.slice(0, 5).forEach(item => {
            html += `
                <div class="flex items-start gap-2">
                    <span class="text-blue-500 mt-1">•</span>
                    <span class="text-sm">${item}</span>
                </div>
            `;
        });
        
        if (section.items.length > 5) {
            html += `<div class="text-xs text-gray-500 mt-2">+${section.items.length - 5} daha...</div>`;
        }
        
        html += `
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

// Önerileri formatla
function formatSuggestions(suggestionsText) {
    const suggestions = suggestionsText.split('\n').filter(line => 
        line.trim().length > 0 && 
        (line.includes('•') || line.includes('-') || line.includes('1.') || line.includes('2.'))
    );
    
    if (suggestions.length === 0) {
        return '<p class="text-sm text-gray-500 italic">Öneriler oluşturulamadı</p>';
    }
    
    let html = '<div class="space-y-3">';
    
    suggestions.slice(0, 6).forEach((suggestion, index) => {
        const colors = [
            'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200',
            'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200',
            'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200',
            'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200',
            'bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-200',
            'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200'
        ];
        
        const icons = ['💡', '🎯', '📚', '⚡', '🔧', '📈'];
        
        const cleanSuggestion = suggestion.replace(/^[•\-\d+\.\s]+/, '').trim();
        
        html += `
            <div class="flex items-start gap-3 p-3 rounded-lg ${colors[index % colors.length]}">
                <span class="text-lg mt-0.5">${icons[index % icons.length]}</span>
                <span class="text-sm font-medium">${cleanSuggestion}</span>
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

// İşlem geçmişine ekle
function addToHistory(fileName, summary, template) {
    const historyContainer = document.getElementById('notebookHistory');
    const timestamp = new Date().toLocaleString('tr-TR');
    
    const historyItem = document.createElement('div');
    historyItem.className = 'bg-accent-light dark:bg-accent-dark rounded-lg p-4 border border-border-light dark:border-border-dark';
    historyItem.innerHTML = `
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h4 class="font-semibold">${fileName}</h4>
                </div>
                <p class="text-sm text-subtle-light dark:text-subtle-dark">${timestamp}</p>
            </div>
            <button onclick="removeHistoryItem(this)" class="text-red-500 hover:text-red-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
    
    // İlk öğeyi kaldır (Henüz işlem geçmişi yok mesajı)
    if (historyContainer.querySelector('p.italic')) {
        historyContainer.innerHTML = '';
    }
    
    historyContainer.prepend(historyItem);
}

// Geçmiş öğesini kaldır
function removeHistoryItem(button) {
    const item = button.closest('.bg-accent-light, .bg-accent-dark');
    if (item) {
        item.remove();
        
        // Eğer hiç öğe kalmadıysa mesaj göster
        const historyContainer = document.getElementById('notebookHistory');
        if (historyContainer.children.length === 0) {
            historyContainer.innerHTML = '<p class="text-subtle-light dark:text-subtle-dark italic text-center py-8">Henüz işlem geçmişi yok</p>';
        }
    }
}

// Panoya kopyala
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.innerText;
    
    navigator.clipboard.writeText(text).then(() => {
        showToast('Panoya kopyalandı!', 'success');
    }).catch(err => {
        console.error('Kopyalama hatası:', err);
        showToast('Kopyalama başarısız', 'error');
    });
}

// Şablonu not olarak kaydet
async function saveTemplateAsNote() {
    const template = document.getElementById('notebookTemplate').innerText;
    
    if (!template || template.includes('Dosya yüklendiğinde')) {
        showToast('Önce bir dosya işleyin', 'error');
        return;
    }
    
    try {
        const response = await fetch('save_note.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                title: currentFile ? currentFile.name.replace(/\.[^/.]+$/, "") : 'NotebookLM Notu',
                content: template,
                is_public: false
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Not başarıyla kaydedildi!', 'success');
        } else {
            throw new Error(data.error || 'Not kaydedilemedi');
        }
        
    } catch (error) {
        console.error('Not kaydetme hatası:', error);
        showToast('Not kaydedilemedi: ' + error.message, 'error');
    }
}

// Toast bildirim fonksiyonu (varsa kullan, yoksa basit alert)
function showToast(message, type = 'info') {
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
    } else {
        alert(message);
    }
}

