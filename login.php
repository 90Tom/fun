<?php
// Start the session to manage user login state
session_start();

// Define the path to the file where user data is stored
$users_file = 'users.txt';
$error_message = ''; // Variable to store any error messages

// If the user is already logged in, redirect them to the main page
if (isset($_SESSION['loggedin_user'])) {
    header('Location: index.html'); // Redirect to HTML main page
    exit; // Stop further script execution
}

// Check if the form was submitted using POST method and the login button was pressed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Get the username and password from the form, trimming whitespace from username
    $username_attempt = trim($_POST['username']);
    $password_attempt = $_POST['password'];

    // Validate that both fields are filled
    if (empty($username_attempt) || empty($password_attempt)) {
        $error_message = 'Prosím, vyplňte uživatelské jméno i heslo.';
    } else {
        // Check if the users file exists
        if (file_exists($users_file)) {
            // Read all lines from the users file into an array
            $users = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $loggedin = false; // Flag to check if login was successful

            // Loop through each user line in the file
            foreach ($users as $user_line) {
                // Ensure the line has a colon before trying to explode
                if (strpos($user_line, ':') !== false) {
                    list($stored_username, $stored_hashed_password) = explode(':', $user_line, 2);

                    // Compare the submitted username with the stored username
                    // And verify the submitted password against the stored hashed password
                    if ($username_attempt === $stored_username && password_verify($password_attempt, $stored_hashed_password)) {
                        $_SESSION['loggedin_user'] = $stored_username; // Set the session variable
                        $loggedin = true; // Set login flag to true
                        header('Location: index.html'); // Redirect to HTML main page
                        exit; // Stop further script execution
                    }
                }
            }
            // If loop finishes and user is not logged in, credentials were incorrect
            if (!$loggedin) {
                $error_message = 'Nesprávné uživatelské jméno nebo heslo.';
            }
        } else {
            // If the users file doesn't exist, no users can be logged in
            $error_message = 'Žádní uživatelé nejsou registrováni.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení - 90Tom fun!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif; color: #333; line-height: 1.6;
            background-image: url('IndexImages/backgroundindex.png'); background-size: cover;
            background-position: center; background-attachment: fixed;
            display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.85); padding: 30px; border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15); text-align: center; max-width: 400px; width: 100%;
        }
        h2 { color: #e83e8c; margin-bottom: 25px; font-size: 2em; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd;
            border-radius: 6px; font-size: 1em;
        }
        input[type="submit"] {
            background-color: #e83e8c; color: white; padding: 12px 20px; border: none;
            border-radius: 6px; cursor: pointer; font-size: 1.1em; font-weight: 600; transition: background-color 0.3s ease;
            width: 100%;
        }
        input[type="submit"]:hover { background-color: #c42d78; }
        .link { margin-top: 20px; font-size: 0.9em; }
        .link a { color: #0056b3; text-decoration: none; font-weight: 500; }
        .link a:hover { text-decoration: underline; }
        .error-message { color: red; margin-bottom: 15px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Přihlášení</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
            <input type="text" name="username" placeholder="Uživatelské jméno" required><br>
            <input type="password" name="password" placeholder="Heslo" required><br>
            <input type="submit" name="login" value="Přihlásit se">
        </form>
        <p class="link">Nemáte účet? <a href="registrace.php">Zaregistrujte se</a></p>
        <p class="link"><a href="index.html">Zpět na hlavní stránku</a></p>
    </div>
</body>
</html>