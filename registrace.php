<?php
// Start the session
session_start();

// Define the path to the file where user data is stored
$users_file = 'users.txt';
$error_message = '';    // Variable to store any error messages
$success_message = '';  // Variable to store success message

// If the user is already logged in, redirect them to the main page
if (isset($_SESSION['loggedin_user'])) {
    header('Location: index.html'); // Redirect to HTML main page
    exit; // Stop further script execution
}

// Check if the form was submitted using POST method and the register button was pressed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Get the username and password from the form, trimming whitespace from username
    $username_reg = trim($_POST['username']);
    $password_reg = $_POST['password'];

    // --- Input Validations ---
    if (empty($username_reg) || empty($password_reg)) {
        $error_message = 'Prosím, vyplňte všechny údaje.';
    } elseif (strlen($password_reg) < 6) { // Basic password length check
        $error_message = 'Heslo musí mít alespoň 6 znaků.';
    } elseif (preg_match('/[:\s]/', $username_reg)) { // Check for forbidden characters
        $error_message = 'Uživatelské jméno nesmí obsahovat dvojtečky ani mezery.';
    } else {
        // --- Check if username already exists ---
        $user_exists = false;
        if (file_exists($users_file)) {
            $users = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($users as $user_line) {
                // Make sure the line has a colon before trying to explode
                if (strpos($user_line, ':') !== false) {
                    list($stored_username, ) = explode(':', $user_line, 2);
                    if ($username_reg === $stored_username) {
                        $user_exists = true;
                        break;
                    }
                }
            }
        }

        if ($user_exists) {
            $error_message = 'Uživatelské jméno již existuje. Zvolte prosím jiné.';
        } else {
            // --- Register the new user ---
            // Hash the password for security before storing it
            $hashed_password = password_hash($password_reg, PASSWORD_DEFAULT);
            // Create the line to be stored in the file (username:hashed_password)
            $new_user_line = $username_reg . ':' . $hashed_password . PHP_EOL;

            // Append the new user to the users file
            if (file_put_contents($users_file, $new_user_line, FILE_APPEND | LOCK_EX) !== false) {
                $success_message = 'Registrace byla úspěšná! Nyní se můžete přihlásit.';
            } else {
                $error_message = 'Nepodařilo se zaregistrovat. Zkuste to prosím znovu.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace - 90Tom fun!</title>
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
        .success-message { color: green; margin-bottom: 15px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Registrace</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <form method="post" action="registrace.php">
            <input type="text" name="username" placeholder="Uživatelské jméno" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"><br>
            <input type="password" name="password" placeholder="Heslo (min. 6 znaků)" required><br>
            <input type="submit" name="register" value="Registrovat">
        </form>
        <p class="link">Máte již účet? <a href="login.php">Přihlaste se</a></p>
        <p class="link"><a href="index.html">Zpět na hlavní stránku</a></p>
    </div>
</body>
</html>