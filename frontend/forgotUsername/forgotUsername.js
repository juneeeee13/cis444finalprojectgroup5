var dom = document.getElementById("forgotUsernameForm");

dom.email.addEventListener("change", validateEmail);

dom.addEventListener("submit", verifyEmail);

function validateEmail() {
    console.log("Inside validate email function.");

}

function verifyEmail(event) {
    alert("Placeholder request to Database.");
    event.preventDefault();
}
