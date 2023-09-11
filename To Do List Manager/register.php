<?php
// Initialize a variable for error message
$error = "";

// Handle user registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize user input
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the username is already taken
    $conn = new mysqli('localhost', 'root', '', 'MySQL');
    $check_query = "SELECT id FROM users WHERE username=?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Username already taken. Please choose another username.";
    } else {
        // Insert the user into the database if the username is not taken
        $insert_query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ss", $username, $password);

        if ($insert_stmt->execute()) {
            // Redirect to the login page or another appropriate page
            header('Location: login.php');
            exit;
        } else {
            $error = "Registration failed. Please try again later.";
        }
    }

    $check_stmt->close();
    //$insert_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }
        .error-message {
            color: red;
            font-weight: bold;
            
        }
        .signup-link {
            margin-top: 15px;
        }
        @media screen and (max-width: 480px) {
            .container {
                width: 100%;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Registration Form</h1>
        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required>

            <button type="submit">Register</button>
        </form>
        <p class="signup-link">Already have an account? <a href="login.php">login</a></p>
        <div class="error-message"><?php echo $error; ?></div>
    </div>
</body>
</html>


