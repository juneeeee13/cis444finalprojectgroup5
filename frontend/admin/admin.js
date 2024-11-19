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
        ],
        warnCount: 0
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
        reports: [],
        warnCount: 2
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
        reports: [],
        warnCount: 3
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
        reports: [],
        warnCount: 4
    }
];

// Navigation functions
function navigateToReports() {
    window.location.href = "see_reports.html";
}

function navigateToUserLog() {
    window.location.href = "user_log.html";
}

function navigateToBlacklist() {
    window.location.href = "blacklists.html";
}

function navigateToDashboard() {
    window.location.href = "admin.html";
}

// Sign out function
function signOut() {
    if (confirm("Are you sure you want to sign out?")) {
        alert("You have been signed out.");
        location.reload();
    }
}


// // User Log Logic
document.addEventListener("DOMContentLoaded", () => {
    const userList = document.getElementById("userList");
    const userDetails = document.getElementById("userDetails");
    const userSearchBar = document.getElementById("userSearchBar");

    if (userList && userDetails && userSearchBar) {
        function displayUserList(filter = "") {
            userList.innerHTML = "";
            userDetails.style.display = "none";
            users
                .filter(user => user.userID.toLowerCase().includes(filter.toLowerCase()))
                .forEach(user => {
                    const userItem = document.createElement("p");
                    userItem.textContent = user.userID;
                    userItem.style.cursor = "pointer";
                    userItem.addEventListener("click", () => displayUserDetails(user));
                    userList.appendChild(userItem);
                });
        }

        function displayUserDetails(user) {
            userDetails.innerHTML = `
                <p><strong>User ID:</strong> ${user.userID}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Status:</strong> ${user.status}</p>
                <p><strong>Warn Count:</strong> <span id="user-warn-${user.userID}">${user.warnCount || 0}</span></p>
                <p><strong>Login Time:</strong> ${user.loginTime}</p>
                <p><strong>Logout Time:</strong> ${user.logoutTime}</p>
                                <button onclick="addToBlacklist('${user.userID}')">Add to the blacklist</button>
                <div id="blacklist-options-${user.userID}" style="display:none; margin-top:10px;">
                    <button onclick="updateStatus('${user.userID}', '24h timeout')">Set to 24h timeout</button>
                    <button onclick="updateStatus('${user.userID}', '72h timeout')">Set to 72h timeout</button>
                    <button onclick="updateStatus('${user.userID}', 'banned')">Set to banned</button>
                </div>
                <hr>
            `;
            userDetails.style.display = "block";
        }

        // Function to show blacklist options
        window.addToBlacklist = function (userID) {
            const optionsDiv = document.getElementById(`blacklist-options-${userID}`);
            if (optionsDiv) optionsDiv.style.display = "block";
        };

        // Function to update user status
        window.updateStatus = function (userID, newStatus) {
            const user = users.find(u => u.userID === userID);
            if (user) {
                user.status = newStatus; // Update the user's status
                alert(`${user.userID} status updated to "${newStatus}"`);

                // Update the status in User Details
                const statusSpan = document.getElementById(`user-status-${userID}`);
                if (statusSpan) statusSpan.textContent = newStatus;

                // Hide the options after selection
                const optionsDiv = document.getElementById(`blacklist-options-${userID}`);
                if (optionsDiv) optionsDiv.style.display = "none";

                // Refresh the blacklist display if visible
                const blacklistSearchBar = document.getElementById("blacklistSearchBar");
                if (blacklistSearchBar) displayBlacklist(blacklistSearchBar.value);
            }
        };

        displayUserList();
        userSearchBar.addEventListener("input", () => displayUserList(userSearchBar.value));
    }

    // Blacklist Logic
    const blacklistDetails = document.getElementById("blacklistDetails");
    const blacklistSearchBar = document.getElementById("blacklistSearchBar");

    if (blacklistDetails && blacklistSearchBar) {
        function displayBlacklist(filter = "") {
            blacklistDetails.innerHTML = "";
            users
                .filter(user => ['24h timeout', '72h timeout', 'banned'].includes(user.status) &&
                    user.userID.toLowerCase().includes(filter.toLowerCase()))
                .forEach(user => {
                    const userDiv = document.createElement("div");
                    userDiv.innerHTML = `
                    <p>
                        <strong>User ID:</strong> ${user.userID} - 
                        <strong>Status:</strong> ${user.status}
                        <button onclick="removeFromBlacklist('${user.userID}')">Remove from the blacklist</button>
                    </P>
                `;
                    blacklistDetails.appendChild(userDiv);
                });
        }

        // Function to remove a user from the blacklist
        window.removeFromBlacklist = function (userID) {
            const user = users.find(u => u.userID === userID);
            if (user) {
                user.status = "good"; // Update the status of the user
                alert(`${user.userID} has been removed from the blacklist.`);
                displayBlacklist(blacklistSearchBar.value); // Refresh the blacklist display
            }
        };

        displayBlacklist();
        blacklistSearchBar.addEventListener("input", () => displayBlacklist(blacklistSearchBar.value));
    }

    // See Reports Logic
    const reportDetails = document.getElementById("reportDetails");

    if (reportDetails) {
        function displayReports() {
            reportDetails.innerHTML = "";
            users.forEach(user => {
                user.reports.forEach((report, index) => {
                    const reportDiv = document.createElement("div");
                    reportDiv.innerHTML = `
                        <p><strong>Report #${index + 1}:</strong> ${report.content} <em>(${report.timestamp})</em></p>
                        <button onclick="increaseWarnCount('${user.userID}')">Warn</button>
                        <span id="warn-${user.userID}">Warn Count: ${user.warnCount || 0}</span>
                    `;
                    reportDetails.appendChild(reportDiv);
                });
            });
        }

        //function increaseWarnCount(userID) {
        window.increaseWarnCount = function (userID) {
            const user = users.find(u => u.userID === userID);
            if (user) {
                user.warnCount = (user.warnCount || 0) + 1;
                document.getElementById(`warn-${userID}`).textContent = `Warn Count: ${user.warnCount}`;

                // Update warn count in User Activity Log if visible
                const warnSpan = document.getElementById(`user-warn-${userID}`);
                if (warnSpan) {
                    warnSpan.textContent = user.warnCount;
                }
            }

        };

        displayReports();
    }
});
