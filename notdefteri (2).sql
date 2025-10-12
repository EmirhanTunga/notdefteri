-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 12 Eki 2025, 22:42:58
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
-- Tablo için tablo yapısı `action_plans`
--

CREATE TABLE `action_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `action_plans`
--

INSERT INTO `action_plans` (`id`, `user_id`, `title`, `description`, `created_at`) VALUES
(1, 1, 'proje raporu hazırlamak istiyorum', 'proje raporu hazırlamak istiyorum', '2025-10-05 12:50:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `action_plan_steps`
--

CREATE TABLE `action_plan_steps` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `step_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `action_plan_steps`
--

INSERT INTO `action_plan_steps` (`id`, `plan_id`, `step_number`, `title`, `description`, `status`, `due_date`, `completed_at`, `created_at`) VALUES
(1, 1, 1, 'Proje Planlaması', 'Adım 1: Proje Planlaması', 'completed', NULL, '2025-10-05 09:50:40', '2025-10-05 12:50:25'),
(2, 1, 2, 'Kaynak Belirleme', 'Adım 2: Kaynak Belirleme', 'completed', NULL, '2025-10-05 09:52:26', '2025-10-05 12:50:25'),
(3, 1, 3, 'Uygulama', 'Adım 3: Uygulama', 'completed', NULL, '2025-10-05 09:52:08', '2025-10-05 12:50:25'),
(4, 1, 4, 'Test ve Değerlendirme', 'Adım 4: Test ve Değerlendirme', 'completed', NULL, '2025-10-05 09:52:10', '2025-10-05 12:50:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `daily_tasks`
--

CREATE TABLE `daily_tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `description` text,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `duration_minutes` int(11) DEFAULT '60',
  `due_date` datetime DEFAULT NULL,
  `is_done` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_favorite` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `daily_tasks`
--

INSERT INTO `daily_tasks` (`id`, `user_id`, `task`, `description`, `priority`, `duration_minutes`, `due_date`, `is_done`, `created_at`, `is_favorite`) VALUES
(2, 1, 'asdgf', NULL, 'medium', 60, NULL, 0, '2025-07-04 15:26:26', 0),
(3, 1, 'sagasgsa', NULL, 'medium', 60, NULL, 0, '2025-07-04 15:26:30', 0),
(4, 1, 'sdfa', 'asdgsda', 'medium', 120, '2025-10-19 14:41:00', 0, '2025-10-05 14:41:12', 0),
(5, 1, 'sdfa', 'asdgsda', 'medium', 120, '2025-10-19 14:41:00', 0, '2025-10-05 14:41:12', 0),
(6, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:23', 0),
(7, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:24', 0),
(8, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:29', 0),
(9, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:29', 0),
(10, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:29', 0),
(11, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:29', 0),
(12, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:29', 0),
(13, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:30', 0),
(14, 1, 'sagsadg', 'sagsadg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 22:38:30', 0),
(15, 1, 'asdg', 'sadgsda', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 23:02:07', 0),
(16, 1, 'asd', 'sdfsdg', 'medium', 60, '2025-10-12 18:00:00', 0, '2025-10-11 23:10:40', 0);

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
-- Tablo için tablo yapısı `friendships`
--

CREATE TABLE `friendships` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `requested_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `responded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `friendships`
--

INSERT INTO `friendships` (`id`, `user_id`, `friend_id`, `status`, `requested_at`, `responded_at`) VALUES
(1, 1, 4, 'pending', '2025-10-05 14:17:32', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gemini_conversations`
--

CREATE TABLE `gemini_conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `context` varchar(50) DEFAULT 'general',
  `user_message` text NOT NULL,
  `ai_response` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `gemini_conversations`
--

INSERT INTO `gemini_conversations` (`id`, `user_id`, `context`, `user_message`, `ai_response`, `created_at`) VALUES
(1, 1, 'general', 'Proje yönetimi için en iyi pratikleri öner', 'Merhaba emirhan, NotDefteri uygulamasının yapay zeka asistanı olarak, proje yönetimi süreçlerinde başarılı olman için sana en iyi pratikleri sunmaktan mutluluk duyarım. Bu pratikler, projelerini daha verimli, öngörülebilir ve başarılı bir şekilde tamamlamana yardımcı olacaktır.\n\nİşte proje yönetimi için temel ve en etkili pratikler:\n\n1.  **Net Hedefler ve Kapsam Belirleme:**\n    *   **SMART Hedefler:** Projenin hedeflerini Spesifik, Ölçülebilir, Ulaşılabilir, İlgili ve Zaman Sınırlı (SMART) olarak tanımla.\n    *   **Kapsam Tanımı:** Projenin neyi kapsayıp neyi kapsamadığını net bir şekilde belirle. Bu, \"kapsam kayması\" (scope creep) denilen istenmeyen durumların önüne geçer.\n    *   **Paydaş Beklentileri:** Başlangıçta tüm kilit paydaşları (stakeholders) belirle ve onların beklentilerini, ihtiyaçlarını ve katkılarını netleştir.\n\n2.  **Detaylı ve Gerçekçi Planlama:**\n    *   **İş Kırılım Yapısı (WBS):** Projeyi yönetilebilir küçük görevlere ayır. Bu, hem planlamayı hem de takibi kolaylaştırır.\n    *   **Zaman Çizelgesi ve Kaynak Planlaması:** Her görev için tahmini süreleri ve gerekli kaynakları (insan, bütçe, ekipman) belirle. Gantt şemaları veya Kanban panoları gibi araçlar kullanabilirsin.\n    *   **Risk Yönetimi:** Potansiyel riskleri (teknik, finansal', '2025-10-05 13:59:28'),
(2, 1, 'notes', 'Aşağıdaki metni özetle. Net, anlaşılır ve yapılandırılmış bir özet oluştur:\n\nKAT SAYI TEKSE KAT SAYIYI SİL ÇİFTSE HEPSİNİ SİL\r\n\r\nTAM SAYI doğal sayı DİYORSA ÜSTLER NEGATİF VEYA SIFIR OLMADIĞI SÜRECE ÜSLERİ SİL\r\n\r\nASAL SAYILAR:ARALARINDA ASAL  BİRDEN BAŞKA ORTAK BÖLENİ OLMAYAN SAYILARDIR ARDIŞIK SAYILAR BİR ÖRNEKTİR.\r\n\r\n\r\n   ARDIŞIK SAYILARIN TOPLAMI İÇİN N.N+1 BÖLÜ 2\r\n   ', 'Merhaba emirhan, Notlarını özetledim ve daha düzenli hale getirmene yardımcı olacak öneriler ekledim. İşte notlarının özeti:\n\n---\n\n### Matematik Notları Özeti\n\nBu notlar, belirli matematiksel kuralları, sayı teorisi tanımlarını ve bir formülü içermektedir.\n\n#### 1. Kat Sayı ve Üs Kuralları\n\n*   **Kat Sayı (Katsayı) Kuralları:**\n    *   Eğer bir terimin kat sayısı tek ise, sadece kat sayıyı sil.\n    *   Eğer bir terimin kat sayısı çift ise, ilgili terimin tamamını sil (varsayılan anlamı budur).\n\n*   **Üs (Kuvvet) Kuralları (Doğal Sayılar Bağlamında):**\n    *   Eğer tam sayılar doğal sayılar olarak ele alınıyorsa (yani pozitif tam sayılar), üsler negatif veya sıfır olmadığı sürece üsleri sil. (Bu kural, sayının doğal sayı olma şartını sağlayan üslerin sadeleştirilebileceği veya göz ardı edilebileceği anlamına gelebilir.)\n\n#### 2. Sayı Teorisi Tanımları\n\n*   **Aralarında Asal Sayılar:**\n    *   Birden başka ortak böleni olmayan sayılardır.\n    *   **Örnek:** Ardışık sayılar her zaman aralarında asaldır (örneğin 2 ve 3, 7 ve 8).\n\n#### 3. Formüller\n\n*   **Ardışık Sayıların Toplamı Formülü:**\n    *   İlk `n` doğal sayının toplamını bulmak için kullanılan formül: `n * (n + 1) / 2`\n\n---\n\n### Not Alma Teknikleri ve Yapılandırma Önerileri:\n\nemirhan, notlarını daha etkili ve anlaşılır hale getirmek için sana bazı önerilerim var:\n\n1.  **Başlıklandırma ve Kategorizasyon:** Notlarını ana başlıklar (Matematik Kuralları, Tanımlar, Formüller gibi) ve alt başlıklar altında gruplandırmak, aradığın bilgiye daha hızlı ulaşmanı sağlar. Yukarıdaki özet bu yapıyı kullanıyor.\n    *   Örnek: \"Kat Sayı ve Üs Kuralları\" altında kendi alt başlıkları olabilir.\n\n2.  **Net ve Tam İfadeler Kullanma:** Bazı ifadeler biraz kısa veya yoruma açık olabilir.\n    *   \"KAT SAYI TEKSE KAT SAYIYI SİL ÇİFTSE HEPSİNİ SİL\" gibi kuralları, \"Eğer bir terimin katsayısı tek ise, sadece katsayıyı kaldırın. Eğer çift ise, terimin tamamını (veya ilgili kısmı) kaldırın.\" şeklinde daha açık yazabilirsin.\n    *   \"TAM SAYI doğal sayı DİYORSA ÜSTLER NEGATİF VEYA SIFIR OLMADIĞI SÜRECE ÜSLERİ SİL\" ifades', '2025-10-05 23:13:47'),
(3, 1, 'notes', 'Aşağıdaki metin ve özete dayanarak, düzenli bir not şablonu oluştur. Başlıklar, alt başlıklar, önemli noktalar ve madde işaretli listeler içermeli:\n\nMETİN:\nKAT SAYI TEKSE KAT SAYIYI SİL ÇİFTSE HEPSİNİ SİL\r\n\r\nTAM SAYI doğal sayı DİYORSA ÜSTLER NEGATİF VEYA SIFIR OLMADIĞI SÜRECE ÜSLERİ SİL\r\n\r\nASAL SAYILAR:ARALARINDA ASAL  BİRDEN BAŞKA ORTAK BÖLENİ OLMAYAN SAYILARDIR ARDIŞIK SAYILAR BİR ÖRNEKTİR.\r\n\r\n\r\n   ARDIŞIK SAYILARIN TOPLAMI İÇİN N.N+1 BÖLÜ 2\r\n   \n\nÖZET:\nMerhaba emirhan, Notlarını özetledim ve daha düzenli hale getirmene yardımcı olacak öneriler ekledim. İşte notlarının özeti:\n\n---\n\n### Matematik Notları Özeti\n\nBu notlar, belirli matematiksel kuralları, sayı teorisi tanımlarını ve bir formülü içermektedir.\n\n#### 1. Kat Sayı ve Üs Kuralları\n\n*   **Kat Sayı (Katsayı) Kuralları:**\n    *   Eğer bir terimin kat sayısı tek ise, sadece kat sayıyı sil.\n    *   Eğer bir terimin kat sayısı çift ise, ilgili terimin tamamını sil (varsayılan anlamı budur).\n\n*   **Üs (Kuvvet) Kuralları (Doğal Sayılar Bağlamında):**\n    *   Eğer tam sayılar doğal sayılar olarak ele alınıyorsa (yani pozitif tam sayılar), üsler negatif veya sıfır olmadığı sürece üsleri sil. (Bu kural, sayının doğal sayı olma şartını sağlayan üslerin sadeleştirilebileceği veya göz ardı edilebileceği anlamına gelebilir.)\n\n#### 2. Sayı Teorisi Tanımları\n\n*   **Aralarında Asal Sayılar:**\n    *   Birden başka ortak böleni olmayan sayılardır.\n    *   **Örnek:** Ardışık sayılar her zaman aralarında asaldır (örneğin 2 ve 3, 7 ve 8).\n\n#### 3. Formüller\n\n*   **Ardışık Sayıların Toplamı Formülü:**\n    *   İlk `n` doğal sayının toplamını bulmak için kullanılan formül: `n * (n + 1) / 2`\n\n---\n\n### Not Alma Teknikleri ve Yapılandırma Önerileri:\n\nemirhan, notlarını daha etkili ve anlaşılır hale getirmek için sana bazı önerilerim var:\n\n1.  **Başlıklandırma ve Kategorizasyon:** Notlarını ana başlıklar (Matematik Kuralları, Tanımlar, Formüller gibi) ve alt başlıklar altında gruplandırmak, aradığın bilgiye daha hızlı ulaşmanı sağlar. Yukarıdaki özet bu yapıyı kullanıyor.\n    *   Örnek: \"Kat Sayı ve Üs Kuralları\" altında kendi alt başlıkları olabilir.\n\n2.  **Net ve Tam İfadeler Kullanma:** Bazı ifadeler biraz kısa veya yoruma açık olabilir.\n    *   \"KAT SAYI TEKSE KAT SAYIYI SİL ÇİFTSE HEPSİNİ SİL\" gibi kuralları, \"Eğer bir terimin katsayısı tek ise, sadece katsayıyı kaldırın. Eğer çift ise, terimin tamamını (veya ilgili kısmı) kaldırın.\" şeklinde daha açık yazabilirsin.\n    *   \"TAM SAYI doğal sayı DİYORSA ÜSTLER NEGATİF VEYA SIFIR OLMADIĞI SÜRECE ÜSLERİ SİL\" ifades', 'Harika bir fikir emirhan! Notlarını daha düzenli ve anlaşılır hale getirmek için sana özel olarak hazırlanmış bir şablon ve öneriler sunuyorum.\n\n---\n\n### 📝 Matematik Notları Şablonu\n\n#### 1. Cebirsel İfadeler ve Sadeleştirme Kuralları\n\n*   **1.1. Katsayı Kuralları:**\n    *   **Tek Katsayı Durumu:** Eğer bir terimin katsayısı **tek** ise, sadece katsayıyı silin (terimi değil).\n    *   **Çift Katsayı Durumu:** Eğer bir terimin katsayısı **çift** ise, ilgili terimin tamamını silin.\n\n*   **1.2. Üs Kuralları (Tam Sayılar Doğal Sayı Olarak Kabul Edildiğinde):**\n    *   **Koşul:** Eğer bağlamda tam sayılar doğal sayılar olarak ele alınıyorsa (yani pozitif tam sayılar) ve üsler negatif veya sıfır değilse, üsleri silebilirsiniz.\n    *   **Anlamı:** Bu kural, belirli bir şart altında üslerin sadeleştirilebileceği veya göz ardı edilebileceği durumları ifade eder.\n\n#### 2. Sayı Teorisi Tanımları\n\n*   **2.1. Aralarında Asal Sayılar:**\n    *   **Tanım:** Birden başka ortak böleni olmayan sayılardır.\n    *   **Önemli Not:** Aralarında asal sayılar, asal olmak zorunda değildirler (örn. 8 ve 15).\n    *   **Örnek:** Ardışık sayılar her zaman aralarında asaldır (örn. 2 ve 3, 7 ve 8, 10 ve 11).\n\n#### 3. Formüller\n\n*   **3.1. Ardışık Sayıların Toplamı Formülü:**\n    *   **Kullanım Amacı:** İlk `n` doğal sayının toplamını bulmak için kullanılır.\n    *   **Formül:** `n * (n + 1) / 2`\n    *   **Örnek:** İlk 5 doğal sayının toplamı (1+2+3+4+5) = `5 * (5 + 1) / 2 = 5 * 6 / 2 = 15`.\n\n---\n\n### 💡 Not Alma Teknikleri ve Yapılandırma Önerileri (emirhan için):\n\nNotlarını daha etkili ve anlaşılır hale getirmek için sana bazı önerilerim var:\n\n1.  **Başlıklandırma ve Kategorizasyon:** Notlarını ana başlıklar (Matematik Kuralları, Tanımlar, Formüller gibi) ve alt başlıklar altında gruplandırmak, aradığın bilgiye daha hızlı ulaşmanı sağlar. Yukarıdaki şablon bu yapıyı kullanıyor.\n    *   **Örnek:** \"Cebirsel İfadeler ve Sadeleştirme Kuralları\" altında \"Katsayı Kuralları\" gibi alt başlıklar oluşturabilirsin.\n\n2.  **Net ve Tam İfadeler Kullanma:** Bazı ifadeler ilk notlarında biraz kısa veya yoruma açık olabiliyor. Bu durum, notları daha sonra tekrar okuduğunda anlamanı zorlaştırabilir.\n    *   **İyileştirme Örneği:** \"KAT SAYI TEKSE KAT SAYIYI SİL ÇİFTSE HEPSİNİ SİL\" gibi kuralları, \"Eğer bir terimin katsayısı tek ise, sadece katsayıyı kaldırın. Eğer çift ise, terimin tamamını (veya ilgili kısmı) kaldırın.\" şeklinde daha açık yazabilirsin.\n    *   **İyileştirme Örneği:** \"TAM SAYI doğal sayı DİYORSA ÜSTLER NEGATİF VEYA SIFIR OLMADIĞI SÜRECE ÜSLERİ SİL\" ifadesini, yukarıdaki şablonda olduğu gibi daha açıklayıcı bir şekilde ifade etmek, kuralın ne zaman ve nasıl uygulanacağını netleştirir.\n\n3.  **Madde İşaretleri ve Numaralı Listeler:** Bilgileri daha kolay sindirilebilir hale getirmek için madde işaretleri (`*` veya `-`) ve numaralı listeler kullan. Bu, görsel olarak da notlarını daha düzenli gösterir.\n\n4.  **Anahtar Kelimeleri Vurgulama:** Önemli terimleri veya kuralları **kalın** yaparak veya farklı renklerle işaretleyerek (dij', '2025-10-05 23:14:01'),
(4, 1, 'notes', 'Aşağıdaki metni özetle. Net, anlaşılır ve yapılandırılmış bir özet oluştur:\n\nPK\0\0\0\0\0!\02�oWf\0\0�\0\0\0[Content_Types].xml �(�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0���j�0E����Ѷ�J�(��ɢ�eh��4ND�B�����81�$14�� ��{�1��ښl	�w%�=���^i7+���-d&�0�A�6�l4��L60#�Ò�S\nO����X�\0�*��V$:�B~���^�K����ڃ\r/P��I��~7$��i��J&B0Z�Du�t�OJ�K(H����xG\r�L�+�v����dcӻ���W>*��\\XR�mp���Z}����HwnM�V��n���-�\")/�ZwB`���4���	��s�DX���j��;A*ʝ����c�֝��\04���[�S��9�> ������{�V�4p����W�&����A����|�d�?\0\0\0��\0PK\0\0\0\0\0!\0�\Z��\0\0\0N\0\0\0_rels/.rels �(�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0���j�0@���ѽQ���N/c���[IL��j���<��]�aG��ӓ�zs�Fu��]�\Z�U\r��	��^�[��x\0����1x�p����f��#I)ʃ�Y��\Z�������*D��i\")��c$���qU���~3��1��jH[{�=E����~\nf?��3-���޲]�Tꓸ2�j)�,\Zl0/%��b�\n\Z���z���ŉ�,	�	�/�|f\\Z���?6�!Y�_�o�]A�\0\0��\0PK\0\0\0\0\0!\0�l<�\0\0\0`�\0\0\0\0\0word/document.xml�]KoI��/��!W�B�B�X�b�F\Z�E��x�l�4<���d�Țz��!�Z���`N���W��NK�mDV��$J�J)�m��z�#2⋈�Ȍ?��������o(;�\r¼�oZ^w����g�\rF�3��{lc�?�������wb�y�\"�p�c���ы����n��1��;��	��?�v:���[����]����~�wXB}�Nh����$V�Џ�2��vz4�ا�2�̅�v�����AU�vQ��E�ت[iRA�n�T�+iF�t����%��J*�.�\"W�-vro3��g�<��F�3��4���3(�O#�m9V4�2��jy�D��I	n��\\By��M��q)��Fx{���&�c����ӏ������O�F�w�%�7R`�T�\r�t���g�\'��ʖ7{�BN�#��댟��WE�.hk$�pU�H�ӱs������F���!҄�u�[�_U,E�)�*��3.@�U��a��b\\F%-c�s%�X�%(V�r�Q�r�+�*�x�1S�q�\"�����O�����V�x�v�]\Z�\r\'B��x,���s��ϰL��h�I�wj����y����Ҭ�J{y��x�PV*�� �טw=�$w;{/��ж-�% ���\0�F���}�ב�/�~1c���q\0F`�7�ه�^��%ȐR-V˺���WA�Fx�����{`p�o�7\n��Z�+�ɥ�`��;��ݾs8u	[�,�p+u/������\'l��=�Č�؍ɟv�������hЇ��>s�w��TvP;|���*���8�����\"x,\"ËS��3�G�̱�\"m+ ��A�<B=gtnof��,0��t���k]����b�Z�vx�:.uG秣s�љC�#X�����B�`�Ao��Ajm�y�E�A7v�K\Z�α˔<���$�\"xp��>�e���!^����(���o�����5j��BK�������\nVĺ#�dh��ε���m�Ҍ�Qn5���!� �hs���p7i-^����9��~�t6���[����t���!��N��.�F���8��ҴJ=�����ei(`�Դ��\' �bbǎC=����,&�8�PB��`�`O`�tMm���#	8(��圀��;�`����������v\'/�/�8J�]�`F��90?y��r�넓����69���\Z�aũf�T܎�؋�4O>|��/�O�D����\\E(m6�j��]�t��owX+s�o=�\r�+عw�`ш��\n��s�_���h@�G�	�%�:�L����\Z\0���HK�)��69��FR|J����0�/q\0<\"|���\0������\rC�� x� !<�<ڋ�*{�?��t�+��?P����3����θN�,��M��o�c.C��wX�ɞuj��F.�=E3�-!$2��y��h�G�;h�>e���6�\r\'=��q�%J<��kJ��+g�\"с�X���v��\nwY���ܦ�3���q:]!T4�r�%K�$�;����m�	�\\�(��<���$�$0���%L>� ��&�U���:b�wh����А�c,��\"�� ���_�]	��m�����E�q�ˋ��9�\"E�v����y�����v�\0=����|:�揙c:�]�����^��קʶ��94�����b��m�9��m���[(��p;�����r��4��d��5b�\\�aF_/π~9�r�\0�z�0�e��d�iA�xX�P�/�޼���O�n�N`�#��O։r%�k?⫺y�C��ĝ	0��\0Abp)G,�L�|�St��\Z�pk\"W��٢y	,K��������|�w0�:\'A\0�<�>�pgpI>O��A_{N���\0+�g���S�c�O\0e�z�T�ZZv��Ŗ�Gp�G���+��n�9e���9p����� dzA���Z�1۽.�w��^�F��l��,$u\\v�Q�X�Sj��}��i-\\�.�F,?�W�ٗ�ٜgW�:۠�%ِ�[p�V�q�ob2�J���\\���ft��4fU���3Y�{�+���l���#��V��4��7����ޘ��7v8�z���jR:;�`X\'E����v�������7��j�u�Z6��X�����bFa6葵Х��^DË~�z,�ai7a�M`���_��x�gM:�)_��N��j�Z��n�͵�0���NM�c��m�}7��C�}�C��ES��j�b,=ry8&�S+�N8s����z�YnՌ��o|�W�/z0*�cy6	�0^>xi��Ҩ�@s\0A��\0�6^�\"��\0kȇ��w�\0������A�����C����K�79dBM9��Z�����5��ڲ�}�`�?�5bJ�B��	�˳�!��������ɣ��F���w�g�(1�K�[yh?�0�n�(��7���䧓6H{�\r�4wx��ړ�Y�lO�W3^�Û���SG���Q�>�����\\a~R�GXd[��.�\\Sd#���ږMI8��Hm^*�F�ؒ�o���i_Q�O�l<w�{M)�j��rj-��a��Ti��3��Q�I?R�l;���=m;�+��ħq�,��/p�#\Z2j�8�\'��������V�����?��a�h���X���������翨S1\"�����xqt����į]��2��V�X���&���ӎ�f��Z�.6�t��i:)ux��������Z�ެ�)��<�E�\ZS\0σns�\Z����������ܚ����}-y�|�|���n�+U�_,�/�f^����ɍ%p+���d��^,�[��w��r���0�ί�@>!Ϟ=#�&&�\r����(A���Gh���z���!��-�}��n�����6_�Ҭ��|�S��`�׵�ko_�~��M�Ą��7�&�p`�#-�C�J�Z7�F.V^#-8��^i���.>z�����mڄ��ڻ��&��gڷ��ޥ��\Z�]u��+��iq2�{�!0�V��j�W1�jIiUK����g+p�?4HW�]��0��˒�:V48��0�Z�D�A�F����K.@��6g66D�Nm���\"\Z����8���h�4�8��DV�Ŧ\"*z�Y�V��Z,�R]�A�*���Tp�]ڽ�#��E@�l(J���>�CC��6۔�}Lh����wg�,h��̶\0��q�^��d[���+z���2G�,�%�����)3h�b��H��쾀����?�=�.0�Z�(�FM�����س��p�e+uYX�l���Q�y���n��`t6:�Y�9��9#:�\"�w}�e%�o�jɨ�W�ʘ���u��<\n6��\'X2���~x�`�N�<5Y2!큑kZŶ�AU�J�U�ة��q�,���k�cզ\0��Vi��6�a*getA�,\n2�����~�����s&��2�=�����', 'Merhaba Emirhan, NotDefteri uygulamasının yapay zeka asistanı olarak notlarını düzenlemene yardımcı olmak için buradayım.\n\nSağladığın metin, doğrudan insan tarafından okunabilir bir içerik değil. Bu veriler, bir Word belgesinin (genellikle .docx uzantılı) iç yapısını oluşturan ikili (binary) ve sıkıştırılmış verilere benziyor. Görünen kısımlar, dosyanın bileşenlerini (örneğin `[Content_Types].xml`, `_rels/.rels`, `word/document.xml` gibi) ve bunların sıkıştırılmış hallerini gösteriyor.\n\nBu nedenle, bu verilerden anlamlı ve yapılandırılmış bir özet çıkarmak teknik olarak mümkün değil. Metin içeriği yerine, dosyanın nasıl organize edildiğine dair teknik bilgiler içeriyor.\n\n**Özetle:**\n\n*   **Veri Türü:** Sağlanan metin, bir Word belgesine (DOCX) ait sıkıştırılmış ve ikili (binary) dosya yapısıdır.\n*   **İçerik:** Doğrudan okunabilir bir metin içeriği barındırmaz; dosyanın bileşenlerinin (XML dosyaları) kodlanmış formunu temsil eder.\n*   **Özetleme Durumu:** Bu haliyle anlamlı bir özet oluşturmak mümkün değildir.\n\nEğer özetlemek istediğin bir metin varsa, lütfen belgenin içindeki **asıl yazılı içeriği** kopyalayıp bana yapıştırabilir misin? O zaman sana net, anlaşılır ve yapılandırılmış bir özet sunmaktan memnuniyet duyarım.\n\nNotlarını daha iyi organize etmen ve önemli bilgileri kolayca erişilebilir kılman için buradayım!', '2025-10-05 23:20:04'),
(5, 1, 'notes', 'Aşağıdaki dosya içeriği ve özetine dayanarak, kullanıcıya 5-7 adet pratik öneri ver. Her öneri kısa, net ve uygulanabilir olsun. Öneriler şu kategorilerde olabilir: öğrenme, uygulama, geliştirme, takip, organizasyon:\n\nMETİN:\nPK\0\0\0\0\0!\02�oWf\0\0�\0\0\0[Content_Types].xml �(�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0���j�0E����Ѷ�J�(��ɢ�eh��4ND�B�����81�$14�� ��{�1��ښl	�w%�=���^i7+���-d&�0�A�6�l4��L60#�Ò�S\nO����X�\0�*��V$:�B~���^�K����ڃ\r/P��I��~7$��i��J&B0Z�Du�t�OJ�K(H����xG\r�L�+�v����dcӻ���W>*��\\XR�mp���Z}����HwnM�V��n���-�\")/�ZwB`���4���	��s�DX���j��;A*ʝ����c�֝��\04���[�S��9�> ������{�V�4p����W�&����A����|�d�?\0\0\0��\0PK\0\0\0\0\0!\0�\Z��\0\0\0N\0\0\0_rels/.rels �(�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0���j�0@���ѽQ���N/c���[IL��j���<��]�aG��ӓ�zs�Fu��]�\Z�U\r��	��^�[��x\0����1x�p����f��#I)ʃ�Y��\Z�������*D��i\")��c$���qU���~3��1��jH[{�=E����~\nf?��3-���޲]�Tꓸ2�j)�,\Zl0/%��b�\n\Z���z���ŉ�,	�	�/�|f\\Z���?6�!Y�_�o�]A�\0\0��\0PK\0\0\0\0\0!\0�l<�\0\0\0`�\0\0\0\0\0word/document.xml�]KoI��/��!W�B�B�X�b�F\Z�E��x�l�4<���d�Țz��!�Z���`N���W��NK�mDV��$J�J)�m��z�#2⋈�Ȍ?��������o(;�\r¼�oZ^w����g�\rF�3��{lc�?�������wb�y�\"�p�c���ы����n��1��;��	��?�v:���[����]����~�wXB}�Nh����$V�Џ�2��vz4�ا�2�̅�v�����AU�vQ��E�ت[iR\n\nÖZET:\nMerhaba Emirhan, NotDefteri uygulamasının yapay zeka asistanı olarak notlarını düzenlemene yardımcı olmak için buradayım.\n\nSağladığın metin, doğrudan insan tarafından okunabilir bir içerik değil. Bu veriler, bir Word belgesinin (genellikle .docx uzantılı) iç yapısını oluşturan ikili (binary) ve sıkıştırılmış verilere benziyor. Görünen kısımlar, dosyanın bileşenlerini (örneğin `[Content_Types].xml`, `_rels/.rels`, `word/document.xml` gibi) ve bunların sıkıştırılmış hallerini gösteriyor.\n\nBu nedenle, bu verilerden anlamlı ve yapılandırılmış bir özet çıkarmak teknik olarak mümkün değil. Metin içeriği yerine, dosyanın nasıl organize edildiğine dair teknik bilgiler içeriyor.\n\n**Özetle:**\n\n*   **Veri Türü:** Sağlanan metin, bir Word belgesine (DOCX) ait sıkıştırılmış ve ikili (binary) dosya yapısıdır.\n*   **İçerik:** Doğrudan okunabilir bir metin içeriği barındırmaz; dosyanın bileşenlerinin (XML dosyaları) kodlanmış formunu temsil eder.\n*   **Özetleme Durumu:** Bu haliyle anlamlı bir özet oluşturmak mümkün değildir.\n\nEğer özetlemek istediğin bir metin varsa, lütfen belgenin içindeki **asıl yazılı içeriği** kopyalayıp bana yapıştırabilir misin? O zaman sana net, anlaşılır ve yapılandırılmış bir özet sunmaktan memnuniyet duyarım.\n\nNotlarını daha iyi organize etmen ve önemli bilgileri kolayca erişilebilir kılman için buradayım!', 'Merhaba Emirhan,\n\nSağladığın verilerin doğrudan okunabilir metin içeriği olmaması nedeniyle, notlarını daha verimli kullanabilmen için genel ama çok pratik öneriler sunabilirim:\n\n1.  **Metin İçeriğini Doğrudan Paylaş:** Özetlemek veya düzenlemek istediğin belgenin **asıl yazılı metnini** kopyalayıp bana yapıştır. Bu, anlamlı analizler için kritik.\n2.  **Sıkıştırılmış Dosyalardan Kaçın:** Word (DOCX) veya PDF gibi sıkıştırılmış/ikili dosya formatlarını doğrudan göndermek yerine, içindeki metin içeriğini çıkarıp düz metin olarak ilet.\n3.  **Okunabilir Notlar Oluştur:** Notlarını düz metin olarak, insan ve yapay zeka tarafından kolayca okunabilecek ve işlenebilecek şekilde almaya özen göster.\n4.  **Anahtar Bilgileri Vurgula:** Notlarını alırken, en önemli gördüğünüz kısımları, ana fikirleri veya anahtar kelimeleri belirginleştir. Bu, özetleme sürecini hızlandırır.\n5.  **Amacını Belirt:** Notunla ne yapmamı istediğini (özetleme, düzenleme, anahtar kelime çıkarma, soru cevaplama vb.) açıkça belirtmen, daha hedefli yardım almanı sağlar.\n6.  **Farklı Kaynaklardan Kopyalamayı Dene:** Eğer bir metin kopyalama sorunu yaşarsan, metni farklı bir metin düzenleyiciye (örneğin Not Defteri) yapıştırıp oradan tekrar kopyalamayı dene.\n\nUnutma, NotDefteri uygulamasının yapay zeka asistanı olarak sana en iyi yardımı sunabilmem için, notlarının bana net ve okunabilir metin formatında ulaşması gerekiyor.', '2025-10-05 23:20:49'),
(6, 1, 'notes', 'Aşağıdaki metni özetle. Net, anlaşılır ve yapılandırılmış bir özet oluştur:\n\nPK\0\0\0\0\0!\02�oWf\0\0�\0\0\0[Content_Types].xml �(�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0���j�0E����Ѷ�J�(��ɢ�eh��4ND�B�����81�$14�� ��{�1��ښl	�w%�=���^i7+���-d&�0�A�6�l4��L60#�Ò�S\nO����X�\0�*��V$:�B~���^�K����ڃ\r/P��I��~7$��i��J&B0Z�Du�t�OJ�K(H����xG\r�L�+�v����dcӻ���W>*��\\XR�mp���Z}����HwnM�V��n���-�\")/�ZwB`���4���	��s�DX���j��;A*ʝ����c�֝��\04���[�S��9�> ������{�V�4p����W�&����A����|�d�?\0\0\0��\0PK\0\0\0\0\0!\0�\Z��\0\0\0N\0\0\0_rels/.rels �(�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0���j�0@���ѽQ���N/c���[IL��j���<��]�aG��ӓ�zs�Fu��]�\Z�U\r��	��^�[��x\0����1x�p����f��#I)ʃ�Y��\Z�������*D��i\")��c$���qU���~3��1��jH[{�=E����~\nf?��3-���޲]�Tꓸ2�j)�,\Zl0/%��b�\n\Z���z���ŉ�,	�	�/�|f\\Z���?6�!Y�_�o�]A�\0\0��\0PK\0\0\0\0\0!\0�l<�\0\0\0`�\0\0\0\0\0word/document.xml�]KoI��/��!W�B�B�X�b�F\Z�E��x�l�4<���d�Țz��!�Z���`N���W��NK�mDV��$J�J)�m��z�#2⋈�Ȍ?��������o(;�\r¼�oZ^w����g�\rF�3��{lc�?�������wb�y�\"�p�c���ы����n��1��;��	��?�v:���[����]����~�wXB}�Nh����$V�Џ�2��vz4�ا�2�̅�v�����AU�vQ��E�ت[iRA�n�T�+iF�t����%��J*�.�\"W�-vro3��g�<��F�3��4���3(�O#�m9V4�2��jy�D��I	n��\\By��M��q)��Fx{���&�c����ӏ������O�F�w�%�7R`�T�\r�t���g�\'��ʖ7{�BN�#��댟��WE�.hk$�pU�H�ӱs������F���!҄�u�[�_U,E�)�*��3.@�U��a��b\\F%-c�s%�X�%(V�r�Q�r�+�*�x�1S�q�\"�����O�����V�x�v�]\Z�\r\'B��x,���s��ϰL��h�I�wj����y����Ҭ�J{y��x�PV*�� �טw=�$w;{/��ж-�% ���\0�F���}�ב�/�~1c���q\0F`�7�ه�^��%ȐR-V˺���WA�Fx�����{`p�o�7\n��Z�+�ɥ�`��;��ݾs8u	[�,�p+u/������\'l��=�Č�؍ɟv�������hЇ��>s�w��TvP;|���*���8�����\"x,\"ËS��3�G�̱�\"m+ ��A�<B=gtnof��,0��t���k]����b�Z�vx�:.uG秣s�љC�#X�����B�`�Ao��Ajm�y�E�A7v�K\Z�α˔<���$�\"xp��>�e���!^����(���o�����5j��BK�������\nVĺ#�dh��ε���m�Ҍ�Qn5���!� �hs���p7i-^����9��~�t6���[����t���!��N��.�F���8��ҴJ=�����ei(`�Դ��\' �bbǎC=����,&�8�PB��`�`O`�tMm���#	8(��圀��;�`����������v\'/�/�8J�]�`F��90?y��r�넓����69���\Z�aũf�T܎�؋�4O>|��/�O�D����\\E(m6�j��]�t��owX+s�o=�\r�+عw�`ш��\n��s�_���h@�G�	�%�:�L����\Z\0���HK�)��69��FR|J����0�/q\0<\"|���\0������\rC�� x� !<�<ڋ�*{�?��t�+��?P����3����θN�,��M��o�c.C��wX�ɞuj��F.�=E3�-!$2��y��h�G�;h�>e���6�\r\'=��q�%J<��kJ��+g�\"с�X���v��\nwY���ܦ�3���q:]!T4�r�%K�$�;����m�	�\\�(��<���$�$0���%L>� ��&�U���:b�wh����А�c,��\"�� ���_�]	��m�����E�q�ˋ��9�\"E�v����y�����v�\0=����|:�揙c:�]�����^��קʶ��94�����b��m�9��m���[(��p;�����r��4��d��5b�\\�aF_/π~9�r�\0�z�0�e��d�iA�xX�P�/�޼���O�n�N`�#��O։r%�k?⫺y�C��ĝ	0��\0Abp)G,�L�|�St��\Z�pk\"W��٢y	,K��������|�w0�:\'A\0�<�>�pgpI>O��A_{N���\0+�g���S�c�O\0e�z�T�ZZv��Ŗ�Gp�G���+��n�9e���9p����� dzA���Z�1۽.�w��^�F��l��,$u\\v�Q�X�Sj��}��i-\\�.�F,?�W�ٗ�ٜgW�:۠�%ِ�[p�V�q�ob2�J���\\���ft��4fU���3Y�{�+���l���#��V��4��7����ޘ��7v8�z���jR:;�`X\'E����v�������7��j�u�Z6��X�����bFa6葵Х��^DË~�z,�ai7a�M`���_��x�gM:�)_��N��j�Z��n�͵�0���NM�c��m�}7��C�}�C��ES��j�b,=ry8&�S+�N8s����z�YnՌ��o|�W�/z0*�cy6	�0^>xi��Ҩ�@s\0A��\0�6^�\"��\0kȇ��w�\0������A�����C����K�79dBM9��Z�����5��ڲ�}�`�?�5bJ�B��	�˳�!��������ɣ��F���w�g�(1�K�[yh?�0�n�(��7���䧓6H{�\r�4wx��ړ�Y�lO�W3^�Û���SG���Q�>�����\\a~R�GXd[��.�\\Sd#���ږMI8��Hm^*�F�ؒ�o���i_Q�O�l<w�{M)�j��rj-��a��Ti��3��Q�I?R�l;���=m;�+��ħq�,��/p�#\Z2j�8�\'��������V�����?��a�h���X���������翨S1\"�����xqt����į]��2��V�X���&���ӎ�f��Z�.6�t��i:)ux��������Z�ެ�)��<�E�\ZS\0σns�\Z����������ܚ����}-y�|�|���n�+U�_,�/�f^����ɍ%p+���d��^,�[��w��r���0�ί�@>!Ϟ=#�&&�\r����(A���Gh���z���!��-�}��n�����6_�Ҭ��|�S��`�׵�ko_�~��M�Ą��7�&�p`�#-�C�J�Z7�F.V^#-8��^i���.>z�����mڄ��ڻ��&��gڷ��ޥ��\Z�]u��+��iq2�{�!0�V��j�W1�jIiUK����g+p�?4HW�]��0��˒�:V48��0�Z�D�A�F����K.@��6g66D�Nm���\"\Z����8���h�4�8��DV�Ŧ\"*z�Y�V��Z,�R]�A�*���Tp�]ڽ�#��E@�l(J���>�CC��6۔�}Lh����wg�,h��̶\0��q�^��d[���+z���2G�,�%�����)3h�b��H��쾀����?�=�.0�Z�(�FM�����س��p�e+uYX�l���Q�y���n��`t6:�Y�9��9#:�\"�w}�e%�o�jɨ�W�ʘ���u��<\n6��\'X2���~x�`�N�<5Y2!큑kZŶ�AU�J�U�ة��q�,���k�cզ\0��Vi��6�a*getA�,\n2�����~�����s&��2�=�����', 'Merhaba Emirhan, NotDefteri uygulaması asistanın olarak sana yardımcı olmaktan memnuniyet duyarım.\n\nAncak, bana sağladığın metin, bir belge dosyasının (muhtemelen bir Word belgesi - `.docx` gibi) ham, sıkıştırılmış verisi gibi görünmektedir. Bu format, bilgisayarlar tarafından okunmak üzere tasarlanmış ikili bir yapıdır ve insan tarafından doğrudan anlaşılabilecek metin içeriği barındırmamaktadır.\n\nBu nedenle, bu ham veriyi doğrudan özetlemem veya anlamlandırmam mümkün değildir.\n\n**Ne Yapabiliriz?**\n\nEğer bu belgenin içeriğini özetlememi istersen, lütfen belgenin içindeki **metin içeriğini** kopyalayıp buraya yapıştır. Metin içeriği elimde olduğunda, sana net, anlaşılır ve yapılandırılmış bir özet sunmaktan, notlarını düzenlemene ve daha iyi organize etmene yardımcı olacak not alma teknikleri ve yapılandırma önerileri sunmaktan memnuniyet duyarım.\n\nYardımcı olabilmem için doğru metin içeriğini bekliyorum!', '2025-10-05 23:26:16'),
(7, 1, 'notes', 'Aşağıdaki dosya içeriği ve özetine dayanarak, kullanıcıya 5-7 adet pratik öneri ver. Her öneri kısa, net ve uygulanabilir olsun. Öneriler şu kategorilerde olabilir: öğrenme, uygulama, geliştirme, takip, organizasyon:\n\nMETİN:\nPK\0\0\0\0\0!\02�oWf\0\0�\0\0\0[Content_Types].xml �(�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0���j�0E����Ѷ�J�(��ɢ�eh��4ND�B�����81�$14�� ��{�1��ښl	�w%�=���^i7+���-d&�0�A�6�l4��L60#�Ò�S\nO����X�\0�*��V$:�B~���^�K����ڃ\r/P��I��~7$��i��J&B0Z�Du�t�OJ�K(H����xG\r�L�+�v����dcӻ���W>*��\\XR�mp���Z}����HwnM�V��n���-�\")/�ZwB`���4���	��s�DX���j��;A*ʝ����c�֝��\04���[�S��9�> ������{�V�4p����W�&����A����|�d�?\0\0\0��\0PK\0\0\0\0\0!\0�\Z��\0\0\0N\0\0\0_rels/.rels �(�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0���j�0@���ѽQ���N/c���[IL��j���<��]�aG��ӓ�zs�Fu��]�\Z�U\r��	��^�[��x\0����1x�p����f��#I)ʃ�Y��\Z�������*D��i\")��c$���qU���~3��1��jH[{�=E����~\nf?��3-���޲]�Tꓸ2�j)�,\Zl0/%��b�\n\Z���z���ŉ�,	�	�/�|f\\Z���?6�!Y�_�o�]A�\0\0��\0PK\0\0\0\0\0!\0�l<�\0\0\0`�\0\0\0\0\0word/document.xml�]KoI��/��!W�B�B�X�b�F\Z�E��x�l�4<���d�Țz��!�Z���`N���W��NK�mDV��$J�J)�m��z�#2⋈�Ȍ?��������o(;�\r¼�oZ^w����g�\rF�3��{lc�?�������wb�y�\"�p�c���ы����n��1��;��	��?�v:���[����]����~�wXB}�Nh����$V�Џ�2��vz4�ا�2�̅�v�����AU�vQ��E�ت[iR\n\nÖZET:\nMerhaba Emirhan, NotDefteri uygulaması asistanın olarak sana yardımcı olmaktan memnuniyet duyarım.\n\nAncak, bana sağladığın metin, bir belge dosyasının (muhtemelen bir Word belgesi - `.docx` gibi) ham, sıkıştırılmış verisi gibi görünmektedir. Bu format, bilgisayarlar tarafından okunmak üzere tasarlanmış ikili bir yapıdır ve insan tarafından doğrudan anlaşılabilecek metin içeriği barındırmamaktadır.\n\nBu nedenle, bu ham veriyi doğrudan özetlemem veya anlamlandırmam mümkün değildir.\n\n**Ne Yapabiliriz?**\n\nEğer bu belgenin içeriğini özetlememi istersen, lütfen belgenin içindeki **metin içeriğini** kopyalayıp buraya yapıştır. Metin içeriği elimde olduğunda, sana net, anlaşılır ve yapılandırılmış bir özet sunmaktan, notlarını düzenlemene ve daha iyi organize etmene yardımcı olacak not alma teknikleri ve yapılandırma önerileri sunmaktan memnuniyet duyarım.\n\nYardımcı olabilmem için doğru metin içeriğini bekliyorum!', 'Merhaba Emirhan, NotDefteri uygulamasının yapay zeka asistanı olarak sana yardımcı olmaktan memnuniyet duyarım.\n\nSağladığın metin, bir belge dosyasının (muhtemelen Word belgesi gibi) ham, sıkıştırılmış verisi olduğu için içeriğini okumam ve anlamlandırmam mümkün değil. Bu tür dosya formatlarını doğrudan işleyemiyorum.\n\nAncak, bu durumu aşmak ve notlarını en verimli şekilde kullanman için sana birkaç pratik öneri sunabilirim:\n\n1.  **Metin İçeriğini Kopyala-Yapıştır:** Belgelerinin (örneğin Word dosyası) içindeki **metin içeriğini** doğrudan kopyalayıp buraya yapıştır. Yapay zeka, ham dosya formatlarını değil, okunabilir metinleri analiz edebilir.\n2.  **Düz Metin Formatını Tercih Et:** Notlarını mümkünse düz metin (text) olarak sun. Madde işaretleri, numaralı listeler veya paragraflar halinde düzenlenmiş metinler, AI\'ın anlaması için idealdir.\n3.  **Net Talimatlar Ver:** Metni gönderirken, \"Özetle\", \"Ana fikirleri çıkar\", \"Düzenle\" veya \"Soru-cevap hazırla\" gibi ne istediğini açıkça belirten bir talimat ekle.\n4.  **Uzun Metinleri Bölümle:** Çok uzun notların veya belgelerin varsa, AI\'ın daha etkili analiz yapabilmesi için ana bölümlerini veya özetlemek istediğin kısımları ayrı ayrı göndermeyi düşünebilirsin.\n5.  **Geri Bildirimi Dikkate Al:** Eğer AI bir metni işleyemediğini belirtirse, gönderdiğin formatı gözden geçir ve metin içeriği olduğundan emin ol. Bu, gelecekteki etkileşimlerini iyileştirecektir.\n6.  **Küçük Bir Notla Dene:** Büyük bir belgeyle başlamadan önce, NotDefteri\'nin nasıl çalıştığını anlamak için kısa bir notla veya birkaç cümleyle deneme yapabilirsin. Bu, sürecin nasıl işlediğini görmene yardımcı olur.\n\nDoğru metin içeriğini bekliyorum. Notlarını düzenlemene, özetlemene ve daha iyi organize etmene yardımcı olmaktan mutluluk duyarım!', '2025-10-05 23:27:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `created_by`, `created_at`) VALUES
(1, 'asgsdg', 'sgsdg', 1, '2025-10-05 12:25:44');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `group_members`
--

CREATE TABLE `group_members` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('admin','member') DEFAULT 'member',
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `user_id`, `role`, `joined_at`) VALUES
(1, 1, 1, 'admin', '2025-10-05 12:25:44');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `group_tasks`
--

CREATE TABLE `group_tasks` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `assigned_to` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `status` enum('todo','in_progress','completed') DEFAULT 'todo',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `due_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `group_tasks`
--

INSERT INTO `group_tasks` (`id`, `group_id`, `title`, `description`, `assigned_to`, `created_by`, `status`, `priority`, `due_date`, `created_at`) VALUES
(1, 1, 'sadgas', 'asdgasdg', 1, 1, 'todo', 'low', NULL, '2025-10-05 12:35:03');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `group_task_comments`
--

CREATE TABLE `group_task_comments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kanban_tasks`
--

CREATE TABLE `kanban_tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('todo','in_progress','completed') DEFAULT 'todo',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `kanban_tasks`
--

INSERT INTO `kanban_tasks` (`id`, `user_id`, `title`, `description`, `priority`, `status`, `due_date`, `created_at`) VALUES
(1, 1, 'asdf', 'sdhfdhsdf', 'medium', 'completed', '0000-00-00', '2025-10-04 23:51:05'),
(2, 1, 'sdfh', 'sdfhdfs', 'medium', 'completed', '0000-00-00', '2025-10-04 23:51:18'),
(3, 1, 'asdg', 'asdgasd', 'high', 'completed', '0000-00-00', '2025-10-04 23:52:32'),
(4, 1, 'sadg', 'sagsdsdg', 'medium', 'completed', '0000-00-00', '2025-10-04 23:53:10'),
(5, 1, 'dasfg', 'asdgsda', 'medium', 'completed', '0000-00-00', '2025-10-04 23:58:27'),
(6, 1, 'sgsdgsgsag', 'asdgsadsagddas', 'medium', 'completed', '0000-00-00', '2025-10-11 20:25:12'),
(7, 1, 'sdf', 'sdf', 'medium', 'completed', '0000-00-00', '2025-10-11 21:12:42'),
(8, 1, 'merhaba', 'merha', 'low', 'completed', '0000-00-00', '2025-10-11 21:16:31');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Tablo için tablo yapısı `monthly_tasks`
--

CREATE TABLE `monthly_tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `description` text,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `duration_minutes` int(11) DEFAULT '60',
  `due_date` datetime DEFAULT NULL,
  `is_done` tinyint(1) DEFAULT '0',
  `is_favorite` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `monthly_tasks`
--

INSERT INTO `monthly_tasks` (`id`, `user_id`, `task`, `description`, `priority`, `duration_minutes`, `due_date`, `is_done`, `is_favorite`, `created_at`) VALUES
(1, 1, 'asdgs', 'sadgd', 'medium', 60, '2025-11-05 18:00:00', 0, 0, '2025-10-05 14:40:38'),
(2, 1, 'sdfa', 'asdgsda', 'medium', 120, '2025-10-19 14:41:00', 0, 0, '2025-10-05 14:41:09'),
(3, 1, 'asd', 'dsa', 'medium', 60, '2025-11-05 18:00:00', 0, 0, '2025-10-05 14:55:39');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `note` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `color` varchar(20) DEFAULT 'yellow',
  `is_favorite` tinyint(1) DEFAULT '0',
  `is_public` tinyint(1) DEFAULT '0',
  `tags` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `title`, `content`, `note`, `created_at`, `color`, `is_favorite`, `is_public`, `tags`, `file_path`) VALUES
(3, 1, '85', '85', '85', '2025-07-03 23:11:53', 'yellow', 0, 0, '', NULL),
(4, 1, 'merhabaa', 'merhabaa', 'merhabaa', '2025-07-04 14:34:23', 'blue', 0, 0, '#555', NULL),
(5, 1, '123', '456', '', '2025-10-03 11:33:51', 'yellow', 1, 0, NULL, NULL),
(6, 1, '123', '456', '', '2025-10-03 11:38:17', 'yellow', 1, 0, NULL, NULL),
(7, 1, '1223', '56456342kjllhgjkl', '', '2025-10-03 11:38:31', 'yellow', 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `note_tags`
--

CREATE TABLE `note_tags` (
  `id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('friend_request','group_invite','task_assigned','system') DEFAULT 'system',
  `title` varchar(255) NOT NULL,
  `message` text,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notlar`
--

CREATE TABLE `notlar` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `baslik` varchar(100) NOT NULL,
  `icerik` text NOT NULL,
  `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Tablo için tablo yapısı `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(7) DEFAULT '#4a90e2',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `tags`
--

INSERT INTO `tags` (`id`, `user_id`, `name`, `color`, `created_at`) VALUES
(2, 1, 'asdefd', '#f5a623', '2025-10-05 12:38:57');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(32) DEFAULT 'cat',
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `google_picture` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `avatar`, `email`, `phone`, `google_id`, `google_picture`) VALUES
(1, 'emirhan', '$2y$10$niu5n19zdTOObmBqpZFffucRHVxON4x3r9cJ.nzEbxmf6GUd1j2qO', 'rabbit', NULL, NULL, NULL, NULL),
(2, 'emirhan27', '$2y$10$42HYlaq.S5AnyTymqLOXjO340rQnxMIsUrHnKo6mHHg6awau86AfO', 'cat', NULL, NULL, NULL, NULL),
(3, 'Emirhan Tunga', '$2y$10$NmeUW.jUbfRNRX5AOp8BSOYmTyowED/1mUHDQ5cfdioQkEuTpO0T6', 'cat', 'emirhantunga27@outlook.com', NULL, '116119644185334848399', 'https://lh3.googleusercontent.com/a/ACg8ocIQ0FMEhAbeZ-oNHEUOU14oL6i36PwkWfEaJHGHZkuOpKNrMUE=s96-c'),
(4, 'emirhan_tunga', '$2y$10$/MkvOP/ZyxT4c6A2xUoel.f9TgMg9xEofza2GsN00IlWCSQZXnkoy', 'cat', 'emirhantunga27@gmail.com', '5519876357', NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `weekly_tasks`
--

CREATE TABLE `weekly_tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `description` text,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `duration_minutes` int(11) DEFAULT '60',
  `due_date` datetime DEFAULT NULL,
  `is_done` tinyint(1) DEFAULT '0',
  `week_start` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_favorite` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `weekly_tasks`
--

INSERT INTO `weekly_tasks` (`id`, `user_id`, `task`, `description`, `priority`, `duration_minutes`, `due_date`, `is_done`, `week_start`, `created_at`, `is_favorite`) VALUES
(1, 1, 'asdsf', 'wqeoıtpew', 'medium', 60, '2025-10-12 18:00:00', 0, '0000-00-00', '2025-10-05 14:40:19', 0),
(2, 1, 'asdsf', 'wqeoıtpew', 'medium', 60, '2025-10-12 18:00:00', 0, '0000-00-00', '2025-10-05 14:40:27', 0),
(3, 1, 'sdfa', 'asdgsda', 'medium', 60, '2025-10-12 18:00:00', 0, '0000-00-00', '2025-10-05 14:40:55', 0),
(4, 1, 'sdfa', 'asdgsda', 'medium', 120, '2025-10-19 14:41:00', 0, '0000-00-00', '2025-10-05 14:41:03', 0),
(5, 1, 'sdfa', 'asdgsda', 'medium', 120, '2025-10-19 14:41:00', 0, '0000-00-00', '2025-10-05 14:41:03', 0),
(6, 1, 'sdfa', 'asdgsda', 'medium', 120, '2025-10-19 14:41:00', 0, '0000-00-00', '2025-10-05 14:41:04', 0),
(7, 1, 'sadga', 'asdgsdagdsgs', 'low', 60, '2025-10-12 18:00:00', 0, '0000-00-00', '2025-10-05 14:43:52', 0),
(8, 1, 'sadfsad', 'asdgsda', 'medium', 60, '2025-10-12 18:00:00', 0, '0000-00-00', '2025-10-05 14:53:04', 0);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `action_plans`
--
ALTER TABLE `action_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_action_plans_user` (`user_id`);

--
-- Tablo için indeksler `action_plan_steps`
--
ALTER TABLE `action_plan_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_plan_steps_plan` (`plan_id`);

--
-- Tablo için indeksler `daily_tasks`
--
ALTER TABLE `daily_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_daily_tasks_user_created` (`user_id`,`created_at`),
  ADD KEY `idx_daily_tasks_user_due` (`user_id`,`due_date`),
  ADD KEY `idx_daily_tasks_user_status` (`user_id`,`is_done`);

--
-- Tablo için indeksler `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `friendships`
--
ALTER TABLE `friendships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_friend_status` (`friend_id`,`status`);

--
-- Tablo için indeksler `gemini_conversations`
--
ALTER TABLE `gemini_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_context` (`user_id`,`context`),
  ADD KEY `idx_created` (`created_at`);

--
-- Tablo için indeksler `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Tablo için indeksler `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_group_member` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `group_tasks`
--
ALTER TABLE `group_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `created_by` (`created_by`);

--
-- Tablo için indeksler `group_task_comments`
--
ALTER TABLE `group_task_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `kanban_tasks`
--
ALTER TABLE `kanban_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `monthly_tasks`
--
ALTER TABLE `monthly_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_created` (`user_id`,`created_at`),
  ADD KEY `idx_user_due` (`user_id`,`due_date`),
  ADD KEY `idx_user_status` (`user_id`,`is_done`);

--
-- Tablo için indeksler `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `note_tags`
--
ALTER TABLE `note_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_note_tag` (`note_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`is_read`);

--
-- Tablo için indeksler `notlar`
--
ALTER TABLE `notlar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

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
-- Tablo için indeksler `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_tag` (`user_id`,`name`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- Tablo için indeksler `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_weekly_tasks_user_created` (`user_id`,`created_at`),
  ADD KEY `idx_weekly_tasks_user_due` (`user_id`,`due_date`),
  ADD KEY `idx_weekly_tasks_user_status` (`user_id`,`is_done`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `action_plans`
--
ALTER TABLE `action_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `action_plan_steps`
--
ALTER TABLE `action_plan_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `daily_tasks`
--
ALTER TABLE `daily_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `friendships`
--
ALTER TABLE `friendships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `gemini_conversations`
--
ALTER TABLE `gemini_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `group_tasks`
--
ALTER TABLE `group_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `group_task_comments`
--
ALTER TABLE `group_task_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kanban_tasks`
--
ALTER TABLE `kanban_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `monthly_tasks`
--
ALTER TABLE `monthly_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `note_tags`
--
ALTER TABLE `note_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `notlar`
--
ALTER TABLE `notlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Tablo için AUTO_INCREMENT değeri `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `action_plans`
--
ALTER TABLE `action_plans`
  ADD CONSTRAINT `action_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `action_plan_steps`
--
ALTER TABLE `action_plan_steps`
  ADD CONSTRAINT `action_plan_steps_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `action_plans` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `daily_tasks`
--
ALTER TABLE `daily_tasks`
  ADD CONSTRAINT `daily_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `friendships`
--
ALTER TABLE `friendships`
  ADD CONSTRAINT `friendships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friendships_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `gemini_conversations`
--
ALTER TABLE `gemini_conversations`
  ADD CONSTRAINT `gemini_conversations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `group_tasks`
--
ALTER TABLE `group_tasks`
  ADD CONSTRAINT `group_tasks_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `group_tasks_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `group_task_comments`
--
ALTER TABLE `group_task_comments`
  ADD CONSTRAINT `group_task_comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `group_tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_task_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `kanban_tasks`
--
ALTER TABLE `kanban_tasks`
  ADD CONSTRAINT `kanban_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `monthly_tasks`
--
ALTER TABLE `monthly_tasks`
  ADD CONSTRAINT `monthly_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `note_tags`
--
ALTER TABLE `note_tags`
  ADD CONSTRAINT `note_tags_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `note_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `notlar`
--
ALTER TABLE `notlar`
  ADD CONSTRAINT `notlar_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`);

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
-- Tablo kısıtlamaları `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  ADD CONSTRAINT `weekly_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
