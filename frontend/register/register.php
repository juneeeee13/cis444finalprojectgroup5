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

//Step 3: Hash the Password + Salt.
//Uses bcrypt algorithm for encryption.
$hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

//Step 4: Insert Data into the Database
$SQLstring = "INSERT INTO users (username, password, age, email) VALUES (?, ?, ?, ?)"; // The question mark is a placeholder to prevent SQL injection.
$stmt = $DBConnect->prepare($SQLstring); //This prepares a statement for executing SQL query.

//This replaces the question marks with the variables and their types.
//"ssis" is string, string, integer, string
$stmt->bind_param("ssis", $user_username, $hashed_password, $user_age, $user_email); 

//This determines whether or not the query executed successfully.
//The statement is ran first, then returns true or false.
if ($stmt->execute()) {
    echo "Registration successful!";
    echo "\nReturning you to the login page...";
    header("Location: ../login/login.html");
} else {
    echo "Error: " . $stmt->error;
}

//Close the connection
//Frees up resources for PHP and database server
$stmt->close();
$DBConnect->close();

?>