//------------------------
//-- 1. USERS
//------------------------ 
-- 
-- CREATE TABLE Users (
--     user_id INT AUTO_INCREMENT PRIMARY KEY,
--   username VARCHAR(100) NOT NULL,
--    password_hash VARCHAR(255) NOT NULL,
--   email VARCHAR(255) NOT NULL UNIQUE
--);

//-------------------------
 2. MEMBERS
//-------------------------
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
    --FOREIGN KEY (user_id) REFERENCES Users(user_id) -- user_id should not be a foreign key
);

//-------------------------
3. TEXTS
//-------------------------
CREATE TABLE Texts (
    text_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    popularity INT DEFAULT 0,
    date_published DATE,
    member_author INT,
    FOREIGN KEY (member_author) REFERENCES Members(user_id)
);

//-------------------------
 4. READERS
//-------------------------
CREATE TABLE Readers (
    user_id INT,
    text_id INT,
    PRIMARY KEY (user_id, text_id),
    FOREIGN KEY (user_id) REFERENCES Members(user_id),
    FOREIGN KEY (text_id) REFERENCES Texts(text_id)
);

//-------------------------
5. TEXTCOMMENTS
//-------------------------
CREATE TABLE TextComments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    reader_id INT,
    text_id INT,
    comment TEXT,
    date_published DATE,
    FOREIGN KEY (reader_id) REFERENCES Members(user_id),
    FOREIGN KEY (text_id) REFERENCES Texts(text_id)
);

//-------------------------
6. DONATIONS
//-------------------------
CREATE TABLE Donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    text_id INT,
    user_id INT,
    quantity DECIMAL(10,2),
    destination VARCHAR(255),
    date DATE,
    FOREIGN KEY (text_id) REFERENCES Texts(text_id),
    FOREIGN KEY (user_id) REFERENCES Members(user_id)
);

//-------------------------
7. QUESTIONS
//-------------------------
CREATE TABLE Questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    question TEXT,
    FOREIGN KEY (user_id) REFERENCES Members(user_id)
);

//-------------------------
 8. ANSWERS
//-------------------------
CREATE TABLE Answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    question_id INT,
    answer TEXT,
    FOREIGN KEY (user_id) REFERENCES Members(user_id),
    FOREIGN KEY (question_id) REFERENCES Questions(question_id)
);

//-------------------------
9. COMMITTEE
//-------------------------
CREATE TABLE Committee (
    committee_id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255)
);

///-------------------------
10. COMMITTEEVOLUNTEERS
//-------------------------
CREATE TABLE CommitteeVolunteers (
    committee_id INT,
    volunteer_id INT,
    PRIMARY KEY (committee_id, volunteer_id),
    FOREIGN KEY (committee_id) REFERENCES Committee(committee_id),
    FOREIGN KEY (volunteer_id) REFERENCES Members(user_id)
);

//-------------------------
11. MEMBERAUTHOR
//-------------------------
CREATE TABLE MemberAuthor (
    user_id INT,
    text_id INT,
    PRIMARY KEY (user_id, text_id),
    FOREIGN KEY (user_id) REFERENCES Members(user_id),
    FOREIGN KEY (text_id) REFERENCES Texts(text_id)
);

//-------------------------
12. VOTE
//-------------------------
CREATE TABLE Vote (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    plagiarized_item INT,
    result VARCHAR(50),
    FOREIGN KEY (plagiarized_item) REFERENCES Texts(text_id)
);

//-------------------------
13. INBOXMESSAGES
//-------------------------
CREATE TABLE InboxMessages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    subject VARCHAR(255),
    body VARCHAR(2048),
    sent_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    system_generated BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (sender_id) REFERENCES Members(user_id),
    FOREIGN KEY (recipient_id) REFERENCES Members(user_id)
);

//-------------------------
14. STATISTICS
//-------------------------
CREATE TABLE Statistics (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255),
    year INT,
    value INT
);

