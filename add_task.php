<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize user input
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];

    $user_id = $_SESSION['user_id'];

    // Insert the task into the database
    $conn = new mysqli('localhost', 'root', '', 'MySQL');
    $query = "INSERT INTO tasks (user_id, title, description, due_date, status, priority) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $user_id, $title, $description, $due_date, $status, $priority);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit;
    } else {
        echo "Task creation failed.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Task</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }

        input[type="text"],
        textarea,
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
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
        <h1>Add Task</h1>
        <form method="POST" action="add_task.php">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" placeholder="Title" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" placeholder="Description"></textarea>

            <label for="due_date">Due Date</label>
            <input type="date" name="due_date" id="due_date" required>

            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="Not Started">Not Started</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
            <select name="priority">
                <option value="high">High</option>
                <option value="medium" >Medium</option>
                <option value="low" >Low</option>
            </select>


            <button type="submit">Add Task</button>
        </form>
    </div>
</body>

</html>