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
    header("Location: ../login/login.php");
} else {
    echo "Error: " . $stmtInsert->error;
}

//Close the connection
//Frees up resources for PHP and database server
$stmtInsert->close();
$DBConnect->close();

?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <title>Register</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="register.css">
        <script src="register.js" defer></script>
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
        <div id="register-card">
            <h2>Register</h2>
            <form id="registerForm" action="register.php" method="POST">
                <div id="username-container" class="input-container">
                    <label for="username">Username</label>
                    <input id="username" name="username" class="input-field" type="text" maxlength="20" pattern="[a-zA-Z0-9]{1,20}" required>
                </div>
                <div id="password-container" class="input-container">
                    <label for="password">Password</label>
                    <input id="password" name="password" class="input-field" type="password" autocomplete="off" minLength="8" maxlength="128" required>
                </div>
                <div id="password-2-container" class="input-container">
                    <label for="password2">Verify password</label>
                    <input id="password2" class="input-field" type="password" autocomplete="off" minLength="8" maxlength="128" required>
                </div>
                <div id="email-container" class="input-container">
                    <label for="email">Email</label>
                    <input id="email" name="email" class="input-field" type="email" maxlength="254" required>
                </div>
                <div id="email-verify-container"  class="input-container">
                    <label for="email2">Verify email</label>
                    <input id="email2" class="input-field" type="email" maxlength="254" required>
                </div>
                <div id="age-container" class="input-container">
                    <label for="age">Age</label>
                    <input id="age" name="age" class="input-field" type="number" maxlength="3" required>
                </div>
                <div id="button-container">
                    <input type="submit" id="registerSubmit" value="register">
                    <a href="../login/login.php" class="return-login"><span>Return to login</span></a>
                </div>
            </form>
        </div>
        <footer>
            <img class="footerimage" src="../../esdeeimgs/esdeefooter.png">
        </footer>
    </body>
</html>