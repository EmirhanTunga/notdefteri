-- Basit ve güvenli görev tabloları güncelleme scripti
-- Her kolonu tek tek kontrol ederek ekler

-- Daily tasks tablosuna kolonlar ekle
ALTER TABLE `daily_tasks` 
ADD COLUMN IF NOT EXISTS `description` TEXT AFTER `task`,
ADD COLUMN IF NOT EXISTS `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER `description`,
ADD COLUMN IF NOT EXISTS `duration_minutes` INT DEFAULT 60 AFTER `priority`,
ADD COLUMN IF NOT EXISTS `due_date` DATETIME AFTER `duration_minutes`;

-- Weekly tasks tablosuna kolonlar ekle
ALTER TABLE `weekly_tasks` 
ADD COLUMN IF NOT EXISTS `description` TEXT AFTER `task`,
ADD COLUMN IF NOT EXISTS `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER `description`,
ADD COLUMN IF NOT EXISTS `duration_minutes` INT DEFAULT 60 AFTER `priority`,
ADD COLUMN IF NOT EXISTS `due_date` DATETIME AFTER `duration_minutes`;

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

-- İndeksler ekle
CREATE INDEX IF NOT EXISTS idx_daily_tasks_user_created ON daily_tasks(user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_daily_tasks_user_due ON daily_tasks(user_id, due_date);
CREATE INDEX IF NOT EXISTS idx_daily_tasks_user_status ON daily_tasks(user_id, is_done);

CREATE INDEX IF NOT EXISTS idx_weekly_tasks_user_created ON weekly_tasks(user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_weekly_tasks_user_due ON weekly_tasks(user_id, due_date);
CREATE INDEX IF NOT EXISTS idx_weekly_tasks_user_status ON weekly_tasks(user_id, is_done);
