<?php
//step 1: Connect to the Database
$dotenv = parse_ini_file('../../.env');//this takes the credentials from the .env file

$servername = $dotenv['DB_SERVERNAME'];
$username = $dotenv['DB_USERNAME'];
$password = $dotenv['DB_PASSWORD'];
$database = $dotenv['DB_DATABASE'];

$DBConnect = new mysqli($servername, $username, $password, $database);

//Check connection
if($DBConnect->connect_error) {
    die("Connection failed: " . $DBConnect->connect_error);
}


//Step 2: Get the Form Data
$user_username = $_POST['username'];
$user_password = $_POST['password'];
$user_age = $_POST['age'];
$user_email = $_POST['email'];


/*
STEP 3: CHECK IF THE USERNAME AND/OR EMAIL ALREADY EXISTS
DOING SO AVOIDS AUTO INCREMENTING WHEN PERFORMING INSERT STATEMENTS THAT FAIL
I.E. Incrementing user_id but failing because username already exists
Avoids creating a gap when a user_id is incremented but row is skipped(1,2,3, 5...etc)
*/
$SQLCheckUsername = "SELECT COUNT(*) FROM users WHERE username = ?";
$stmtCheckUsername = $DBConnect->prepare($SQLCheckUsername); //This prepares our statement for executing a SQL Query
$stmtCheckUsername->bind_param("s", $user_username); //This replaces the question mark with the variable $user_username of type string.
$stmtCheckUsername->execute(); //This performs the database query.
$stmtCheckUsername->bind_result($userCountUsername); //Binds results of the query to $userCountUsername.
$stmtCheckUsername->fetch(); //Retrieves row result of query after it's been executed.
$stmtCheckUsername->close(); //Close $stmtCheckUsername

//If a user with that name already exists...
if($userCountUsername > 0) {
    echo "Error: That username is already in use.<br>";
    echo "Click the back button and choose a different username.";
    exit();
}

//STEP 3.5:
//TL;DR Same thing as above, but with email instead of username.
$SQLCheckEmail = "SELECT COUNT(*) FROM users WHERE email = ?";
$stmtCheckEmail = $DBConnect->prepare($SQLCheckEmail); //This prepares our statement for executing a SQL Query
$stmtCheckEmail->bind_param("s", $user_email); //This replaces the question mark with the variable $user_email of type string.
$stmtCheckEmail->execute(); //This performs the database query.
$stmtCheckEmail->bind_result($userCountEmail); //Binds results of the query to $userCountEmail.
$stmtCheckEmail->fetch(); //Retrieves row result of query after it's been executed.
$stmtCheckEmail->close(); //Close $stmtCheckEmail

//If that email is already associated with an account...
if ($userCountEmail > 0) {
    echo "Error: That email is already in use by another account.<br>";
    echo "Click the back button and choose a different email.";
    exit();
}


//Step 4: Hash the Password + Salt.
//Uses bcrypt algorithm for encryption.
$hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

//Step 5: Insert Data into the Database
$SQLInsert = "INSERT INTO users (username, password, age, email) VALUES (?, ?, ?, ?)"; // The question mark is a placeholder to prevent SQL injection.
$stmtInsert = $DBConnect->prepare($SQLInsert); //This prepares a statement for executing SQL query.

//This replaces the question marks with the variables and their types.
//"ssis" is string, string, integer, string
$stmtInsert->bind_param("ssis", $user_username, $hashed_password, $user_age, $user_email); 

//This determines whether or not the query executed successfully.
//The statement is ran first, then returns true or false.
if ($stmtInsert->execute()) {
    echo "Registration successful!";
    echo "\nReturning you to the login page...";
    header("Location: ../login/login.html");
} else {
    echo "Error: " . $stmtInsert->error;
}

//Close the connection
//Frees up resources for PHP and database server
$stmtInsert->close();
$DBConnect->close();

?>