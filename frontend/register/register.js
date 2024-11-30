dom = document.getElementById("registerForm");

dom.username.addEventListener("change", validateUsername);
dom.password.addEventListener("change", validatePassword);
dom.password2.addEventListener("change", validatePassword);
dom.email.addEventListener("change", validateEmail);
dom.email2.addEventListener("change", validateEmail2);
dom.age.addEventListener("change", validateAge);

dom.registerSubmit.addEventListener("click", verifyRegistration);
dom.addEventListener("submit", verifyRegistration);

var usernameValid = false;
var passwordValid = false;
var emailValid = false;
var email2Valid = false;
var sameEmail = false;
var ageValid = false;

var regUsername = /^[a-zA-Z0-9]{1,20}$/;
var regEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

var regAge = /^(?:[1-9][0-9]?|1[0-9]{2})$/;

function validateUsername() {
    if(!regUsername.test(dom.username.value)) {
        console.log("Invalid username");
        alert("Invalid username format. Use alphanumerical characters only, 1 - 20 chars long.");
        dom.username.style.boxShadow = "0px 0px 8px red";
        usernameValid = false;
    } else {
        console.log("Valid username: " + dom.username.value);
        dom.username.style.boxShadow = "0px 0px 4px green";
        usernameValid = true;
    }
}

function validatePassword() {
    if((dom.password.value != "" && dom.password2.value != "") && (dom.password.value != dom.password2.value)) {
        console.log("Invalid password");
        alert("Your passwords are not the same. Try again.");
        dom.password.style.boxShadow = "0px 0px 8px red";
        dom.password2.style.boxShadow = "0px 0px 8px red";
        passwordValid = false;
    } else {
        dom.password.style.boxShadow = "0px 0px 4px green";
        dom.password2.style.boxShadow = "0px 0px 4px green";
        passwordValid = true;
    }
}

function validateEmail() {
    if(!regEmail.test(dom.email.value) && dom.email.value != "") {
        console.log("Invalid password");
        alert("Please enter a valid email address.");
        dom.email.style.boxShadow = "0px 0px 8px red";
        emailValid = false;
    } 
    else {
        dom.email.style.boxShadow = "0px 0px 4px green";
        emailValid = true;
        validateSameEmail();
    }
}

function validateEmail2() {
    if(!regEmail.test(dom.email2.value) && dom.email2.value != "") {
        console.log("Invalid first name format.");
        alert("Please enter a valid email address.");
        dom.email2.style.boxShadow = "0px 0px 8px red";
        email2Valid = false;
    } else {
        console.log("Valid first name: " + dom.email2.value);
        dom.email2.style.boxShadow = "0px 0px 4px green";
        email2Valid = true;
        validateSameEmail();
    }
}

function validateSameEmail() {
    if(emailValid && email2Valid && (dom.email.value != dom.email2.value)) {
        sameEmail = false;
        alert("Your emails are not the same. Try again.");
        dom.email.style.boxShadow = "0px 0px 8px red";
        dom.email2.style.boxShadow = "0px 0px 8px red";
        
    } else if (emailValid && email2Valid && (dom.email.value === dom.email2.value)) {
        sameEmail = true;
        dom.email.style.boxShadow = "0px 0px 4px green";
        dom.email2.style.boxShadow = "0px 0px 4px green";
    }
}

function validateAge() {
    console.log("Inside validateAge function.");
    if(!regAge.test(dom.age.value)) {
        console.log("Invalid age.");
        alert("Invalid age format. Use number characters only 1 - 199.");
        dom.age.style.boxShadow = "0px 0px 8px red";
        ageValid = false;
    } else {
        console.log("Valid age: " + dom.age.value);
        dom.age.style.boxShadow = "0px 0px 4px green";
        ageValid = true;
    } 
}

function verifyRegistration(event) {
    console.log("Inside verifyRegistration function.");
    if(usernameValid && passwordValid && emailValid && email2Valid && sameEmail && ageValid) {
        alert("Sending credentials to Database...");
    }
    else {
        event.preventDefault();
        alert("Fix the errors on the form before resubmitting.");

        if(!usernameValid) {
            dom.username.style.boxShadow = "0px 0px 8px red";
        }
        if(!passwordValid) {
            dom.password.style.boxShadow = "0px 0px 8px red";
            dom.password2.style.boxShadow = "0px 0px 8px red";
        }
        if(!emailValid || !email2Valid || !sameEmail) {
            dom.email.style.boxShadow = "0px 0px 8px red";
            dom.email2.style.boxShadow = "0px 0px 8px red";
        }
        if(!ageValid){
            dom.age.style.boxShadow = "0px 0px 8px red";
        }
    }
}