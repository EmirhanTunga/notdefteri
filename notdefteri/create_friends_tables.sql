-- Arkadaşlık sistemi için tablolar

-- Arkadaşlık istekleri ve arkadaşlar tablosu
CREATE TABLE IF NOT EXISTS `friendships` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `friend_id` INT NOT NULL,
  `status` ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
  `requested_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `responded_at` DATETIME NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`friend_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_friendship` (`user_id`, `friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- İndeks ekle (performans için)
CREATE INDEX idx_user_status ON friendships(user_id, status);
CREATE INDEX idx_friend_status ON friendships(friend_id, status);
