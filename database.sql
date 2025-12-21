-- Database schema for Comfort Travel API

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `comfort_otdyh` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `comfort_otdyh`;

-- Countries table
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(3) NOT NULL,
  `visa_required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clients table
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(200) NOT NULL,
  `passport_number` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `birth_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `passport_number` (`passport_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tours table
CREATE TABLE IF NOT EXISTS `tours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `max_people` int(11) NOT NULL,
  `available_spots` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `tours_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `tour_id` (`tour_id`),
  UNIQUE KEY `unique_booking` (`client_id`, `tour_id`, `booking_date`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for countries
INSERT INTO `countries` (`name`, `code`, `visa_required`) VALUES
('Турция', 'TR', 0),
('Египет', 'EG', 1),
('Таиланд', 'TH', 1),
('Испания', 'ES', 1),
('Италия', 'IT', 1);

-- Sample data for clients
INSERT INTO `clients` (`full_name`, `passport_number`, `phone`, `email`, `birth_date`) VALUES
('Иванов Иван Иванович', '1234567890', '+79161234567', 'ivanov@mail.ru', '1985-05-15'),
('Петрова Анна Сергеевна', '0987654321', '+79031234567', 'petrova@gmail.com', '1990-08-22'),
('Сидоров Алексей Петрович', '5678901234', '+79261234567', 'sidorov@yandex.ru', '1978-12-10');

-- Sample data for tours
INSERT INTO `tours` (`country_id`, `name`, `description`, `start_date`, `end_date`, `price`, `max_people`, `available_spots`) VALUES
(1, 'Анталия: Все включено', 'Отдых на берегу Средиземного моря', '2024-06-01', '2024-06-15', 85000.00, 20, 18),
(2, 'Хургада: Дайвинг тур', 'Погружения в Красное море', '2024-07-10', '2024-07-20', 95000.00, 15, 15),
(3, 'Пхукет: Экзотика Таиланда', 'Экскурсии по островам', '2024-08-05', '2024-08-19', 120000.00, 25, 22),
(4, 'Барселона: Искусство и море', 'Экскурсии по достопримечательностям', '2024-09-01', '2024-09-10', 110000.00, 18, 17);

-- Sample data for bookings
INSERT INTO `bookings` (`client_id`, `tour_id`, `booking_date`, `total_price`, `status`, `notes`) VALUES
(1, 1, '2024-05-10', 85000.00, 'confirmed', 'Оплата 50%'),
(2, 3, '2024-06-15', 120000.00, 'pending', 'Бронь до 20.05'),
(3, 2, '2024-05-20', 95000.00, 'confirmed', 'Оплачен полностью');

