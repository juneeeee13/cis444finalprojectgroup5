dom = document.getElementById("loginForm");

dom.username.addEventListener("change", validateUsername);
dom.password.addEventListener("change", validatePassword);

dom.loginSubmit.addEventListener("click", verifyCredentials);
dom.addEventListener("submit", verifyCredentials);

var regUsername = /^[a-zA-Z0-9]{3,20}$/;
var regPassword = /^[a-zA-Z0-9]{3,264}/;

/*
What should username input be?
Just do alphanum for now
*/

function validateUsername() {
    console.log("Inside validateUsername function.");
    if(!regUsername.test(dom.username.value)) {
        console.log("Invalid username");
        console.log(dom.username.value);
        alert("Invalid username format. Use alphanumerical.");
        dom.username.style.boxShadow = "0px 0px 8px red";
        //unnecessary to validate password input
    } else {
        console.log("Test successful, it works.");
        console.log("Testing username: " + dom.username.value);
        dom.username.style.boxShadow = "none";

    }
}

function validatePassword() {
    console.log("Inside validatePassword function.");
    //unnecessary to validate password input
    //only verify input for security
}

function verifyCredentials(event) {
    //event.preventDefault();
    console.log("Inside verifyCredentials function.");
    alert("Will verify credentials with Database...");
    console.log("Form not submitted successfully.");    
}
