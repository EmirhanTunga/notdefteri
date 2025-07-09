-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 09 Tem 2025, 21:28:13
-- Sunucu sürümü: 8.0.17
-- PHP Sürümü: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `notdefteri`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `daily_tasks`
--

CREATE TABLE `daily_tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `is_done` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_favorite` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `daily_tasks`
--

INSERT INTO `daily_tasks` (`id`, `user_id`, `task`, `is_done`, `created_at`, `is_favorite`) VALUES
(2, 1, 'asdgf', 0, '2025-07-04 15:26:26', 0),
(3, 1, 'sagasgsa', 0, '2025-07-04 15:26:30', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `status`, `created_at`) VALUES
(1, 1, 2, 'pending', '2025-07-09 01:00:33');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `color` varchar(20) DEFAULT 'yellow',
  `is_favorite` tinyint(1) DEFAULT '0',
  `tags` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `note`, `created_at`, `color`, `is_favorite`, `tags`, `file_path`) VALUES
(3, 1, '85', '2025-07-03 23:11:53', 'yellow', 0, '', NULL),
(4, 1, 'merhabaa', '2025-07-04 14:34:23', 'blue', 0, '#555', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `public_comments`
--

CREATE TABLE `public_comments` (
  `id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `public_comments`
--

INSERT INTO `public_comments` (`id`, `note_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 2, 'harikaa', '2025-07-04 14:35:30'),
(2, 1, 2, 'harikaa', '2025-07-04 14:35:33'),
(3, 1, 2, 'harikaa', '2025-07-04 14:35:34'),
(4, 1, 2, 'harikaa', '2025-07-04 14:35:34'),
(5, 1, 2, 'harikaa', '2025-07-04 14:35:34'),
(6, 1, 2, 'harikaa', '2025-07-04 14:35:34'),
(7, 1, 2, 'harikaa', '2025-07-04 14:35:34'),
(8, 1, 2, 'harikaa', '2025-07-04 14:35:34'),
(9, 1, 2, 'harikaa', '2025-07-04 14:35:35'),
(10, 1, 2, 'harikaa', '2025-07-04 14:35:35'),
(11, 1, 2, 'harikaa', '2025-07-04 14:35:35');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `public_likes`
--

CREATE TABLE `public_likes` (
  `id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `public_likes`
--

INSERT INTO `public_likes` (`id`, `note_id`, `user_id`, `created_at`) VALUES
(3, 1, 1, '2025-07-04 14:34:33'),
(4, 1, 2, '2025-07-04 14:35:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `public_notes`
--

CREATE TABLE `public_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `like_count` int(11) DEFAULT '0',
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `public_notes`
--

INSERT INTO `public_notes` (`id`, `user_id`, `content`, `created_at`, `like_count`, `file_path`) VALUES
(1, 1, 'merhabaa', '2025-07-04 14:34:23', 2, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(32) DEFAULT 'cat'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `avatar`) VALUES
(1, 'emirhan', '$2y$10$niu5n19zdTOObmBqpZFffucRHVxON4x3r9cJ.nzEbxmf6GUd1j2qO', 'rabbit'),
(2, 'emirhan27', '$2y$10$42HYlaq.S5AnyTymqLOXjO340rQnxMIsUrHnKo6mHHg6awau86AfO', 'cat');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `weekly_tasks`
--

CREATE TABLE `weekly_tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `is_done` tinyint(1) DEFAULT '0',
  `week_start` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_favorite` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `daily_tasks`
--
ALTER TABLE `daily_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `public_comments`
--
ALTER TABLE `public_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `public_likes`
--
ALTER TABLE `public_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`note_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `public_notes`
--
ALTER TABLE `public_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tablo için indeksler `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `daily_tasks`
--
ALTER TABLE `daily_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `public_comments`
--
ALTER TABLE `public_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `public_likes`
--
ALTER TABLE `public_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `public_notes`
--
ALTER TABLE `public_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `daily_tasks`
--
ALTER TABLE `daily_tasks`
  ADD CONSTRAINT `daily_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `public_comments`
--
ALTER TABLE `public_comments`
  ADD CONSTRAINT `public_comments_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `public_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `public_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `public_likes`
--
ALTER TABLE `public_likes`
  ADD CONSTRAINT `public_likes_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `public_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `public_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `public_notes`
--
ALTER TABLE `public_notes`
  ADD CONSTRAINT `public_notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  ADD CONSTRAINT `weekly_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
