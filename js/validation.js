// validation.js checks the login form before it is sent.

// Wait until the HTML is parsed, otherwise getElementById finds nothing.
document.addEventListener("DOMContentLoaded", function () {
  var form = document.getElementById("loginForm");

  // The setup page has no #loginForm so it returns nothing
  if (!form) {
    return;
  }

  var username = document.getElementById("username");
  var password = document.getElementById("password");

  var usernameError = document.getElementById("usernameError");
  var passwordError = document.getElementById("passwordError");

  // Puts a message under a field and outlines it in red.
  function showError(input, slot, message) {
    slot.textContent = message;
    input.classList.add("has-error");
  }

  // Clears whatever showError put there.
  function clearError(input, slot) {
    slot.textContent = "";
    input.classList.remove("has-error");
  }

  // As soon as someone starts fixing a field, the error is dropped
  username.addEventListener("input", function () {
    clearError(username, usernameError);
  });

  password.addEventListener("input", function () {
    clearError(password, passwordError);
  });

  form.addEventListener("submit", function (event) {
    var valid = true;

    clearError(username, usernameError);
    clearError(password, passwordError);

    if (username.value.trim() === "") {
      showError(username, usernameError, "Enter your username.");
      valid = false;
    }

    if (password.value === "") {
      showError(password, passwordError, "Enter your password.");
      valid = false;
    }

    if (!valid) {
      // Stop the form submitting and put the cursor in the first problem field.
      event.preventDefault();

      if (username.classList.contains("has-error")) {
        username.focus();
      } else {
        password.focus();
      }
    }
  });
});
