-- Görev tablolarını güncelleme scripti
-- Mevcut tabloları genişletip monthly_tasks tablosunu ekliyoruz

-- Daily tasks tablosunu güncelle
ALTER TABLE `daily_tasks` 
ADD COLUMN `description` TEXT AFTER `task`,
ADD COLUMN `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER `description`,
ADD COLUMN `duration_minutes` INT DEFAULT 60 AFTER `priority`,
ADD COLUMN `due_date` DATETIME AFTER `duration_minutes`;

-- Weekly tasks tablosunu güncelle
ALTER TABLE `weekly_tasks` 
ADD COLUMN `description` TEXT AFTER `task`,
ADD COLUMN `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER `description`,
ADD COLUMN `duration_minutes` INT DEFAULT 60 AFTER `priority`,
ADD COLUMN `due_date` DATETIME AFTER `duration_minutes`;

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
CREATE INDEX idx_daily_tasks_user_created ON daily_tasks(user_id, created_at);
CREATE INDEX idx_daily_tasks_user_due ON daily_tasks(user_id, due_date);
CREATE INDEX idx_daily_tasks_user_status ON daily_tasks(user_id, is_done);

CREATE INDEX idx_weekly_tasks_user_created ON weekly_tasks(user_id, created_at);
CREATE INDEX idx_weekly_tasks_user_due ON weekly_tasks(user_id, due_date);
CREATE INDEX idx_weekly_tasks_user_status ON weekly_tasks(user_id, is_done);
