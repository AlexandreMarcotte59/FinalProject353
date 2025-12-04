

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
    is_blacklisted BOOLEAN DEFAULT FALSE,
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
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    text_id INT NOT NULL,
    charity_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    charity_percent INT NOT NULL,
    author_percent INT NOT NULL,
    cfp_percent INT NOT NULL,
    donation_date DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (user_id) REFERENCES Members(user_id),
    FOREIGN KEY (text_id) REFERENCES Texts(text_id),
    FOREIGN KEY (charity_id) REFERENCES Charities(charity_id)
);


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


CREATE TABLE Committee (
    committee_id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255)
);


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
    plagiarized_item INT NOT NULL,
    result VARCHAR(50) NOT NULL,
    voter_id INT NOT NULL,
    FOREIGN KEY (plagiarized_item) REFERENCES Texts(text_id),
    FOREIGN KEY (voter_id) REFERENCES Members(user_id)
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
    charity_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('approved', 'pending') DEFAULT 'approved'
);

CREATE TABLE SuggestedCharities (
    suggestion_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES Members(user_id)
);




