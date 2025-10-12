-- Etiket sistemi için tablolar

-- Etiketler tablosu
CREATE TABLE IF NOT EXISTS `tags` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `color` VARCHAR(7) DEFAULT '#4a90e2',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_tag` (`user_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Not-Etiket ilişki tablosu
CREATE TABLE IF NOT EXISTS `note_tags` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `note_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`note_id`) REFERENCES `notes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_note_tag` (`note_id`, `tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
