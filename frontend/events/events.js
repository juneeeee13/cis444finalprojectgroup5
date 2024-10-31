// Post button to display post modal
var postButton = document.getElementById("postButton");
postButton.addEventListener("click", openPostModal, false);

// Close button for post/reply modals
var postCloseButton = document.querySelector(".postClose");
var replyCloseButton = document.querySelector(".replyClose");
postCloseButton.addEventListener("click", closePostModal, false);
replyCloseButton.addEventListener("click", closeReplyModal, false);

// Close modal when clicking outside window
window.addEventListener("click", closeModalOnWindowClick, false);

// Image preview events
var postImageInput = document.getElementById("postImage");
var replyImageInput = document.getElementById("replyImage");
postImageInput.addEventListener("change", previewPostImages, false);
replyImageInput.addEventListener("change", previewReplyImages, false);

// Post form submission handler
var postForm = document.getElementById("postForm");
postForm.addEventListener("submit", submitPost, false);

// Reply form submission handler
var replyForm = document.getElementById("replyForm");
replyForm.addEventListener("submit", submitReply, false);

// Display reply modal when "Reply" button is clicked
var replyModalHandler = document.addEventListener("click", handleReplyButtonClick, false);

// Add click event for "Like" button
var postsSection = document.getElementById("postsSection");
postsSection.addEventListener("click", handleLikeButtonClick, false);

// Add click event for "Report" button in posts section
postsSection.addEventListener("click", handleReportButtonClick, false);
