CREATE TABLE users (
    user_id INT PRIMARY KEY NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(15) NOT NULL,
    age INT NOT NULL,
    email VARCHAR(254) NOT NULL,
    isAdmin BOOLEAN DEFAULT FALSE,
    isBlacklisted BOOLEAN DEFAULT FALSE,
    profileImg VARCHAR
);

CREATE TABLE posts (
    post_id INT PRIMARY KEY NOT NULL,
    title VARCHAR(20) NOT NULL,
    content VARCHAR(300) NOT NULL,
    images VARCHAR,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    like_no INT DEFAULT 0,
    hashtags VARCHAR,
    category VARCHAR
);

CREATE TABLE reports (
    report_id INT PRIMARY KEY NOT NULL,
    reporter_id INT NOT NULL,
    user_reported INT NOT NULL,
    post_id INT NOT NULL,
    FOREIGN KEY (reporter_id) REFERENCES users(user_id),
    FOREIGN KEY (user_reported) REFERENCES users(user_id),
    FOREIGN KEY (post_id) REFERENCES posts(post_id)
);

CREATE TABLE replies (
    reply_id INT PRIMARY KEY NOT NULL,
    post_id INT,
    content VARCHAR(300) NOT NULL,
    image VARCHAR,
    user_id INT,
    FOREIGN KEY (post_id) REFERENCES posts(post_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
