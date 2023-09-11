<?php
$message = "";
// Handle user login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize user input
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to retrieve user data
    $conn = new mysqli('localhost', 'root', '', 'MySQL');
    $query = "SELECT id, username, password FROM users WHERE username=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['id'];
            header('Location: dashboard.php');
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "User not found.";
    }

    $stmt->close();
    $conn->close();

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            justify-content: center;
            
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
        <h1>Login</h1>
        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>
        <p class="signup-link">Don't have an account? <a href="register.php">Sign up</a></p>
        <div class="error-message"><?php echo $message; ?></div>
    </div>
    
</body>
</html>
