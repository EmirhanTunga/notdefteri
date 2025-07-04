// Not eklenince animasyon
const noteForm = document.getElementById('noteForm');
if (noteForm) {
    noteForm.addEventListener('submit', function() {
        const btn = noteForm.querySelector('button');
        btn.style.transform = 'scale(1.2) rotate(-5deg)';
        btn.style.transition = 'transform 0.2s';
        setTimeout(() => {
            btn.style.transform = '';
        }, 300);
    });
}
// Giriş/kayıt ekranında başlık animasyonu
const loginTitle = document.querySelector('.login-container h1');
if (loginTitle) {
    loginTitle.animate([
        { transform: 'scale(1) rotate(-2deg)' },
        { transform: 'scale(1.1) rotate(2deg)' },
        { transform: 'scale(1) rotate(-2deg)' }
    ], {
        duration: 1800,
        iterations: Infinity
    });
}

// Not düzenleme fonksiyonları
function editNote(id) {
    document.getElementById('note-text-' + id).style.display = 'none';
    document.querySelector('#note-' + id + ' .note-actions').style.display = 'none';
    document.getElementById('edit-form-' + id).style.display = 'block';
}
function cancelEdit(id) {
    document.getElementById('note-text-' + id).style.display = '';
    document.querySelector('#note-' + id + ' .note-actions').style.display = '';
    document.getElementById('edit-form-' + id).style.display = 'none';
}

// Plan sekmeleri
function showPlan(type) {
    document.getElementById('plan-daily').style.display = (type === 'daily') ? '' : 'none';
    document.getElementById('plan-weekly').style.display = (type === 'weekly') ? '' : 'none';
    document.getElementById('tab-daily').classList.toggle('active', type === 'daily');
    document.getElementById('tab-weekly').classList.toggle('active', type === 'weekly');
    document.getElementById('task-type').value = type;
    document.getElementById('week-start-container').style.display = (type === 'weekly') ? '' : 'none';
}
// Mavi baloncuk ve modal
function openTaskModal() {
    const type = document.getElementById('tab-daily').classList.contains('active') ? 'daily' : 'weekly';
    document.getElementById('task-type').value = type;
    document.getElementById('week-start-container').style.display = (type === 'weekly') ? '' : 'none';
    document.getElementById('task-modal').style.display = 'flex';
}
function closeTaskModal() {
    document.getElementById('task-modal').style.display = 'none';
    document.getElementById('taskForm').reset();
}
// Modal dışına tıklayınca kapansın
window.onclick = function(event) {
    const modal = document.getElementById('task-modal');
    if (event.target === modal) {
        closeTaskModal();
    }
}

// Karanlık mod anahtarı
const darkToggle = document.getElementById('darkModeToggle');
if (darkToggle) {
    // Tercihi localStorage'da sakla
    if (localStorage.getItem('darkMode') === 'on') {
        document.body.classList.add('dark-mode');
    }
    darkToggle.onclick = function() {
        document.body.classList.toggle('dark-mode');
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'on');
        } else {
            localStorage.setItem('darkMode', 'off');
        }
    };
}

// Sayfa yüklenince toast parametresini kontrol et
(function(){
    const params = new URLSearchParams(window.location.search);
    const toastMap = {
        'not_eklendi': 'Not kaydedildi!',
        'not_silindi': 'Not silindi!',
        'not_guncellendi': 'Not güncellendi!',
        'favori_eklendi': 'Favorilere eklendi!',
        'favori_silindi': 'Favorilerden çıkarıldı!'
    };
    if (params.has('toast') && toastMap[params.get('toast')]) {
        setTimeout(() => toast(toastMap[params.get('toast')]), 300);
        // URL'den parametreyi kaldır
        window.history.replaceState({}, document.title, window.location.pathname);
    }
})();
// Favori yıldız butonunda tam yönlendirme
function toggleFavorite(noteId) {
    window.location.href = 'toggle_favorite.php?id=' + noteId + '&redirect=1';
}

// Etiket filtresi doldurma
if (window.allNoteTags && document.getElementById('tagFilter')) {
    const tagFilter = document.getElementById('tagFilter');
    window.allNoteTags.forEach(tag => {
        const opt = document.createElement('option');
        opt.value = tag;
        opt.textContent = '#' + tag;
        tagFilter.appendChild(opt);
    });
}
// Notlar için canlı arama + etiket filtresi
function filterNotes() {
    const input = document.getElementById('noteSearch');
    const filter = input.value.toLowerCase();
    const tag = document.getElementById('tagFilter').value;
    const notes = document.querySelectorAll('.note-list li');
    notes.forEach(note => {
        const text = note.innerText.toLowerCase();
        const tags = Array.from(note.querySelectorAll('.note-tag')).map(e => e.textContent.replace('#',''));
        const tagMatch = !tag || tags.includes(tag);
        note.style.display = (text.includes(filter) && tagMatch) ? '' : 'none';
    });
}

// Profil resmi (avatar) modalı
const avatarBtn = document.getElementById('avatarBtn');
const avatarModal = document.getElementById('avatarModal');
function closeAvatarModal() {
    avatarModal.style.display = 'none';
}
if (avatarBtn && avatarModal) {
    avatarBtn.onclick = function() {
        avatarModal.style.display = 'flex';
    };
    window.addEventListener('click', function(event) {
        if (event.target === avatarModal) closeAvatarModal();
    });
}

// Tema seçici modalı
const themeBtn = document.getElementById('themeBtn');
const themeModal = document.getElementById('themeModal');
function closeThemeModal() { themeModal.style.display = 'none'; }
if (themeBtn && themeModal) {
    themeBtn.onclick = function() { themeModal.style.display = 'flex'; };
    window.addEventListener('click', function(event) {
        if (event.target === themeModal) closeThemeModal();
    });
}
// Tema kaydetme
function saveTheme() {
    const selected = document.querySelector('input[name="theme"]:checked');
    if (selected) {
        localStorage.setItem('theme', selected.value);
        setTheme(selected.value);
        closeThemeModal();
    }
}
function setTheme(theme) {
    // Tüm tema class'larını kaldır
    document.body.className = document.body.className
        .split(' ')
        .filter(c => !c.startsWith('theme-'))
        .join(' ');
    if (theme) document.body.classList.add('theme-' + theme);
}
// Sayfa yüklenince tema uygula
const savedTheme = localStorage.getItem('theme');
if (savedTheme) setTheme(savedTheme);
// Modal açıldığında seçili temayı işaretle
if (themeModal) {
    themeModal.addEventListener('show', function() {
        const saved = localStorage.getItem('theme');
        if (saved) {
            const radio = document.querySelector('input[name="theme"][value="'+saved+'"]');
            if (radio) radio.checked = true;
        }
    });
}

// Motivasyon mesajı
const motivasyonlar = [
    'Harika bir gün seni bekliyor! ✨',
    'Bugün yeni bir başlangıç! 🚀',
    'Küçük adımlar büyük farklar yaratır. 🐾',
    'Hayallerin için çalışmaya devam! 💡',
    'Sen çok güçlüsün! 💪',
    'Her not, bir adım daha ileri! 📒',
    'Gülümsemeyi unutma! 😊',
    'Başarı, vazgeçmeyenlerindir! 🏆',
    'Bugün de harika işler başaracaksın! 🌟',
    'Pozitif kal, üretken ol! 🌈'
];
const motivationBox = document.getElementById('motivationBox');
if (motivationBox) {
    const msg = motivasyonlar[Math.floor(Math.random()*motivasyonlar.length)];
    motivationBox.textContent = msg;
}

// Not eklerken karakter sayacı
const noteInput = document.getElementById('note');
if (noteInput) {
    let charCount = document.createElement('div');
    charCount.id = 'charCount';
    noteInput.parentNode.insertBefore(charCount, noteInput.nextSibling);
    function updateCharCount() {
        charCount.textContent = noteInput.value.length + ' karakter';
    }
    noteInput.addEventListener('input', updateCharCount);
    updateCharCount();
}

// Görev tamamlandığında kutlama animasyonu (konfeti)
function showConfetti() {
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    for (let i = 0; i < 30; i++) {
        const piece = document.createElement('span');
        piece.className = 'confetti-piece';
        piece.style.left = (Math.random()*100)+'vw';
        piece.style.background = 'hsl('+(Math.random()*360)+',80%,60%)';
        piece.style.animationDelay = (Math.random()*0.5)+'s';
        confetti.appendChild(piece);
    }
    document.body.appendChild(confetti);
    setTimeout(()=>{ confetti.remove(); }, 1200);
}
// Görev tik kutusuna tıklanınca kutlama
const taskLists = document.querySelectorAll('.task-list input[type="checkbox"]');
taskLists.forEach(cb => {
    cb.addEventListener('change', function() {
        if (cb.checked) showConfetti();
    });
});

// Toast bildirim fonksiyonu
toast = (msg) => {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2200);
}
// Örnek: toast('Not kaydedildi!');

// Haftalık plan için takvim görünümü
document.addEventListener('DOMContentLoaded', function() {
    const calendarView = document.getElementById('calendarView');
    if (calendarView && window.weeklyTasksForCalendar) {
        const days = ['Pzt','Sal','Çar','Per','Cum','Cmt','Paz'];
        const today = new Date();
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - (today.getDay() + 6) % 7); // Pazartesi
        let html = '';
        for (let i = 0; i < 7; i++) {
            const d = new Date(weekStart);
            d.setDate(weekStart.getDate() + i);
            const dateStr = d.toISOString().slice(0,10);
            const isToday = d.toDateString() === today.toDateString();
            html += `<div class="calendar-day${isToday ? ' active' : ''}">
                <div>${days[i]}</div>
                <div class="calendar-tasks">`;
            (window.weeklyTasksForCalendar[dateStr]||[]).forEach(task => {
                html += `<div class="calendar-task">${task}</div>`;
            });
            html += '</div></div>';
        }
        calendarView.innerHTML = html;
    }
});

// Emoji reaksiyon animasyonu
function showReaction(btn) {
    const emojis = ['🎉','😊','👍','❤️','👏','🥳','✨','😻','🔥','🙌'];
    const emoji = emojis[Math.floor(Math.random()*emojis.length)];
    const span = document.createElement('span');
    span.textContent = emoji;
    span.className = 'reaction-emoji';
    btn.parentNode.insertBefore(span, btn.nextSibling);
    setTimeout(() => { span.classList.add('show'); }, 10);
    setTimeout(() => { span.classList.remove('show'); }, 1200);
    setTimeout(() => { span.remove(); }, 1500);
}
// Günün sözü kutusu
(function(){
    const gununSozleri = [
        '“Başlamak için mükemmel olmak zorunda değilsin, ama mükemmel olmak için başlamak zorundasın.”',
        '“Her gün yeni bir şans.”',
        '“Küçük adımlar büyük farklar yaratır.”',
        '“Hayallerin için çalışmaya devam!”',
        '“Gülümsemek bulaşıcıdır, yaymaya devam et!”',
        '“Bugün de harika işler başaracaksın!”',
        '“Pozitif kal, üretken ol!”',
        '“Her gün bir fırsattır.”',
        '“Kendine inan, başarırsın!”',
        '“Zorluklar, büyümenin anahtarıdır.”'
    ];
    const box = document.createElement('div');
    box.className = 'motivation-box';
    box.style.marginTop = '8px';
    box.textContent = gununSozleri[new Date().getDate() % gununSozleri.length];
    const container = document.querySelector('.container');
    if (container) container.insertBefore(box, container.children[1]);
})();

// Arka plan not kağıtları ve raptiye efekti
(function(){
    const bg = document.getElementById('bgNotes');
    if (!bg) return;
    const notes = [];
    const colors = ['#fffbe6','#e3f0ff','#ffe3f0','#e6ffed'];
    const angles = [-12, -6, 0, 8, 14, 4, -8, 10];
    const positions = [
        {top:30,left:40},{top:120,left:180},{top:80,right:60},{top:220,left:90},{top:300,right:120},
        {top:400,left:60},{top:500,right:40},{top:200,left:400},{top:350,right:300},{top:100,left:600},
        {top:520,left:320},{top:60,right:320},{top:420,left:520}
    ];
    for(let i=0;i<positions.length;i++){
        const note = document.createElement('div');
        note.className = 'bg-note';
        note.style.background = colors[i%colors.length];
        note.style.top = (positions[i].top + Math.random()*20) + 'px';
        if(positions[i].left!==undefined) note.style.left = (positions[i].left + Math.random()*20) + 'px';
        if(positions[i].right!==undefined) note.style.right = (positions[i].right + Math.random()*20) + 'px';
        note.style.setProperty('--angle', angles[i%angles.length] + 'deg');
        // Raptiye
        const pin = document.createElement('div');
        pin.className = 'pin';
        pin.innerHTML = '<div class="pin-head"></div><div class="pin-body"></div>';
        note.appendChild(pin);
        // Çizgiler
        const lines = document.createElement('div');
        lines.className = 'note-lines';
        for(let l=0;l<5;l++){
            const line = document.createElement('div');
            line.className = 'note-line';
            lines.appendChild(line);
        }
        note.appendChild(lines);
        bg.appendChild(note);
    }
})();

// Arka plan hayvan patileri
(function(){
    const bg = document.getElementById('bgPaws');
    if (!bg) return;
    const paws = [
        {emoji:'🐾',cls:'cat'},{emoji:'🐾',cls:'dog'},{emoji:'🐾',cls:'bear'},{emoji:'🐾',cls:'fox'},
        {emoji:'🐾',cls:'panda'},{emoji:'🐾',cls:'rabbit'},{emoji:'🐾',cls:'penguin'},{emoji:'🐾',cls:'tiger'},
        {emoji:'🐾',cls:'koala'},{emoji:'🐾',cls:'monkey'}
    ];
    const positions = [
        {top:40,left:120},{top:180,left:60},{top:90,right:80},{top:320,left:200},{top:420,right:160},
        {top:520,left:80},{top:600,right:60},{top:260,left:500},{top:380,right:320},{top:140,left:700},
        {top:480,left:600},{top:80,right:320},{top:420,left:720}
    ];
    for(let i=0;i<positions.length;i++){
        const paw = document.createElement('span');
        const p = paws[i%paws.length];
        paw.className = 'bg-paw ' + p.cls;
        paw.textContent = p.emoji;
        paw.style.top = (positions[i].top + Math.random()*20) + 'px';
        if(positions[i].left!==undefined) paw.style.left = (positions[i].left + Math.random()*20) + 'px';
        if(positions[i].right!==undefined) paw.style.right = (positions[i].right + Math.random()*20) + 'px';
        paw.style.transform += ' rotate('+(Math.random()*40-20)+'deg)';
        paw.style.fontSize = (2.6 + Math.random()*1.5) + 'em';
        bg.appendChild(paw);
    }
})();

// Tema değiştirme
function toggleTheme() {
    const body = document.body;
    body.classList.toggle('dark-mode');
    localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
}
window.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
    // Tema butonu
    const themeBtn = document.getElementById('theme-toggle-btn');
    if (themeBtn) themeBtn.onclick = toggleTheme;
});

// Toast bildirim
function showToast(msg, type = 'success') {
    let toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerText = msg;
    document.body.appendChild(toast);
    setTimeout(() => { toast.classList.add('show'); }, 10);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(()=>toast.remove(), 400); }, 3000);
}

// Kutlama efekti (confetti)
function confettiBurst() {
    for (let i = 0; i < 32; i++) {
        let conf = document.createElement('div');
        conf.className = 'confetti';
        conf.style.left = Math.random()*100 + '%';
        conf.style.background = `hsl(${Math.random()*360},90%,60%)`;
        conf.style.animationDelay = (Math.random()*0.7)+'s';
        document.body.appendChild(conf);
        setTimeout(()=>conf.remove(), 1800);
    }
}

// Emoji patlaması
function emojiBurst(emoji = '🎉') {
    for (let i = 0; i < 18; i++) {
        let e = document.createElement('div');
        e.className = 'emoji-burst';
        e.innerText = emoji;
        e.style.left = Math.random()*100 + '%';
        e.style.animationDelay = (Math.random()*0.5)+'s';
        document.body.appendChild(e);
        setTimeout(()=>e.remove(), 1400);
    }
}

// AJAX ile beğeni
function likePublicNote(noteId, btn) {
    fetch('like_public_note.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'note_id=' + encodeURIComponent(noteId)
    })
    .then(r=>r.json())
    .then(data=> {
        if(data.success) {
            btn.classList.toggle('liked', data.liked);
            btn.innerText = data.liked ? '❤️' : '🤍';
            btn.parentElement.nextElementSibling.innerText = data.like_count + ' beğeni';
            emojiBurst('✨');
            showToast(data.liked ? 'Beğendin!' : 'Beğeni geri alındı');
        }
    });
    return false;
}

// AJAX ile yorum ekleme
function addPublicComment(form, noteId) {
    const input = form.querySelector('input[name="comment"]');
    const comment = input.value.trim();
    if (!comment) return false;
    fetch('add_public_comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'note_id=' + encodeURIComponent(noteId) + '&comment=' + encodeURIComponent(comment)
    })
    .then(r=>r.json())
    .then(data=> {
        if(data.success) {
            let commentsDiv = form.parentElement;
            let newDiv = document.createElement('div');
            newDiv.className = 'public-feed-comment';
            newDiv.innerHTML = `<span class='public-feed-comment-user'>${data.username}</span><span>${data.comment}</span><span class='public-feed-comment-date'>${data.date}</span>`;
            commentsDiv.insertBefore(newDiv, form);
            input.value = '';
            emojiBurst('💬');
            showToast('Yorum eklendi!');
        }
    });
    return false;
}

// AJAX ile paylaşım ekleme
function addPublicNote(form) {
    const textarea = form.querySelector('textarea[name="public_note"]');
    const content = textarea.value.trim();
    if (!content) return false;
    fetch('feed.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'public_note=' + encodeURIComponent(content) + '&ajax=1'
    })
    .then(r=>r.json())
    .then(data=> {
        if(data.success) {
            location.reload(); // Şimdilik reload, isterseniz dinamik eklenebilir
            confettiBurst();
            showToast('Paylaşım yapıldı!');
        }
    });
    return false;
} 