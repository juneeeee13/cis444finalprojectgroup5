dom = document.getElementById("loginForm");

dom.username.addEventListener("change", validateUsername);

var regUsername = /^[a-zA-Z0-9]{3,20}$/;
var regPassword = /^[a-zA-Z0-9]{3,264}/;

function validateUsername() {
    if(!regUsername.test(dom.username.value)) {
        alert("Invalid username format. Use alphanumerical.");
        dom.username.style.boxShadow = "0px 0px 8px red";
        //unnecessary to validate password input
    } else {
        dom.username.style.boxShadow = "none";
    }
}