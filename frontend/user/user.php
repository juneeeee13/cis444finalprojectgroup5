
<?php
// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit(); 
}
// Connect to the database
$dotenv = parse_ini_file('../../.env');
$servername = $dotenv['DB_SERVERNAME'];
$username = $dotenv['DB_USERNAME'];
$password = $dotenv['DB_PASSWORD'];
$database = $dotenv['DB_DATABASE'];

$DBConnect = new mysqli($servername, $username, $password, $database);
if ($DBConnect->connect_error) {
    die("Connection failed: " . $DBConnect->connect_error);
}


// Get the user_id from the URL
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die("User ID not provided.");
}

$user_id = intval($_GET['user_id']); 
// Fetch user details
$stmt = $DBConnect->prepare("SELECT username, email, age FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>User</title>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="user.css">
    </head>
    <body>
        <script src="user.js"></script>
        <header>
            <h1><a href="../home/home.php">
                <img class="icon" src="../../esdeeimgs/esdeebrowsericon.png" alt="San Diego Logo">
                    eSDee     
                <img class="icon" src="../../esdeeimgs/pinkshell.png">
</a>
                <br>
                <img class="headerimage" src="../../esdeeimgs/waves.png" alt="Waves">
            </h1>
        </header>
        <section class="user-section">
            <div class="content-section">
                <p> Username: <span id="username-display"><?php echo htmlspecialchars($user['username']); ?></span> </p>
                <p> Email: <span id="email-display"><?php echo htmlspecialchars($user['email']); ?></span> </p>
                <p> Age: <span id="username-display"><?php echo htmlspecialchars($user['age']); ?></span> </p>
            </div>
            
        </section>
        <div class="button-section">
                <button onclick="goBack()">Go Back</button>
            </div>
        <footer>
            <img class="footerimage" src="../../esdeeimgs/esdeefooter.png">
        </footer>
    </body>
</html>

<?php
$DBConnect->close(); 
?>
