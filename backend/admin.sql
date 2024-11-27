SELECT user_id from users WHERE isAdmin = 1;

SELECT * FROM reports;

UPDATE users SET blacklisted = 1 WHERE user_id = ?;
