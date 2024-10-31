dom = document.getElementById("loginForm");

dom.usernameInput.addEventListener("change", validateUsername);
dom.passwordInput.addEventListener("change", validatePassword);

dom.loginSubmit.addEventListener("click", verifyCredentials);
dom.addEventListener("submit", verifyCredentials);

var username = "";
var password = "";

var regUsername = /^[a-zA-Z0-9]{3,20}$/;

/*
What should username input be?
Just do alphanum for now
*/

function validateUsername() {
    console.log("Inside validateUsername function.");
    if(!regUsername.test(dom.usernameInput.value)) {
        console.log("Invalid username");
        console.log(dom.usernameInput.value);
        alert("Invalid username format. Use alphanumerical.");
        dom.usernameInput.style.boxShadow = "0px 0px 8px red";
        //unnecessary to validate password input
    } else {
        console.log("Test successful, it works.");
        username = dom.usernameInput.value;
        console.log("Testing username: " + username);
        dom.usernameInput.style.boxShadow = "none";

    }
}

function validatePassword() {
    console.log("Inside validatePassword function.");
    //unnecessary to validate password input
    //only verify input for security
}

function verifyCredentials(event) {
    event.preventDefault();
    console.log("Inside verifyCredentials function.");
    alert("Will verify credentials with Database...");
    console.log("Form not submitted successfully.");    
}
