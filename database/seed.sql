-- Insert fake users
INSERT INTO users (user_id, name, email, password, phone, profile_picture) VALUES
(1, 'John Doe', 'johndoe@example.com', 'password123', '1234567890', 'john.jpg'),
(2, 'Alice Smith', 'alice@example.com', 'password123', '0987654321', 'alice.jpg'),
(3, 'Bob Johnson', 'bob@example.com', 'password123', '1112223333', 'bob.jpg'),
(4,'Hanine Khalil', 'hanine.khalil@gmail.com','password123','70 123 456','hanine.jpg'),
(5,'Hadi Hijazi', 'hadi.hijazi@gmail.com','password123','12 222 333','hadi.jpg');

-- Insert fake taskers
INSERT INTO taskers (user_id, skill, availability_status, rating) VALUES
(1, 'Plumbing', true, 4.5),
(2, 'Electrician', true, 4.7),
(3, 'Carpentry', true, 4.3);

-- Insert categories
INSERT INTO categories (category_id, category_name) VALUES
(1, 'Home Repair'),
(2, 'Electrical Work'),
(3, 'Woodwork');

-- Insert tasks
INSERT INTO tasks (task_id, requester_id, tasker_id, category_id, task_description, status) VALUES
(1, 5, 2, 2, 'Fix the kitchen lights', 'pending'),
(2, 4, 3, 3, 'Build a wooden table', 'in_progress'),
(3, 3, 1, 1, 'Fix the bathroom sink', 'completed');

-- Insert bookings
INSERT INTO bookings (booking_id, task_id, requester_id, tasker_id, status) VALUES
(1, 1, 5, 2, 'confirmed'),
(2, 2, 4, 3, 'pending'),
(3, 3, 3, 1, 'completed');

-- Insert reviews
INSERT INTO reviews (review_id, task_id, reviewer_id, tasker_id, rating, review_content) VALUES
(1, 3, 3, 1, 5, 'Great job fixing the sink!'),
(2, 1, 5, 2, 4, 'Good work on the kitchen lights!'),
(3, 2, 4, 3, 3, 'Table was okay, but took too long.');

-- Insert favorites
INSERT INTO favorites (id, user_id, tasker_id) VALUES
(1, 1, 2),
(2, 4, 3),
(3, 3, 1);

-- Insert past taskers
INSERT INTO past_taskers (id, user_id, tasker_id, completed_jobs) VALUES
(1, 1, 2, 2),
(2, 2, 3, 3),
(3, 3, 1, 1);
