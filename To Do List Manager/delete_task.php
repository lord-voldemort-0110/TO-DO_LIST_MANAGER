<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Delete the task from the database
    $conn = new mysqli('localhost', 'root', '', 'MySQL');
    $query = "DELETE FROM tasks WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error_message = "Task deletion failed.";
    }

    $stmt->close();
    $conn->close();
} else {
    $error_message = "Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Deletion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 100px auto;
            padding: 20px;
            max-width: 400px;
        }

        h1 {
            color: #333;
        }

        .error-message {
            color: #ff0000;
            margin-top: 10px;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Task Deletion</h1>
        <?php if (isset($error_message)) { ?>
            <p class="error-message"><?= $error_message ?></p>
        <?php } else { ?>
            <p>Task deleted successfully!</p>
        <?php } ?>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
