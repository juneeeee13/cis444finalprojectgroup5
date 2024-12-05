<?php
// Starts or continues a session.
session_start(); //Keep this at the top of the file.

// Check if the user is logged in by verifying that required session variables are set.
// If these session variables are not set, it indicates that the user has not logged in,
// or their session has expired. In this case, access to this page is denied.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../login/login.html"); // redirects a user who is not logged in that tries to access home.php, to the login page
    die("Access denied. Please log in first."); //if a user somehow bypasses the header redirect, they will only see this message.
}

// Retrieve session variables to display or use in the page. 
// These variables were set during login (login.php) and are used here for personalization or role-based access control.
$user_id = $_SESSION['user_id']; //Grab the user_id from the saved session state.
$username = $_SESSION['username']; //Grab the username from the saved session state.
$isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : 0; //If isAdmin is not set, defaults to 0. Allows admin-specific functionality or views.

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

// Fetch the latest 3 posts regardless of category
$latestPosts = $DBConnect->query("
    SELECT posts.*, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.user_id 
    ORDER BY posts.created_at DESC 
    LIMIT 3
");

// Fetch the latest post from each category
$categories = ['food', 'events', 'culture', 'place'];
$latestByCategory = []; // Store the latest post for each category.

foreach ($categories as $category) {
    $stmt = $DBConnect->prepare("
        SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.user_id 
        WHERE posts.category = ? 
        ORDER BY posts.created_at DESC 
        LIMIT 1
    ");
    $stmt->bind_param("s", $category); // Bind the category dynamically.
    $stmt->execute();
    $result = $stmt->get_result();
    $latestByCategory[$category] = $result->fetch_assoc(); // Save the latest post for the category.
    $stmt->close();
}
$DBConnect->close(); // Close the database connection.
?>

<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <link rel="icon" href="../../esdeeimgs/2.png">
    <link rel="stylesheet" type="text/css" href="home.css">
    <link rel="stylesheet" type="text/css" href="../logout/logout.css">
    <script src="home.js"></script>
</head>

<body>

    <h1>
        <img class="icon" src="../../esdeeimgs/esdeebrowsericon.png">
        eSDee
        <img class="icon" src="../../esdeeimgs/pinkshell.png">
        <br>
        <img class="headerimage" src="../../esdeeimgs/waves.png">
    </h1>
    <div class="topnav">
        <a href="../home/home.php">home</a>
        <a href="../about/about.html">about us</a>
        <a href="../food/food.php">food</a>
        <a href="../events/events.php">events</a>
        <a href="../culture/culture.php">culture</a>
        <a href="../place/place.php">places</a>
        <a href="../settings/settings.php">settings</a>
    </div>

    <div class="container">
        <!-- Left Column -->
        <div class="column left">
            <div class="card left">
                <h2>Latest from Food</h2>
                <?php if ($latestByCategory['food']): ?>
                    <p><strong><?php echo htmlspecialchars($latestByCategory['food']['title']); ?></strong></p>
                    <p><?php echo htmlspecialchars($latestByCategory['food']['content']); ?></p>
                    <p><strong>By:</strong> <?php echo htmlspecialchars($latestByCategory['food']['username']); ?></p>
                    <p><strong>Created at:</strong> <?php echo htmlspecialchars($latestByCategory['food']['created_at']); ?></p>
                    <p class="hashtag"> <?php echo htmlspecialchars($latestByCategory['food']['hashtags']); ?></p>
                    <p><strong>Likes:</strong> <?php echo htmlspecialchars($latestByCategory['food']['like_no']); ?></p>
                    <?php if (!empty($latestByCategory['food']['post_image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($latestByCategory['food']['post_image']); ?>" alt="Post Image" style="max-width: 150px; height: auto;">
                    <?php endif; ?>
                <?php else: ?>
                    <p>No posts available.</p>
                <?php endif; ?>
            </div>

            <div class="card left">
                <h2>Latest from Events</h2>
                <?php if ($latestByCategory['events']): ?>
                    <p><strong><?php echo htmlspecialchars($latestByCategory['events']['title']); ?></strong></p>
                    <p><?php echo htmlspecialchars($latestByCategory['events']['content']); ?></p>
                    <p><strong>By:</strong> <?php echo htmlspecialchars($latestByCategory['events']['username']); ?></p>
                    <p><strong>Created at:</strong> <?php echo htmlspecialchars($latestByCategory['events']['created_at']); ?></p>
                    <p class="hashtag"> <?php echo htmlspecialchars($latestByCategory['events']['hashtags']); ?></p>
                    <p><strong>Likes:</strong> <?php echo htmlspecialchars($latestByCategory['events']['like_no']); ?></p>
                    <?php if (!empty($latestByCategory['events']['post_image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($latestByCategory['events']['post_image']); ?>" alt="Post Image" style="max-width: 150px; height: auto;">
                    <?php endif; ?>
                <?php else: ?>
                    <p>No posts available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Middle Column -->
        <div class="column middle">
            <h1>New posts for you...</h1>
            <?php while ($post = $latestPosts->fetch_assoc()): ?>
                <div class="card middle">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                    <p><strong>By:</strong> <?php echo htmlspecialchars($post['username']); ?></p>
                    <p><strong>Created at:</strong> <?php echo htmlspecialchars($post['created_at']); ?></p>
                    <p class="hashtag"><?php echo htmlspecialchars($post['hashtags']); ?></p>
                    <p><strong>Likes:</strong> <?php echo htmlspecialchars($post['like_no']); ?></p>
                    <?php if (!empty($post['post_image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($post['post_image']); ?>" alt="Post Image" style="max-width: 150px; height: auto;">
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Right Column -->
        <div class="column right">
            <div class="card right">
                <h2>Latest from Culture</h2>
                <?php if ($latestByCategory['culture']): ?>
                    <p><strong><?php echo htmlspecialchars($latestByCategory['culture']['title']); ?></strong></p>
                    <p><?php echo htmlspecialchars($latestByCategory['culture']['content']); ?></p>
                    <p><strong>By:</strong> <?php echo htmlspecialchars($latestByCategory['culture']['username']); ?></p>
                    <p><strong>Created at:</strong> <?php echo htmlspecialchars($latestByCategory['culture']['created_at']); ?></p>
                    <p class="hashtag"> <?php echo htmlspecialchars($latestByCategory['culture']['hashtags']); ?></p>
                    <p><strong>Likes:</strong> <?php echo htmlspecialchars($latestByCategory['culture']['like_no']); ?></p>
                    <?php if (!empty($latestByCategory['culture']['post_image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($latestByCategory['culture']['post_image']); ?>" alt="Post Image" style="max-width: 150px; height: auto;">
                    <?php endif; ?>
                <?php else: ?>
                    <p>No posts available.</p>
                <?php endif; ?>
            </div>

            <div class="card right">
                <h2>Latest from Place</h2>
                <?php if ($latestByCategory['place']): ?>
                    <p><strong><?php echo htmlspecialchars($latestByCategory['place']['title']); ?></strong></p>
                    <p><?php echo htmlspecialchars($latestByCategory['place']['content']); ?></p>
                    <p><strong>By:</strong> <?php echo htmlspecialchars($latestByCategory['place']['username']); ?></p>
                    <p><strong>Created at:</strong> <?php echo htmlspecialchars($latestByCategory['place']['created_at']); ?></p>
                    <p class="hashtag"> <?php echo htmlspecialchars($latestByCategory['place']['hashtags']); ?></p>
                    <p><strong>Likes:</strong> <?php echo htmlspecialchars($latestByCategory['place']['like_no']); ?></p>
                    <?php if (!empty($latestByCategory['place']['post_image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($latestByCategory['place']['post_image']); ?>" alt="Post Image" style="max-width: 150px; height: auto;">
                    <?php endif; ?>
                <?php else: ?>
                    <p>No posts available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <br>
</body>

<footer>
    <img class="footerimage" src="../../esdeeimgs/esdeefooter.png">
</footer>

</html>
