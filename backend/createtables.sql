CREATE TABLE users (
	user_id INT Primary Key NOT NULL,
	username VARCHAR(20) NOT NULL,
	password VARCHAR(15) NOT NULL,
	age INT NOT NULL,
	email VARCHAR(254) NOT NULL,
	isAdmin BOOL,
	isBlacklisted BOOL,
	profileImg VARCHAR
);

CREATE TABLE posts (
	post_id INT Primary Key NOT NULL,
	title VARCHAR(20) NOT NULL,
	content VARCHAR(300) NOT NULL,
	images VARCHAR,
	created_at TIMESTAMP,
	like_no INT,
	hashtags VARCHAR,
	category VARCHAR
);

CREATE TABLE reports (
	report_id INT Primary Key NOT NULL,
	reporter_id INT Foreign Key NOT NULL,
	user_reported INT Foreign Key NOT NULL,
	post_id INT Foreign Key NOT NULL
);

CREATE TABLE replies (
	reply_id INT Primary Key NOT NULL,
	post_id INT Foreign Key,
	content VARCHAR(300) NOT NULL,
	image VARCHAR NOT NULL,
	user_id INT Foreign Key
);