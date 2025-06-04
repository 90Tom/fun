<?php
// Start the session to access session variables
session_start();

// Unset all of the session variables
$_SESSION = array(); 
// This empties the $_SESSION superglobal array.
// Alternative: session_unset(); also removes all session variables.

// If it's desired to kill the session completely, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params(); // Get current cookie parameters
    setcookie(session_name(), '', time() - 42000, // Set cookie expiration to the past
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session from the server-side.
session_destroy();

// Redirect the user back to the main HTML page after logout
header('Location: index.html');
exit; // Stop further script execution
?>