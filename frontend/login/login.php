<?php
//step 1: Connect to the Database

//Load the environment variables
$dotenv = parse_ini_file('../../.env');//this takes the credentials from the .env file

$servername = $dotenv['DB_SERVERNAME'];
$username = $dotenv['DB_USERNAME'];
$password = $dotenv['DB_PASSWORD'];
$database = $dotenv['DB_DATABASE'];

$DBConnect = new mysqli($servername, $username, $password, $database);
if($DBConnect->connect_error) {
    die("Connection failed: " . $DBConnect->connect_error);
}

//Step 2: Get the Form Data
$user_username = $_POST['username'];
$user_password = $_POST['password'];

$TableName = "users";
$SQLstring = "SELECT * FROM $TableName WHERE username = ?";
$stmt = $DBConnect->prepare($SQLstring);
$stmt->bind_param("s", $user_username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    die("Invalid username or password.");
}

//password verify hashes the plaintext password and checks it against the already hashed password from the database.
if(password_verify($user_password, $user['password'])) {
    //password matches

    session_start();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['isAdmin'] = $user['isAdmin']; //Role-based access

    header("Location: ../home/home.php");
    exit();
} else {
    die("Invalid username or password.");
}









/*
//Step 3: Hash the Password
$hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

try{

} catch(Exception $e) {
    $connect_error = $e->getMessage();
    $connect_errno = $e->getCode();
}
if($connect_errno) echo "<p>The database server is not available</p>";
try{
    $DBConnect->select_db($database);
}catch(Exception $e){
    $error = $e->getMessage();
    $errno = $e->getCode();
}
if($errno) echo "<p>The database is not available. Error Code: ".$errno." Error:" .$error. "</p>";
$TableName = "users";
$SQLstring = "SELECT * FROM $TableName WHERE username = ? AND password = ?";
$QueryResult = $DBConnect->query($SQLstring);
*/


die("Invalid credentials.");
?>