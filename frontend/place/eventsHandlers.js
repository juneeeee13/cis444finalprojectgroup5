// Event handlers for modals
function openPostModal() {
    var postModal = document.getElementById("postModal");
    openModal(postModal);
}

function closePostModal() {
    var postModal = document.getElementById("postModal");
    closeModal(postModal);
}

function closeReplyModal() {
    var replyModal = document.getElementById("replyModal");
    closeModal(replyModal);
}

function closeModalOnWindowClick(event) {
    if (event.target.id === "postModal" || event.target.id === "replyModal") {
        closeModal(event.target);
    }
}

// Image preview handlers
function previewPostImages() {
    previewFiles("postImage", "imagePreview");
}

function previewReplyImages() {
    previewFiles("replyImage", "replyImagePreview");
}

// Submission handlers
function submitPost(event) {
    event.preventDefault();

    //try
    var postForm = document.getElementById("postForm");

    var postsSection = document.getElementById("postsSection");
    createPost(postForm, postsSection);
    closeModal(document.getElementById("postModal"));
}

function submitReply(event) {
    event.preventDefault();

    var replyForm = document.getElementById("replyForm");

    var activeReplyTarget = document.querySelector(".activeReply");
    createReply(replyForm, activeReplyTarget);
    closeModal(document.getElementById("replyModal"));
}

// Interaction handlers
function handleReplyButtonClick(event) {
    if (event.target.textContent === "Reply") {
        document.getElementById("replyImage").value = "";
        document.getElementById("replyImagePreview").innerHTML = "";
        openModal(document.getElementById("replyModal"));
        activeReplyTarget = event.target.closest(".post, .reply");
        document.querySelectorAll(".activeReply").forEach(el => el.classList.remove("activeReply"));
        activeReplyTarget.classList.add("activeReply");
    }
}

function handleLikeButtonClick(event) {
    if (event.target.classList.contains("like-button")) {
        var likeButton = event.target;
        var likeCountSpan = likeButton.nextElementSibling;
        var likeCount = parseInt(likeCountSpan.textContent.trim().split(" ")[0]);

        likeButton.classList.toggle("liked") ? likeCount++ : likeCount--;
        likeCountSpan.textContent = ` ${likeCount} Likes`;
    }
}

function handleReportButtonClick(event) {
    if (event.target.classList.contains("report-button")) {
        alert("Your report has been sent to the administrator or the database.");
    }
}

function filterPostsByDate() {
    var filter = document.getElementById("dateFilter").value;
    if (filter === "new") {
        sortPostsByDate("desc"); // 新しい順
    } else if (filter === "old") {
        sortPostsByDate("asc"); // 古い順
    } else if (filter === "3days") {
        filterPostsByDays(3);
    } else if (filter === "2week") {
        filterPostsByDays(14);
    } else if (filter === "1month") {
        filterPostsByDays(30);
    }
}
