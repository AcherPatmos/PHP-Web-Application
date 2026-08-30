// Get the form
const loginForm = document.getElementById("loginForm");

// Listen for form submission
loginForm.addEventListener("submit", function (event) {

    // Stop form submission until validation passes
    event.preventDefault();

    // Get form values
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    // Get error message elements
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");

    // Clear previous error messages
    emailError.textContent = "";
    passwordError.textContent = "";

    // Validation flag
    let isValid = true;

    // Email validation
    if (email === "") {
        emailError.textContent = "Email is required.";
        isValid = false;
    } else {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(email)) {
            emailError.textContent = "Please enter a valid email address.";
            isValid = false;
        }
    }

    // Password validation
    if (password === "") {
        passwordError.textContent = "Password is required.";
        isValid = false;
    } else if (password.length < 8) {
        passwordError.textContent = "Password must be at least 8 characters.";
        isValid = false;
    }

    // Submit only if everything is valid
    if (isValid) {
        loginForm.submit();
    }

});