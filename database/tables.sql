

CREATE TABLE Members (
    user_id INT PRIMARY KEY,
    recovery_email VARCHAR(255),
    name_or_username VARCHAR(100),
    organization VARCHAR(255),
    address VARCHAR(255),
    verification_token VARCHAR(255),
    download_limit INT DEFAULT 0,
    referral_code VARCHAR(50),
    is_admin BOOLEAN DEFAULT FALSE,
);


CREATE TABLE Texts (
    text_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    popularity INT DEFAULT 0,
    date_published DATE,
    member_author INT,
    FOREIGN KEY (member_author) REFERENCES Members(user_id)
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
    download_date DATE DEFAULT CURRENT_DATE,
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
)

CREATE TABLE CommitteeVolunteers (
    committee_id INT,
    volunteer_id INT,
    PRIMARY KEY (committee_id, volunteer_id),
    FOREIGN KEY (committee_id) REFERENCES Committee(committee_id),
    FOREIGN KEY (volunteer_id) REFERENCES Members(user_id)
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

CREATE TABLE Charities (
  char_id int AUTO_INCREMENT PRIMARY KEY,
  name varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  about varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  organization varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL 
)

CREATE TABLE SuggestedCharities (
    suggestion_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES Members(user_id)
);




