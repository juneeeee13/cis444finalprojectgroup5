<?php

//Starts a new session or continues an existing one.
session_start(); //Put this at the top of the file.

//step 1: Load the environment variables
$dotenv = parse_ini_file('../../.env'); //This takes the credentials from the .env file

$servername = $dotenv['DB_SERVERNAME']; //Gets the servername for the database from the .env file
$username = $dotenv['DB_USERNAME']; //Gets the MySQL username for the database from the .env file
$password = $dotenv['DB_PASSWORD']; //Gets the MySQL password for the database from the .env file
$database = $dotenv['DB_DATABASE']; //Gets the database we are using from the .env file


//step 2: Connect to the DataBase using the credentials we loaded from the .env file
$DBConnect = new mysqli($servername, $username, $password, $database); 
if($DBConnect->connect_error) {
    die("Connection failed: " . $DBConnect->connect_error);
}


//Step 3: Use the login form data we got from the user.
$user_username = $_POST['username'];
$user_password = $_POST['password'];



//Step 4: Prepare the query
$TableName = "users";

//This is the query we'll use to see if the username exists in the users table.
//The question mark is a placeholder to prevent SQL injection.
//Separates SQL logic from user input.
$SQLstring = "SELECT * FROM $TableName WHERE username = ?";

$stmt = $DBConnect->prepare($SQLstring); //This prepares a statement for executing SQL query.
$stmt->bind_param("s", $user_username); //This replaces the question mark with the variable user_name of type string.
$stmt->execute(); //This performs the database query.
$result = $stmt->get_result(); //This returns the results of the query.

//We only want 1 user with that username.
//If less, no username exists. If more, duplicate usernames which is also bad.
if($result->num_rows === 1) {
    $user = $result->fetch_assoc();//If unique user exists, stores row data into $user.
} else {
    die("Invalid username or password.");//Ends the script with an error message for invalid credentials.
}


// Step 5: Verify password
//password_verify hashes the plaintext password and checks it against the already hashed password from the database.
if(password_verify($user_password, $user['password'])) {
    //If passwords match

    $_SESSION['user_id'] = $user['user_id']; //user id attached to session
    $_SESSION['username'] = $user['username']; //username attached to session
    $_SESSION['isAdmin'] = $user['isAdmin']; //In case we need to load an admin view

    // This will redirect a user to the home screen upon a successful login.
    header("Location: ../home/home.php");
    $stmt->close(); //Close the prepaired statement.
    $DBConnect->close(); //Close the database connection.
    exit();// Exit stops further php code from being executed. Provides a "clean exit".
} else {
    die("Invalid username or password."); //Ends the script with an error message.
}

// Clean up
$stmt->close();
$DBConnect->close();
?>