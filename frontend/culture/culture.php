<?php
// connect to the database
$conn = new mysqli("localhost", "takeh", "Hanateddy@87", "sample_db");

// Check if the connection to the database was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Handle creating a new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post'])) {
    // Escape user inputs to prevent SQL injection
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $hashtags = $conn->real_escape_string($_POST['hashtags']);
    // $poster_id = 1; // Example static user ID. Replace with session-based dynamic user ID.
    $user_id = 1;

    // ここで画像データを処理します
    $imageData = null; // 初期化
    if (!empty($_FILES['images']['tmp_name'][0])) {
        $tmpName = $_FILES['images']['tmp_name'][0];
        $imageData = file_get_contents($tmpName); // 画像をバイナリ形式で取得
    }

    // データベースへの挿入時にバイナリデータを使用
    // $stmt = $conn->prepare("INSERT INTO posts (poster_id, title, content, post_image, created_at, hashtags) VALUES (?, ?, ?, ?, NOW(), ?)");
    // $stmt->bind_param("issss", $poster_id, $title, $content, $imageData, $hashtags);
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, post_image, created_at, hashtags) VALUES (?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param("issss", $user_id, $title, $content, $imageData, $hashtags);
    $stmt->execute();

    // Redirect to the homepage after submitting the post
    header("Location: culture.php");
    exit();
}


// Handle reply submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $post_id = intval($_POST['post_id']); // Get the post ID
    $content = trim($_POST['content']);
    $user_id = 1; // 仮のユーザーID

    // Validate that reply content is not empty
    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Reply content cannot be empty.']);
        exit;
    }

    // ここで画像データを処理します
    $imageData = null; // 初期化
    if (!empty($_FILES['image']['tmp_name'])) {
        $tmpName = $_FILES['image']['tmp_name'];
        $imageData = file_get_contents($tmpName); // 画像をバイナリ形式で取得
    }

    // データベースへの挿入時にバイナリデータを使用
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
    header("Location: culture.php");
    exit();
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post'])) {
    $post_id = intval($_POST['post_id']);
    $user_id = 1; // 仮のユーザーID（セッションなどから取得）

    // セッションにライク済みのポストを追跡
    session_start();
    if (!isset($_SESSION['liked_posts'])) {
        $_SESSION['liked_posts'] = []; // 初期化
    }

    if (in_array($post_id, $_SESSION['liked_posts'])) {
        // すでにライクしている場合
        echo json_encode(['success' => false, 'message' => 'You have already liked this post.']);
    } else {
        // 初めてライクする場合
        $_SESSION['liked_posts'][] = $post_id;

        // like_no を増加
        $stmt = $conn->prepare("UPDATE posts SET like_no = like_no + 1 WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Post liked successfully.']);
    }
    exit();
}



// Handle editing a post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {
    $post_id = intval($_POST['post_id']);
    $new_title = $conn->real_escape_string($_POST['title']);
    $new_content = $conn->real_escape_string($_POST['content']);

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE post_id = ?");
    $stmt->bind_param("ssi", $new_title, $new_content, $post_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit();
}

// Handle deleting a post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = intval($_POST['post_id']);

    $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit();
}

// Handle reporting a post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_post'])) {
    $post_id = intval($_POST['post_id']);
    $reporter_id = 1; // 仮の通報者ID
    // $reporter_id = intval($_POST['reporter_id']); // Assume user ID is passed from the frontend.

    $stmt = $conn->prepare("INSERT INTO reports (post_id, reporter_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $post_id, $reporter_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Like Reply
    if (isset($_POST['like_reply']) && isset($_POST['reply_id'])) {
        $reply_id = intval($_POST['reply_id']);
        $conn->query("UPDATE replies SET like_count = like_count + 1 WHERE reply_id = $reply_id");
        echo json_encode(['success' => true, 'message' => 'Reply liked.']);
        exit;
    }

    // Edit Reply
    if (isset($_POST['edit_reply']) && isset($_POST['reply_id']) && isset($_POST['content'])) {
        $reply_id = intval($_POST['reply_id']);
        $content = $conn->real_escape_string($_POST['content']);
        $conn->query("UPDATE replies SET content = '$content' WHERE reply_id = $reply_id");
        echo json_encode(['success' => true, 'message' => 'Reply edited.']);
        exit;
    }

    // Delete Reply
    if (isset($_POST['delete_reply']) && isset($_POST['reply_id'])) {
        $reply_id = intval($_POST['reply_id']);
        $conn->query("DELETE FROM replies WHERE reply_id = $reply_id");
        echo json_encode(['success' => true, 'message' => 'Reply deleted.']);
        exit;
    }

    // Report Reply
    if (isset($_POST['report_reply']) && isset($_POST['reply_id'])) {
        $reply_id = intval($_POST['reply_id']);
        $conn->query("UPDATE replies SET report_count = report_count + 1 WHERE reply_id = $reply_id");
        echo json_encode(['success' => true, 'message' => 'Reply reported.']);
        exit;
    }
}


// Handle reply-to-reply submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply_to_reply'])) {
    $reply_id = intval($_POST['reply_id']); // Get the reply ID
    $content = trim($_POST['content']);
    $user_id = 1; // 仮のユーザーID（セッションなどから取得する）

    // Validate that reply content is not empty
    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Reply-to-reply content cannot be empty.']);
        exit;
    }

    // ここで画像データを処理します
    $imageData = null; // 初期化
    if (!empty($_FILES['image']['tmp_name'])) {
        $tmpName = $_FILES['image']['tmp_name'];
        $imageData = file_get_contents($tmpName); // 画像をバイナリ形式で取得
    }

    // データベースへの挿入時にバイナリデータを使用
    $stmt = $conn->prepare("INSERT INTO replies (post_id, content, reply_image, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $reply_id, $content, $imageData, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reply-to-reply submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save reply-to-reply.']);
    }
    $stmt->close();
    $conn->close();

    // Redirect to the homepage after submitting the reply-to-reply
    header("Location: culture.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_top_posts') {
    $stmt = $conn->prepare("SELECT title, hashtags, like_no FROM posts ORDER BY like_no DESC LIMIT 10");
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


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'filter_posts') {
    $order = $_GET['order'] === 'new' ? 'DESC' : 'ASC';

    // 投稿を取得
    $stmt = $conn->prepare("
        SELECT posts.*, users.username
        FROM posts
        JOIN users ON posts.user_id = users.user_id
        ORDER BY posts.created_at $order
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    $filteredPosts = [];
    while ($row = $result->fetch_assoc()) {
        // リプライを取得
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




?>



<!--start html-->
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Culture</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../eSDee-logo/darkmode.png">
    <link rel="stylesheet" type="text/css" href="culture.css">
    <script src="script.js" defar></script>
</head>

<body>
    <header id="header">
        <div class="topic">
            <h1><b>Culture</b></h1>

        
            <img class="icon1" src="../../esdeeimgs/esdeebrowsericon.png" alt="img">
            <img class="icon2" src="../../esdeeimgs/pinkshell.png" alt="img">
            <img class="headerimage" src="../../esdeeimgs/waves.png" alt="img">
    
            <div id="description">
                <div class="dText">
                    <p>Explore the vibrant cultural landscape of San Diego on our Culture page.
                        Here, you can freely share your unique insights, images, and experiences related to the city's
                        rich heritage, art, and local traditions.
                        Whether it's about the latest art exhibit, a community festival, or a hidden cultural gem,
                        this is the place to connect and celebrate San Diego's culture with others.</p>
                </div>
            </div>

        </div>

        <div id="navArea">
            <nav>
                <div class="menuInner">
                    <ul>
                        <li><a href="../home/home.html"><img src="../../eSDee-logo/darkmode.png" alt="img"></a></li>
                        <!-- <li><a href="../home/home.html"><img src="img/darkmode.png" alt="img"></a></li> -->
                        <li><a href="../home/home.html">Home</a></li>
                        <li><a href="../culture/culture.html">Culture</a></li>
                        <li><a href="../events/events.html">Event</a></li>
                        <li><a href="../food/food.html">Food</a></li>
                        <li><a href="../place/place.html">Place</a></li>
                        <li><a href="../about/about.html">About us</a></li>
                        <li><a href="../settings/settings.html">Setting</a></li>
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
            <!-- <input type="text" id="searchBar" name="search_query" placeholder="Search posts..." /> -->
            <input type="text" id="searchBar" placeholder="Search posts..." />
            <button type="searchButton">Search</button>
        </div>

        <!-- Date Filter Dropdown -->
        <div id="dateFilterContainer">
            <!-- <form method="GET"> -->
            <section id="filterSection">
                <label for="dateFilter">Filter by Date:</label>
                <select id="dateFilter" name="filter_date" onchange="this.form.submit()">
                    <!-- <option value="">Select</option> -->
                    <option value="new">Newest</option>
                    <option value="old">Oldest</option>
                </select>
                <!-- </form> -->
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


                        <!-- <label for="postHashtags">Hashtags:</label>
                        <textarea id="postHashtags" name="hashtags" rows="1" cols="50" placeholder="#..."></textarea><br> -->


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
            // $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
            // Fetch posts with user details
            $result = $conn->query("
                SELECT posts.*, users.username, users.user_id
                FROM posts
                JOIN users ON posts.user_id = users.user_id
                ORDER BY posts.created_at DESC
            ");

            while ($row = $result->fetch_assoc()) {
                echo "<div class='post' data-post-id='" . $row['post_id'] . "'>";

                echo "<div class='post-user-info'>";
                echo "<p>User ID: {$row['user_id']}, Username: {$row['username']}, Created at: {$row['created_at']}</p>";
                echo "</div>";

                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['content']) . "</p>";

                echo "<p>" . htmlspecialchars($row['hashtags']) . "</p>";



                if (!empty($row['post_image'])) {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Post Image' style='max-width: 100%; height: auto;'>";
                }

                // Post action buttons (like, edit, delete, etc.)
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


                echo "<div id='replyModal-{$row['post_id']}' class='modal'>";
                echo "<div class='modal-content'>";
                echo "<span class='close replyClose'>&times;</span>";
                echo "<section id='postsSection'>";
                echo "<h2>Reply to Post</h2>";

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



                // reply section

                $post_id = $row['post_id'];
                // $replies = $conn->query("SELECT * FROM replies WHERE post_id = $post_id ORDER BY reply_id ASC");

                $replies = $conn->query("
                    SELECT replies.*, users.username, users.user_id
                    FROM replies
                    JOIN users ON replies.user_id = users.user_id
                    WHERE replies.post_id = $post_id
                    ORDER BY replies.created_at ASC
                ");

                echo "<div class='replies'>";
                while ($reply = $replies->fetch_assoc()) {
                    echo "<div class='reply'>";

                    echo "<div class='reply-user-info'>";
                    echo "<p>User ID: {$reply['user_id']}, Username: {$reply['username']}, Created at: {$reply['created_at']}</p>";
                    echo "</div>";

                    echo "<p>" . htmlspecialchars($reply['content']) . "</p>";


                    if (!empty($reply['reply_image'])) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($reply['reply_image']) . "' alt='Reply Image'>";
                    }

                    // モーダルの追加
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


                    echo "<div class='reply-actions'>";
                    // echo "<button class='reply-like-button' data-reply-id='{$reply['reply_id']}'>Like</button>";
                    echo "<button class='reply-edit-button' data-reply-id='{$reply['reply_id']}'>Edit</button>";
                    echo "<button class='reply-reply-button' data-reply-id='{$reply['reply_id']}'>Reply</button>";
                    echo "<button class='reply-delete-button' data-reply-id='{$reply['reply_id']}'>Delete</button>";
                    echo "<button class='reply-report-button' data-reply-id='{$reply['reply_id']}'>Report</button>";
                    echo "</div>";


                    echo "</div>";
                }
                echo "</div>";

                echo "</div>";


            }
            ?>

        </section>

    </main>

    <!-- Ranking Section -->
    <aside id="rankingSection">
        <?php
        // ランキングを取得するクエリ
        $stmt = $conn->prepare("SELECT title, hashtags, like_no FROM posts ORDER BY like_no DESC LIMIT 10");
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h2>Top Posts</h2>";
        echo "<ol id='topPosts'>";

        while ($row = $result->fetch_assoc()) {
            echo "<li>";
            echo "<strong>Title:</strong> " . htmlspecialchars($row['title']) . "<br>";
            echo "<strong>Hashtags:</strong> " . htmlspecialchars($row['hashtags']) . "<br>";
            echo "<strong>Likes:</strong> " . $row['like_no'];
            echo "</li>";
        }

        echo "</ol>";

        $stmt->close();
        ?>
    </aside>


    <footer>
        <!-- <img class="footerimage" src="../../esdeeimgs/esdeefooter.png" alt="img"> -->
        <img class="footerimage" src="img/esdeefooter.png" alt="img">
    </footer>

</body>


</html>

<?php
// close connection
$conn->close();
?>
