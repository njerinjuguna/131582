// Function to search for a user.
function searchUser(name) {
    fetchSearch(name);
}

// Function to search for a username.
function searchUsername(name) {
    fetchUsername(name);
}

// Function to verify if a username is available.
function verifyUsername(b) {
    var unameWarning = document.getElementById("usernameWarning");
    var signupButton = document.getElementById("signupBtn");

    // If 'b' is 1, it means the username is not available.
    if (b == 1) {
        unameWarning.removeAttribute("hidden"); // Show a warning message.
        signupButton.disabled = true; // Disable the signup button.
    } else {
        unameWarning.setAttribute("hidden", true); // Hide the warning message.
        signupButton.disabled = false; // Enable the signup button.
    }
}

// Function to fetch username availability from the server.
function fetchUsername(name) {
    fetch('https://ehealth.co.ke/unameverify.php', {
        method: 'POST',
        body: new URLSearchParams('name=' + name)
    })
    .then(res => res.json())
    .then(res => verifyUsername(res))
    .catch(e => console.error('Error: ' + e))
}

// Function to fetch user search results from the server.
function fetchSearch(name) {
    fetch('https://ehealth.co.ke/usearch.php', {
        method: 'POST',
        body: new URLSearchParams('name=' + name)
    })
    .then(res => res.json())
    .then(res => showUserList(res))
    .catch(e => console.error('Error: ' + e))
}
