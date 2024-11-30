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

//Step 3: Hash the Password
$hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

//Step 4: Insert Data into the Database
$sql = "INSERT INTO users (username, password, age, email) VALUES (?, ?, ?, ?)";
$stmt = $DBConnect->prepare($sql);
$stmt->bind_param("ssis", $user_username, $hashed_password, $user_age, $user_email);

if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    echo "Error: " . $stmt->error;
}

//Close the connection
$stmt->close();
$DBConnect->close();

?>