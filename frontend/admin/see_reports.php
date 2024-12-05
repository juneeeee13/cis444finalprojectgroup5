
<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: ../login/login.php");
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

// Handle blacklist action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blacklist_user_id'])) {
    $user_reported = intval($_POST['blacklist_user_id']);
    $SQLUpdate = "UPDATE users SET isBlacklisted = 1 WHERE user_id = ?";
    $stmt = $DBConnect->prepare($SQLUpdate);
    $stmt->bind_param("i", $user_reported);
    if ($stmt->execute()) {
        echo "<script>alert('User has been blacklisted.');</script>";
    } else {
        echo "<script>alert('Error blacklisting user.');</script>";
    }
    $stmt->close();
}

// Fetch all reports and join with posts and users
$SQLReports = "
    SELECT 
        r.report_id, 
        r.reporter_id, 
        r.user_reported, 
        r.post_id, 
        p.content AS post_content, 
        u1.username AS reporter_username, 
        u2.username AS reported_username
    FROM 
        reports r
    JOIN 
        posts p ON r.post_id = p.post_id
    JOIN 
        users u1 ON r.reporter_id = u1.user_id
    JOIN 
        users u2 ON r.user_reported = u2.user_id
    ORDER BY 
        r.report_id DESC
";
$reports = $DBConnect->query($SQLReports);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See Reports</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="dashboard">
        <h1>Reported Posts</h1>
        <div id="reportDetails">
            <?php if ($reports->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Reporter</th>
                            <th>Reported User</th>
                            <th>Post Content</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($report = $reports->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                                <td><?php echo htmlspecialchars($report['reporter_username']); ?></td>
                                <td><?php echo htmlspecialchars($report['reported_username']); ?></td>
                                <td><?php echo htmlspecialchars($report['post_content']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="blacklist_user_id" value="<?php echo $report['user_reported']; ?>">
                                        <button type="submit">Blacklist User</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No reports found.</p>
            <?php endif; ?>
        </div>
        <button onclick="navigateToDashboard()">Back to Dashboard</button>
    </div>
    <script>
        function navigateToDashboard() {
            window.location.href = "../admin/admin.php";
        }
    </script>
</body>

</html>
<?php $DBConnect->close(); ?>
