<?php
// Start the session to access session variables
session_start();

// Set the content type header to indicate that the response will be JSON
header('Content-Type: application/json');

// Check if the 'loggedin_user' session variable is set
if (isset($_SESSION['loggedin_user'])) {
    // If logged in, send back a JSON object indicating loggedIn is true and the username
    echo json_encode(['loggedIn' => true, 'username' => $_SESSION['loggedin_user']]);
} else {
    // If not logged in, send back a JSON object indicating loggedIn is false and username is null
    echo json_encode(['loggedIn' => false, 'username' => null]);
}
// It's good practice to exit after sending JSON response, especially in small scripts like this
exit;
?>