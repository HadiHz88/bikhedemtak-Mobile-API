CREATE TABLE `users` (
  `user_id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `profile_picture` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `taskers` (
  `user_id` INT PRIMARY KEY,
  `skill` VARCHAR(255),
  `availability_status` BOOLEAN DEFAULT true,
  `rating` DECIMAL(3,2) DEFAULT 0
);

CREATE TABLE `categories` (
  `category_id` INT PRIMARY KEY AUTO_INCREMENT,
  `category_name` VARCHAR(255) NOT NULL
);

CREATE TABLE `tasks` (
  `task_id` INT PRIMARY KEY AUTO_INCREMENT,
  `requester_id` INT NOT NULL,
  `tasker_id` INT NOT NULL,
  `category_id` INT,
  `task_description` TEXT NOT NULL,
  `status` ENUM ('pending', 'in_progress', 'completed') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `bookings` (
  `booking_id` INT PRIMARY KEY AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `requester_id` INT NOT NULL,
  `tasker_id` INT NOT NULL,
  `booking_date` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `status` ENUM ('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending'
);

CREATE TABLE `reviews` (
  `review_id` INT PRIMARY KEY AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `reviewer_id` INT NOT NULL,
  `tasker_id` INT NOT NULL,
  `rating` INT,
  `review_content` TEXT,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `favorites` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `tasker_id` INT NOT NULL
);

CREATE TABLE `past_taskers` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `tasker_id` INT NOT NULL,
  `completed_jobs` INT DEFAULT 1
);

ALTER TABLE `taskers` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `tasks` ADD FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `tasks` ADD FOREIGN KEY (`tasker_id`) REFERENCES `taskers` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `tasks` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

ALTER TABLE `bookings` ADD FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

ALTER TABLE `bookings` ADD FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `bookings` ADD FOREIGN KEY (`tasker_id`) REFERENCES `taskers` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `reviews` ADD FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

ALTER TABLE `reviews` ADD FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `reviews` ADD FOREIGN KEY (`tasker_id`) REFERENCES `taskers` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `favorites` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `favorites` ADD FOREIGN KEY (`tasker_id`) REFERENCES `taskers` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `past_taskers` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `past_taskers` ADD FOREIGN KEY (`tasker_id`) REFERENCES `taskers` (`user_id`) ON DELETE CASCADE;
