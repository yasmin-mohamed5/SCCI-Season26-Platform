const form = document.getElementById("form");

form.addEventListener("submit", function (e) {
    e.preventDefault();

    const errors = form.querySelectorAll(".error-msg");
    errors.forEach(err => err.remove());

    let isValid = true;

    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const phoneInput = document.getElementById("phone");
    const passwordInput = document.getElementById("password");
    const workshopSelect = document.getElementById("workshop");
    const roleSelect = document.getElementById("roleID");
    const githubInput = document.getElementById("github");
    const linkedinInput = document.getElementById("linkedin");
    const imageInput = document.getElementById("image");

    // Name validation
    const nameValue = nameInput.value.trim();
    const nameParts = nameValue.split(" ").filter(part => part !== "");
    if (nameValue === "") {
        showError(nameInput, "Name is required");
        isValid = false;
    } else if (nameParts.length < 2) {
        showError(nameInput, "Please enter your full name (first and last name)");
        isValid = false;
    }

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailValue = emailInput.value.trim();
    if (emailValue === "") {
        showError(emailInput, "Email is required");
        isValid = false;
    } else if (!emailPattern.test(emailValue)) {
        showError(emailInput, "Enter a valid email");
        isValid = false;
    }

    // Phone validation (Egyptian)
    const phonePattern = /^01[0-2,5][0-9]{8}$/;
    const phoneValue = phoneInput.value.trim();
    if (phoneValue === "") {
        showError(phoneInput, "Phone number is required");
        isValid = false;
    } else if (!phonePattern.test(phoneValue)) {
        showError(phoneInput, "Enter a valid Egyptian phone number");
        isValid = false;
    }

    // Password validation (letters + numbers + symbols)
    const passwordValue = passwordInput.value.trim();
    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{6,}$/;
    if (passwordValue === "") {
        showError(passwordInput, "Password is required");
        isValid = false;
    } else if (passwordValue.length < 6) {
        showError(passwordInput, "Password must be at least 6 characters");
        isValid = false;
    } else if (!passwordPattern.test(passwordValue)) {
        showError(passwordInput, "Password must contain letters, numbers, and symbols (!@#$%^&*)");
        isValid = false;
    }

    // Workshop validation
    if (workshopSelect.value === "") {
        showError(workshopSelect, "Please select a workshop");
        isValid = false;
    }

    // Role validation
    if (roleSelect.value === "") {
        showError(roleSelect, "Please select a role");
        isValid = false;
    }

    // GitHub & LinkedIn (basic URL validation)
    const urlPattern = /^(https?:\/\/)?([\w\d-]+\.){1,2}[\w]{2,}(\/\S*)?$/i;

    if (githubInput.value.trim() !== "" && !urlPattern.test(githubInput.value.trim())) {
        showError(githubInput, "Enter a valid GitHub URL");
        isValid = false;
    }

    if (linkedinInput.value.trim() !== "" && !urlPattern.test(linkedinInput.value.trim())) {
        showError(linkedinInput, "Enter a valid LinkedIn URL");
        isValid = false;
    }

    if (imageInput.files.length === 0) {
        showError(imageInput, "Please choose an image");
        isValid = false;
    } else {
        const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
        if (!allowedTypes.includes(imageInput.files[0].type)) {
            showError(imageInput, "Only JPEG, PNG, GIF are allowed");
            isValid = false;
        }
    }

    if (isValid) {
        form.submit();
    }
});


function showError(input, message) {
    // Prevent error message from repeating
    if (input.parentElement.querySelector(".error-msg")) return;
    
    const error = document.createElement("div");
    error.className = "error-msg";
    error.style.color = "red";
    error.style.fontSize = "0.9rem";
    error.style.marginTop = "5px";
    error.style.textAlign = "left"; // Error على الشمال
    error.textContent = message;
    input.parentElement.appendChild(error);
}
