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
    const navLinks = document.querySelectorAll('.nav-link[data-section]');
    const contentSections = document.querySelectorAll('.content-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav links
            navLinks.forEach(navLink => {
                navLink.classList.remove('active');
            });
            
            // Add active class to clicked link
            this.classList.add('active');
            
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
    
    // New Note Button functionality
    const newNoteBtn = document.getElementById('newNoteBtn');
    if (newNoteBtn) {
        newNoteBtn.addEventListener('click', function() {
            // Show note modal directly
            stitchApp.showNoteModal();
        });
    }
    
    // Initialize with notes section active
    const notesLink = document.querySelector('.nav-link[data-section="notes"]');
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
    const navLinks = document.querySelectorAll('.nav-link[data-section]');
    console.log('Found navigation links:', navLinks.length);
    
    navLinks.forEach((link) => {
      const section = link.getAttribute("data-section");
      console.log('Setting up navigation for section:', section);
      
      link.addEventListener("click", (e) => {
        e.preventDefault();
        console.log('Navigation link clicked for section:', section);
        this.navigateToSection(section);
      });
    });
  }

  navigateToSection(section) {
    console.log('Navigating to section:', section);
    
    // Update active nav link
    document.querySelectorAll('.nav-link').forEach((link) => {
      link.classList.remove("active");
    });
    const activeLink = document.querySelector(`[data-section="${section}"]`);
    if (activeLink) {
      activeLink.classList.add("active");
    }

    // Hide all sections
    document.querySelectorAll(".content-section").forEach((sec) => {
      sec.classList.remove("active");
      sec.style.display = "none";
    });

    // Show target section
    const targetSection = document.getElementById(`${section}-section`);
    console.log('Target section found:', !!targetSection);
    if (targetSection) {
      targetSection.style.display = "block";
      targetSection.classList.add("active");
    }

    this.currentSection = section;
    console.log('Current section set to:', this.currentSection);

    // Load section-specific data
    console.log('Loading section data for:', section);
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
    console.log('loadSectionData called with section:', section);
    
    switch (section) {
      case "notes":
        console.log('Loading notes...');
        await this.loadNotes();
        break;
      case "favorites":
        console.log('Loading favorites...');
        await this.loadFavorites();
        break;
      case "kanban":
        console.log('Loading kanban tasks...');
        await this.loadKanbanTasks();
        this.setupKanbanEventListeners();
        break;
      case "daily-tasks":
        console.log('Loading daily tasks...');
        await this.loadDailyTasks();
        break;
      case "weekly-tasks":
        console.log('Loading weekly tasks...');
        await this.loadWeeklyTasks();
        break;
      case "public-notes":
        console.log('Loading public notes...');
        await this.loadPublicNotes();
        break;
      case "friends":
        console.log('Loading friends...');
        await this.loadFriends();
        break;
      case "tasks":
        console.log('Loading tasks...');
        await this.loadTasks();
        break;
      case "settings":
        console.log('Loading settings...');
        await this.loadSettings();
        break;
      case "gemini":
        console.log('Loading Gemini AI...');
        // Gemini kendi gemini.js dosyasında yönetiliyor
        break;
      default:
        console.log('Unknown section:', section);
    }
  }

  async loadNotes() {
    try {
      const response = await fetch("get_notes.php");
      const data = await response.json();

      const container = document.getElementById("notesContainer");
      if (!container) return;

      if (data.success && data.notes.length > 0) {
        const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'];
        
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
                                (note, index) => {
                                  const noteColor = colors[index % colors.length];
                                  return `
                                <tr class="border-b border-border-light dark:border-border-dark hover:bg-accent-light dark:hover:bg-accent-dark">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 rounded-full mr-3" style="background-color: ${noteColor}"></div>
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
                                            <button class="favorite-btn transition-transform hover:scale-125" onclick="app.toggleFavorite(${note.id}, ${note.is_favorite || 0})" title="${note.is_favorite ? 'Favorilerden çıkar' : 'Favorilere ekle'}">
                                                <svg class="w-5 h-5 transition-colors ${note.is_favorite ? 'text-yellow-500 fill-current' : 'text-gray-400 hover:text-yellow-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                </svg>
                                            </button>
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
                            `;
                                }
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
    const container = document.getElementById("dailyTasksTable");
    if (!container) return;
    
    try {
      const response = await fetch('task_get.php?type=daily');
      const result = await response.json();
      
      if (result.success && result.tasks.length > 0) {
        container.innerHTML = result.tasks.map(task => `
          <tr class="hover:bg-accent-light dark:hover:bg-accent-dark transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <input type="checkbox" ${task.is_done ? 'checked' : ''} 
                       class="w-4 h-4 text-primary rounded focus:ring-primary border-gray-300"
                       onchange="toggleTaskComplete('daily', ${task.id}, this.checked)">
                <div>
                  <div class="font-semibold text-foreground-light dark:text-foreground-dark">${task.task}</div>
                  ${task.description ? `<div class="text-sm text-subtle-light dark:text-subtle-dark">${task.description}</div>` : ''}
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 rounded-full text-xs font-medium ${
                task.priority === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300' :
                task.priority === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' :
                'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
              }">
                ${task.priority === 'high' ? '🔴 Yüksek' : task.priority === 'medium' ? '🟡 Orta' : '🟢 Düşük'}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-subtle-light dark:text-subtle-dark">
              ${task.duration_minutes} dk
            </td>
            <td class="px-6 py-4 text-sm text-subtle-light dark:text-subtle-dark">
              ${task.due_date ? new Date(task.due_date).toLocaleDateString('tr-TR') : 'Belirtilmemiş'}
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <button onclick="editTask('daily', ${task.id})" 
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button onclick="deleteTask('daily', ${task.id})" 
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        `).join('');
      } else {
        container.innerHTML = `
          <tr class="hover:bg-accent-light dark:hover:bg-accent-dark transition-colors">
            <td colspan="5" class="px-6 py-16 text-center text-subtle-light dark:text-subtle-dark">
              <div class="flex flex-col items-center gap-4">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-full flex items-center justify-center">
                  <svg class="w-12 h-12 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-lg font-semibold mb-1">Henüz günlük görev yok</p>
                  <p class="text-sm">Bugünün görevlerini ekleyerek başlayın</p>
                </div>
              </div>
            </td>
          </tr>
        `;
      }
    } catch (error) {
      console.error('Error loading daily tasks:', error);
      container.innerHTML = `
        <tr>
          <td colspan="5" class="px-6 py-16 text-center text-red-500">
            Görevler yüklenirken hata oluştu
          </td>
        </tr>
      `;
    }
  }

  async loadWeeklyTasks() {
    const container = document.getElementById("weeklyTasksTable");
    if (!container) return;
    
    try {
      const response = await fetch('task_get.php?type=weekly');
      const result = await response.json();
      
      if (result.success && result.tasks.length > 0) {
        container.innerHTML = result.tasks.map(task => `
          <tr class="hover:bg-accent-light dark:hover:bg-accent-dark transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <input type="checkbox" ${task.is_done ? 'checked' : ''} 
                       class="w-4 h-4 text-primary rounded focus:ring-primary border-gray-300"
                       onchange="toggleTaskComplete('weekly', ${task.id}, this.checked)">
                <div>
                  <div class="font-semibold text-foreground-light dark:text-foreground-dark">${task.task}</div>
                  ${task.description ? `<div class="text-sm text-subtle-light dark:text-subtle-dark">${task.description}</div>` : ''}
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 rounded-full text-xs font-medium ${
                task.priority === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300' :
                task.priority === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' :
                'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
              }">
                ${task.priority === 'high' ? '🔴 Yüksek' : task.priority === 'medium' ? '🟡 Orta' : '🟢 Düşük'}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-subtle-light dark:text-subtle-dark">
              ${task.duration_minutes} dk
            </td>
            <td class="px-6 py-4 text-sm text-subtle-light dark:text-subtle-dark">
              ${task.due_date ? new Date(task.due_date).toLocaleDateString('tr-TR') : 'Belirtilmemiş'}
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <button onclick="editTask('weekly', ${task.id})" 
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button onclick="deleteTask('weekly', ${task.id})" 
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        `).join('');
      } else {
        container.innerHTML = `
          <tr class="hover:bg-accent-light dark:hover:bg-accent-dark transition-colors">
            <td colspan="5" class="px-6 py-16 text-center text-subtle-light dark:text-subtle-dark">
              <div class="flex flex-col items-center gap-4">
                <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-full flex items-center justify-center">
                  <svg class="w-12 h-12 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-lg font-semibold mb-1">Henüz haftalık görev yok</p>
                  <p class="text-sm text-subtle-light dark:text-subtle-dark">Bu haftanın görevlerini planlayın</p>
                </div>
              </div>
            </td>
          </tr>
        `;
      }
    } catch (error) {
      console.error('Error loading weekly tasks:', error);
      container.innerHTML = `
        <tr>
          <td colspan="5" class="px-6 py-16 text-center text-red-500">
            Görevler yüklenirken hata oluştu
          </td>
        </tr>
      `;
    }
  }

  async loadMonthlyTasks() {
    const container = document.getElementById("monthlyTasksTable");
    if (!container) return;
    
    try {
      const response = await fetch('task_get.php?type=monthly');
      const result = await response.json();
      
      if (result.success && result.tasks.length > 0) {
        container.innerHTML = result.tasks.map(task => `
          <tr class="hover:bg-accent-light dark:hover:bg-accent-dark transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <input type="checkbox" ${task.is_done ? 'checked' : ''} 
                       class="w-4 h-4 text-primary rounded focus:ring-primary border-gray-300"
                       onchange="toggleTaskComplete('monthly', ${task.id}, this.checked)">
                <div>
                  <div class="font-semibold text-foreground-light dark:text-foreground-dark">${task.task}</div>
                  ${task.description ? `<div class="text-sm text-subtle-light dark:text-subtle-dark">${task.description}</div>` : ''}
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 rounded-full text-xs font-medium ${
                task.priority === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300' :
                task.priority === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' :
                'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
              }">
                ${task.priority === 'high' ? '🔴 Yüksek' : task.priority === 'medium' ? '🟡 Orta' : '🟢 Düşük'}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-subtle-light dark:text-subtle-dark">
              ${task.duration_minutes} dk
            </td>
            <td class="px-6 py-4 text-sm text-subtle-light dark:text-subtle-dark">
              ${task.due_date ? new Date(task.due_date).toLocaleDateString('tr-TR') : 'Belirtilmemiş'}
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <button onclick="editTask('monthly', ${task.id})" 
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button onclick="deleteTask('monthly', ${task.id})" 
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        `).join('');
      } else {
        container.innerHTML = `
          <tr class="hover:bg-accent-light dark:hover:bg-accent-dark transition-colors">
            <td colspan="5" class="px-6 py-16 text-center text-subtle-light dark:text-subtle-dark">
              <div class="flex flex-col items-center gap-4">
                <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/20 dark:to-pink-900/20 rounded-full flex items-center justify-center">
                  <svg class="w-12 h-12 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-lg font-semibold mb-1">Henüz aylık görev yok</p>
                  <p class="text-sm text-subtle-light dark:text-subtle-dark">Bu ayın uzun vadeli hedeflerini belirleyin</p>
                </div>
              </div>
            </td>
          </tr>
        `;
      }
    } catch (error) {
      console.error('Error loading monthly tasks:', error);
      container.innerHTML = `
        <tr>
          <td colspan="5" class="px-6 py-16 text-center text-red-500">
            Görevler yüklenirken hata oluştu
          </td>
        </tr>
      `;
    }
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

  async loadTasks() {
    console.log('Loading tasks...');
    
    // Task tab switching
    const taskTabs = document.querySelectorAll('.task-tab');
    const taskPeriodContents = document.querySelectorAll('.task-period-content');
    
    taskTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const period = tab.dataset.period;
        
        // Update active tab
        taskTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Update visible content
        taskPeriodContents.forEach(content => {
          content.classList.remove('active');
          content.style.display = 'none';
        });
        
        const targetContent = document.getElementById(`${period}-tasks-content`);
        if (targetContent) {
          targetContent.style.display = 'block';
          targetContent.classList.add('active');
        }
      });
    });
    
    // Add new task button
    const addTaskBtn = document.getElementById('addNewTaskBtn');
    if (addTaskBtn) {
      addTaskBtn.addEventListener('click', () => {
        this.showTaskModal();
      });
    }
    
    // Load tasks from existing endpoints
    this.loadDailyTasks();
    this.loadWeeklyTasks();
  }

  async loadSettings() {
    console.log('Loading settings...');
    
    // Profil Güncelleme
    const updateProfileBtn = document.getElementById('updateProfileBtn');
    if (updateProfileBtn) {
      updateProfileBtn.addEventListener('click', async () => {
        const fullName = document.getElementById('fullName').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!fullName || !email) {
          alert('Lütfen tüm alanları doldurun');
          return;
        }
        
        const formData = new FormData();
        formData.append('username', fullName);
        formData.append('email', email);
        if (password) {
          formData.append('password', password);
        }
        
        try {
          const response = await fetch('update_account.php', {
            method: 'POST',
            body: formData
          });
          
          const result = await response.json();
          
          if (result.success) {
            alert('✅ Profil başarıyla güncellendi!');
            document.getElementById('password').value = '';
          } else {
            alert('❌ Hata: ' + (result.message || 'Profil güncellenemedi'));
          }
        } catch (error) {
          console.error('Error updating profile:', error);
          alert('Profil güncellenirken bir hata oluştu');
        }
      });
    }
    
    // Gizlilik Ayarları Kaydet
    const savePrivacyBtn = document.getElementById('savePrivacyBtn');
    if (savePrivacyBtn) {
      savePrivacyBtn.addEventListener('click', () => {
        alert('✅ Gizlilik ayarları kaydedildi!');
        // Burada gizlilik ayarlarını kaydetme işlemi yapılabilir
      });
    }
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

  showTaskModal(type = null) {
    // Get current active tab if no type specified
    if (!type) {
      const activeTab = document.querySelector('.task-tab.active');
      type = activeTab ? activeTab.dataset.period : 'daily';
    }
    
    // Calculate auto due date based on type
    const now = new Date();
    let dueDate = new Date();
    
    switch(type) {
      case 'daily':
        dueDate.setDate(now.getDate() + 1);
        dueDate.setHours(18, 0, 0, 0); // Tomorrow 6 PM
        break;
      case 'weekly':
        dueDate.setDate(now.getDate() + 7);
        dueDate.setHours(18, 0, 0, 0); // Next week same day 6 PM
        break;
      case 'monthly':
        dueDate.setMonth(now.getMonth() + 1);
        dueDate.setHours(18, 0, 0, 0); // Next month same day 6 PM
        break;
      default:
        dueDate.setDate(now.getDate() + 1);
        dueDate.setHours(18, 0, 0, 0);
    }
    
    const modalContent = `
        <div class="p-8 max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-primary to-primary-accent rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-foreground-light dark:text-foreground-dark">Yeni Görev Ekle</h2>
                        <p class="text-sm text-subtle-light dark:text-subtle-dark">${this.getTaskTypeLabel(type)} görev oluşturun</p>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" onclick="modalManager.closeModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="taskForm" class="space-y-6">
                <!-- Task Type Selection -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <label class="block text-sm font-semibold text-blue-700 dark:text-blue-300 mb-3">Görev Tipi</label>
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="task_type" value="daily" ${type === 'daily' ? 'checked' : ''} class="sr-only peer">
                            <div class="peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center transition-all duration-200 hover:border-blue-300">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <div class="text-sm font-medium">Günlük</div>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="task_type" value="weekly" ${type === 'weekly' ? 'checked' : ''} class="sr-only peer">
                            <div class="peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:border-indigo-500 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center transition-all duration-200 hover:border-indigo-300">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div class="text-sm font-medium">Haftalık</div>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="task_type" value="monthly" ${type === 'monthly' ? 'checked' : ''} class="sr-only peer">
                            <div class="peer-checked:bg-purple-500 peer-checked:text-white peer-checked:border-purple-500 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center transition-all duration-200 hover:border-purple-300">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <div class="text-sm font-medium">Aylık</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Task Title -->
                <div>
                    <label for="taskTitle" class="block text-sm font-semibold text-foreground-light dark:text-foreground-dark mb-2">Görev Başlığı</label>
                    <input 
                        type="text" 
                        id="taskTitle" 
                        name="title" 
                        class="w-full px-4 py-3 border-2 border-border-light dark:border-border-dark rounded-xl bg-card-light dark:bg-card-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all duration-200 text-lg"
                        placeholder="Görev başlığını girin..."
                        required
                    >
                </div>
                
                <!-- Task Description -->
                <div>
                    <label for="taskDescription" class="block text-sm font-semibold text-foreground-light dark:text-foreground-dark mb-2">Açıklama</label>
                    <textarea 
                        id="taskDescription" 
                        name="description" 
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-border-light dark:border-border-dark rounded-xl bg-card-light dark:bg-card-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-none transition-all duration-200"
                        placeholder="Görev detaylarını açıklayın..."
                    ></textarea>
                </div>
                
                <!-- Priority and Duration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="taskPriority" class="block text-sm font-semibold text-foreground-light dark:text-foreground-dark mb-2">Öncelik</label>
                        <select 
                            id="taskPriority" 
                            name="priority"
                            class="w-full px-4 py-3 border-2 border-border-light dark:border-border-dark rounded-xl bg-card-light dark:bg-card-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all duration-200"
                        >
                            <option value="low">🟢 Düşük</option>
                            <option value="medium" selected>🟡 Orta</option>
                            <option value="high">🔴 Yüksek</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="taskDuration" class="block text-sm font-semibold text-foreground-light dark:text-foreground-dark mb-2">Tahmini Süre</label>
                        <select 
                            id="taskDuration" 
                            name="duration"
                            class="w-full px-4 py-3 border-2 border-border-light dark:border-border-dark rounded-xl bg-card-light dark:bg-card-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all duration-200"
                        >
                            <option value="15">15 dakika</option>
                            <option value="30">30 dakika</option>
                            <option value="60" selected>1 saat</option>
                            <option value="120">2 saat</option>
                            <option value="240">4 saat</option>
                            <option value="480">8 saat</option>
                        </select>
                    </div>
                </div>
                
                <!-- Due Date -->
                <div>
                    <label for="taskDueDate" class="block text-sm font-semibold text-foreground-light dark:text-foreground-dark mb-2">Bitiş Tarihi</label>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                        <input 
                            type="datetime-local" 
                            id="taskDueDate" 
                            name="due_date"
                            class="w-full px-4 py-3 border-2 border-green-200 dark:border-green-800 rounded-xl bg-white dark:bg-gray-800 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all duration-200"
                        >
                        <p class="text-xs text-green-600 dark:text-green-400 mt-2">💡 Bitiş tarihi görev tipine göre otomatik ayarlandı</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-4 pt-6 border-t border-border-light dark:border-border-dark">
                    <button 
                        type="button" 
                        onclick="modalManager.closeModal()"
                        class="px-6 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors font-medium"
                    >
                        İptal
                    </button>
                    <button 
                        type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-primary to-primary-accent text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Görev Oluştur
                    </button>
                </div>
            </form>
        </div>
    `;
    
    modalManager.showModal(modalContent);
    
    // Set auto-calculated due date
    const dueDateInput = document.getElementById('taskDueDate');
    if (dueDateInput) {
        dueDateInput.value = dueDate.toISOString().slice(0, 16);
    }
    
    // Handle task type change to update due date
    const taskTypeInputs = document.querySelectorAll('input[name="task_type"]');
    taskTypeInputs.forEach(input => {
        input.addEventListener('change', () => {
            const selectedType = input.value;
            const now = new Date();
            let newDueDate = new Date();
            
            switch(selectedType) {
                case 'daily':
                    newDueDate.setDate(now.getDate() + 1);
                    newDueDate.setHours(18, 0, 0, 0);
                    break;
                case 'weekly':
                    newDueDate.setDate(now.getDate() + 7);
                    newDueDate.setHours(18, 0, 0, 0);
                    break;
                case 'monthly':
                    newDueDate.setMonth(now.getMonth() + 1);
                    newDueDate.setHours(18, 0, 0, 0);
                    break;
            }
            
            if (dueDateInput) {
                dueDateInput.value = newDueDate.toISOString().slice(0, 16);
            }
        });
    });
    
    // Handle form submission
    const taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(taskForm);
            const selectedType = formData.get('task_type');
            
            const taskData = {
                title: formData.get('title'),
                description: formData.get('description'),
                priority: formData.get('priority'),
                duration: formData.get('duration'),
                due_date: formData.get('due_date'),
                type: selectedType
            };
            
            try {
                const response = await fetch('task_add.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(taskData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(`${this.getTaskTypeLabel(selectedType)} görev başarıyla eklendi!`, 'success');
                    modalManager.closeModal();
                    
                    // Reload tasks based on type
                    if (selectedType === 'daily') {
                        this.loadDailyTasks();
                    } else if (selectedType === 'weekly') {
                        this.loadWeeklyTasks();
                    } else if (selectedType === 'monthly') {
                        this.loadMonthlyTasks();
                    }
                } else {
                    showToast(result.error || 'Görev eklenirken hata oluştu', 'error');
                }
            } catch (error) {
                console.error('Error adding task:', error);
                showToast('Görev eklenirken hata oluştu: ' + error.message, 'error');
            }
        });
    }
  }
  
  getTaskTypeLabel(type) {
    switch(type) {
      case 'daily': return 'Günlük';
      case 'weekly': return 'Haftalık';
      case 'monthly': return 'Aylık';
      default: return 'Görev';
    }
  }
  
  // Görev tamamlama fonksiyonu
  async toggleTaskComplete(type, taskId, isComplete) {
    try {
      const response = await fetch('task_toggle.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          type: type,
          task_id: taskId,
          is_done: isComplete
        })
      });
      
      const result = await response.json();
      
      if (result.success) {
        showToast(isComplete ? 'Görev tamamlandı!' : 'Görev tamamlanmadı olarak işaretlendi', 'success');
        // Görevleri yeniden yükle
        if (type === 'daily') {
          this.loadDailyTasks();
        } else if (type === 'weekly') {
          this.loadWeeklyTasks();
        } else if (type === 'monthly') {
          this.loadMonthlyTasks();
        }
      } else {
        showToast(result.error || 'Görev durumu güncellenemedi', 'error');
      }
    } catch (error) {
      console.error('Error toggling task:', error);
      showToast('Görev durumu güncellenirken hata oluştu', 'error');
    }
  }
  
  // Görev silme fonksiyonu
  async deleteTask(type, taskId) {
    if (!confirm('Bu görevi silmek istediğinizden emin misiniz?')) {
      return;
    }
    
    try {
      const response = await fetch('task_delete.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          type: type,
          task_id: taskId
        })
      });
      
      const result = await response.json();
      
      if (result.success) {
        showToast('Görev silindi!', 'success');
        // Görevleri yeniden yükle
        if (type === 'daily') {
          this.loadDailyTasks();
        } else if (type === 'weekly') {
          this.loadWeeklyTasks();
        } else if (type === 'monthly') {
          this.loadMonthlyTasks();
        }
      } else {
        showToast(result.error || 'Görev silinemedi', 'error');
      }
    } catch (error) {
      console.error('Error deleting task:', error);
      showToast('Görev silinirken hata oluştu', 'error');
    }
  }
  
  // Görev düzenleme fonksiyonu
  async editTask(type, taskId) {
    showToast('Görev düzenleme özelliği yakında eklenecek!', 'info');
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
      const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'];
      
      tbody.innerHTML = notes.map((note, index) => {
        const noteColor = colors[index % colors.length];
        return `
        <tr class="border-b border-border-light dark:border-border-dark hover:bg-accent-light dark:hover:bg-accent-dark">
          <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-foreground-light dark:text-foreground-dark cursor-pointer" onclick="stitchApp.showNoteModal(${note.id})">
            <div class="flex items-center">
              <div class="w-2 h-2 rounded-full mr-3" style="background-color: ${noteColor}"></div>
              ${note.title}
            </div>
          </td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">${this.formatDate(note.created_at)}</td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">${this.formatDate(note.updated_at)}</td>
          <td class="px-6 py-5 whitespace-nowrap text-sm text-subtle-light dark:text-subtle-dark">
            <div class="flex items-center gap-2">
              <button 
                onclick="event.stopPropagation(); stitchApp.toggleFavorite(${note.id}, ${note.is_favorite || 0})"
                class="p-2 rounded-lg hover:bg-accent-light dark:hover:bg-accent-dark transition-transform hover:scale-125"
                title="Favorilerden çıkar"
              >
                <svg class="w-5 h-5 text-yellow-500 fill-current transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
      `;
      }).join('');
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

// Global toggleFavorite function for use with onclick
window.toggleFavorite = async function (noteId, currentStatus) {
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
      // Refresh notes list if on notes page
      if (window.app && window.app.currentSection === "notes") {
        await window.app.loadNotes();
      }
      // Refresh favorites if on favorites page
      if (window.app && window.app.currentSection === "favorites") {
        await window.app.loadFavorites();
      }
      if (window.app) {
        window.app.loadDashboardData(); // Refresh dashboard stats
      }
      
      // Show success message
      const message = currentStatus ? '❌ Favorilerden çıkarıldı' : '⭐ Favorilere eklendi';
      showToast(message);
    } else {
      alert("Hata: " + (result.message || "Favori durumu değiştirilemedi"));
    }
  } catch (error) {
    console.error("Error toggling favorite:", error);
    alert("Favori durumu değiştirilirken bir hata oluştu");
  }
};

// Helper function to show toast messages
function showToast(message, type = 'info') {
  const toast = document.createElement('div');
  
  // Color based on type
  let bgColor = 'bg-blue-500';
  let icon = 'ℹ️';
  
  if (type === 'success') {
    bgColor = 'bg-green-500';
    icon = '✅';
  } else if (type === 'error') {
    bgColor = 'bg-red-500';
    icon = '❌';
  } else if (type === 'warning') {
    bgColor = 'bg-yellow-500';
    icon = '⚠️';
  }
  
  toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-fade-in flex items-center gap-2 font-medium`;
  toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
  
  document.body.appendChild(toast);
  
  // Auto remove after 3 seconds
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Global fonksiyonlar
window.toggleTaskComplete = function(type, taskId, isComplete) {
  if (window.app) {
    window.app.toggleTaskComplete(type, taskId, isComplete);
  }
};

window.deleteTask = function(type, taskId) {
  if (window.app) {
    window.app.deleteTask(type, taskId);
  }
};

window.editTask = function(type, taskId) {
  if (window.app) {
    window.app.editTask(type, taskId);
  }
};

StitchApp.prototype.toggleFavorite = window.toggleFavorite;

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

StitchApp.prototype.saveKanbanTask = async function () {
  const form = document.getElementById("taskForm");
  const formData = new FormData(form);

  try {
    const response = await fetch("kanban_task_add.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      modalManager.closeModal();
      this.loadKanbanTasks(); // Refresh kanban board
      this.loadDashboardData(); // Refresh dashboard stats
    } else {
      alert("Hata: " + (result.message || "Görev kaydedilemedi"));
    }
  } catch (error) {
    console.error("Error saving kanban task:", error);
    alert("Görev kaydedilirken bir hata oluştu");
  }
};

StitchApp.prototype.loadKanbanTasks = async function () {
  try {
    console.log('Loading kanban tasks...');
    const response = await fetch("get_kanban_tasks.php");
    const result = await response.json();
    
    console.log('Kanban tasks response:', result);
    
    if (result.success) {
      console.log('Tasks found:', result.tasks);
      this.updateKanbanBoard(result.tasks || []);
    } else {
      console.error('Failed to load kanban tasks:', result.message);
      // Show empty state
      this.updateKanbanBoard([]);
    }
  } catch (error) {
    console.error("Error loading kanban tasks:", error);
    // Show empty state on error
    this.updateKanbanBoard([]);
  }
};

StitchApp.prototype.updateKanbanBoard = function (tasks) {
  console.log('updateKanbanBoard called with tasks:', tasks);
  
  // Separate active and completed tasks
  const activeTasks = tasks.filter(task => task.status !== 'completed');
  const completedTasks = tasks.filter(task => task.status === 'completed');

  console.log('Active tasks:', activeTasks);
  console.log('Completed tasks:', completedTasks);

  // Update active tasks column
  this.updateActiveTasksColumn(activeTasks);
  // Update completed tasks column
  this.updateCompletedTasksColumn(completedTasks);
};

StitchApp.prototype.updateActiveTasksColumn = function (tasks) {
  console.log('updateActiveTasksColumn called with:', tasks);
  
  const container = document.getElementById('active-tasks-container');
  const countElement = document.querySelector('#kanban-section .lg\\:col-span-2 .text-lg');

  console.log('Container found:', !!container);
  console.log('Count element found:', !!countElement);

  if (container && countElement) {
    // Update count
    countElement.textContent = tasks.length;
    console.log('Updated count to:', tasks.length);

    // Update tasks
    if (tasks.length > 0) {
      console.log('Rendering', tasks.length, 'active tasks');
      container.innerHTML = tasks.map(task => `
        <div class="kanban-task-card bg-accent-light dark:bg-accent-dark p-4 rounded-lg border border-border-light dark:border-border-dark cursor-pointer relative group" 
             data-task-id="${task.id}">
          <div class="flex items-start justify-between">
            <div class="flex items-start gap-3 flex-1">
              <!-- Checkbox -->
              <div class="task-checkbox mt-1">
                <button onclick="event.stopPropagation(); stitchApp.markTaskCompleted(${task.id})" 
                        class="w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center justify-center transition-all duration-200 group-hover:border-green-400">
                  <svg class="w-4 h-4 text-green-500 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </button>
              </div>
              
              <div class="flex-1">
                <h4 class="text-base font-semibold text-foreground-light dark:text-foreground-dark mb-2">${task.title}</h4>
                <p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">${task.description || ''}</p>
                <div class="flex items-center gap-4 text-sm text-subtle-light dark:text-subtle-dark">
                  <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    ${task.due_date ? new Date(task.due_date).toLocaleDateString('tr-TR') : 'Tarih yok'}
                  </span>
                  <span class="px-2 py-1 rounded text-xs ${this.getPriorityClass(task.priority)}">${this.getPriorityText(task.priority)}</span>
                  <span class="px-2 py-1 rounded text-xs ${this.getStatusClass(task.status)}">${this.getStatusText(task.status)}</span>
                </div>
              </div>
            </div>
            
            <div class="flex items-center gap-2 ml-4">
              <button onclick="event.stopPropagation(); stitchApp.toggleTaskStatus(${task.id}, '${task.status}')" class="text-blue-500 hover:text-blue-600 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Durum Değiştir">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
              </button>
              <button onclick="event.stopPropagation(); stitchApp.deleteKanbanTask(${task.id})" class="text-red-500 hover:text-red-600 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20" title="Sil">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      `).join('');
    } else {
      console.log('No active tasks, showing empty state');
      container.innerHTML = '<div class="text-center py-12 text-subtle-light dark:text-subtle-dark"><p class="text-lg">Henüz aktif görev yok</p><p class="text-sm mt-2">İlk görevinizi eklemek için aşağıdaki butona tıklayın</p></div>';
    }
  } else {
    console.error('Container or count element not found!');
  }
};

StitchApp.prototype.updateCompletedTasksColumn = function (tasks) {
  const container = document.getElementById('completed-tasks-container');
  const countElement = document.querySelector('#kanban-section .lg\\:col-span-1 .text-lg');

  if (container && countElement) {
    // Update count
    countElement.textContent = tasks.length;

    // Update tasks
    if (tasks.length > 0) {
      container.innerHTML = tasks.map(task => `
        <div class="completed-task-card bg-accent-light dark:bg-accent-dark p-3 rounded-lg border border-border-light dark:border-border-dark">
          <div class="flex items-start gap-2">
            <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center mt-1 flex-shrink-0">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-foreground-light dark:text-foreground-dark mb-1 line-through">${task.title}</h4>
              <p class="text-xs text-subtle-light dark:text-subtle-dark mb-2 truncate">${task.description || ''}</p>
              <div class="flex items-center gap-2 text-xs text-subtle-light dark:text-subtle-dark">
                <span class="px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Tamamlandı</span>
                ${task.due_date ? `<span class="text-xs">${new Date(task.due_date).toLocaleDateString('tr-TR')}</span>` : ''}
              </div>
            </div>
            <button onclick="stitchApp.deleteKanbanTask(${task.id})" class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 flex-shrink-0" title="Sil">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>
      `).join('');
    } else {
      container.innerHTML = '<div class="text-center py-8 text-subtle-light dark:text-subtle-dark"><p class="text-sm">Henüz tamamlanan görev yok</p></div>';
    }
  }
};

StitchApp.prototype.getPriorityClass = function (priority) {
  const classes = {
    'low': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'medium': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'high': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
  };
  return classes[priority] || classes.medium;
};

StitchApp.prototype.getPriorityText = function (priority) {
  const texts = {
    'low': 'Düşük',
    'medium': 'Orta',
    'high': 'Yüksek'
  };
  return texts[priority] || 'Orta';
};

StitchApp.prototype.getStatusClass = function (status) {
  const classes = {
    'todo': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'in_progress': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
  };
  return classes[status] || classes.todo;
};

StitchApp.prototype.getStatusText = function (status) {
  const texts = {
    'todo': 'Yapılacak',
    'in_progress': 'Devam Eden',
    'completed': 'Tamamlandı'
  };
  return texts[status] || 'Yapılacak';
};

StitchApp.prototype.toggleTaskStatus = async function (taskId, currentStatus) {
  // Status cycle: todo -> in_progress -> completed -> todo
  const statusCycle = {
    'todo': 'in_progress',
    'in_progress': 'completed',
    'completed': 'todo'
  };
  
  const newStatus = statusCycle[currentStatus];
  
  try {
    const response = await fetch("kanban_task_update.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id=${taskId}&status=${newStatus}`,
    });

    const result = await response.json();

    if (result.success) {
      this.loadKanbanTasks(); // Refresh kanban board
    } else {
      alert("Hata: " + (result.message || "Görev durumu güncellenemedi"));
    }
  } catch (error) {
    console.error("Error updating task status:", error);
    alert("Görev durumu güncellenirken bir hata oluştu");
  }
};

StitchApp.prototype.markTaskCompleted = async function (taskId) {
  try {
    const response = await fetch("kanban_task_update.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id=${taskId}&status=completed`,
    });

    const result = await response.json();

    if (result.success) {
      // Show success animation
      this.showTaskCompletedAnimation(taskId);
      // Refresh kanban board after a short delay
      setTimeout(() => {
        this.loadKanbanTasks();
      }, 500);
    } else {
      alert("Hata: " + (result.message || "Görev tamamlanamadı"));
    }
  } catch (error) {
    console.error("Error marking task completed:", error);
    alert("Görev tamamlanırken bir hata oluştu");
  }
};

StitchApp.prototype.showTaskCompletedAnimation = function (taskId) {
  const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
  if (taskCard) {
    // Add completion animation
    taskCard.style.transition = 'all 0.5s ease';
    taskCard.style.transform = 'scale(0.95)';
    taskCard.style.opacity = '0.5';
    
    // Add checkmark animation
    const checkmark = document.createElement('div');
    checkmark.className = 'absolute inset-0 flex items-center justify-center bg-green-500 bg-opacity-90 rounded-lg';
    checkmark.innerHTML = `
      <svg class="w-12 h-12 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
      </svg>
    `;
    taskCard.style.position = 'relative';
    taskCard.appendChild(checkmark);
    
    // Remove animation after delay
    setTimeout(() => {
      taskCard.style.transform = '';
      taskCard.style.opacity = '';
      checkmark.remove();
    }, 1000);
  }
};

StitchApp.prototype.deleteKanbanTask = async function (taskId) {
  if (!confirm("Bu görevi silmek istediğinizden emin misiniz?")) {
    return;
  }

  try {
    const response = await fetch("kanban_task_delete.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id=${taskId}`,
    });

    const result = await response.json();

    if (result.success) {
      this.loadKanbanTasks(); // Refresh kanban board
      this.loadDashboardData(); // Refresh dashboard stats
    } else {
      alert("Hata: " + (result.message || "Görev silinemedi"));
    }
  } catch (error) {
    console.error("Error deleting kanban task:", error);
    alert("Görev silinirken bir hata oluştu");
  }
};

StitchApp.prototype.setupKanbanEventListeners = function () {
  // Add event listener to the single "Yeni Görev Ekle" button
  const addTaskButton = document.querySelector('#kanban-section button');
  if (addTaskButton) {
    addTaskButton.addEventListener('click', () => {
      this.showTaskModal('kanban');
    });
  }
};


// Logout confirmation function
function confirmLogout() {
    if (confirm('Çıkış yapmak istediğinizden emin misiniz?')) {
        window.location.href = 'logout.php';
    }
}
