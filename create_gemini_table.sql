-- Gemini AI konuşma geçmişi tablosu (isteğe bağlı)

CREATE TABLE IF NOT EXISTS `gemini_conversations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `context` VARCHAR(50) DEFAULT 'general',
  `user_message` TEXT NOT NULL,
  `ai_response` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX idx_user_context (`user_id`, `context`),
  INDEX idx_created (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
