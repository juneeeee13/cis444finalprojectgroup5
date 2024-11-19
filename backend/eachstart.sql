-- page: login.html
SELECT * FROM users WHERE username = 'entered_username' AND password = 'entered_password';
-- How results will be displayed to those pages:
-- If a user table row exists, the user will be logged in using the tablerow data and redirected to the home.html page.


-- page: register.html
SELECT * FROM users WHERE username = 'entered_username' OR email = 'entered_email';
INSERT INTO users(username, password, age, email)
VALUES('entered_username', 'entered_password', 'entered_age', 'entered_email');
-- How results will be displayed to those pages:
-- If the username or email entered in the form submission already exists in the database, the webpage will remain on the register.html page and indicate to the user that the username or email are already in use. 
-- If no user row data exists, then the form submission data is inserted into the database and the user is notified of a successful account creation before being redirected to the login page. 


-- page: forgotPassword.html
SELECT password FROM users WHERE email = 'user@email.com';
-- How results will be displayed to those pages:
-- A modal or alert will pop up on the forgotPassword webpage indicating to the user to check the email they entered as the password will be sent to that email address. 
-- If there was no password associated with the email entered, we will send this pop up or modal message anyway for security purposes to prevent from leaking user account information.
-- We will improve the security and encryption of this process in a future sprint. 

  
-- page: forgotUsername.html
SELECT username FROM users WHERE email = 'user@email.com';
-- How results will be displayed to those pages:
-- A modal or alert will pop up on the forgotUsername webpage indicating to the user to check the email they entered as the username will be sent to that email address. 
-- If there was no email associated with the email entered, we will send this pop up or modal message anyway for security purposes to prevent from leaking user account information. 
