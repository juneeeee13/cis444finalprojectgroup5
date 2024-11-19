// Post button to display post modal
document.getElementById("postButton").addEventListener("click", openPostModal, false);

// Close button for post/reply modals
document.querySelector(".postClose").addEventListener("click", closePostModal, false);
document.querySelector(".replyClose").addEventListener("click", closeReplyModal, false);

// Close modal when clicking outside window
window.addEventListener("click", closeModalOnWindowClick, false);

// Image preview events
document.getElementById("postImage").addEventListener("change", previewPostImages, false);
document.getElementById("replyImage").addEventListener("change", previewReplyImages, false);

// Post form submission handler
document.getElementById("postForm").addEventListener("submit", submitPost, false);

// Reply form submission handler
document.getElementById("replyForm").addEventListener("submit", submitReply, false);

// Display reply modal when "Reply" button is clicked
document.addEventListener("click", handleReplyButtonClick, false);

// Add click event for "Like" button
document.getElementById("postsSection").addEventListener("click", handleLikeButtonClick, false);

// Add click event for "Report" button in posts section
document.getElementById("postsSection").addEventListener("click", handleReportButtonClick, false);

// Filter and redirect to sorted posts
//document.getElementById("dateFilter").addEventListener("change", redirectToSortedPostsPage, false);
document.getElementById("dateFilter").addEventListener("change", filterPostsByDate, false);

// Attach search functionality to the search bar
document.getElementById("searchBar").addEventListener("input", searchPostsByHashtag);
