-- Reset tables to start fresh
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE reviews;
TRUNCATE TABLE bookings;
TRUNCATE TABLE tasks;
TRUNCATE TABLE favorites;
TRUNCATE TABLE past_taskers;
TRUNCATE TABLE taskers;
TRUNCATE TABLE categories;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- Seed categories
INSERT INTO categories (category_name)
VALUES
#     General Home Services
('Window Cleaning'),
('Roof Repair'),
('Pest Control'),
('Deep Cleaning'),
('Home Renovation'),
('Pool Maintenance'),
('Garage Cleaning'),
('Curtain Installation'),
('Pressure Washing'),
('Locksmith Services'),
# Technology Services
('IT Support'),
('Computer Repair'),
('Smart Home Installation'),
('Network Setup'),
('Software Installation'),
('Data Recovery'),
('Website Development'),
('App Development'),
('Graphic Design'),
('Digital Marketing'),
# Personal Care and Beauty
('Hair Styling'),
('Makeup Artist'),
('Personal Training'),
('Massage Therapy'),
('Nail Care'),
('Skincare Services'),
('Tattoo Artist'),
('Barber Services'),
('Personal Shopper'),
('Wardrobe Consultation'),
# Automotive Services
('Car Wash'),
('Oil Change'),
('Tire Replacement'),
('Brake Repair'),
('Car Detailing'),
('Battery Replacement'),
('Roadside Assistance'),
('Car Wrapping'),
('Paint Touch-Ups'),
('Engine Repair'),
# Childcare & Education
('Babysitting'),
('Tutoring'),
('Homework Help'),
('Music Lessons'),
('Language Classes'),
('Art Classes'),
('Swimming Lessons'),
('Child Safety Training'),
('Dance Classes'),
('Test Preparation'),
# Food Services
('Catering Services'),
('Meal Preparation'),
('Personal Chef'),
('Bakery Services'),
('Event Bartending'),
('Wine Tasting'),
('Grocery Shopping'),
('Special Diet Planning'),
('Snack Delivery'),
('Food Truck Service'),
# Event Planning & Entertainment
('Event Planning'),
('Wedding Planning'),
('DJ Services'),
('Photography'),
('Videography'),
('Balloon Decoration'),
('Floral Arrangement'),
('Party Rental Services'),
('Photo Booth Setup'),
('Karaoke Services'),
# Fitness & Health
('Yoga Instruction'),
('Pilates Classes'),
('Nutritionist'),
('Weight Loss Coaching'),
('Wellness Coaching'),
('Rehabilitation Services'),
('Chiropractor'),
('Martial Arts Classes'),
('Meditation Sessions'),
('Group Fitness Classes'),
# Pet Services
('Dog Walking'),
('Pet Grooming'),
('Pet Sitting'),
('Dog Training'),
('Veterinary Services'),
('Pet Photography'),
('Pet Boarding'),
('Pet Adoption'),
('Pet Taxi'),
('Pet Behavioral Therapy'),
# Miscellaneous
('Sewing & Alterations'),
('Laundry Services'),
('Furniture Restoration'),
('Interior Design'),
('Travel Planning'),
('Virtual Assistance'),
('Business Consulting'),
('Courier Services'),
('Translation Services'),
('Public Speaking Training');


-- Seed users
INSERT INTO users (name, email, password, phone, profile_picture, created_at)
VALUES ('John Smith', 'john.smith@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567890', 'profile1.jpg', NOW()),

       ('Sarah Johnson', 'sarah.j@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567891', 'profile2.jpg', NOW()),

       ('Michael Brown', 'michael.b@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567892', 'profile3.jpg', NOW()),

       ('Emma Wilson', 'emma.w@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567893', 'profile4.jpg', NOW()),

       ('David Lee', 'david.l@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567894', 'profile5.jpg', NOW()),

       ('Lisa Anderson', 'lisa.a@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567895', 'profile6.jpg', NOW()),

       ('James Wilson', 'james.w@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567896', 'profile7.jpg', NOW()),

       ('Maria Garcia', 'maria.g@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567897', 'profile8.jpg', NOW()),

       ('Hadi Hijazi', 'hadi@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567891', 'profile9.jpg', NOW()),

       ('Melanie Karam', 'michael.b@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567892', 'profile10.jpg', NOW()),

       ('Hanine Khalil', 'hanine@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567893', 'profile11.jpg', NOW()),

       ('Jana Kassab', 'jana@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567894', 'profile12.jpg', NOW()),

       ('Raghad Alloush', 'raghad@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567895', 'profile13.jpg', NOW()),

       ('Lionel Messi', 'messi@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567896', 'profile14.jpg', NOW()),

       ('Selena Gomez', 'selena.g@email.com', '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj2NXFSpVE.y',
        '+1234567897', 'profile15.jpg', NOW());

-- Seed taskers
INSERT INTO taskers (user_id, skill, availability_status, rating, description, hourly_rate)
VALUES (2, 'Professional House Cleaner', true, 0, 'Experienced in deep cleaning and organization', 35),
       (3, 'Certified Handyman', true, 0, 'Licensed handyman with 10 years of experience', 45),
       (5, 'Moving Specialist', true, 0, 'Strong and reliable moving professional', 40),
       (6, 'Expert Gardener', true, 0, 'Passionate about garden design and maintenance', 30),
       (8, 'Master Painter', true, 0, 'Detail-oriented painter with interior/exterior experience', 38),
       (9, 'Web Developer', true, 0, 'Experienced web developer with 5 years of experience', 50);

-- Seed tasks
INSERT INTO tasks (requester_id, tasker_id, category_id, task_description, status)
VALUES (1, 2, 1, 'Deep clean 2-bedroom apartment', 'completed'),
       (4, 3, 2, 'Assemble IKEA furniture set', 'in_progress'),
       (7, 5, 3, 'Help moving heavy furniture', 'pending'),
       (1, 6, 5, 'Garden maintenance and lawn mowing', 'completed'),
       (4, 8, 6, 'Paint living room and kitchen', 'pending'),
       (7, 9, 7, 'Build a website for my business', 'pending'),
       (1, 2, 8, 'Clean windows in the living room', 'completed'),
       (4, 3, 9, 'Install new curtains in the bedroom', 'completed'),
       (7, 5, 10, 'Change locks on front door', 'pending'),
       (1, 6, 11, 'Pressure wash driveway and patio', 'completed'),
       (4, 8, 12, 'Fix leaky roof in the garage', 'pending'),
       (7, 9, 13, 'Develop a mobile app for my startup', 'pending');

-- Seed bookings
INSERT INTO bookings (task_id, requester_id, tasker_id, booking_date, status)
VALUES (1, 1, 2, '2024-02-15 10:00:00', 'completed'),
       (2, 4, 3, '2024-02-20 14:00:00', 'confirmed'),
       (3, 7, 5, '2024-02-25 09:00:00', 'pending'),
       (4, 1, 6, '2024-02-18 11:00:00', 'completed'),
       (5, 4, 8, '2024-02-28 13:00:00', 'pending');

-- Seed reviews
INSERT INTO reviews (task_id, reviewer_id, tasker_id, rating, review_content)
VALUES (1, 1, 2, 5, 'Excellent cleaning service! The apartment looks spotless.'),
       (4, 1, 6, 4, 'Did a great job with the garden. Very professional.');

-- Seed favorites
INSERT INTO favorites (user_id, tasker_id)
VALUES (1, 2),
       (4, 3),
       (7, 5);

-- Seed past taskers
INSERT INTO past_taskers (user_id, tasker_id, completed_jobs)
VALUES (1, 2, 3),
       (4, 3, 2),
       (1, 6, 1);