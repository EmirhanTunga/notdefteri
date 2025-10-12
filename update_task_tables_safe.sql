-- Görev tablolarını güvenli şekilde güncelleme scripti
-- Mevcut kolonları kontrol ederek sadece eksik olanları ekler

-- Daily tasks tablosunu güncelle
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'daily_tasks' 
     AND COLUMN_NAME = 'description') = 0,
    'ALTER TABLE daily_tasks ADD COLUMN description TEXT AFTER task',
    'SELECT "description column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'daily_tasks' 
     AND COLUMN_NAME = 'priority') = 0,
    'ALTER TABLE daily_tasks ADD COLUMN priority ENUM("low", "medium", "high") DEFAULT "medium" AFTER description',
    'SELECT "priority column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'daily_tasks' 
     AND COLUMN_NAME = 'duration_minutes') = 0,
    'ALTER TABLE daily_tasks ADD COLUMN duration_minutes INT DEFAULT 60 AFTER priority',
    'SELECT "duration_minutes column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'daily_tasks' 
     AND COLUMN_NAME = 'due_date') = 0,
    'ALTER TABLE daily_tasks ADD COLUMN due_date DATETIME AFTER duration_minutes',
    'SELECT "due_date column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Weekly tasks tablosunu güncelle
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'weekly_tasks' 
     AND COLUMN_NAME = 'description') = 0,
    'ALTER TABLE weekly_tasks ADD COLUMN description TEXT AFTER task',
    'SELECT "description column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'weekly_tasks' 
     AND COLUMN_NAME = 'priority') = 0,
    'ALTER TABLE weekly_tasks ADD COLUMN priority ENUM("low", "medium", "high") DEFAULT "medium" AFTER description',
    'SELECT "priority column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'weekly_tasks' 
     AND COLUMN_NAME = 'duration_minutes') = 0,
    'ALTER TABLE weekly_tasks ADD COLUMN duration_minutes INT DEFAULT 60 AFTER priority',
    'SELECT "duration_minutes column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'weekly_tasks' 
     AND COLUMN_NAME = 'due_date') = 0,
    'ALTER TABLE weekly_tasks ADD COLUMN due_date DATETIME AFTER duration_minutes',
    'SELECT "due_date column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Monthly tasks tablosunu oluştur
CREATE TABLE IF NOT EXISTS `monthly_tasks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `task` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium',
  `duration_minutes` INT DEFAULT 60,
  `due_date` DATETIME,
  `is_done` BOOLEAN DEFAULT FALSE,
  `is_favorite` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX idx_user_created (`user_id`, `created_at`),
  INDEX idx_user_due (`user_id`, `due_date`),
  INDEX idx_user_status (`user_id`, `is_done`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- İndeksler ekle (sadece yoksa)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'daily_tasks' 
     AND INDEX_NAME = 'idx_daily_tasks_user_created') = 0,
    'CREATE INDEX idx_daily_tasks_user_created ON daily_tasks(user_id, created_at)',
    'SELECT "Index already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'daily_tasks' 
     AND INDEX_NAME = 'idx_daily_tasks_user_due') = 0,
    'CREATE INDEX idx_daily_tasks_user_due ON daily_tasks(user_id, due_date)',
    'SELECT "Index already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'daily_tasks' 
     AND INDEX_NAME = 'idx_daily_tasks_user_status') = 0,
    'CREATE INDEX idx_daily_tasks_user_status ON daily_tasks(user_id, is_done)',
    'SELECT "Index already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'weekly_tasks' 
     AND INDEX_NAME = 'idx_weekly_tasks_user_created') = 0,
    'CREATE INDEX idx_weekly_tasks_user_created ON weekly_tasks(user_id, created_at)',
    'SELECT "Index already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'weekly_tasks' 
     AND INDEX_NAME = 'idx_weekly_tasks_user_due') = 0,
    'CREATE INDEX idx_weekly_tasks_user_due ON weekly_tasks(user_id, due_date)',
    'SELECT "Index already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'weekly_tasks' 
     AND INDEX_NAME = 'idx_weekly_tasks_user_status') = 0,
    'CREATE INDEX idx_weekly_tasks_user_status ON weekly_tasks(user_id, is_done)',
    'SELECT "Index already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
