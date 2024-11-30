dom = document.getElementById("registerForm");

dom.username.addEventListener("change", validateUsername);
dom.password.addEventListener("change", validatePassword);
dom.password2.addEventListener("change", validatePassword);
dom.email.addEventListener("change", validateEmail);
dom.email2.addEventListener("change", validateEmail2);

/*
dom.firstNameInput.addEventListener("change", validateFirstName);
dom.lastNameInput.addEventListener("change", validateLastName);
*/
dom.age.addEventListener("change", validateAge);

dom.registerSubmit.addEventListener("click", verifyRegistration);
dom.addEventListener("submit", verifyRegistration);



var usernameValid = false;
var passwordValid = false;
var emailValid = false;
var email2Valid = false;

/*
var firstNameValid = false;
var lastNameValid = false;
*/

var ageValid = false;

var regUsername = /^[a-zA-Z0-9]{1,20}$/;
var regEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
//var regName = /^[a-zA-Z]{1,20}$/;
var regAge = /^(?:[1-9][0-9]?|1[0-9]{2})$/;

function validateUsername() {
    console.log("Inside validateUsername function.");
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
    console.log("Inside validatePassword function.");
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
    console.log("Inside validateEmail function.");
    if(!regEmail.test(dom.email.value)) {
        console.log("Invalid password");
        alert("Please enter a valid email address.");
        dom.email.style.boxShadow = "0px 0px 8px red";
        emailValid = false;
    } 
    else {
        dom.email.style.boxShadow = "0px 0px 4px green";
        emailValid = true;
    }
}

function validateEmail2() {
    console.log("Inside validateEmail2 function.");
    if(!regEmail.test(dom.email2.value)) {
        console.log("Invalid first name format.");
        alert("Please enter a valid email address.");
        dom.email2.style.boxShadow = "0px 0px 8px red";
        email2Valid = false;

    } else {
        console.log("Valid first name: " + dom.email2.value);
        dom.email2.style.boxShadow = "0px 0px 4px green";
        email2Valid = true;
    }
}
/*
function validateFirstName() {
    console.log("Inside validateFirstName function.");
    if(!regName.test(dom.firstNameInput.value)) {
        console.log("Invalid first name format.");
        alert("Invalid name format. Use alphabetical characters only, 1 - 20 chars long.");
        dom.firstNameInput.style.boxShadow = "0px 0px 8px red";
        firstNameValid = false;
    } else {
        console.log("Valid first name: " + dom.firstNameInput.value);
        dom.firstNameInput.style.boxShadow = "0px 0px 4px green";
        firstNameValid = true;
    }
}*/


/*
function validateLastName() {
    console.log("Inside validateLastName function.");
    if(!regName.test(dom.lastNameInput.value)) {
        console.log("Invalid last name.");
        alert("Invalid name format. Use alphabetical characters only, 1 - 20 chars long.");
        dom.lastNameInput.style.boxShadow = "0px 0px 8px red";
        lastNameValid = false;
    } else {
        console.log("Valid last name: " + dom.lastNameInput.value);
        dom.lastNameInput.style.boxShadow = "0px 0px 4px green";
        lastNameValid = true;
    }   
}
    */

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
    //event.preventDefault();
    if(usernameValid && passwordValid && emailValid && email2Valid && ageValid) {
        alert("Sending credentials to Database...");
    }
    else {
        alert("Fix the errors on the form before resubmitting.");
    }
}