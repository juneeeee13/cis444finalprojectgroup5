<?php
// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.html");
    exit(); 
}
//check if it's admin
if ($_SESSION['isAdmin'] != 1) {
    header("Location: ../settings/settings.php");
    exit(); 
}
//Log out
if (isset($_GET['logout'])) {
    
    session_unset(); 
    session_destroy(); 
    
    header("Location: ../login/login.html");
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

// initial page load
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_SESSION['user_id'];
    $SQLstring = "SELECT username, password, email, age FROM users WHERE user_id = ?";
    $stmt = $DBConnect->prepare($SQLstring);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        die("Error fetching user data.");
    }
}

// Handle updates to user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];
    $new_email = $_POST['email'];
    $new_age = $_POST['age'];

    // Validate password length
    if (strlen($new_password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update user data in the database
    $SQLUpdate = "UPDATE users SET username = ?, password = ?, email = ?, age = ? WHERE user_id = ?";
    $stmt = $DBConnect->prepare($SQLUpdate);
    $stmt->bind_param("sssii", $new_username, $hashed_password, $new_email, $new_age, $user_id);
    if ($stmt->execute()) {
        // Set success message and updated data
        $_SESSION['success'] = "User data updated successfully.";
        $_SESSION['updated_user'] = [
            'username' => $new_username,
            'email' => $new_email,
            'age' => $new_age
        ];
    } else {
        // Set error message for failure
        $_SESSION['error'] = "Error updating user data.";
    }

    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin</title>
    <meta charset='UTF-8' name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" type="image/png" src="../../esdeeimgs/browsericon.png">
</head>
<body>
    <script src="admin.js"></script>
    <header>
        <h1>
            <img class="icon" src="../../esdeeimgs/esdeebrowsericon.png" alt="San Diego Logo">
                eSDee     
            <img class="icon" src="../../esdeeimgs/pinkshell.png">
            <br>
            <img class="headerimage" src="../../esdeeimgs/waves.png" alt="Waves">
        </h1>
    </header>
    <section class="settings-section">
        <nav>
            <ul>
                <li><a href="../admin/admin.php">My profile</a></li>
                <li><a href="../home/home.php">Home</a></li>
                <li><a href="see_reports.php">View reports</a></li>
                <li><a href="blacklists.php">View blacklist</a></li>
                <li><a href="?logout=true">Log out</a></li>
            </ul>
        </nav>
        <div class="content-section">

        <form method="POST" onsubmit="return validateForm();">
                <p>Username: <input type="text" id="username-input" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></p>
                <p>Password: <input type="password" id="password-input" name="password" required></p>
                <p>Email: <input type="email" id="email-input" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></p>
                <p>Age: <input type="number" id="age-input" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required></p>
                <button type="submit">Update Settings</button>
            </form>
        </div>
    </section>
    <footer>
        <img class="footerimage" src="../../esdeeimgs/esdeefooter.png">
    </footer>
    
</body>
</html>

