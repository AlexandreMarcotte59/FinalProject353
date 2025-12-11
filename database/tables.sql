

CREATE TABLE Members (
    user_id INT PRIMARY KEY,
    recovery_email VARCHAR(255),
    name_or_username VARCHAR(100),
    organization VARCHAR(255),
    address VARCHAR(255),
    verification_token VARCHAR(255),
    download_limit INT DEFAULT 0,
    referral_code VARCHAR(50),
    is_admin BOOLEAN DEFAULT FALSE
);


CREATE TABLE Texts (
    text_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    popularity INT DEFAULT 0,
    downloads INT DEFAULT 0,
    date_published DATE,
    uploaded_at DATETIME,    
    uploader INT,
    is_member_author BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (uploader) REFERENCES Members(user_id)
);


CREATE TABLE Readers (
    user_id INT,
    text_id INT,
    PRIMARY KEY (user_id, text_id),
    FOREIGN KEY (user_id) REFERENCES Members(user_id),
    FOREIGN KEY (text_id) REFERENCES Texts(text_id)
);

CREATE TABLE Downloads (
    download_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    text_id INT NOT NULL,
    download_date DATE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Members(user_id),
    FOREIGN KEY (text_id) REFERENCES Texts(text_id)
);

CREATE TABLE TextComments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    reader_id INT,
    text_id INT,
    comment TEXT,
    date_published DATE,
    FOREIGN KEY (reader_id) REFERENCES Members(user_id),
    FOREIGN KEY (text_id) REFERENCES Texts(text_id)
);

CREATE TABLE Charities (
  char_id int AUTO_INCREMENT PRIMARY KEY,
  name varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  about varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  organization varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL 
);

CREATE TABLE SuggestedCharities (
    suggestion_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    organization varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES Members(user_id)
);


CREATE TABLE Donations (
  donation_id int AUTO_INCREMENT PRIMARY KEY,
  text_id int DEFAULT NULL,
  user_id int DEFAULT NULL,
  quantity decimal(10,2) DEFAULT NULL,
  destination int DEFAULT NULL,
  date datetime DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `Members` (`user_id`),
  FOREIGN KEY (`destination`) REFERENCES `Charities` (`char_id`),
  FOREIGN KEY (`text_id`) REFERENCES `Texts` (`text_id`)
)


CREATE TABLE Questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    question TEXT,
    FOREIGN KEY (user_id) REFERENCES Members(user_id)
);


CREATE TABLE Answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    question_id INT,
    answer TEXT,
    FOREIGN KEY (user_id) REFERENCES Members(user_id),
    FOREIGN KEY (question_id) REFERENCES Questions(question_id)
);


CREATE TABLE Committees (
  committee_id int AUTO_INCREMENT PRIMARY KEY,
  subject varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  purpose text COLLATE utf8mb3_unicode_ci NOT NULL,
  created_by int NOT NULL
);

CREATE TABLE CommitteeVolunteers (
    committee_id INT,
    volunteer_id INT,
    PRIMARY KEY (committee_id, volunteer_id),
    FOREIGN KEY (committee_id) REFERENCES Committees(committee_id),
    FOREIGN KEY (volunteer_id) REFERENCES Members(user_id)
);


CREATE TABLE CommitteeJoinRequests (
    request_id INT,
    committee_id INT,
    user_id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    request_date DATE DEFAULT (CURRENT_DATE()),
    PRIMARY KEY (request_id),
    FOREIGN KEY (committee_id) REFERENCES Committees(committee_id),
    FOREIGN KEY (user_id) REFERENCES Members(user_id)
);

CREATE TABLE MemberAuthor (
    user_id INT,
    text_id INT,
    PRIMARY KEY (user_id, text_id),
    FOREIGN KEY (user_id) REFERENCES Members(user_id),
    FOREIGN KEY (text_id) REFERENCES Texts(text_id)
);


CREATE TABLE Vote (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    plagiarized_item INT,
    result VARCHAR(50),
    FOREIGN KEY (plagiarized_item) REFERENCES Texts(text_id)
);


CREATE TABLE InboxMessages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    subject VARCHAR(255),
    body VARCHAR(2048),
    sent_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    is_system_message BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (sender_id) REFERENCES Members(user_id),
    FOREIGN KEY (recipient_id) REFERENCES Members(user_id)
);


CREATE TABLE Statistics (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255),
    year INT,
    value INT
);

DELIMITER $$
CREATE TRIGGER add_approved_charity
AFTER UPDATE ON SuggestedCharities 
FOR EACH ROW 
BEGIN
IF NEW.status = 'approved' THEN
    INSERT INTO Charities VALUES (NEW.suggestion_id, NEW.name, NEW.description, NEW.organization);
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER remove_approved_charity
AFTER INSERT ON Charities 
FOR EACH ROW 
BEGIN
    DELETE FROM SuggestedCharities s WHERE s.suggestion_id = NEW.char_id AND s.status = 'approved';
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE vote_down(IN a_id INT)
BEGIN
    UPDATE Answers SET downvotes = downvotes - 1 WHERE answer_id = a_id;
    DELETE FROM Answers WHERE ABS(downvotes) > upvotes;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER set_is_member_author
BEFORE INSERT ON Texts 
FOR EACH ROW 
BEGIN
DECLARE uploader_name VARCHAR(255);
SELECT name_or_username INTO uploader_name 
FROM Members 
WHERE user_id = NEW.uploader;
IF uploader_name = NEW.author THEN 
    SET NEW.is_member_author = TRUE;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE delete_flagged_questions()
BEGIN
    DELETE a
    FROM Answers a
    JOIN Questions q ON a.question_id = q.question_id
    WHERE q.flagged = 1;

    DELETE FROM Questions WHERE flagged = 1;
END$$
DELIMITER ;
