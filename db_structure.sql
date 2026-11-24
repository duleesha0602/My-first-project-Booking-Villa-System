-- ====================================================================
-- IT1208 - Web Technologies CA 02 Assignment
-- Project: COASTAL VILLA - Web-Based Villa & Room Booking System
-- Database Setup and Structure
-- ====================================================================

-- 1. Create database if it does not already exist
CREATE DATABASE IF NOT EXISTS `coastal_villa_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `coastal_villa_db`;

-- Disable foreign key checks while resetting/building tables
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `room_facilities`;
DROP TABLE IF EXISTS `room_images`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `enquiries`;
DROP TABLE IF EXISTS `gallery`;
DROP TABLE IF EXISTS `facilities`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `villa_details`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- Table 1: users
-- Stores both Registered Guests and System Administrators
-- ====================================================================
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `contact_number` VARCHAR(25) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('guest', 'admin') NOT NULL DEFAULT 'guest',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- Table 2: rooms
-- Stores room types, descriptions, pricing, and main photo
-- ====================================================================
CREATE TABLE `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_name` VARCHAR(100) NOT NULL,
  `room_type` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `facilities` TEXT NOT NULL,
  `price_per_night` DECIMAL(10,2) NOT NULL,
  `max_guests` INT NOT NULL,
  `room_image` VARCHAR(255) NOT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- Table 3: room_images
-- Stores additional multiple photographs for each room
-- ====================================================================
CREATE TABLE `room_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_room_images_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- Table 4: bookings
-- Stores guest accommodation reservations
-- ====================================================================
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_reference` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `room_id` INT NOT NULL,
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `guests` INT NOT NULL,
  `price_per_night` DECIMAL(10,2) NOT NULL,
  `total_nights` INT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `special_requests` TEXT DEFAULT NULL,
  `booking_status` ENUM('Pending', 'Confirmed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `booking_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookings_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- Table 5: villa_details
-- Stores overall villa information, contact, and branding
-- ====================================================================
CREATE TABLE `villa_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `villa_name` VARCHAR(150) NOT NULL,
  `tagline` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `contact_number` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `facilities` TEXT NOT NULL,
  `facebook_link` VARCHAR(255) DEFAULT '',
  `instagram_link` VARCHAR(255) DEFAULT '',
  `twitter_link` VARCHAR(255) DEFAULT '',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- Table 6: gallery
-- Stores dynamic villa photo gallery categorized
-- ====================================================================
CREATE TABLE `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- Table 7: enquiries
-- Stores guest inquiries submitted via the Contact Us form
-- ====================================================================
CREATE TABLE `enquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sender_name` VARCHAR(100) NOT NULL,
  `sender_email` VARCHAR(100) NOT NULL,
  `sender_phone` VARCHAR(30) DEFAULT '',
  `subject` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'replied') NOT NULL DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- Table 8: facilities
-- Master list of villa and room amenities
-- ====================================================================
CREATE TABLE `facilities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `facility_name` VARCHAR(100) NOT NULL,
  `facility_icon` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- Table 9: room_facilities (Normalization Junction Table)
-- Maps many-to-many relationship between rooms and facilities
-- ====================================================================
CREATE TABLE `room_facilities` (
  `room_id` INT NOT NULL,
  `facility_id` INT NOT NULL,
  PRIMARY KEY (`room_id`, `facility_id`),
  CONSTRAINT `fk_rf_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rf_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ====================================================================
-- DEFAULT DATA SEEDING
-- ====================================================================

-- 1. Insert Default Users (Admin and Sample Guest)
-- Admin Password: AdminPassword123
-- Guest Password: GuestPassword123
INSERT INTO `users` (`id`, `full_name`, `email`, `contact_number`, `password`, `role`, `status`) VALUES
(1, 'Villa Administrator', 'admin@coastalvilla.com', '+94 77 123 4567', '$2y$10$RfMWvF4ATOMihkflxcIucuOXPaV5k5v1Vea6LfI3WIQo30brxuV7i', 'admin', 'active'),
(2, 'Kasun Perera', 'guest@gmail.com', '+94 71 987 6543', '$2y$10$4CChwT5x.C1VbQUYk5zxGOYlVq4VcHY2spyutXrNhaKY8LG.2SMFy', 'guest', 'active');

-- 2. Insert Villa Details
INSERT INTO `villa_details` (`id`, `villa_name`, `tagline`, `description`, `address`, `location`, `contact_number`, `email`, `facilities`, `facebook_link`, `instagram_link`, `twitter_link`) VALUES
(1, 'Coastal Villa Sanctuary', 'Your Private Luxury Escape by the Indian Ocean', 
'Nestled along the pristine golden shores of Mirissa, Coastal Villa Sanctuary offers an unforgettable tropical retreat. Featuring panoramic ocean views, private beach access, infinity swimming pool, and bespoke Sri Lankan hospitality, we provide luxury, peace, and serenity for discerning travelers.',
'No. 45, Beach Road, Mirissa, Southern Province, Sri Lanka', 'Mirissa, Sri Lanka',
'+94 41 223 8899', 'reservations@coastalvilla.com',
'Infinity Swimming Pool, Free High-Speed Wi-Fi, Direct Beach Access, Gourmet Oceanfront Restaurant, Air Conditioning, Free Secure Parking, 24/7 Concierge Service, Ocean View Balconies',
'https://facebook.com/coastalvilla', 'https://instagram.com/coastalvilla', 'https://twitter.com/coastalvilla');

-- 3. Insert Master Facilities
INSERT INTO `facilities` (`id`, `facility_name`, `facility_icon`, `description`) VALUES
(1, 'Swimming Pool', 'fa-solid fa-water-ladder', 'Private crystal clear outdoor infinity pool overlooking the ocean'),
(2, 'Free High-Speed Wi-Fi', 'fa-solid fa-wifi', 'Ultra-fast fiber optic wireless internet throughout the property'),
(3, 'Direct Beach Access', 'fa-solid fa-umbrella-beach', 'Step straight onto the golden sand beach from villa grounds'),
(4, 'Oceanfront Restaurant', 'fa-solid fa-utensils', 'Fresh seafood and authentic Sri Lankan and Western cuisine'),
(5, 'Air Conditioning', 'fa-solid fa-snowflake', 'Modern climate control air conditioning in every suite'),
(6, 'Free Secure Parking', 'fa-solid fa-square-parking', 'Spacious on-site private parking with 24/7 security'),
(7, 'Ocean View Terrace', 'fa-solid fa-eye', 'Spacious private balconies with sunset sea views'),
(8, '24/7 Concierge & Service', 'fa-solid fa-bell-concierge', 'Dedicated staff ready to assist your every holiday request');

-- 4. Insert Sample Rooms
INSERT INTO `rooms` (`id`, `room_name`, `room_type`, `description`, `facilities`, `price_per_night`, `max_guests`, `room_image`, `status`) VALUES
(1, 'Deluxe Ocean View Suite', 'Deluxe Ocean', 
'Wake up to the gentle sound of ocean waves and breathtaking sea vistas. This spacious suite features a king-size bed, private oceanfront balcony, en-suite bathroom with rainfall shower, and premium coastal decor.', 
'Free Wi-Fi, Air Conditioning, Ocean View Balcony, King Bed, Mini Bar, Rainfall Shower, Smart TV', 
28000.00, 2, 'assets/images/rooms/deluxe-ocean.jpg', 'active'),

(2, 'Presidential Luxury Villa Suite', 'Luxury Suite', 
'The pinnacle of coastal indulgence. Featuring an expansive private deck, jacuzzi tub overlooking the ocean, separate lounge area, king-size canopy bed, and dedicated butler service for ultimate relaxation.', 
'Private Jacuzzi, Free Wi-Fi, Air Conditioning, Panoramic Ocean View, Lounge Area, Espresso Machine, King Bed', 
45000.00, 2, 'assets/images/rooms/luxury-suite.jpg', 'active'),

(3, 'Family Coastal Beach Villa', 'Family Villa', 
'Designed especially for families and small groups seeking a shared holiday experience. Includes two spacious interconnected bedrooms, private garden patio leading to the beach, dining area, and kitchenette.', 
'2 King Beds, Free Wi-Fi, Air Conditioning, Beach Access, Garden Patio, Kitchenette, 2 Bathrooms, Smart TV', 
52000.00, 4, 'assets/images/rooms/family-villa.jpg', 'active'),

(4, 'Standard Beachfront Room', 'Standard Beach', 
'A cozy and elegant room steps away from the sand. Features comfortable queen bedding, soothing sea-inspired minimalist interior, private patio with garden views, and modern amenities for value-conscious travelers.', 
'Queen Bed, Free Wi-Fi, Air Conditioning, Garden & Partial Sea View, Rainfall Shower, Work Desk', 
18500.00, 2, 'assets/images/rooms/beach-front.jpg', 'active');

-- 5. Insert Additional Room Images
INSERT INTO `room_images` (`room_id`, `image_path`, `is_primary`) VALUES
(1, 'assets/images/rooms/deluxe-ocean.jpg', 1),
(1, 'assets/images/rooms/luxury-suite.jpg', 0),
(2, 'assets/images/rooms/luxury-suite.jpg', 1),
(2, 'assets/images/rooms/deluxe-ocean.jpg', 0),
(3, 'assets/images/rooms/family-villa.jpg', 1),
(3, 'assets/images/rooms/beach-front.jpg', 0),
(4, 'assets/images/rooms/beach-front.jpg', 1),
(4, 'assets/images/rooms/family-villa.jpg', 0);

-- 6. Map Room Facilities
INSERT INTO `room_facilities` (`room_id`, `facility_id`) VALUES
(1, 2), (1, 3), (1, 5), (1, 7),
(2, 1), (2, 2), (2, 3), (2, 5), (2, 7), (2, 8),
(3, 2), (3, 3), (3, 5), (3, 6), (3, 8),
(4, 2), (4, 3), (4, 5), (4, 6);

-- 7. Insert Sample Villa Gallery Images
INSERT INTO `gallery` (`id`, `title`, `category`, `image_path`) VALUES
(1, 'Infinity Swimming Pool at Sunset', 'Swimming Pool', 'assets/images/gallery/pool.jpg'),
(2, 'Open-Air Seafood Restaurant', 'Dining Area', 'assets/images/gallery/dining.jpg'),
(3, 'Coastal Villa Main Exterior Architecture', 'Villa Exterior', 'assets/images/gallery/exterior.jpg'),
(4, 'Golden Sandy Beach Shoreline', 'Beach and Surroundings', 'assets/images/gallery/beach.jpg'),
(5, 'Sunset Over the Ocean View Balcony', 'Beach and Surroundings', 'assets/images/gallery/sunset.jpg'),
(6, 'Lush Tropical Palm Garden', 'Villa Exterior', 'assets/images/gallery/garden.jpg');

-- 8. Insert Sample Bookings for Demonstration
INSERT INTO `bookings` (`id`, `booking_reference`, `user_id`, `room_id`, `check_in`, `check_out`, `guests`, `price_per_night`, `total_nights`, `total_amount`, `special_requests`, `booking_status`, `booking_date`) VALUES
(1, 'CV-2026-1001', 2, 1, '2026-12-01', '2026-12-04', 2, 28000.00, 3, 84000.00, 'Ocean view top floor preferred. Late check-in around 6 PM.', 'Confirmed', '2026-11-20 10:15:00'),
(2, 'CV-2026-1002', 2, 2, '2026-12-10', '2026-12-12', 2, 45000.00, 2, 90000.00, 'Anniversary holiday celebration.', 'Pending', '2026-11-22 14:30:00'),
(3, 'CV-2026-1003', 2, 4, '2026-11-15', '2026-11-17', 2, 18500.00, 2, 37000.00, 'Airport transfer pickup request.', 'Cancelled', '2026-11-10 09:00:00');

-- 9. Insert Sample Guest Enquiries
INSERT INTO `enquiries` (`id`, `sender_name`, `sender_email`, `sender_phone`, `subject`, `message`, `status`) VALUES
(1, 'Mahesh Silva', 'mahesh.silva@example.com', '+94 77 345 6789', 'Wedding Anniversary Villa Package Inquiry', 'Hello, do you provide customized beach dinner arrangements for wedding anniversaries during December?', 'read'),
(2, 'Emily Watson', 'emily.w@uktravel.com', '+44 79 111 2233', 'Airport Transfer & Whale Watching Tour', 'We are traveling from London in January. Does the villa arrange whale watching boat tours in Mirissa?', 'unread');
