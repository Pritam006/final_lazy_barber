-- Database: `lazy_barber`
CREATE DATABASE IF NOT EXISTS `lazy_barber`;
USE `lazy_barber`;

-- Table: `USERS`
CREATE TABLE IF NOT EXISTS `USERS` (
  `userid` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `role` ENUM('customer', 'barber') DEFAULT 'customer',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: `SERVICES`
CREATE TABLE IF NOT EXISTS `SERVICES` (
  `serviceid` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price_aud` DECIMAL(10, 2) NOT NULL,
  `duration_minutes` INT NOT NULL,
  `is_active` BOOLEAN DEFAULT TRUE
);

-- Table: `AVAILABILITY`
CREATE TABLE IF NOT EXISTS `AVAILABILITY` (
  `availabilityid` INT AUTO_INCREMENT PRIMARY KEY,
  `barberid` INT NOT NULL,
  `day_of_week` TINYINT NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `is_blocked` BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (`barberid`) REFERENCES `USERS`(`userid`) ON DELETE CASCADE
);

-- Table: `APPOINTMENTS`
CREATE TABLE IF NOT EXISTS `APPOINTMENTS` (
  `appointmentid` INT AUTO_INCREMENT PRIMARY KEY,
  `customerid` INT NOT NULL,
  `barberid` INT NOT NULL,
  `serviceid` INT NOT NULL,
  `appointment_date` DATE NOT NULL,
  `time_slot` TIME NOT NULL,
  `total_price` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`customerid`) REFERENCES `USERS`(`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`barberid`) REFERENCES `USERS`(`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`serviceid`) REFERENCES `SERVICES`(`serviceid`) ON DELETE CASCADE
);

-- Table: `NOTIFICATIONS`
CREATE TABLE IF NOT EXISTS `NOTIFICATIONS` (
  `notifid` INT AUTO_INCREMENT PRIMARY KEY,
  `customerid` INT NOT NULL,
  `barberid` INT NOT NULL,
  `appointmentid` INT NOT NULL,
  `type` ENUM('booking_confirmation', 'reminder', 'cancellation', 'update') NOT NULL,
  `sent_at` TIMESTAMP NULL DEFAULT NULL,
  `status` ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
  `retry_count` TINYINT DEFAULT 0,
  FOREIGN KEY (`customerid`) REFERENCES `USERS`(`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`barberid`) REFERENCES `USERS`(`userid`) ON DELETE CASCADE,
  FOREIGN KEY (`appointmentid`) REFERENCES `APPOINTMENTS`(`appointmentid`) ON DELETE CASCADE
);

-- Insert Default Barber Admin
INSERT INTO `USERS` (`name`, `email`, `password_hash`, `phone`, `role`) VALUES 
('Admin Barber', 'admin@lazybarber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0000000000', 'barber'); 
-- default password is 'password'

-- Insert Default Services
INSERT INTO `SERVICES` (`name`, `description`, `price_aud`, `duration_minutes`) VALUES 
('Classic Haircut', 'A standard men\'s haircut.', 30.00, 30),
('Beard Trim', 'Professional beard trimming and shaping.', 15.00, 15),
('Haircut & Beard Trim', 'Complete package for haircut and beard.', 40.00, 45);

-- Insert Default Availability for Barber (Mon-Fri 9 AM to 5 PM)
INSERT INTO `AVAILABILITY` (`barberid`, `day_of_week`, `start_time`, `end_time`) VALUES 
(1, 1, '09:00:00', '17:00:00'),
(1, 2, '09:00:00', '17:00:00'),
(1, 3, '09:00:00', '17:00:00'),
(1, 4, '09:00:00', '17:00:00'),
(1, 5, '09:00:00', '17:00:00');
