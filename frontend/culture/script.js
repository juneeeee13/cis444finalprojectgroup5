document.addEventListener("DOMContentLoaded", () => {
    // Handle showing and hiding the "Create Post" modal
    const postModal = document.getElementById("postModal");
    const postButton = document.getElementById("postButton");
    const postClose = document.querySelector(".postClose");
    // Show the modal
    postButton.addEventListener("click", () => {
        postModal.style.display = "block";
    });
    // Hide the modal
    postClose.addEventListener("click", () => {
        postModal.style.display = "none";
    });
    // Close modal on outside click
    window.addEventListener("click", (event) => {
        if (event.target === postModal) {
            postModal.style.display = "none";
        }
    });

    // Image preview functionality
    document.getElementById('postImage').addEventListener('change', (event) => {
        // const imagePreview = document.getElementById('imagePreview');
        // imagePreview.innerHTML = ''; // Clear existing previews
        const preview = document.getElementById("imagePreview");
        preview.innerHTML = ""; // 既存のプレビューをクリア

        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            // const file = files[i];
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;// Set the preview image source
                img.style.maxWidth = '100px';
                img.style.margin = '5px';
                imagePreview.appendChild(img);// Append image to preview container
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);// Read file as data URL
        }
    });


    document.querySelectorAll(".reply-button").forEach((button) => {
        button.addEventListener("click", () => {
            const postId = button.getAttribute("data-post-id");
            const replyModal = document.getElementById(`replyModal-${postId}`);

            // Show the modal
            replyModal.style.display = "block";

            // Close modal on close button click
            replyModal.querySelector(".replyClose").addEventListener("click", () => {
                replyModal.style.display = "none";
            });

            // Close modal on outside click
            window.addEventListener("click", (event) => {
                if (event.target === replyModal) {
                    replyModal.style.display = "none";
                }
            });

            // Submit reply form via AJAX
            const replyForm = document.getElementById(`replyForm-${postId}`);
            if (!replyForm.hasAttribute("data-event-added")) {
                replyForm.setAttribute("data-event-added", "true");
                replyForm.addEventListener("submit", async (e) => {
                    e.preventDefault();

                    const formData = new FormData(replyForm);
                    try {
                        const response = await fetch("index.php", {
                            method: "POST",
                            body: formData,
                        });

                        if (!response.ok) {
                            throw new Error("Network response was not ok");
                        }

                        const data = await response.json();
                        if (data.success) {
                            const repliesContainer = document.querySelector(
                                `.post[data-post-id="${postId}"] .replies`
                            );
                            const newReply = document.createElement("div");
                            newReply.classList.add("reply");
                            newReply.innerHTML = `<p>${formData.get("content")}</p>`;
                            repliesContainer.appendChild(newReply);

                            replyForm.reset();
                            replyModal.style.display = "none";
                        } else {
                            console.error("Reply failed:", data.message);
                            alert(data.message);
                        }
                    } catch (error) {
                        console.error("Error:", error);
                        // alert("An unexpected error occurred. Please try again later.");
                    }
                }, { once: true });
            }
        });
    });




    // Like button functionality
    document.querySelectorAll(".like-button").forEach((button) => {
        button.addEventListener("click", () => {
            const postId = button.getAttribute("data-post-id");
            fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `like_post=1&post_id=${postId}&action=like`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Post liked!");
                    }
                });
        });
    });

    // Handle reporting a post
    document.querySelectorAll(".report-button").forEach((button) => {
        button.addEventListener("click", () => {
            const postId = button.getAttribute("data-post-id");
            fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `report_post=1&post_id=${postId}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Post reported!");
                    }
                });
        });
    });

    // Delete button functionality
    document.querySelectorAll(".delete-button").forEach((button) => {
        button.addEventListener("click", () => {
            const postId = button.getAttribute("data-post-id");
            fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `delete_post=1&post_id=${postId}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Post deleted!");
                        location.reload();
                    }
                });
        });
    });

    // Handle editing a post
    document.querySelectorAll(".edit-button").forEach((button) => {
        button.addEventListener("click", () => {
            const postId = button.getAttribute("data-post-id");

            // Dynamically create and display an edit modal
            const editModal = document.createElement("div");
            editModal.classList.add("modal");
            editModal.innerHTML = `
                <div class="modal-content">
                    <span class="close editClose">&times;</span>
                    <h2>Edit Post</h2>
                    <form id="editForm" method="POST">
                        <label for="editTitle">Title:</label>
                        <input type="text" id="editTitle" name="title" placeholder="New title" required><br>
                        
                        <label for="editContent">Content:</label>
                        <textarea id="editContent" name="content" rows="4" cols="50" placeholder="New content" required></textarea><br>
                        
                        <button type="submit">Save Changes</button>
                    </form>
                </div>
            `;
            document.body.appendChild(editModal);

            // Handle closing the modal
            const closeModal = () => {
                editModal.remove();// Remove modal from DOM
            };


            editModal.querySelector(".editClose").addEventListener("click", closeModal);

            // Close modal on outside click
            window.addEventListener("click", (event) => {
                if (event.target === editModal) {
                    closeModal();
                }
            });

            // Handle form submission to update the post
            const editForm = editModal.querySelector("#editForm");
            editForm.addEventListener("submit", (e) => {
                e.preventDefault();

                const formData = new FormData(editForm);
                formData.append("edit_post", "1");
                formData.append("post_id", postId);

                fetch("index.php", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert("Post updated successfully!");
                            location.reload(); // Reload page to reflect changes
                        } else {
                            alert("Failed to update post.");
                        }
                    });
            });

            // Display the modal
            editModal.style.display = "block";
        });
    });

    // Like Reply
    // document.querySelectorAll(".reply-like-button").forEach((button) => {
    //     button.addEventListener("click", () => {
    //         const replyId = button.getAttribute("data-reply-id");
    //         fetch("index.php", {
    //             method: "POST",
    //             headers: { "Content-Type": "application/x-www-form-urlencoded" },
    //             body: `like_reply=1&reply_id=${replyId}`,
    //         })
    //             .then((response) => response.json())
    //             .then((data) => {
    //                 if (data.success) {
    //                     alert("Reply liked!");
    //                     // Optional: Update like count visually
    //                 }
    //             });
    //     });
    // });

    // Like button functionality
    // document.querySelectorAll(".like-button").forEach((button) => {
    //     button.addEventListener("click", async () => {
    //         const postId = button.getAttribute("data-post-id");

    //         try {
    //             const response = await fetch("index.php", {
    //                 method: "POST",
    //                 headers: { "Content-Type": "application/x-www-form-urlencoded" },
    //                 body: `like_post=1&post_id=${postId}`,
    //             });

    //             if (!response.ok) {
    //                 throw new Error("Network response was not ok");
    //             }

    //             const data = await response.json();
    //             if (data.success) {
    //                 const likeCount = button.querySelector(".like-count");
    //                 const heartIcon = button.querySelector(".heart-icon");

    //                 if (data.action === "liked") {
    //                     heartIcon.textContent = "❤️"; // Filled heart for liked
    //                     likeCount.textContent = parseInt(likeCount.textContent) + 1;
    //                 } else if (data.action === "unliked") {
    //                     heartIcon.textContent = "🤍"; // Empty heart for unliked
    //                     likeCount.textContent = parseInt(likeCount.textContent) - 1;
    //                 }
    //             }
    //         } catch (error) {
    //             console.error("Error:", error);
    //         }
    //     });
    // });

    // document.querySelectorAll(".like-button").forEach((button) => {
    //     button.addEventListener("click", async () => {
    //         const postId = button.getAttribute("data-post-id");

    //         try {
    //             const response = await fetch("index.php", {
    //                 method: "POST",
    //                 headers: { "Content-Type": "application/x-www-form-urlencoded" },
    //                 body: `like_post=1&post_id=${postId}`,
    //             });

    //             if (!response.ok) {
    //                 throw new Error("Network response was not ok");
    //             }

    //             const data = await response.json();
    //             if (data.success) {
    //                 const likeCount = button.querySelector(".like-count");
    //                 const heartIcon = button.querySelector(".heart-icon");

    //                 if (data.action === "liked") {
    //                     heartIcon.textContent = "❤️"; // Filled heart for liked
    //                     likeCount.textContent = parseInt(likeCount.textContent) + 1;
    //                 } else if (data.action === "unliked") {
    //                     heartIcon.textContent = "🤍"; // Empty heart for unliked
    //                     likeCount.textContent = parseInt(likeCount.textContent) - 1;
    //                 }
    //             }
    //         } catch (error) {
    //             console.error("Error:", error);
    //         }
    //     });
    // });

    document.querySelectorAll(".like-button").forEach((button) => {
        button.addEventListener("click", async () => {
            const postId = button.getAttribute("data-post-id");

            try {
                const response = await fetch("index.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `like_post=1&post_id=${postId}`,
                });

                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }

                const data = await response.json();
                // if (data.success) {
                // 初回クリック時
                const likeCount = button.querySelector(".like-count");
                const heartIcon = button.querySelector(".heart-icon");

                heartIcon.textContent = "❤️"; // ハートを埋める
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
                // } else {
                //     // 2回目以降のクリック時
                //     alert(data.message); // "You have already liked this post."
                // }
            } catch (error) {
                console.error("Error:", error);
            }
        });
    });


    // Edit Reply
    document.querySelectorAll(".reply-edit-button").forEach((button) => {
        button.addEventListener("click", () => {
            const replyId = button.getAttribute("data-reply-id");
            const newContent = prompt("Edit your reply:");
            if (newContent) {
                fetch("index.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `edit_reply=1&reply_id=${replyId}&content=${encodeURIComponent(newContent)}`,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert("Reply edited!");
                            location.reload(); // Reload to see the changes
                        }
                    });
            }
        });
    });

    // Delete Reply
    document.querySelectorAll(".reply-delete-button").forEach((button) => {
        button.addEventListener("click", () => {
            const replyId = button.getAttribute("data-reply-id");
            if (confirm("Are you sure you want to delete this reply?")) {
                fetch("index.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `delete_reply=1&reply_id=${replyId}`,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert("Reply deleted!");
                            location.reload(); // Reload to remove the deleted reply
                        }
                    });
            }
        });
    });

    // Report Reply
    document.querySelectorAll(".reply-report-button").forEach((button) => {
        button.addEventListener("click", () => {
            const replyId = button.getAttribute("data-reply-id");
            fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `report_reply=1&reply_id=${replyId}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Reply reported!");
                    }
                });
        });
    });

    // Reply-to-reply button functionality
    // document.querySelectorAll(".reply-reply-button").forEach((button) => {
    //     button.addEventListener("click", () => {
    //         const replyId = button.getAttribute("data-reply-id");
    //         const replyModal = document.getElementById(`replyModal-${replyId}`);

    //         // Show the modal
    //         replyModal.style.display = "block";

    //         // Close modal on close button click
    //         replyModal.querySelector(".replyClose").addEventListener("click", () => {
    //             replyModal.style.display = "none";
    //         });

    //         // Close modal on outside click
    //         window.addEventListener("click", (event) => {
    //             if (event.target === replyModal) {
    //                 replyModal.style.display = "none";
    //             }
    //         });

    //         // Submit reply-to-reply form via AJAX
    //         const replyForm = document.getElementById(`replyForm-${replyId}`);
    //         if (!replyForm.hasAttribute("data-event-added")) {
    //             replyForm.setAttribute("data-event-added", "true");
    //             replyForm.addEventListener("submit", (e) => {
    //                 e.preventDefault();

    //                 const formData = new FormData(replyForm);
    //                 fetch("index.php", {
    //                     method: "POST",
    //                     body: formData,
    //                 })
    //                     .then((response) => response.json())
    //                     .then((data) => {
    //                         if (data.success) {
    //                             alert("Reply-to-reply submitted successfully!");
    //                             replyForm.reset();
    //                             replyModal.style.display = "none";
    //                             location.reload(); // Optional: reload to see the update
    //                         } else {
    //                             alert(data.message);
    //                         }
    //                     })
    //                     .catch((error) => {
    //                         console.error("Error:", error);
    //                         alert("An unexpected error occurred.");
    //                     });
    //             });
    //         }
    //     });
    // });

    // Reply-to-reply button functionality
    document.querySelectorAll(".reply-reply-button").forEach((button) => {
        button.addEventListener("click", () => {
            const replyId = button.getAttribute("data-reply-id");
            const replyModal = document.getElementById(`replyModal-${replyId}`);

            // Show the modal
            replyModal.style.display = "block";

            // Close modal on close button click
            replyModal.querySelector(".replyClose").addEventListener("click", () => {
                replyModal.style.display = "none";
            });

            // Close modal on outside click
            window.addEventListener("click", (event) => {
                if (event.target === replyModal) {
                    replyModal.style.display = "none";
                }
            });

            // Submit reply-to-reply form via AJAX
            const replyForm = document.getElementById(`replyForm-${replyId}`);
            if (!replyForm.hasAttribute("data-event-added")) {
                replyForm.setAttribute("data-event-added", "true");
                replyForm.addEventListener("submit", async (e) => {
                    e.preventDefault();

                    const formData = new FormData(replyForm);
                    try {
                        const response = await fetch("index.php", {
                            method: "POST",
                            body: formData,
                        });

                        if (!response.ok) {
                            throw new Error("Network response was not ok");
                        }

                        const data = await response.json();
                        if (data.success) {
                            const repliesContainer = document.querySelector(
                                `.reply[data-reply-id="${replyId}"] .replies`
                            );
                            const newReply = document.createElement("div");
                            newReply.classList.add("reply");
                            newReply.innerHTML = `<p>${formData.get("content")}</p>`;
                            repliesContainer.appendChild(newReply);

                            replyForm.reset();
                            replyModal.style.display = "none";
                        } else {
                            console.error("Reply-to-reply failed:", data.message);
                            alert(data.message);
                        }
                    } catch (error) {
                        console.error("Error:", error);
                        alert("An unexpected error occurred. Please try again later.");
                    }
                }, { once: true });
            }
        });
    });

    async function fetchTopPosts() {
        try {
            const response = await fetch("index.php?action=get_top_posts");
            if (!response.ok) {
                throw new Error("Failed to fetch top posts");
            }

            const data = await response.json();
            const rankingSection = document.querySelector(".ranking-section ul");
            rankingSection.innerHTML = ""; // Clear current ranking

            data.forEach(post => {
                const listItem = document.createElement("li");
                listItem.innerHTML = `
                    <strong>Title:</strong> ${post.title}<br>
                    <strong>Hashtags:</strong> ${post.hashtags}<br>
                    <strong>Likes:</strong> ${post.like_no}
                `;
                rankingSection.appendChild(listItem);
            });
        } catch (error) {
            console.error("Error fetching top posts:", error);
        }
    }

    // Refresh the ranking every 30 seconds
    setInterval(fetchTopPosts, 30000);

    // Initial fetch on page load
    fetchTopPosts();





    // document.getElementById("dateFilter").addEventListener("change", async (event) => {
    //     const order = event.target.value; // "new" or "old"

    //     try {
    //         const response = await fetch(`index.php?action=filter_posts&order=${order}`);
    //         if (!response.ok) {
    //             throw new Error("Failed to fetch posts");
    //         }

    //         const posts = await response.json();
    //         const postsSection = document.getElementById("postsSection");
    //         postsSection.innerHTML = ""; // Clear current posts

    //         posts.forEach(post => {
    //             const postElement = document.createElement("div");
    //             postElement.classList.add("post");
    //             postElement.setAttribute("data-post-id", post.post_id);
    //             postElement.innerHTML = `
    //                 <div class='post-user-info'>
    //                     <p>User ID: ${post.user_id}, Username: ${post.username}, Created at: ${post.created_at}</p>
    //                 </div>
    //                 <h3>${post.title}</h3>
    //                 <p>${post.content}</p>
    //                 <p><strong>Hashtags:</strong> ${post.hashtags}</p>
    //                 <p><strong>Likes:</strong> ${post.like_no}</p>
    //                 <button class='like-button' data-post-id='${post.post_id}'>
    //                     <span class='heart-icon'>🤍</span>
    //                     <span class='like-count'>${post.like_no}</span>
    //                 </button>
    //             `;
    //             postsSection.appendChild(postElement);
    //         });
    //     } catch (error) {
    //         console.error("Error fetching filtered posts:", error);
    //     }
    // });


    // document.getElementById("dateFilter").addEventListener("change", async (event) => {
    //     const order = event.target.value; // "new" or "old"
    
    //     try {
    //         const response = await fetch(`index.php?action=filter_posts&order=${order}`);
    //         if (!response.ok) {
    //             throw new Error("Failed to fetch posts");
    //         }
    
    //         const posts = await response.json();
    //         const postsSection = document.getElementById("postsSection");
    //         postsSection.innerHTML = ""; // Clear current posts
    
    //         posts.forEach(post => {
    //             const postElement = document.createElement("div");
    //             postElement.classList.add("post");
    //             postElement.setAttribute("data-post-id", post.post_id);
    //             postElement.innerHTML = `
    //                 <div class='post-user-info'>
    //                     <p>User ID: ${post.user_id}, Username: ${post.username}, Created at: ${post.created_at}</p>
    //                 </div>
    //                 <h3>${post.title}</h3>
    //                 <p>${post.content}</p>
    //                 <p><strong>Hashtags:</strong> ${post.hashtags}</p>
    //                 <p><strong>Likes:</strong> ${post.like_no}</p>
    //                 ${post.post_image ? `<img src="data:image/jpeg;base64,${post.post_image}" alt="Post Image" style="max-width: 100%; height: auto;">` : ''}
    //                 <button class='like-button' data-post-id='${post.post_id}'>
    //                     <span class='heart-icon'>🤍</span>
    //                     <span class='like-count'>${post.like_no}</span>
    //                 </button>
    //             `;
    //             postsSection.appendChild(postElement);
    //         });
    //     } catch (error) {
    //         console.error("Error fetching filtered posts:", error);
    //     }
    // });
    

    document.getElementById("dateFilter").addEventListener("change", async (event) => {
        const order = event.target.value; // "new" or "old"
    
        try {
            const response = await fetch(`index.php?action=filter_posts&order=${order}`);
            if (!response.ok) {
                throw new Error("Failed to fetch posts");
            }
    
            const posts = await response.json();
            const postsSection = document.getElementById("postsSection");
            postsSection.innerHTML = ""; // Clear current posts
    
            posts.forEach(post => {
                const postElement = document.createElement("div");
                postElement.classList.add("post");
                postElement.setAttribute("data-post-id", post.post_id);
    
                let postHTML = `
                    <div class='post-user-info'>
                        <p>User ID: ${post.user_id}, Username: ${post.username}, Created at: ${post.created_at}</p>
                    </div>
                    <h3>${post.title}</h3>
                    <p>${post.content}</p>
                    <p><strong>Hashtags:</strong> ${post.hashtags}</p>
                    <p><strong>Likes:</strong> ${post.like_no}</p>
                    ${post.post_image ? `<img src="data:image/jpeg;base64,${post.post_image}" alt="Post Image" style="max-width: 100%; height: auto;">` : ''}
                    <div class='post-actions'>
                        <button class='like-button' data-post-id='${post.post_id}'>
                            <span class='heart-icon'>🤍</span>
                            <span class='like-count'>${post.like_no}</span>
                        </button>
                        <button class='edit-button' data-post-id='${post.post_id}'>Edit</button>
                        <button class='reply-button' data-post-id='${post.post_id}'>Reply</button>
                        <button class='delete-button' data-post-id='${post.post_id}'>Delete</button>
                        <button class='report-button' data-post-id='${post.post_id}'>Report</button>
                    </div>
                    <div class='replies'>
                `;
    
                post.replies.forEach(reply => {
                    postHTML += `
                        <div class='reply' data-reply-id='${reply.reply_id}'>
                            <div class='reply-user-info'>
                                <p>User ID: ${reply.user_id}, Username: ${reply.username}, Created at: ${reply.created_at}</p>
                            </div>
                            <p>${reply.content}</p>
                            ${reply.reply_image ? `<img src="data:image/jpeg;base64,${reply.reply_image}" alt="Reply Image">` : ''}
                            <div class='reply-actions'>
                                <button class='reply-edit-button' data-reply-id='${reply.reply_id}'>Edit</button>
                                <button class='reply-reply-button' data-reply-id='${reply.reply_id}'>Reply</button>
                                <button class='reply-delete-button' data-reply-id='${reply.reply_id}'>Delete</button>
                                <button class='reply-report-button' data-reply-id='${reply.reply_id}'>Report</button>
                            </div>
                        </div>
                    `;
                });
    
                postHTML += `</div>`; // Close replies
                postElement.innerHTML = postHTML;
                postsSection.appendChild(postElement);
            });
        } catch (error) {
            console.error("Error fetching filtered posts:", error);
        }
    });
    



});
