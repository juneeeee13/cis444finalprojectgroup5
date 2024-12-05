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

// connect to the database
$conn = new mysqli("localhost", "takeh", "Hanateddy@87", "sample_db");

// Check if the connection to the database was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// $conn = new mysqli("127.0.0.1", "team_5", "h7pqwqs1", "team_5");

//step 2: Connect to the DataBase using the credentials we loaded from the .env file
// $DBConnect = new mysqli($servername, $username, $password, $database); 
// if($DBConnect->connect_error) {
//     die("Connection failed: " . $DBConnect->connect_error);
// }

// Check if the session for liked posts is initialized
if (!isset($_SESSION['liked_posts'])) {
    $_SESSION['liked_posts'] = [];
}

// Handle creating a new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post'])) {
    // Escape user inputs to prevent SQL injection
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $hashtags = $conn->real_escape_string($_POST['hashtags']);
    $category = 'events'; // Set dynamically based on the current page

    // Handle image data (if any)
    $imageData = null; // Initialize image data
    if (!empty($_FILES['images']['tmp_name'][0])) {
        $tmpName = $_FILES['images']['tmp_name'][0];
        $imageData = file_get_contents($tmpName); // Read image as binary
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, post_image, created_at, hashtags, category) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("isssss", $user_id, $title, $content, $imageData, $hashtags, $category);
    $stmt->execute();

    // Redirect to the homepage after submitting the post
    header("Location: " . $category . ".php");
    exit();
}


// Handle reply submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $post_id = intval($_POST['post_id']); // Get the post ID
    $content = trim($_POST['content']);

    // Validate that reply content is not empty
    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Reply content cannot be empty.']);
        exit;
    }

    // Handle image data for the reply
    $imageData = null; // Initialize image data
    if (!empty($_FILES['image']['tmp_name'])) {
        $tmpName = $_FILES['image']['tmp_name'];
        $imageData = file_get_contents($tmpName); // Read image as binary
    }

    // Insert reply data into the database
    $stmt = $conn->prepare("INSERT INTO replies (post_id, content, reply_image, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $post_id, $content, $imageData, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reply submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save reply.']);
    }
    $stmt->close();
    $conn->close();

    // Redirect to the homepage after submitting the post
    header("Location: events.php");
    exit();
}

// Handle liking a post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post'])) {
    $post_id = intval($_POST['post_id']);

    // Retrieve user ID from the session
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to like posts.']);
        exit();
    }
    $user_id = $_SESSION['user_id'];

    // Initialize the session variable to track likes
    if (!isset($_SESSION['liked_posts'])) {
        $_SESSION['liked_posts'] = []; // Initialize if not set
    }

    // Check if the user has already liked this post
    if (isset($_SESSION['liked_posts'][$user_id]) && in_array($post_id, $_SESSION['liked_posts'][$user_id])) {
        echo json_encode(['success' => false, 'message' => 'You have already liked this post.']);
        exit();
    }

    // Add the post to the user's liked list in the session
    if (!isset($_SESSION['liked_posts'][$user_id])) {
        $_SESSION['liked_posts'][$user_id] = [];
    }
    $_SESSION['liked_posts'][$user_id][] = $post_id;

    // Increment the like count in the database
    $stmt = $conn->prepare("UPDATE posts SET like_no = like_no + 1 WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Post liked successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to like the post.']);
    }
    $stmt->close();
    exit();
}

// Handle editing a post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {
    $post_id = intval($_POST['post_id']);
    $new_title = $conn->real_escape_string($_POST['title']);
    $new_content = $conn->real_escape_string($_POST['content']);

    // Update the post in the database
    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE post_id = ?");
    $stmt->bind_param("ssi", $new_title, $new_content, $post_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit();
}

// Handle deleting a post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = intval($_POST['post_id']);

    // Delete the post from the database
    $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit();
}

// Handle reporting a post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_post'])) {
    $post_id = intval($_POST['post_id']);

    // Retrieve reporter ID from the session
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "You must be logged in to report posts."]);
        exit();
    }
    $reporter_id = $_SESSION['user_id'];

    // Retrieve the user ID of the post owner
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->bind_result($user_reported);
    $stmt->fetch();
    $stmt->close();

    if (!$user_reported) {
        echo json_encode(["success" => false, "message" => "Post not found."]);
        exit();
    }

    // Insert the report into the database
    $stmt = $conn->prepare("INSERT INTO reports (reporter_id, user_reported, post_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $reporter_id, $user_reported, $post_id);

    if ($stmt->execute()) {
        // Log the report for admin page processing
        error_log("New report: Reporter ID $reporter_id reported User ID $user_reported for Post ID $post_id.");
        echo json_encode(["success" => true, "message" => "Report has been sent to the admin."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to send the report."]);
    }
    $stmt->close();
    exit();
}


// Handle requests related to replies and posts
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle editing a reply
    if (isset($_POST['edit_reply']) && isset($_POST['reply_id']) && isset($_POST['content'])) {
        $reply_id = intval($_POST['reply_id']);
        $content = $conn->real_escape_string($_POST['content']);

        // Update the reply content in the database
        $conn->query("UPDATE replies SET content = '$content' WHERE reply_id = $reply_id");
        echo json_encode(['success' => true, 'message' => 'Reply edited.']);
        exit;
    }

    // Handle deleting a reply
    if (isset($_POST['delete_reply']) && isset($_POST['reply_id'])) {
        $reply_id = intval($_POST['reply_id']);
        $conn->query("DELETE FROM replies WHERE reply_id = $reply_id");
        echo json_encode(['success' => true, 'message' => 'Reply deleted.']);
        exit;
    }

    // Handle reporting a reply
    // if (isset($_POST['report_reply'])) {
    //     $reply_id = intval($_POST['reply_id']);

    //     // Retrieve reporter ID from the session
    //     if (!isset($_SESSION['user_id'])) {
    //         echo json_encode(["success" => false, "message" => "You must be logged in to report replies."]);
    //         exit();
    //     }
    //     $reporter_id = $_SESSION['user_id'];

    //     // Retrieve the user ID of the reply owner
    //     $stmt = $conn->prepare("SELECT user_id FROM replies WHERE reply_id = ?");
    //     $stmt->bind_param("i", $reply_id);
    //     $stmt->execute();
    //     $stmt->bind_result($user_reported);
    //     $stmt->fetch();
    //     $stmt->close();

    //     if (!$user_reported) {
    //         echo json_encode(["success" => false, "message" => "Reply not found."]);
    //         exit();
    //     }

    //     // Insert the report into the database
    //     $stmt = $conn->prepare("INSERT INTO reports (reporter_id, user_reported, post_id) VALUES (?, ?, NULL)");
    //     $stmt->bind_param("ii", $reporter_id, $user_reported);

    //     if ($stmt->execute()) {
    //         // Log the report for admin page processing
    //         error_log("New reply report: Reporter ID $reporter_id reported User ID $user_reported for Reply ID $reply_id.");
    //         echo json_encode(["success" => true, "message" => "Reply report has been sent to the admin."]);
    //     } else {
    //         echo json_encode(["success" => false, "message" => "Failed to send the reply report."]);
    //     }
    //     $stmt->close();
    //     exit();
    // }
}


// Handle fetching the top posts
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_top_posts') {
    // Get the category dynamically based on the page
    $current_category = basename($_SERVER['PHP_SELF'], '.php'); // Extract category from the file name

    // Fetch the top posts based on like count and category
    $stmt = $conn->prepare("SELECT title, hashtags, like_no FROM posts WHERE category = ? ORDER BY like_no DESC LIMIT 10");
    $stmt->bind_param("s", $current_category); // Bind the category dynamically

    $stmt->execute();
    $result = $stmt->get_result();

    $topPosts = [];
    while ($row = $result->fetch_assoc()) {
        $topPosts[] = [
            'title' => $row['title'],
            'hashtags' => $row['hashtags'],
            'like_no' => $row['like_no']
        ];
    }

    echo json_encode($topPosts);
    exit();
}

// Handle filtering posts
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'filter_posts') {
    $order = $_GET['order'] === 'new' ? 'DESC' : 'ASC';

    // Fetch posts sorted by creation date
    $category = basename($_SERVER['PHP_SELF'], '.php'); // Get category dynamically based on current page

    // Fetch posts sorted by creation date and filtered by category
    $stmt = $conn->prepare("
        SELECT posts.*, users.username
        FROM posts
        JOIN users ON posts.user_id = users.user_id
        WHERE posts.category = ?
        ORDER BY posts.created_at $order
    ");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    $filteredPosts = [];
    while ($row = $result->fetch_assoc()) {
        // Fetch replies for the current post
        $replyStmt = $conn->prepare("
            SELECT replies.*, users.username AS reply_username
            FROM replies
            JOIN users ON replies.user_id = users.user_id
            WHERE replies.post_id = ?
            ORDER BY replies.created_at $order
        ");
        $replyStmt->bind_param("i", $row['post_id']);
        $replyStmt->execute();
        $replyResult = $replyStmt->get_result();

        $replies = [];
        while ($reply = $replyResult->fetch_assoc()) {
            $replies[] = [
                'reply_id' => $reply['reply_id'],
                'user_id' => $reply['user_id'],
                'username' => $reply['reply_username'],
                'content' => $reply['content'],
                'created_at' => $reply['created_at'],
                'reply_image' => !empty($reply['reply_image']) ? base64_encode($reply['reply_image']) : null,
            ];
        }

        $filteredPosts[] = [
            'post_id' => $row['post_id'],
            'user_id' => $row['user_id'],
            'username' => $row['username'],
            'title' => $row['title'],
            'content' => $row['content'],
            'hashtags' => $row['hashtags'],
            'like_no' => $row['like_no'],
            'created_at' => $row['created_at'],
            'post_image' => !empty($row['post_image']) ? base64_encode($row['post_image']) : null,
            'replies' => $replies,
        ];
    }

    echo json_encode($filteredPosts);
    exit();
}

// Handle searching posts by hashtags
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search_posts') {
    $searchTerm = $conn->real_escape_string($_GET['query']);

    // Fetch posts matching the search term in hashtags
    $category = basename($_SERVER['PHP_SELF'], '.php'); // Get category dynamically based on current page

    // Fetch posts matching the search term in hashtags and filtered by category
    $stmt = $conn->prepare("
        SELECT posts.*, users.username
        FROM posts
        JOIN users ON posts.user_id = users.user_id
        WHERE posts.category = ? AND posts.hashtags LIKE CONCAT('%', ?, '%')
        ORDER BY posts.created_at DESC
    ");
    $stmt->bind_param("ss", $category, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $filteredPosts = [];
    while ($row = $result->fetch_assoc()) {
        $filteredPosts[] = [
            'post_id' => $row['post_id'],
            'user_id' => $row['user_id'],
            'username' => $row['username'],
            'title' => $row['title'],
            'content' => $row['content'],
            'hashtags' => $row['hashtags'],
            'like_no' => $row['like_no'],
            'created_at' => $row['created_at'],
            'post_image' => !empty($row['post_image']) ? base64_encode($row['post_image']) : null,
        ];
    }

    echo json_encode($filteredPosts);
    exit();
}

?>


<!--start html-->
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Event</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../esdeeimgs/2.png">
    <link rel="stylesheet" type="text/css" href="events.css">
    <script src="script.js" defar></script>
</head>

<body>
    <header id="header">
        <div class="topic">
            <h1><b>Event</b></h1>
            <img class="icon1" src="../../esdeeimgs/esdeebrowsericon.png" alt="img">
            <img class="icon2" src="../../esdeeimgs/pinkshell.png" alt="img">
            <img class="headerimage" src="../../esdeeimgs/waves.png" alt="img">

            <div id="description">
                <div class="dText">
                    <p>Stay connected to the pulse of San Diego with our Event page. From festivals and concerts to
                        local gatherings, this is your space to discover, discuss, and share upcoming events around the
                        city. Whether it's an annual tradition or a one-time pop-up, post and find the latest happenings
                        here to stay in the know and help others join in on the fun!</p>
                </div>
            </div>

        </div>

        <div id="navArea">
            <nav>
                <div class="menuInner">
                    <ul>
                        <li><a href="../home/home.php"><img src="../../esdeeimgs/2.png" alt="img"></a></li>
                        <li><a href="../home/home.php">Home</a></li>
                        <li><a href="../culture/culture.php">Culture</a></li>
                        <li><a href="../events/events.php">Event</a></li>
                        <li><a href="../food/food.php">Food</a></li>
                        <li><a href="../place/place.php">Place</a></li>
                        <li><a href="../about/about.html">About us</a></li>
                        <li><a href="../settings/settings.php">Setting</a></li>
                    </ul>
                </div>
            </nav>

            <div class="toggle_btn">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div id="mask"></div>
        </div>

        <!-- Search Bar -->
        <div id="searchContainer">
            <input type="text" id="searchBar" placeholder="Search posts..." />
            <button id="searchButton" type="button">Search</button>
        </div>

        <!-- Date Filter Dropdown -->
        <div id="dateFilterContainer">
            <section id="filterSection">
                <label for="dateFilter">Filter by Date:</label>
                <select id="dateFilter" name="filter_date" onchange="this.form.submit()">
                    <option value="new">Newest</option>
                    <option value="old">Oldest</option>
                </select>
            </section>
        </div>

    </header>

    <main>
        <!-- Post Button -->
        <button id="postButton">Post</button>

        <!-- Modal for New Post Form -->
        <div id="postModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close postClose">&times;</span>
                <section id="postSection">
                    <h2>Create a New Post</h2>
                    <form id="postForm" method="POST" enctype="multipart/form-data">

                        <label for="postTitle">Title:</label>
                        <textarea id="postTitle" name="title" rows="4" cols="50"
                            placeholder="Enter the title of post here..."></textarea><br>

                        <label for="postText">Text:</label>
                        <textarea id="postText" name="content" rows="4" cols="50" placeholder="Enter your post here..."></textarea><br>

                        <label for="postHashtags">Hashtags:</label>
                        <textarea id="postHashtags" name="hashtags" rows="1" cols="50" placeholder="#..."></textarea><br>

                        <label for="postImage">Images:</label>
                        <input type="file" id="postImage" name="images[]" multiple><br><br>
                        <div id="imagePreview"></div>

                        <button type="submit" name="submit_post">Submit Post</button>
                    </form>
                </section>
            </div>
        </div>

        <section id="postsSection">
            <?php
            // Retrieve all posts from the database and order them by the creation date
            $current_category = basename($_SERVER['PHP_SELF'], '.php'); // Extracts 'culture', 'events', etc.
            $stmt = $conn->prepare("
                SELECT posts.*, users.username
                FROM posts
                JOIN users ON posts.user_id = users.user_id
                WHERE posts.category = ?
                ORDER BY posts.created_at DESC
            ");
            $stmt->bind_param("s", $current_category);
            $stmt->execute();
            $result = $stmt->get_result();

            // Iterate through all fetched posts
            while ($row = $result->fetch_assoc()) {
                // Start creating the post container
                echo "<div class='post' data-post-id='" . $row['post_id'] . "'>";

                // Display user information for the post
                echo "<div class='post-user-info'>";
                echo "<p>User ID: {$row['user_id']},
                <a href='../user/user.php?user_id={$row['user_id']}' style='text-decoration: none; color: blue;'>
                 Username: {$row['username']}</a>, Created at: {$row['created_at']}</p>";
                echo "</div>";

                // Display post title, content, and hashtags
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['content']) . "</p>";
                echo "<p class='hashtag'>" . htmlspecialchars($row['hashtags']) . "</p>";

                // Display the post image if available
                if (!empty($row['post_image'])) {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Post Image' style='max-width: 100%; height: auto;'>";
                }

                // Create action buttons for liking, editing, replying, deleting, and reporting the post
                echo "<div class='post-actions'>";
                echo "<button class='like-button' data-post-id='{$row['post_id']}'>
                        <span class='heart-icon'>🤍</span> 
                        <span class='like-count'>{$row['like_no']}</span>
                        </button>";
                echo "<button class='edit-button' data-post-id='" . $row['post_id'] . "'>Edit</button>";
                echo "<button class='reply-button' data-post-id='" . $row['post_id'] . "'>Reply</button>";
                echo "<button class='delete-button' data-post-id='" . $row['post_id'] . "'>Delete</button>";
                echo "<button class='report-button' data-post-id='" . $row['post_id'] . "'>Report</button>";
                echo "</div>";

                // Add a modal for replying to the post
                echo "<div id='replyModal-{$row['post_id']}' class='modal'>";
                echo "<div class='modal-content'>";
                echo "<span class='close replyClose'>&times;</span>";
                echo "<section id='postsSection'>";
                echo "<h2>Reply to Post</h2>";

                // Reply form for the post
                echo "<form id='replyForm-{$row['post_id']}' method='POST' enctype='multipart/form-data'>";
                echo "<input type='hidden' name='post_id' value='" . $row['post_id'] . "'>";
                echo "<label for='replyText-{$row['post_id']}'>Reply:</label>";
                echo "<textarea id='replyText-{$row['post_id']}' name='content' rows='4' cols='50' placeholder='Enter your reply here...'></textarea><br>";
                echo "<label for='replyImage-{$row['post_id']}'>Images:</label>";
                echo "<input type='file' id='replyImage-{$row['post_id']}' name='image' multiple><br><br>";
                echo "<div id='replyImagePreview-{$row['post_id']}'></div>";
                echo "<button type='submit' name='submit_reply'>Submit Reply</button>
                        </form>
                        </section>
                        </div>
                        </div>";

                // Retrieve replies associated with the current post
                $post_id = $row['post_id'];
                $replies = $conn->query("
                    SELECT replies.*, users.username, users.user_id
                    FROM replies
                    JOIN users ON replies.user_id = users.user_id
                    WHERE replies.post_id = $post_id
                    ORDER BY replies.created_at ASC
                ");

                // Start displaying replies
                echo "<div class='replies'>";
                while ($reply = $replies->fetch_assoc()) {
                    // Display each reply
                    echo "<div class='reply'>";
                    echo "<div class='reply-user-info'>";
                    echo "<p>User ID: {$reply['user_id']},<a href='../user/user.php?user_id={$row['user_id']}' style='text-decoration: none; color: blue;'>
                     Username: {$reply['username']}</a>, Created at: {$reply['created_at']}</p>";
                    echo "</div>";
                    echo "<p>" . htmlspecialchars($reply['content']) . "</p>";

                    // Display reply image if available
                    if (!empty($reply['reply_image'])) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($reply['reply_image']) . "' alt='Reply Image'>";
                    }

                    // Modal for replying to a reply
                    echo "<div id='replyModal-{$reply['reply_id']}' class='modal'>";
                    echo "<div class='modal-content'>";
                    echo "<span class='close replyClose'>&times;</span>";
                    echo "<h2>Reply to Reply</h2>";
                    echo "<form id='replyForm-{$reply['reply_id']}' method='POST' enctype='multipart/form-data'>";
                    echo "<input type='hidden' name='reply_id' value='{$reply['reply_id']}'>";
                    echo "<label for='replyContent-{$reply['reply_id']}'>Reply:</label>";
                    echo "<textarea id='replyContent-{$reply['reply_id']}' name='content' rows='4' cols='50' placeholder='Enter your reply...'></textarea><br>";
                    echo "<button type='submit' name='submit_reply_to_reply'>Submit Reply</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";

                    // Action buttons for each reply (edit, delete)
                    echo "<div class='reply-actions'>";
                    echo "<button class='reply-edit-button' data-reply-id='{$reply['reply_id']}'>Edit</button>";
                    echo "<button class='reply-delete-button' data-reply-id='{$reply['reply_id']}'>Delete</button>";
                    // echo "<button class='reply-report-button' data-reply-id='{$reply['reply_id']}'>Report</button>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div>"; // End of replies section
                echo "</div>"; // End of post
            }
            ?>

        </section>

    </main>

    <!-- Ranking Section -->
    <aside id="rankingSection">
        <?php
        // Get the category dynamically based on the page
        $current_category = basename($_SERVER['PHP_SELF'], '.php'); // Extract category from the file name

        // Query to retrieve the top posts ordered by like count and category
        $stmt = $conn->prepare("SELECT title, content, hashtags, like_no FROM posts WHERE category = ? ORDER BY like_no DESC LIMIT 5");
        $stmt->bind_param("s", $current_category); // Bind the category dynamically

        $stmt->execute();   // Execute the prepared statement
        $result = $stmt->get_result(); // Fetch the results of the query

        // Display the ranking header
        echo "<h2>Top Posts</h2>";
        echo "<ol id='topPosts'>"; // Start an ordered list to display top posts

        // Loop through each post in the ranking
        while ($row = $result->fetch_assoc()) {
            echo "<li>"; // List item for each top post
            echo "<strong>Title:</strong> " . htmlspecialchars($row['title']) . "<br>"; // Display the post title
            echo "<strong>Content:</strong> " . htmlspecialchars($row['content']) . "<br>"; // Display the post content
            echo "<p class='hashtag'>" . htmlspecialchars($row['hashtags']) . "<br></p>"; // Display hashtags
            echo "<strong>Likes:</strong> " . $row['like_no']; // Display the number of likes
            echo "</li>"; // End of list item
        }

        echo "</ol>"; // End of ordered list

        $stmt->close(); // Close the prepared statement to free resources
        ?>
    </aside>


    <footer>
        <img class="footerimage" src="../../esdeeimgs/esdeefooter.png" alt="img">
    </footer>

</body>


</html>

<?php
// close connection
$conn->close();
?>