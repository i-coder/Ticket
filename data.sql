-- --------------------------------------------------------
-- Хост:                         192.168.10.10
-- Версия сервера:               5.7.29-0ubuntu0.18.04.1 - (Ubuntu)
-- Операционная система:         Linux
-- HeidiSQL Версия:              11.1.0.6116
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Дамп структуры для таблица ticket.subdivisions_name
CREATE TABLE IF NOT EXISTS `subdivisions_name` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы ticket.subdivisions_name: ~23 rows (приблизительно)
/*!40000 ALTER TABLE `subdivisions_name` DISABLE KEYS */;
INSERT INTO `subdivisions_name` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'Все подразделения', NULL, NULL),
	(2, 'Корпоративный секретарь', NULL, NULL),
	(3, 'Служба комплаенс', NULL, NULL),
	(4, 'Служба внутренного аудита', NULL, NULL),
	(10, 'Управление бухгалтерского учета и отчетности', NULL, NULL),
	(11, 'Управление по управлению активами', NULL, NULL),
	(12, 'Направление андерайтинга', NULL, NULL),
	(14, 'Департамент статистики', NULL, NULL),
	(15, 'Департамент продаж', NULL, NULL),
	(17, 'Колл-центр / Контакт центр', NULL, NULL),
	(18, 'Департамент регионального управления', NULL, NULL),
	(19, 'Отдел кадров / HR', NULL, NULL),
	(20, 'Юридическое управление', NULL, NULL),
	(21, 'Департамент страховых выплат', NULL, NULL),
	(22, 'Служба безопасности', NULL, NULL),
	(23, 'Департамент информационных технологий', NULL, NULL),
	(24, 'Управление рисками', NULL, NULL),
	(25, 'Метолодлогия и финансовая отчетность', NULL, NULL),
	(26, 'Секретарь КСЖ', NULL, NULL),
	(27, 'Актуарий', NULL, NULL);
/*!40000 ALTER TABLE `subdivisions_name` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
