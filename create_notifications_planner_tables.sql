-- Bildirimler ve Planlayıcı için tablolar

-- Bildirimler tablosu
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` ENUM('friend_request', 'group_invite', 'task_assigned', 'system') DEFAULT 'system',
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT,
  `link` VARCHAR(255),
  `is_read` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Planlayıcı tablosu (Action Plans)
CREATE TABLE IF NOT EXISTS `action_plans` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Planlayıcı adımları tablosu
CREATE TABLE IF NOT EXISTS `action_plan_steps` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `plan_id` INT NOT NULL,
  `step_number` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `status` ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
  `due_date` DATE,
  `completed_at` DATETIME,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`plan_id`) REFERENCES `action_plans`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- İndeksler
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);
CREATE INDEX idx_action_plans_user ON action_plans(user_id);
CREATE INDEX idx_plan_steps_plan ON action_plan_steps(plan_id);
