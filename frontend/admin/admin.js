// Mock data for users with different statuses
var users = [
    {
        userID: "user1",
        email: "user1@example.com",
        password: "password1",
        status: "good",
        loginTime: "2024-10-31 09:00",
        logoutTime: "2024-10-31 17:00",
        posts: [
            { content: "First post content", timestamp: "2024-10-31 10:00" },
            { content: "Second post content", timestamp: "2024-10-31 12:00" }
        ],
        reports: [
            { content: "Reported post content by user1", timestamp: "2024-10-31 11:00" }
        ]
    },
    {
        userID: "user2",
        email: "user2@example.com",
        password: "password2",
        status: "24h timeout",
        loginTime: "2024-10-30 09:00",
        logoutTime: "2024-10-30 17:00",
        posts: [
            { content: "First post content by user2", timestamp: "2024-10-30 10:00" },
            { content: "Second post content by user2", timestamp: "2024-10-30 12:00" }
        ],
        reports: []
    },
    {
        userID: "user3",
        email: "user3@example.com",
        password: "password3",
        status: "72h timeout",
        loginTime: "2024-10-29 08:30",
        logoutTime: "2024-10-29 17:30",
        posts: [
            { content: "First post content by user3", timestamp: "2024-10-29 09:00" },
            { content: "Second post content by user3", timestamp: "2024-10-29 13:00" }
        ],
        reports: []
    },
    {
        userID: "user4",
        email: "user4@example.com",
        password: "password4",
        status: "banned",
        loginTime: "2024-10-28 08:00",
        logoutTime: "2024-10-28 16:30",
        posts: [
            { content: "First post content by user4", timestamp: "2024-10-28 09:00" },
            { content: "Second post content by user4", timestamp: "2024-10-28 13:00" }
        ],
        reports: []
    }
  ];
  
  // Show modal
  function showReports() {
    document.getElementById("reportModal").style.display = "flex";
    loadReports();
  }
  
  function showUserLog() {
    document.getElementById("userModal").style.display = "flex";
    loadUserList();
  }
  
  function showBlacklist() {
    document.getElementById("blacklistModal").style.display = "flex";
    loadBlacklist();
  }
  
  // Sign out function
  function signOut() {
    if (confirm("Are you sure you want to sign out?")) {
        alert("You have been signed out.");
        location.reload();
    }
  }
  
  // Close modal
  function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
  }
  
  // Load Report data
  function loadReports() {
    var reportDetails = document.getElementById("reportDetails");
    reportDetails.innerHTML = "";
  
    users.forEach(function(user) {
        user.reports.forEach(function(report, index) {
            var reportElement = document.createElement("div");
            reportElement.innerHTML = `<p>Report #${index + 1} from ${user.userID}: ${report.content} <em>(${report.timestamp})</em></p>`;
  
            var actionButton = document.createElement("button");
            actionButton.textContent = "Flag as Inappropriate";
            actionButton.onclick = function() {
                if (confirm("Flag this post as inappropriate?")) {
                    applyTimeout(user);  // Apply timeout based on report
                    alert("Timeout has been applied.");
                }
            };
            reportElement.appendChild(actionButton);
            reportDetails.appendChild(reportElement);
        });
    });
  }
  
  // Apply timeout based on user status
  function applyTimeout(user) {
    if (user.status === "good") {
        user.status = "24h timeout";
    } else if (user.status === "24h timeout") {
        user.status = "72h timeout";
    } else if (user.status === "72h timeout") {
        user.status = "banned";
    }
  }
  
  // Load User List with clickable user IDs
  function loadUserList() {
    var userDetails = document.getElementById("userDetails");
    userDetails.innerHTML = "";  // Clear previous content
  
    users.forEach(function(user) {
        var userElement = document.createElement("p");
        userElement.innerHTML = `<a href="#" onclick="showUserDetails('${user.userID}')">${user.userID}</a>`;
        userDetails.appendChild(userElement);
    });
  }
  
  // Show individual user details in a separate modal
  function showUserDetails(userID) {
    var user = users.find(function(u) { return u.userID === userID; });
    if (user) {
        var userDetailModal = document.getElementById("userDetailModal");
        
        // If the modal does not exist, create it
        if (!userDetailModal) {
            userDetailModal = document.createElement("div");
            userDetailModal.id = "userDetailModal";
            userDetailModal.className = "modal";
            userDetailModal.innerHTML = `
                <div class="modal-content">
                    <span class="close" onclick="closeModal('userDetailModal')">&times;</span>
                    <div id="userDetailInfo" class="scrollable-content"></div>
                </div>
            `;
            document.body.appendChild(userDetailModal);
        }
  
        var userDetailInfo = document.getElementById("userDetailInfo");
        userDetailInfo.innerHTML = `
            <p><strong>User ID:</strong> ${user.userID}</p>
            <p><strong>Email:</strong> ${user.email}</p>
            <p><strong>Password:</strong> ${user.password}</p>
            <p><strong>Status:</strong> ${user.status}</p>
            <p><strong>Login Time:</strong> ${user.loginTime}</p>
            <p><strong>Logout Time:</strong> ${user.logoutTime}</p>
            <h3>Posts:</h3>
            <ul>
                ${user.posts.map(post => `<li>${post.content} <em>(${post.timestamp})</em></li>`).join('')}
            </ul>
        `;
  
        // Add a dropdown for selecting blacklist options
        var blacklistSelect = document.createElement("select");
        blacklistSelect.innerHTML = `
            <option value="">Select Blacklist Option</option>
            <option value="24h timeout">24h Timeout</option>
            <option value="72h timeout">72h Timeout</option>
            <option value="banned">Ban</option>
        `;
  
        var blacklistButton = document.createElement("button");
        blacklistButton.textContent = "Apply Blacklist Action";
  
        // Add functionality to apply the selected blacklist option
        blacklistButton.onclick = function() {
            var selectedOption = blacklistSelect.value;
            if (selectedOption && confirm(`Are you sure you want to apply "${selectedOption}" to ${user.userID}?`)) {
                user.status = selectedOption;
                alert(`"${selectedOption}" has been applied to ${user.userID}.`);
                loadUserList();  // Refresh user list with updated status
                closeModal("userDetailModal");
            } else if (!selectedOption) {
                alert("Please select an option from the dropdown.");
            }
        };
  
        userDetailInfo.appendChild(blacklistSelect);
        userDetailInfo.appendChild(blacklistButton);
  
        // Display the user detail modal
        userDetailModal.style.display = "flex";
    }
  }
  
  // Load Blacklist data
  function loadBlacklist() {
    var blacklistDetails = document.getElementById("blacklistDetails");
    blacklistDetails.innerHTML = "";
  
    users.filter(function(user) {
        return ["24h timeout", "72h timeout", "banned"].includes(user.status);
    }).forEach(function(user) {
        var blacklistElement = document.createElement("div");
        blacklistElement.innerHTML = `<p><strong>User ID:</strong> ${user.userID} - ${user.status}</p>`;
  
        var removeButton = document.createElement("button");
        removeButton.textContent = "Remove from Blacklist";
        removeButton.onclick = function() {
            if (confirm(`Are you sure you want to remove ${user.userID} from the blacklist?`)) {
                user.status = "good";
                alert(`${user.userID} has been removed from the blacklist.`);
                loadBlacklist();  // Refresh data after status change
            }
        };
        blacklistElement.appendChild(removeButton);
        blacklistDetails.appendChild(blacklistElement);
    });
  }