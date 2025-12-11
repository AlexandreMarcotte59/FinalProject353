-- Dummy data

INSERT INTO `Members` (`name_or_username`, `recover_email`, `verification_code`, `is_admin`) VALUES
('user1', 'user1@encs.com', 'passwordpasswordpass', 0),
('user2', 'user2@encs.com', 'passwordpasswordpass', 0),
('user3', 'user3@encs.com', 'passwordpasswordpass', 0),
('user4', 'user4@encs.com', 'passwordpasswordpass', 0),
('user5', 'user5@encs.com', 'passwordpasswordpass', 0),
('admin', 'admin1@encs.com', 'firstsound58', 1),
('admin2', 'admin2@encs.com', 'firstsound58', 1),
('admin3', 'admin3@encs.com', 'firstsound58', 1);

INSERT INTO `Texts` (`title`, `author`, `uploader`) VALUES
('Text1', 'user1', '1'),
('Text2', 'user2', '2'),
('Text3', 'user3', '3'),
('Text4', 'user4', '4'),
('Text5', 'user5', '5');

INSERT INTO `TextComments` (`user_id`, `title`, `content`, `created_at`) VALUES
(1, 'Post 1 by User 1', 'This is dummy content for post 1 by user 1.', NOW()),
(1, 'Post 2 by User 1', 'Dummy content for post 2 by user 1.', NOW()),
(2, 'Post 1 by User 2', 'Dummy content for post 1 by user 2.', NOW()),
(3, 'Post 1 by User 3', 'Dummy content for post 1 by user 3.', NOW()),
(4, 'Post 1 by User 4', 'Dummy content for post 1 by user 4.', NOW()),
(5, 'Post 1 by User 5', 'Dummy content for post 1 by user 5.', NOW());