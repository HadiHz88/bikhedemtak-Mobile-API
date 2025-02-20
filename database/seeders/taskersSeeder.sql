USE bikhedemtak_mb;

-- Taskers for Web Development
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(1, 'Web Development', 50.00, 4.5, 'Experienced full-stack developer specializing in React and Node.js.', TRUE),
(2, 'Web Development', 45.00, 4.2, 'Front-end developer with expertise in HTML, CSS, and JavaScript.', TRUE),
(3, 'Web Development', 60.00, 4.8, 'Back-end developer with strong skills in Python and Django.', FALSE);

-- Taskers for Mobile Development
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(4, 'Mobile Development', 55.00, 4.6, 'Android developer with experience in Kotlin and Java.', TRUE),
(5, 'Mobile Development', 65.00, 4.7, 'iOS developer specializing in Swift and Objective-C.', TRUE),
(6, 'Mobile Development', 50.00, 4.3, 'Cross-platform mobile developer using Flutter.', FALSE);

-- Taskers for Plumbing
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(7, 'Plumbing', 40.00, 4.4, 'Licensed plumber with 10 years of experience.', TRUE),
(8, 'Plumbing', 35.00, 4.1, 'Expert in fixing leaks and installing pipes.', TRUE),
(9, 'Plumbing', 45.00, 4.6, 'Specializes in bathroom and kitchen plumbing.', FALSE);

-- Taskers for Electrical
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(10, 'Electrical', 50.00, 4.7, 'Certified electrician with expertise in wiring and installations.', TRUE),
(11, 'Electrical', 55.00, 4.8, 'Specializes in home electrical repairs and upgrades.', TRUE),
(12, 'Electrical', 60.00, 4.9, 'Industrial electrician with experience in large-scale projects.', FALSE);

-- Taskers for Carpentry
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(13, 'Carpentry', 40.00, 4.5, 'Skilled carpenter with expertise in furniture making.', TRUE),
(14, 'Carpentry', 45.00, 4.6, 'Specializes in home renovations and custom woodwork.', TRUE),
(15, 'Carpentry', 50.00, 4.7, 'Experienced in building decks and outdoor structures.', FALSE);

-- Taskers for Painting
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(16, 'Painting', 30.00, 4.3, 'Professional painter for interior and exterior walls.', TRUE),
(17, 'Painting', 35.00, 4.4, 'Specializes in decorative painting and murals.', TRUE),
(18, 'Painting', 40.00, 4.5, 'Experienced in commercial painting projects.', FALSE);

-- Taskers for Cleaning
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(19, 'Cleaning', 25.00, 4.2, 'Professional cleaner for homes and offices.', TRUE),
(20, 'Cleaning', 30.00, 4.3, 'Specializes in deep cleaning and sanitization.', TRUE),
(21, 'Cleaning', 35.00, 4.4, 'Experienced in post-construction cleaning.', FALSE);

-- Taskers for Gardening
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(22, 'Gardening', 30.00, 4.5, 'Expert in landscape design and maintenance.', TRUE),
(23, 'Gardening', 35.00, 4.6, 'Specializes in organic gardening and plant care.', TRUE),
(24, 'Gardening', 40.00, 4.7, 'Experienced in large-scale garden projects.', FALSE);

-- Taskers for Cooking
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(25, 'Cooking', 40.00, 4.6, 'Professional chef specializing in Italian cuisine.', TRUE),
(26, 'Cooking', 45.00, 4.7, 'Expert in baking and pastry arts.', TRUE),
(27, 'Cooking', 50.00, 4.8, 'Specializes in vegan and gluten-free cooking.', FALSE);

-- Taskers for Babysitting
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(28, 'Babysitting', 20.00, 4.4, 'Experienced babysitter with CPR certification.', TRUE),
(29, 'Babysitting', 25.00, 4.5, 'Specializes in caring for toddlers and infants.', TRUE),
(30, 'Babysitting', 30.00, 4.6, 'Provides educational activities for children.', FALSE);

-- Taskers for Pet Care
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(31, 'Pet Care', 25.00, 4.5, 'Professional pet sitter for dogs and cats.', TRUE),
(32, 'Pet Care', 30.00, 4.6, 'Specializes in grooming and training.', TRUE),
(33, 'Pet Care', 35.00, 4.7, 'Experienced in caring for exotic pets.', FALSE);

-- Taskers for Tutoring
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(34, 'Tutoring', 40.00, 4.7, 'Math tutor with expertise in algebra and calculus.', TRUE),
(35, 'Tutoring', 45.00, 4.8, 'English tutor specializing in essay writing.', TRUE),
(36, 'Tutoring', 50.00, 4.9, 'Science tutor with a focus on physics and chemistry.', FALSE);

-- Taskers for Fitness
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(37, 'Fitness', 50.00, 4.6, 'Certified personal trainer for weight loss and muscle building.', TRUE),
(38, 'Fitness', 55.00, 4.7, 'Yoga instructor with expertise in mindfulness and flexibility.', TRUE),
(39, 'Fitness', 60.00, 4.8, 'Specializes in high-intensity interval training (HIIT).', FALSE);

-- Taskers for Photography
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(40, 'Photography', 60.00, 4.7, 'Professional photographer specializing in portraits.', TRUE),
(41, 'Photography', 65.00, 4.8, 'Expert in wedding and event photography.', TRUE),
(42, 'Photography', 70.00, 4.9, 'Specializes in landscape and nature photography.', FALSE);

-- Taskers for Music
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(43, 'Music', 40.00, 4.6, 'Guitar instructor with 10 years of teaching experience.', TRUE),
(44, 'Music', 45.00, 4.7, 'Piano teacher specializing in classical music.', TRUE),
(45, 'Music', 50.00, 4.8, 'Vocal coach with expertise in pop and jazz.', FALSE);

-- Taskers for Dance
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(46, 'Dance', 50.00, 4.7, 'Professional dance instructor specializing in salsa.', TRUE),
(47, 'Dance', 55.00, 4.8, 'Expert in ballet and contemporary dance.', TRUE),
(48, 'Dance', 60.00, 4.9, 'Specializes in hip-hop and street dance.', FALSE);

-- Taskers for Event Planning
INSERT INTO taskers (user_id, skill, hourly_rate, rating, description, availability_status) VALUES
(49, 'Event Planning', 70.00, 4.8, 'Professional event planner for weddings and corporate events.', TRUE),
(50, 'Event Planning', 75.00, 4.9, 'Expert in organizing large-scale festivals.', TRUE),
(51, 'Event Planning', 80.00, 5.0, 'Specializes in themed parties and celebrations.', FALSE);