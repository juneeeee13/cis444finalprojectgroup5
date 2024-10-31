// Function to open modal
function openModal(modal) {
    modal.style.display = 'block';
}

// Function to close modal
function closeModal(modal) {
    modal.style.display = 'none';
}

// Image preview functionality
function previewFiles(inputId, previewId) {
    var preview = document.getElementById(previewId);
    var files = document.getElementById(inputId).files;

    preview.innerHTML = '';

    if (files.length === 0) {
        return;
    }

    for (var i = 0; i < files.length; i++) {
        var reader = new FileReader();
        reader.onload = (function (file) {
            return function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.marginBottom = '10px';
                preview.appendChild(img);
            };
        })(files[i]);
        reader.readAsDataURL(files[i]);
    }
}

// Function to create a new post
function createPost(postForm, postsSection) {
    var postText = document.getElementById('postText').value;
    var postImageFiles = document.getElementById('postImage').files;

    // Create post element
    var postDiv = document.createElement('div');
    postDiv.classList.add('post');

    // Username and ID
    var userName = document.createElement('span');
    userName.textContent = 'User123';
    var userId = document.createElement('span');
    userId.textContent = ' (@user123) ';
    postDiv.appendChild(userName);
    postDiv.appendChild(userId);

    // Add date
    var currentDate = new Date();
    var dateString = currentDate.toLocaleDateString() + ' ' + currentDate.toLocaleTimeString();
    var dateSpan = document.createElement('span');
    dateSpan.textContent = dateString;
    postDiv.appendChild(dateSpan);

    // Post text
    var textParagraph = document.createElement('p');
    textParagraph.textContent = postText;
    postDiv.appendChild(textParagraph);

    // Add images (if any)
    if (postImageFiles.length > 0) {
        var imageContainer = document.createElement('div');
        for (var i = 0; i < postImageFiles.length; i++) {
            var imageElement = document.createElement('img');
            imageElement.src = URL.createObjectURL(postImageFiles[i]);
            imageElement.style.maxWidth = '100%';
            imageElement.style.marginBottom = '10px';
            imageContainer.appendChild(imageElement);
        }
        postDiv.appendChild(imageContainer);
    }

    // "Delete" button
    var deleteButton = document.createElement('button');
    deleteButton.textContent = 'Delete';
    deleteButton.addEventListener('click', function () {
        if (confirm("Are you sure you want to delete this post?")) {
            postsSection.removeChild(postDiv);
        }
    });
    postDiv.appendChild(deleteButton);

    // "Like" button
    var likeButton = document.createElement('button');
    likeButton.innerHTML = '♥';
    likeButton.classList.add('like-button');
    var likeCountSpan = document.createElement('span');
    likeCountSpan.textContent = ` 0 Likes`;
    postDiv.appendChild(likeButton);
    postDiv.appendChild(likeCountSpan);

    // "Reply" button
    var replyButton = document.createElement('button');
    replyButton.textContent = 'Reply';
    replyButton.addEventListener('click', function () {
        activeReplyTarget = postDiv; // Target the entire post
        openModal(document.getElementById('replyModal'));
    });
    postDiv.appendChild(replyButton);

    // Function to create a new post (add this inside createPost)
    var reportButton = document.createElement('button');
    reportButton.textContent = 'Report';
    reportButton.classList.add('report-button');
    postDiv.appendChild(reportButton);

    // Replies section
    var repliesDiv = document.createElement('div');
    repliesDiv.classList.add('replies');
    postDiv.appendChild(repliesDiv);

    // Add post
    postsSection.insertBefore(postDiv, postsSection.firstChild);

    // Reset form
    postForm.reset();
    document.getElementById('imagePreview').innerHTML = '';  // Reset image preview
}

// Function to create a reply
function createReply(replyForm, activeReplyTarget) {
    var replyText = document.getElementById('replyText').value;
    var replyImageFiles = document.getElementById('replyImage').files;

    // Create reply element
    var replyDiv = document.createElement('div');
    replyDiv.classList.add('reply');

    // Username and ID
    var replyUserName = document.createElement('span');
    replyUserName.textContent = 'UserReply';
    var replyUserId = document.createElement('span');
    replyUserId.textContent = ' (@userReply) ';
    replyDiv.appendChild(replyUserName);
    replyDiv.appendChild(replyUserId);

    // Add date
    var currentDate = new Date();
    var dateString = currentDate.toLocaleDateString() + ' ' + currentDate.toLocaleTimeString();
    var dateSpan = document.createElement('span');
    dateSpan.textContent = dateString;
    replyDiv.appendChild(dateSpan);

    // Reply text
    var replyParagraph = document.createElement('p');
    replyParagraph.textContent = replyText;
    replyDiv.appendChild(replyParagraph);

    // Add images (if any)
    if (replyImageFiles.length > 0) {
        var replyImageContainer = document.createElement('div');
        for (var i = 0; i < replyImageFiles.length; i++) {
            var replyImageElement = document.createElement('img');
            replyImageElement.src = URL.createObjectURL(replyImageFiles[i]);
            replyImageElement.style.maxWidth = '100%';
            replyImageElement.style.marginBottom = '10px';
            replyImageContainer.appendChild(replyImageElement);
        }
        replyDiv.appendChild(replyImageContainer);
    }

    // "Delete" button
    var deleteButton = document.createElement('button');
    deleteButton.textContent = 'Delete';
    deleteButton.addEventListener('click', function () {
        if (confirm("Are you sure you want to delete this reply?")) {
            replyDiv.parentNode.removeChild(replyDiv); // Remove replyDiv from parent node
        }
    });
    replyDiv.appendChild(deleteButton);

    // "Like" button
    var likeButton = document.createElement('button');
    likeButton.innerHTML = '♥';
    likeButton.classList.add('like-button');
    var likeCountSpan = document.createElement('span');
    likeCountSpan.textContent = ` 0 Likes`;
    replyDiv.appendChild(likeButton);
    replyDiv.appendChild(likeCountSpan);

    // "Reply" button (allows further replies)
    var replyButton = document.createElement('button');
    replyButton.textContent = 'Reply';
    replyButton.addEventListener('click', function () {
        activeReplyTarget = replyDiv;
        document.getElementById('replyModal').style.display = 'block';
    });
    replyDiv.appendChild(replyButton);

    // Function to create a reply (add this inside createReply)
    var replyReportButton = document.createElement('button');
    replyReportButton.textContent = 'Report';
    replyReportButton.classList.add('report-button');
    replyDiv.appendChild(replyReportButton);

    // Nested replies section
    var nestedRepliesDiv = document.createElement('div');
    nestedRepliesDiv.classList.add('replies');
    replyDiv.appendChild(nestedRepliesDiv);

    // Insert the new reply at the top of the replies section
    var repliesSection = activeReplyTarget.querySelector('.replies');
    repliesSection.insertBefore(replyDiv, repliesSection.firstChild);

    // Reset form
    replyForm.reset();
    document.getElementById('imagePreview').innerHTML = '';  // Reset image preview
}



// Sample data setting the number of "likes" for each post
var posts = [
    { id: 1, content: "Post 1", likes: 10 },
    { id: 2, content: "Post 2", likes: 30 },
    { id: 3, content: "Post 3", likes: 20 },
    { id: 4, content: "Post 4", likes: 25 },
    { id: 5, content: "Post 5", likes: 15 },
    { id: 6, content: "Post 6", likes: 35 }
];

// Function to update the ranking
function updateRanking() {
    // Sort by the number of likes (descending)
    var topPosts = posts.sort(function(a, b) { return b.likes - a.likes; }).slice(0, 5);

    // Display in the ranking section
    var topPostsElement = document.getElementById("topPosts");
    topPostsElement.innerHTML = ""; // Initialize

    topPosts.forEach(function(post, index) {
        var listItem = document.createElement("li");
        listItem.textContent = (index + 1) + ". " + post.content + " - " + post.likes + " likes";
        topPostsElement.appendChild(listItem);
    });
}

// Update the ranking when the page loads
document.addEventListener("DOMContentLoaded", updateRanking);
