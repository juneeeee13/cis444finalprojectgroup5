// Open modal for posting
function openPostModal() {
    var postModal = document.getElementById('postModal');
    openModal(postModal);
}

// Close modal for posting
function closePostModal() {
    var postModal = document.getElementById('postModal');
    closeModal(postModal);
}

// Close modal for replying
function closeReplyModal() {
    var replyModal = document.getElementById('replyModal');
    closeModal(replyModal);

}

// Close modal when clicking outside
function closeModalOnWindowClick(event) {
    var postModal = document.getElementById('postModal');
    var replyModal = document.getElementById('replyModal');
    if (event.target == postModal) {
        closeModal(postModal);
    } else if (event.target == replyModal) {
        closeModal(replyModal);
    }
}

// Image preview for posting
function previewPostImages() {
    previewFiles('postImage', 'imagePreview');
}

// Image preview for replying
function previewReplyImages() {
    previewFiles('replyImage', 'replyImagePreview');
}

// Handle post submission
function submitPost(event) {
    event.preventDefault();
    var postsSection = document.getElementById("postsSection");
    createPost(postForm, postsSection);
    closeModal(document.getElementById("postModal"));
}

// Handle reply submission
function submitReply(event) {
    event.preventDefault();
    var activeReplyTarget = document.querySelector(".activeReply");
    createReply(replyForm, activeReplyTarget);
    closeModal(document.getElementById("replyModal"));
}

// Handle "Reply" button click
function handleReplyButtonClick(event) {
    if (event.target.textContent === "Reply") {
        var replyModal = document.getElementById("replyModal");

        // Reset file selection and image preview when creating a new reply
        document.getElementById('replyImage').value = '';  // Reset file selection
        document.getElementById('replyImagePreview').innerHTML = '';  // Reset image preview

        openModal(replyModal);

        // Set the active reply target
        activeReplyTarget = event.target.closest(".post, .reply");
        document.querySelectorAll(".activeReply").forEach(el => el.classList.remove("activeReply"));
        activeReplyTarget.classList.add("activeReply");
    }
}

// Handle "Like" button click
function handleLikeButtonClick(event) {
    if (event.target.classList.contains("like-button")) {
        var likeButton = event.target;
        var likeCountSpan = likeButton.nextElementSibling;
        var likeCount = parseInt(likeCountSpan.textContent.trim().split(" ")[0]);

        // Toggle like count with double-click functionality
        if (likeButton.classList.toggle('liked')) {
            likeCount++;  // Increment like count
        } else {
            likeCount--;  // Decrement like count
        }
        likeCountSpan.textContent = ` ${likeCount} Likes`;
    }
}

// Handle "Report" button click
function handleReportButtonClick(event) {
    if (event.target.classList.contains("report-button")) {
        alert("Your report has been sent to the administrator or the database.");
    }
}
