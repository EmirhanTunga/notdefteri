// Stitch App JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle functionality
    const themeToggle = document.getElementById('themeToggle');
    const themeIconLight = document.querySelector('.theme-icon-light');
    const themeIconDark = document.querySelector('.theme-icon-dark');
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.classList.toggle('dark', currentTheme === 'dark');
    updateThemeIcon(currentTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            const theme = isDark ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
            updateThemeIcon(theme);
        });
    }
    
    function updateThemeIcon(theme) {
        if (themeIconLight && themeIconDark) {
            if (theme === 'dark') {
                themeIconLight.style.display = 'none';
                themeIconDark.style.display = 'block';
            } else {
                themeIconLight.style.display = 'block';
                themeIconDark.style.display = 'none';
            }
        }
    }
    
    // Navigation functionality
    const navLinks = document.querySelectorAll('nav a[data-section]');
    const contentSections = document.querySelectorAll('.content-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav links
            navLinks.forEach(navLink => {
                navLink.classList.remove('bg-primary-accent/20', 'text-primary-accent');
                navLink.classList.add('hover:bg-accent-light', 'dark:hover:bg-accent-dark');
            });
            
            // Add active class to clicked link
            this.classList.add('bg-primary-accent/20', 'text-primary-accent');
            this.classList.remove('hover:bg-accent-light', 'dark:hover:bg-accent-dark');
            
            // Hide all content sections
            contentSections.forEach(section => {
                section.classList.remove('active');
                section.style.display = 'none';
            });
            
            // Show selected section
            const targetSection = document.getElementById(this.dataset.section + '-section');
            if (targetSection) {
                targetSection.classList.add('active');
                targetSection.style.display = 'block';
            }
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('input[placeholder="Ara"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // New Item Button functionality
    const newItemBtn = document.getElementById('newItemBtn');
    if (newItemBtn) {
        newItemBtn.addEventListener('click', function() {
            // Show a modal with options for new items
            showNewItemModal();
        });
    }
    
    // New Note Button functionality
    const newNoteBtn = document.getElementById('newNoteBtn');
    if (newNoteBtn) {
        newNoteBtn.addEventListener('click', function() {
            // Show note modal directly
            stitchApp.showNoteModal();
        });
    }
    
    // Initialize with notes section active
    const notesLink = document.querySelector('a[data-section="notes"]');
    if (notesLink) {
        notesLink.click();
    }
});

class StitchApp {
  constructor() {
    this.currentSection = "notes";
    this.init();
  }

  init() {
    this.setupNavigation();
    this.loadDashboardData();
    this.setupEventListeners();
    this.loadNotes(); // Load notes when app initializes
  }

  setupNavigation() {
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const section = link.getAttribute("data-section");
        this.navigateToSection(section);
      });
    });
  }

  navigateToSection(section) {
    // Update active nav link
    document.querySelectorAll(".nav-link").forEach((link) => {
      link.classList.remove("active");
    });
    document
      .querySelector(`[data-section="${section}"]`)
      .classList.add("active");

    // Hide all sections
    document.querySelectorAll(".content-section").forEach((sec) => {
      sec.classList.remove("active");
      sec.style.display = "none";
    });

    // Show target section
    const targetSection = document.getElementById(`${section}-section`);
    if (targetSection) {
      targetSection.style.display = "block";
      targetSection.classList.add("active");
    }

    this.currentSection = section;

    // Load section-specific data
    this.loadSectionData(section);
  }

  setupEventListeners() {
    // New Note Button
    const newNoteBtn = document.getElementById("newNoteBtn");
    if (newNoteBtn) {
      newNoteBtn.addEventListener("click", () => this.showNoteModal());
    }

    // New Daily Task Button
    const newDailyTaskBtn = document.getElementById("newDailyTaskBtn");
    if (newDailyTaskBtn) {
      newDailyTaskBtn.addEventListener("click", () =>
        this.showTaskModal("daily")
      );
    }

    // New Weekly Task Button
    const newWeeklyTaskBtn = document.getElementById("newWeeklyTaskBtn");
    if (newWeeklyTaskBtn) {
      newWeeklyTaskBtn.addEventListener("click", () =>
        this.showTaskModal("weekly")
      );
    }

    // Search functionality
    const notesSearch = document.getElementById("notesSearch");
    if (notesSearch) {
      notesSearch.addEventListener("input", (e) =>
        this.searchNotes(e.target.value)
      );
    }
  }

  async loadDashboardData() {
    try {
      const response = await fetch("get_dashboard_stats.php");
      const data = await response.json();

      if (data.success) {
        document.getElementById("totalNotes").textContent =
          data.stats.total_notes || 0;
        document.getElementById("dailyTasks").textContent =
          data.stats.daily_tasks || 0;
        document.getElementById("weeklyTasks").textContent =
          data.stats.weekly_tasks || 0;
        document.getElementById("friendsCount").textContent =
          data.stats.friends_count || 0;

        this.loadRecentActivity();
      }
    } catch (error) {
      console.error("Error loading dashboard data:", error);
    }
  }

  async loadRecentActivity() {
    const recentActivity = document.getElementById("recentActivity");
    if (!recentActivity) return;

    try {
      // Get recent notes from the database
      const response = await fetch('get_notes.php?limit=5&order=updated_at');
      const result = await response.json();
      
      const notes = result.notes || result.data || [];
      if (result.success && notes && notes.length > 0) {
        const activities = notes.map(note => ({
          type: "note",
          title: `"${note.title}" notu ${note.updated_at === note.created_at ? 'oluşturuldu' : 'güncellendi'}`,
          time: this.getTimeAgo(note.updated_at),
          icon: "note",
        }));

        recentActivity.innerHTML = activities
          .map(
            (activity) => `
                <div class="flex items-center gap-4 p-4 rounded-lg border border-border-light dark:border-border-dark">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        ${this.getActivityIcon(activity.icon)}
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">${activity.title}</p>
                        <p class="text-sm text-subtle-light dark:text-subtle-dark">${
                          activity.time
                        }</p>
                    </div>
                </div>
            `
          )
          .join("");
      } else {
        // No activities yet
        recentActivity.innerHTML = `
          <div class="text-center py-8 text-subtle-light dark:text-subtle-dark">
            <svg class="w-12 h-12 mx-auto mb-4 text-subtle-light dark:text-subtle-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-lg font-medium mb-2">Henüz aktivite yok</p>
            <p class="text-sm">İlk notunuzu oluşturduğunuzda aktiviteler burada görünecek.</p>
          </div>
        `;
      }
    } catch (error) {
      console.error('Error loading recent activity:', error);
      recentActivity.innerHTML = `
        <div class="text-center py-8 text-red-500">
          <p>Aktiviteler yüklenirken bir hata oluştu.</p>
        </div>
      `;
    }
  }

  getActivityIcon(type) {
    const icons = {
      note: '<svg class="icon-xs text-primary" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg"><path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM152,88V44l44,44Z"></path></svg>',
      check:
        '<svg class="icon-xs text-green-500" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"></path></svg>',
      user: '<svg class="icon-xs text-blue-500" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg"><path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"></path></svg>',
    };
    return icons[type] || icons.note;
  }

  async loadSectionData(section) {
    switch (section) {
      case "notes":
        await this.loadNotes();
        break;
      case "favorites":
        await this.loadFavorites();
        break;
      case "daily-tasks":
        await this.loadDailyTasks();
        break;
      case "weekly-tasks":
        await this.loadWeeklyTasks();
        break;
      case "public-notes":
        await this.loadPublicNotes();
        break;
      case "friends":
        await this.loadFriends();
        break;
      case "settings":
        await this.loadSettings();
        break;
    }
  }

  async loadNotes() {
    try {
      const response = await fetch("get_notes.php");
      const data = await response.json();

      const container = document.getElementById("notesContainer");
      if (!container) return;

      if (data.success && data.notes.length > 0) {
        container.innerHTML = `
                    <table class="w-full text-left">
                        <thead class="bg-background-light dark:bg-background-dark border-b border-border-light dark:border-border-dark">
                            <tr>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">Başlık</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">Oluşturulma</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">Son Güncelleme</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.notes
                              .map(
                                (note) => `
                                <tr class="border-b border-border-light dark:border-border-dark hover:bg-accent-light dark:hover:bg-accent-dark">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                            <div>
                                                <div class="text-sm font-medium">${
                                                  note.title || "Başlıksız Not"
                                                }</div>
                                                <div class="text-sm text-subtle-light dark:text-subtle-dark">${
                                                  note.content
                                                    ? note.content.substring(
                                                        0,
                                                        50
                                                      ) + "..."
                                                    : ""
                                                }</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">
                                        ${this.formatDate(note.created_at)}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">
                                        ${this.formatDate(note.updated_at)}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <button class="text-primary hover:text-primary/80" onclick="app.editNote(${
                                              note.id
                                            })">
                                                <svg class="icon-xs" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M227.31,73.37,182.63,28.69a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM92.69,208H48V163.31l88-88L180.69,120ZM192,108.69,147.31,64l24-24L216,84.69Z"></path>
                                                </svg>
                                            </button>
                                            <button class="text-red-500 hover:text-red-600" onclick="app.deleteNote(${
                                              note.id
                                            })">
                                                <svg class="icon-xs" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `
                              )
                              .join("")}
                        </tbody>
                    </table>
                `;
      } else {
        container.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-subtle-light dark:text-subtle-dark" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                            <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM152,88V44l44,44Z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium">Henüz not yok</h3>
                        <p class="mt-1 text-sm text-subtle-light dark:text-subtle-dark">İlk notunuzu oluşturmak için başlayın.</p>
                        <div class="mt-6">
                            <button class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors" onclick="app.showNoteModal()">
                                Yeni Not
                            </button>
                        </div>
                    </div>
                `;
      }
    } catch (error) {
      console.error("Error loading notes:", error);
    }
  }

  async loadDailyTasks() {
    const container = document.getElementById("dailyTasksContainer");
    if (!container) return;

    container.innerHTML = `
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-subtle-light dark:text-subtle-dark" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                    <path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium">Günlük görevler yükleniyor...</h3>
            </div>
        `;
  }

  async loadWeeklyTasks() {
    const container = document.getElementById("weeklyTasksContainer");
    if (!container) return;

    container.innerHTML = `
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-subtle-light dark:text-subtle-dark" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium">Haftalık görevler yükleniyor...</h3>
            </div>
        `;
  }

  async loadPublicNotes() {
    const container = document.getElementById("publicNotesContainer");
    if (!container) return;

    container.innerHTML = `
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-subtle-light dark:text-subtle-dark" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                    <path d="M80,64a8,8,0,0,1,8-8H216a8,8,0,0,1,0,16H88A8,8,0,0,1,80,64Zm136,56H88a8,8,0,0,0,0,16H216a8,8,0,0,0,0-16Zm0,64H88a8,8,0,0,0,0,16H216a8,8,0,0,0,0-16ZM44,52A12,12,0,1,0,56,64,12,12,0,0,0,44,52Zm0,64a12,12,0,1,0,12,12A12,12,0,0,0,44,116Zm0,64a12,12,0,1,0,12,12A12,12,0,0,0,44,180Z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium">Herkese açık notlar yükleniyor...</h3>
            </div>
        `;
  }

  async loadFriends() {
    const container = document.getElementById("friendsContainer");
    if (!container) return;

    container.innerHTML = `
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-subtle-light dark:text-subtle-dark" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                    <path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.27,98.63a8,8,0,0,1-11.29.74A80,80,0,0,0,172,168a8,8,0,0,1,0-16,96,96,0,0,1,66.27,26.37A8,8,0,0,1,250.27,206.63ZM172,120a44,44,0,1,1-16.34-84.87,8,8,0,1,1-5.94,14.85,28,28,0,1,0,0,52.06,8,8,0,1,1,5.94,14.85A43.85,43.85,0,0,1,172,120Z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium">Arkadaşlar yükleniyor...</h3>
            </div>
        `;
  }

  async loadSettings() {
    const container = document.getElementById("settingsContainer");
    if (!container) return;

    container.innerHTML = `
            <div class="space-y-6">
                <div class="border-b border-border-light dark:border-border-dark pb-6">
                    <h3 class="text-lg font-medium mb-4">Hesap Bilgileri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Kullanıcı Adı</label>
                            <input type="text" class="w-full px-3 py-2 border border-border-light dark:border-border-dark rounded-lg bg-card-light dark:bg-card-dark" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">E-posta</label>
                            <input type="email" class="w-full px-3 py-2 border border-border-light dark:border-border-dark rounded-lg bg-card-light dark:bg-card-dark" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="border-b border-border-light dark:border-border-dark pb-6">
                    <h3 class="text-lg font-medium mb-4">Tema Ayarları</h3>
                    <div class="flex items-center gap-4">
                        <button class="px-4 py-2 bg-primary text-white rounded-lg">Açık Tema</button>
                        <button class="px-4 py-2 border border-border-light dark:border-border-dark rounded-lg">Koyu Tema</button>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-4">Tehlikeli İşlemler</h3>
                    <div class="space-y-3">
                        <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Tüm Verileri Sil
                        </button>
                        <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Hesabı Sil
                        </button>
                    </div>
                </div>
            </div>
        `;
  }

  formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("tr-TR", {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  showNoteModal() {
    // This would show a modal for creating/editing notes
    alert("Not ekleme modalı açılacak");
  }

  showTaskModal(type) {
    // This would show a modal for creating tasks
    alert(`${type} görev ekleme modalı açılacak`);
  }

  editNote(noteId) {
    // This would open the note for editing
    alert(`Not düzenleme: ${noteId}`);
  }

  deleteNote(noteId) {
    if (confirm("Bu notu silmek istediğinizden emin misiniz?")) {
      // Delete note logic
      alert(`Not silindi: ${noteId}`);
    }
  }

  searchNotes(query) {
    // Search functionality
    console.log("Searching for:", query);
  }
}

// Initialize the app when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  window.app = new StitchApp();
});

// Modal functionality
class ModalManager {
  constructor() {
    this.createModalContainer();
  }

  createModalContainer() {
    if (document.getElementById("modal-container")) return;

    const modalContainer = document.createElement("div");
    modalContainer.id = "modal-container";
    modalContainer.className = "fixed inset-0 z-50 hidden";
    modalContainer.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" id="modal-backdrop"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-card-dark rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto" id="modal-content">
                    <!-- Modal content will be inserted here -->
                </div>
            </div>
        `;
    document.body.appendChild(modalContainer);

    // Close modal when clicking backdrop
    document.getElementById("modal-backdrop").addEventListener("click", () => {
      this.closeModal();
    });

    // Close modal with Escape key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !modalContainer.classList.contains("hidden")) {
        this.closeModal();
      }
    });
  }

  showModal(content) {
    const modalContainer = document.getElementById("modal-container");
    const modalContent = document.getElementById("modal-content");

    modalContent.innerHTML = content;
    modalContainer.classList.remove("hidden");
    document.body.style.overflow = "hidden";
  }

  closeModal() {
    const modalContainer = document.getElementById("modal-container");
    modalContainer.classList.add("hidden");
    document.body.style.overflow = "";
  }
}

// Initialize modal manager
const modalManager = new ModalManager();

// Update StitchApp class methods
StitchApp.prototype.showNoteModal = function (noteId = null) {
  const isEdit = noteId !== null;
  const title = isEdit ? "Not Düzenle" : "Yeni Not";

  const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">${title}</h2>
                <button class="text-gray-400 hover:text-gray-600" onclick="modalManager.closeModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="noteForm" class="space-y-4">
                <div>
                    <label for="noteTitle" class="block text-sm font-medium mb-2">Başlık</label>
                    <input 
                        type="text" 
                        id="noteTitle" 
                        name="title" 
                        class="w-full px-3 py-2 border border-border-light dark:border-border-dark rounded-lg bg-card-light dark:bg-card-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none"
                        placeholder="Not başlığı..."
                    >
                </div>
                
                <div>
                    <label for="noteContent" class="block text-sm font-medium mb-2">İçerik</label>
                    <textarea 
                        id="noteContent" 
                        name="content" 
                        rows="6"
                        class="w-full px-3 py-2 border border-border-light dark:border-border-dark rounded-lg bg-card-light dark:bg-card-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-none"
                        placeholder="Not içeriği..."
                    ></textarea>
                </div>
                
                <div class="flex items-center gap-2">
                    <input 
                        type="checkbox" 
                        id="isPublic" 
                        name="is_public"
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                    >
                    <label for="isPublic" class="text-sm">Herkese açık yap</label>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-border-light dark:border-border-dark">
                    <button 
                        type="button" 
                        onclick="modalManager.closeModal()"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
                    >
                        İptal
                    </button>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
                    >
                        ${isEdit ? "Güncelle" : "Kaydet"}
                    </button>
                </div>
            </form>
        </div>
    `;

  modalManager.showModal(modalContent);

  // Handle form submission
  document.getElementById("noteForm").addEventListener("submit", (e) => {
    e.preventDefault();
    this.saveNote(noteId);
  });

  // If editing, load note data
  if (isEdit) {
    this.loadNoteForEdit(noteId);
  }
};

StitchApp.prototype.saveNote = async function (noteId = null) {
  const form = document.getElementById("noteForm");
  const formData = new FormData(form);

  if (noteId) {
    formData.append("id", noteId);
  }

  try {
    const response = await fetch("save_note.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      modalManager.closeModal();
      if (this.currentSection === "notes") {
        this.loadNotes();
      }
      this.loadDashboardData(); // Refresh dashboard stats
      this.loadRecentActivity(); // Refresh recent activities
    } else {
      alert("Hata: " + (result.message || "Not kaydedilemedi"));
    }
  } catch (error) {
    console.error("Error saving note:", error);
    alert("Not kaydedilirken bir hata oluştu");
  }
};

StitchApp.prototype.loadNoteForEdit = async function (noteId) {
  try {
    const response = await fetch(`get_notes.php?id=${noteId}`);
    const data = await response.json();

    if (data.success && data.note) {
      const note = data.note;
      document.getElementById("noteTitle").value = note.title || "";
      document.getElementById("noteContent").value = note.content || "";
      document.getElementById("isPublic").checked = note.is_public == 1;
    }
  } catch (error) {
    console.error("Error loading note for edit:", error);
  }
};

StitchApp.prototype.formatDate = function(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  });
};

StitchApp.prototype.getTimeAgo = function(dateString) {
  if (!dateString) return '-';
  
  const now = new Date();
  const date = new Date(dateString);
  const diffInSeconds = Math.floor((now - date) / 1000);
  
  if (diffInSeconds < 60) {
    return 'Az önce';
  } else if (diffInSeconds < 3600) {
    const minutes = Math.floor(diffInSeconds / 60);
    return `${minutes} dakika önce`;
  } else if (diffInSeconds < 86400) {
    const hours = Math.floor(diffInSeconds / 3600);
    return `${hours} saat önce`;
  } else if (diffInSeconds < 2592000) {
    const days = Math.floor(diffInSeconds / 86400);
    return `${days} gün önce`;
  } else {
    return this.formatDate(dateString);
  }
};

StitchApp.prototype.loadNotes = async function () {
  try {
    const response = await fetch('get_notes.php');
    const result = await response.json();
    
    console.log('Notes API response:', result); // Debug log
    
    const tbody = document.getElementById('notesTableBody');
    
    // Check for both 'data' and 'notes' properties
    const notes = result.notes || result.data || [];
    
    if (result.success && notes && notes.length > 0) {
      tbody.innerHTML = notes.map(note => `
        <tr class="border-b border-border-light dark:border-border-dark hover:bg-accent-light dark:hover:bg-accent-dark">
          <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-foreground-light dark:text-foreground-dark cursor-pointer" onclick="stitchApp.showNoteModal(${note.id})">${note.title}</td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">${this.formatDate(note.created_at)}</td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">${this.formatDate(note.updated_at)}</td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">
            <div class="flex items-center gap-2">
              <button 
                onclick="event.stopPropagation(); stitchApp.toggleFavorite(${note.id}, ${note.is_favorite || 0})"
                class="p-2 rounded-lg hover:bg-accent-light dark:hover:bg-accent-dark transition-colors"
                title="${note.is_favorite ? 'Favorilerden çıkar' : 'Favorilere ekle'}"
              >
                <svg class="w-4 h-4 ${note.is_favorite ? 'text-yellow-500 fill-current' : 'text-subtle-light dark:text-subtle-dark'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
              </button>
              <button 
                onclick="event.stopPropagation(); stitchApp.showNoteModal(${note.id})"
                class="p-2 rounded-lg hover:bg-accent-light dark:hover:bg-accent-dark transition-colors"
                title="Düzenle"
              >
                <svg class="w-4 h-4 text-subtle-light dark:text-subtle-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
            </div>
          </td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = `
        <tr>
          <td colspan="4" class="px-6 py-12 text-center text-subtle-light dark:text-subtle-dark">
            <div class="flex flex-col items-center">
              <svg class="w-12 h-12 mb-4 text-subtle-light dark:text-subtle-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              <p class="text-lg font-medium mb-2">Henüz not yok</p>
              <p class="text-sm mb-4">İlk notunuzu oluşturmak için başlayın.</p>
              <button class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors" onclick="stitchApp.showNoteModal()">
                Yeni Not
              </button>
            </div>
          </td>
        </tr>
      `;
    }
  } catch (error) {
    console.error('Error loading notes:', error);
    const tbody = document.getElementById('notesTableBody');
    tbody.innerHTML = `
      <tr>
        <td colspan="4" class="px-6 py-12 text-center text-red-500">
          Notlar yüklenirken bir hata oluştu.
        </td>
      </tr>
    `;
  }
};

StitchApp.prototype.loadFavorites = async function () {
  try {
    const response = await fetch('get_notes.php?favorites=1');
    const result = await response.json();
    
    console.log('Favorites API response:', result); // Debug log
    
    const tbody = document.getElementById('favoritesTableBody');
    
    // Check for both 'data' and 'notes' properties
    const notes = result.notes || result.data || [];
    
    if (result.success && notes && notes.length > 0) {
      tbody.innerHTML = notes.map(note => `
        <tr class="border-b border-border-light dark:border-border-dark hover:bg-accent-light dark:hover:bg-accent-dark">
          <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-foreground-light dark:text-foreground-dark cursor-pointer" onclick="stitchApp.showNoteModal(${note.id})">${note.title}</td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">${this.formatDate(note.created_at)}</td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">${this.formatDate(note.updated_at)}</td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">
            <div class="flex items-center gap-2">
              <button 
                onclick="event.stopPropagation(); stitchApp.toggleFavorite(${note.id}, ${note.is_favorite || 0})"
                class="p-2 rounded-lg hover:bg-accent-light dark:hover:bg-accent-dark transition-colors"
                title="Favorilerden çıkar"
              >
                <svg class="w-4 h-4 text-yellow-500 fill-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
              </button>
              <button 
                onclick="event.stopPropagation(); stitchApp.showNoteModal(${note.id})"
                class="p-2 rounded-lg hover:bg-accent-light dark:hover:bg-accent-dark transition-colors"
                title="Düzenle"
              >
                <svg class="w-4 h-4 text-subtle-light dark:text-subtle-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
            </div>
          </td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = `
        <tr>
          <td colspan="4" class="px-6 py-12 text-center text-subtle-light dark:text-subtle-dark">
            <div class="flex flex-col items-center">
              <svg class="w-12 h-12 mb-4 text-subtle-light dark:text-subtle-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
              </svg>
              <p class="text-lg font-medium mb-2">Henüz favori not yok</p>
              <p class="text-sm mb-4">Notlarınızı favorilere ekleyerek burada görebilirsiniz.</p>
            </div>
          </td>
        </tr>
      `;
    }
  } catch (error) {
    console.error('Error loading favorites:', error);
    const tbody = document.getElementById('favoritesTableBody');
    tbody.innerHTML = `
      <tr>
        <td colspan="4" class="px-6 py-12 text-center text-red-500">
          Favori notlar yüklenirken bir hata oluştu.
        </td>
      </tr>
    `;
  }
};

StitchApp.prototype.toggleFavorite = async function (noteId, currentStatus) {
  try {
    const response = await fetch("toggle_favorite.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id=${noteId}&is_favorite=${currentStatus ? 0 : 1}`,
    });

    const result = await response.json();

    if (result.success) {
      // Refresh notes list
      if (this.currentSection === "notes") {
        this.loadNotes();
      }
      // Refresh favorites if on favorites page
      if (this.currentSection === "favorites") {
        this.loadFavorites();
      }
      this.loadDashboardData(); // Refresh dashboard stats
    } else {
      alert("Hata: " + (result.message || "Favori durumu değiştirilemedi"));
    }
  } catch (error) {
    console.error("Error toggling favorite:", error);
    alert("Favori durumu değiştirilirken bir hata oluştu");
  }
};

StitchApp.prototype.deleteNote = async function (noteId) {
  if (!confirm("Bu notu silmek istediğinizden emin misiniz?")) {
    return;
  }

  try {
    const response = await fetch("not_sil.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id=${noteId}`,
    });

    const result = await response.json();

    if (result.success) {
      if (this.currentSection === "notes") {
        this.loadNotes();
      }
      this.loadDashboardData(); // Refresh dashboard stats
      this.loadRecentActivity(); // Refresh recent activities
    } else {
      alert("Hata: " + (result.message || "Not silinemedi"));
    }
  } catch (error) {
    console.error("Error deleting note:", error);
    alert("Not silinirken bir hata oluştu");
  }
};

// New Item Modal function
function showNewItemModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Yeni Öğe Ekle</h2>
                <button class="text-gray-400 hover:text-gray-600" onclick="modalManager.closeModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <button 
                    onclick="stitchApp.showNoteModal(); modalManager.closeModal();"
                    class="w-full flex items-center gap-3 p-4 rounded-lg border border-border-light dark:border-border-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-colors"
                >
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="font-medium">Yeni Not</h3>
                        <p class="text-sm text-subtle-light dark:text-subtle-dark">Yeni bir not oluştur</p>
                    </div>
                </button>
                
                <button 
                    onclick="stitchApp.showTaskModal(); modalManager.closeModal();"
                    class="w-full flex items-center gap-3 p-4 rounded-lg border border-border-light dark:border-border-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-colors"
                >
                    <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="font-medium">Yeni Görev</h3>
                        <p class="text-sm text-subtle-light dark:text-subtle-dark">Yeni bir görev oluştur</p>
                    </div>
                </button>
                
                <button 
                    onclick="alert('Etiket özelliği yakında eklenecek!'); modalManager.closeModal();"
                    class="w-full flex items-center gap-3 p-4 rounded-lg border border-border-light dark:border-border-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-colors"
                >
                    <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="font-medium">Yeni Etiket</h3>
                        <p class="text-sm text-subtle-light dark:text-subtle-dark">Yeni bir etiket oluştur</p>
                    </div>
                </button>
            </div>
            
            <div class="flex justify-end pt-4 border-t border-border-light dark:border-border-dark">
                <button 
                    onclick="modalManager.closeModal()"
                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
                >
                    İptal
                </button>
            </div>
        </div>
    `;
    
    modalManager.showModal(modalContent);
}

// Logout confirmation function
function confirmLogout() {
    if (confirm('Çıkış yapmak istediğinizden emin misiniz?')) {
        window.location.href = 'logout.php';
    }
}
