dom = document.getElementById("registerForm");

dom.emailInput.addEventListener("changed", verifyEmailInput);

function verifyEmailInput() {
    console.log("Inside verifyEmailInput function.");
}

function verifyRegistration() {
    console.log("Inside verifyRegistration function.");
}