// Planlayıcı JavaScript

let currentPlanId = null;

// Planları yükle
async function loadPlans() {
    try {
        const response = await fetch('planner_list.php');
        const data = await response.json();
        
        const plansList = document.getElementById('plansList');
        if (!plansList) return;
        
        if (data.plans && data.plans.length > 0) {
            plansList.innerHTML = data.plans.map(plan => {
                const progress = plan.total_steps > 0 ? Math.round((plan.completed_steps / plan.total_steps) * 100) : 0;
                
                return `
                    <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="openPlanDetail(${plan.id})">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-xl font-bold flex-1">${escapeHtml(plan.title)}</h3>
                            <span class="text-sm text-subtle-light dark:text-subtle-dark whitespace-nowrap ml-4">${formatDate(plan.created_at)}</span>
                        </div>
                        
                        ${plan.description ? `<p class="text-sm text-subtle-light dark:text-subtle-dark mb-4 line-clamp-2">${escapeHtml(plan.description)}</p>` : ''}
                        
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-subtle-light dark:text-subtle-dark">${plan.completed_steps} / ${plan.total_steps} adım tamamlandı</span>
                                <span class="font-semibold">${progress}%</span>
                            </div>
                            <div class="w-full bg-accent-light dark:bg-accent-dark rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full transition-all" style="width: ${progress}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            plansList.innerHTML = `
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-subtle-light dark:text-subtle-dark mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <p class="text-subtle-light dark:text-subtle-dark">Henüz plan oluşturmadınız. Yeni plan oluşturun!</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Planlar yüklenirken hata:', error);
    }
}

// Plan oluşturma modalını aç
function openCreatePlanModal() {
    const modal = document.createElement('div');
    modal.id = 'createPlanModal';
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-xl max-w-2xl w-full mx-4 p-6">
            <h2 class="text-2xl font-bold mb-4">Yeni Plan Oluştur</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Ne yapmak istiyorsunuz?</label>
                    <textarea id="planDescription" rows="4" class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark" placeholder="Örn: Bir web sitesi geliştirmek istiyorum, yeni bir dil öğrenmek istiyorum, proje raporu hazırlamak istiyorum..."></textarea>
                    <p class="text-xs text-subtle-light dark:text-subtle-dark mt-1">Yapacağınız işi detaylı açıklayın. Sistem sizin için otomatik aksiyon planı oluşturacak.</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="generatePlan()" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Şema Oluştur
                    </button>
                    <button onclick="closeCreatePlanModal()" class="px-4 py-2 bg-subtle-light/20 rounded-lg">İptal</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    document.getElementById('planDescription').focus();
}

function closeCreatePlanModal() {
    const modal = document.getElementById('createPlanModal');
    if (modal) modal.remove();
}

// Plan oluştur
async function generatePlan() {
    const description = document.getElementById('planDescription').value.trim();
    
    if (!description) {
        alert('Lütfen ne yapmak istediğinizi açıklayın');
        return;
    }
    
    // Loading göster
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Oluşturuluyor...';
    
    try {
        const response = await fetch('planner_create.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({description})
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeCreatePlanModal();
            loadPlans();
            // Plan detayını aç
            setTimeout(() => openPlanDetail(data.plan_id), 500);
        } else {
            alert(data.error || 'Plan oluşturulamadı');
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Plan oluşturulurken hata:', error);
        alert('Bir hata oluştu');
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// Plan detayını aç
async function openPlanDetail(planId) {
    currentPlanId = planId;
    
    try {
        const response = await fetch(`planner_get_steps.php?plan_id=${planId}`);
        const data = await response.json();
        
        if (!data.plan) return;
        
        // Modal oluştur
        const modal = document.createElement('div');
        modal.id = 'planDetailModal';
        modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-border-light dark:border-border-dark sticky top-0 bg-card-light dark:bg-card-dark">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold">${escapeHtml(data.plan.title)}</h2>
                        <button onclick="closePlanDetail()" class="text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    ${data.plan.description ? `<p class="text-subtle-light dark:text-subtle-dark mt-2">${escapeHtml(data.plan.description)}</p>` : ''}
                </div>
                
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Aksiyon Adımları</h3>
                    <div class="space-y-3" id="planStepsContainer">
                        ${renderPlanSteps(data.steps)}
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    } catch (error) {
        console.error('Plan detayı yüklenirken hata:', error);
    }
}

function closePlanDetail() {
    const modal = document.getElementById('planDetailModal');
    if (modal) modal.remove();
    currentPlanId = null;
}

// Adımları render et
function renderPlanSteps(steps) {
    return steps.map((step, index) => {
        const statusColors = {
            pending: 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700',
            in_progress: 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700',
            completed: 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700'
        };
        
        const statusBadgeColors = {
            pending: 'bg-gray-500',
            in_progress: 'bg-blue-500',
            completed: 'bg-green-500'
        };
        
        const statusLabels = {
            pending: 'Bekliyor',
            in_progress: 'Devam Ediyor',
            completed: 'Tamamlandı'
        };
        
        const isLastStep = index === steps.length - 1;
        
        return `
            <div class="relative">
                <!-- Adım Kartı -->
                <div class="border-2 ${statusColors[step.status]} rounded-xl p-5 ${step.status === 'completed' ? 'opacity-75' : ''} hover:shadow-lg transition-all">
                    <div class="flex items-start gap-4">
                        <!-- Adım Numarası ve İkon -->
                        <div class="flex-shrink-0 relative">
                            <div class="w-12 h-12 rounded-full ${step.status === 'completed' ? 'bg-green-500' : step.status === 'in_progress' ? 'bg-blue-500' : 'bg-gray-400'} flex items-center justify-center font-bold text-white text-lg shadow-lg">
                                ${step.status === 'completed' ? '✓' : step.step_number}
                            </div>
                            ${step.status === 'completed' ? '<div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center"><svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div>' : ''}
                        </div>
                        
                        <!-- İçerik -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <h4 class="font-bold text-lg ${step.status === 'completed' ? 'line-through text-subtle-light dark:text-subtle-dark' : 'text-foreground-light dark:text-foreground-dark'}">${escapeHtml(step.title)}</h4>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold text-white ${statusBadgeColors[step.status]} whitespace-nowrap">
                                    ${statusLabels[step.status]}
                                </span>
                            </div>
                            
                            ${step.description ? `<p class="text-sm text-subtle-light dark:text-subtle-dark mb-3 leading-relaxed">${escapeHtml(step.description)}</p>` : ''}
                            
                            <div class="flex items-center justify-between gap-3">
                                <select onchange="updateStepStatus(${step.id}, this.value)" class="text-sm px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border-2 border-border-light dark:border-border-dark hover:border-primary transition-colors font-medium">
                                    <option value="pending" ${step.status === 'pending' ? 'selected' : ''}>⏳ Bekliyor</option>
                                    <option value="in_progress" ${step.status === 'in_progress' ? 'selected' : ''}>🔄 Devam Ediyor</option>
                                    <option value="completed" ${step.status === 'completed' ? 'selected' : ''}>✅ Tamamlandı</option>
                                </select>
                                
                                ${step.completed_at ? `<span class="text-xs text-green-600 dark:text-green-400 font-medium">✓ ${new Date(step.completed_at).toLocaleDateString('tr-TR')}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ok İşareti (Son adım hariç) -->
                ${!isLastStep ? `
                    <div class="flex justify-center my-3">
                        <div class="relative">
                            <svg class="w-8 h-8 text-primary animate-bounce-slow" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v10.586l2.293-2.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="absolute inset-0 bg-primary/20 rounded-full blur-md"></div>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

// Adım durumunu güncelle
async function updateStepStatus(stepId, status) {
    try {
        const response = await fetch('planner_update_step.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({step_id: stepId, status})
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Adımları yeniden yükle
            const stepsResponse = await fetch(`planner_get_steps.php?plan_id=${currentPlanId}`);
            const stepsData = await stepsResponse.json();
            document.getElementById('planStepsContainer').innerHTML = renderPlanSteps(stepsData.steps);
            
            // Plan listesini güncelle
            loadPlans();
        } else {
            alert(data.error || 'Güncelleme başarısız');
        }
    } catch (error) {
        console.error('Adım güncellenirken hata:', error);
    }
}

// Tarih formatla
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' });
}

// HTML escape
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    const createPlanBtn = document.getElementById('createPlanBtn');
    if (createPlanBtn) {
        createPlanBtn.addEventListener('click', openCreatePlanModal);
    }
    
    // Planlayıcı sekmesi açıldığında yükle
    const plannerLink = document.querySelector('[data-section="planner"]');
    if (plannerLink) {
        plannerLink.addEventListener('click', function() {
            setTimeout(loadPlans, 100);
        });
    }
});
