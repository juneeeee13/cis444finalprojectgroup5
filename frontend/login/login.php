<?php

//Starts a new session or continues an existing one.
session_start(); //Put this at the top of the file.

//If a user is already logged in and tries to go to the login page, take them to settings where the logout button is.
//Hmmm...if they go to login.html, it is not the same as login.php though and this code will likely not execute.
if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
    header("Location: ../settings/settings.html");//code likely not executing because people going to login.html instead of login.php
}



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
    die("Invalid username or password.<br>Click the back button and try again.");//Ends the script with an error message for invalid credentials.
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
    die("Invalid username or password.<br>Click the back button and try again."); //Ends the script with an error message.
}

// Clean up
$stmt->close();
$DBConnect->close();
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <title>Login</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="login.css">
        <script src="login.js" defer></script>
    </head>
    <body>
        <header>
            <h1>
                <img class="icon" src="../../esdeeimgs/esdeebrowsericon.png">
                    eSDee     
                <img class="icon" src="../../esdeeimgs/pinkshell.png">
                <br>
                <img class="headerimage" src="../../esdeeimgs/waves.png">
            </h1>
        </header>
        <div id="login-card">
            <h2>Login</h2>
            <form id="loginForm" action="login.php" method="POST">
                <div id="username-container" class="input-container">
                    <label for="username">Username</label>
                    <input id="username" name="username" class="input-field" type="text" maxlength="20">
                </div>
                <div id="password-container" class="input-container">
                    <label for="password">Password</label>
                    <input id="password" name="password" class="input-field" type="password" minLength="8" maxLength="128" autocomplete="off">
                </div>
                <div id="button-container">
                    <div id="submit-container">
                        <input type="submit" id="loginSubmit" value="login">
                        <button type="button" id="register-button" onclick="location.href='../register/register.php'">register</button>
                    </div>
                    <div id="forgot-container">
                        <!-- Forgot username -->
                        <a href="../forgotUsername/forgotUsername.php" class="forgot-text"><span>Forgot username?</span></a>
                    
                        <!-- Forgot password -->
                        <a href="../forgotPassword/forgotPassword.php" class="forgot-text"><span>Forgot password?</span></a>
                    </div>
                </div>
                

            </form>
        </div>
        <footer>
            <img class="footerimage" src="../../esdeeimgs/esdeefooter.png">
        </footer>
    </body>
</html>