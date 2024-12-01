<?php

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

//Step 3: Use the forgotusername form data we got from the user.
$user_email = $_POST['email'];

//Step 4: Prepare the query
$TableName = "users";

//This is the query we'll use to see if the email is associated with any user in the users table.
//The question mark is a placeholder to prevent SQL injection.
//Separates SQL logic from user input.
$SQLstring = "SELECT * FROM $TableName WHERE email = ?";

$stmtCheckEmail = $DBConnect->prepare($SQLstring); //This prepares a statement for executing SQL query.
$stmtCheckEmail->bind_param("s", $user_email); //This replaces the question mark with the variable user_email of type string.
$stmtCheckEmail->execute(); //This performs the database query.
$result = $stmtCheckEmail->get_result(); //This returns the results of the query.

//We only want 1 user with that email.
//If less, no email exists. If more, duplicate emails which is also bad.
if($result->num_rows === 1) {
    $user = $result->fetch_assoc();//If unique user exists, stores row data into $user.
} else {
    die("Invalid email entered.<br>Click the back button and try again.");//Ends the script with an error message for invalid email.
}

// Step 5: Verify email
//If email entered matches a user's email,
//let user know an email was sent to the email address entered and to check their email for their username.
//Full email functionality will probably not be ready, but is a placeholder in the off chance we get the time to make this work.
if($user_email === $user['email']) {

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
        <title>Forgot Username</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="forgotUsername.css">
        <script src="forgotUsername.js" defer></script>
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
        <div id="forgot-card">
            <h2>Forgot Username?</h2>
            <form id="forgotUsernameForm">
                <div id="" class="input-container">
                    <label for="emailInput">enter email</label>
                    <input id="emailInput" class="input-field" type="email">
                </div>
                <div id="button-container">
                    <input type="submit" id="emailSubmit" value="Submit" maxlength="254">
                    <a href="../login/login.php" class="return-login" style="text-decoration: none;"><span>Return to login</span></a>
                </div>
            </form>
        </div>
        <footer>
            <img class="footerimage" src="../../esdeeimgs/esdeefooter.png">
        </footer>
    </body>
</html>