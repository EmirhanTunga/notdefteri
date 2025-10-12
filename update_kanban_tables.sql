-- Kanban Board İçin Gelişmiş Özellikler - Veritabanı Güncellemeleri

-- 1. kanban_tasks tablosuna yeni kolonlar ekle
ALTER TABLE kanban_tasks 
ADD COLUMN is_template BOOLEAN DEFAULT FALSE,
ADD COLUMN recurrence_type ENUM('none', 'daily', 'weekly', 'monthly') DEFAULT 'none',
ADD COLUMN recurrence_interval INT DEFAULT 1,
ADD COLUMN parent_task_id INT DEFAULT NULL,
ADD COLUMN completion_percentage DECIMAL(5,2) DEFAULT 0.00,
ADD COLUMN estimated_hours DECIMAL(5,2) DEFAULT NULL,
ADD COLUMN actual_hours DECIMAL(5,2) DEFAULT NULL;

-- 2. Alt görevler için tablo
CREATE TABLE IF NOT EXISTS kanban_subtasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES kanban_tasks(id) ON DELETE CASCADE
);

-- 3. Görev şablonları için tablo  
CREATE TABLE IF NOT EXISTS kanban_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    estimated_hours DECIMAL(5,2) DEFAULT NULL,
    subtasks JSON DEFAULT NULL, -- Alt görev şablonları JSON olarak
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Kullanıcı hedefleri için tablo
CREATE TABLE IF NOT EXISTS kanban_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    goal_type ENUM('daily', 'weekly', 'monthly') NOT NULL,
    target_count INT NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_goal_period (user_id, goal_type, period_start)
);

-- 5. Tekrarlanan görevler için takip tablosu
CREATE TABLE IF NOT EXISTS kanban_recurring_instances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_task_id INT NOT NULL,
    instance_date DATE NOT NULL,
    is_generated BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_task_id) REFERENCES kanban_tasks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_task_date (parent_task_id, instance_date)
);

-- Indexler ekleme (performans için)
CREATE INDEX idx_kanban_tasks_user_status ON kanban_tasks(user_id, status);
CREATE INDEX idx_kanban_tasks_created_date ON kanban_tasks(created_at);
CREATE INDEX idx_kanban_tasks_due_date ON kanban_tasks(due_date);
CREATE INDEX idx_kanban_tasks_recurrence ON kanban_tasks(recurrence_type, recurrence_interval);
CREATE INDEX idx_kanban_subtasks_task ON kanban_subtasks(task_id);
CREATE INDEX idx_kanban_goals_user_period ON kanban_goals(user_id, period_start, period_end);