<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: ../login/login.html");
    exit();
}

// Database connection
$dotenv = parse_ini_file('../../.env');
$servername = $dotenv['DB_SERVERNAME'];
$username = $dotenv['DB_USERNAME'];
$password = $dotenv['DB_PASSWORD'];
$database = $dotenv['DB_DATABASE'];

$DBConnect = new mysqli($servername, $username, $password, $database);
if ($DBConnect->connect_error) {
    die("Connection failed: " . $DBConnect->connect_error);
}

// Handle remove from blacklist action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_blacklist_user_id'])) {
    $user_id = intval($_POST['remove_blacklist_user_id']);
    $SQLUpdate = "UPDATE users SET isBlacklisted = 0 WHERE user_id = ?";
    $stmt = $DBConnect->prepare($SQLUpdate);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('User removed from blacklist.');</script>";
    } else {
        echo "<script>alert('Error removing user from blacklist.');</script>";
    }
    $stmt->close();
}

// Fetch all blacklisted users
$SQLBlacklist = "SELECT user_id, username, email FROM users WHERE isBlacklisted = 1";
$blacklistedUsers = $DBConnect->query($SQLBlacklist);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklist</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="dashboard">
        <h1>Blacklist</h1>

        <!-- Search Bar -->
        <input type="text" id="blacklistSearchBar" placeholder="Search by User ID" onkeyup="filterBlacklist()" />

        <!-- Blacklist Details -->
        <div id="blacklistDetails">
            <?php if ($blacklistedUsers->num_rows > 0): ?>
                <table id="blacklistTable">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $blacklistedUsers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="remove_blacklist_user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit">Remove from Blacklist</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No blacklisted users found.</p>
            <?php endif; ?>
        </div>

        <button onclick="navigateToDashboard()">Back to Dashboard</button>
    </div>

    <script>
        // Navigate back to the dashboard
        function navigateToDashboard() {
            window.location.href = "../admin/admin.php";
        }

        // Filter blacklist by User ID
        function filterBlacklist() {
            const searchValue = document.getElementById('blacklistSearchBar').value.toLowerCase();
            const rows = document.querySelectorAll('#blacklistTable tbody tr');

            rows.forEach(row => {
                const userId = row.cells[0].innerText.toLowerCase();
                if (userId.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>
<?php $DBConnect->close(); ?>
