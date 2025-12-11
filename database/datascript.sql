-- Dummy data
INSERT INTO `Member` (`username`, `email`, `password`) VALUES
('user1', 'user1@example.com', 'password1'),
('user2', 'user2@example.com', 'password2'),
('user3', 'user3@example.com', 'password3'),
('user4', 'user4@example.com', 'password4'),
('user5', 'user5@example.com', 'password5');



INSERT INTO `TextComments` (`user_id`, `title`, `content`, `created_at`) VALUES
(1, 'Post 1 by User 1', 'This is dummy content for post 1 by user 1.', NOW()),
(1, 'Post 2 by User 1', 'Dummy content for post 2 by user 1.', NOW()),
(2, 'Post 1 by User 2', 'Dummy content for post 1 by user 2.', NOW()),
(3, 'Post 1 by User 3', 'Dummy content for post 1 by user 3.', NOW()),
(4, 'Post 1 by User 4', 'Dummy content for post 1 by user 4.', NOW()),
(5, 'Post 1 by User 5', 'Dummy content for post 1 by user 5.', NOW());