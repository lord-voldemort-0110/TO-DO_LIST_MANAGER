<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize user input
    $task_id = $_POST['task_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];

    $user_id = $_SESSION['user_id'];

    // Update the task in the database
    $conn = new mysqli('localhost', 'root', '', 'MySQL');
    $query = "UPDATE tasks SET title=?, description=?, due_date=?, status=?, priority=? WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssii", $title, $description, $due_date, $status, $priority, $task_id, $user_id);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit;
    } else {
        echo "Task update failed.";
    }

    $stmt->close();
    $conn->close();
} else {
    // Retrieve the task details to populate the edit form
    $task_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $conn = new mysqli('localhost', 'root', '', 'MySQL');
    $query = "SELECT * FROM tasks WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "Task not found.";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        select {
            height: 40px;
        }

        textarea {
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            form {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <?php header('Location: login.php'); ?>
    <?php else: ?>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <!-- Handle form submission and database update -->
        <?php else: ?>
            <!-- Display task edit form -->
            <form method="POST" action="edit_task.php">
                <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                <input type="text" name="title" placeholder="Title" value="<?= $row['title'] ?>" required>
                <textarea name="description" placeholder="Description"><?= $row['description'] ?></textarea>
                <input type="date" name="due_date" value="<?= $row['due_date'] ?>" required>
                <select name="status">
                    <option value="Not Started" <?= $row['status'] == 'Not Started' ? 'selected' : '' ?>>Not Started</option>
                    <option value="In Progress" <?= $row['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
                <select name="priority">
                    <option value="high" <?php if ($row['priority'] === 'high')
                        echo 'selected'; ?>>High</option>
                    <option value="medium" <?php if ($row['priority'] === 'medium')
                        echo 'selected'; ?>>Medium</option>
                    <option value="low" <?php if ($row['priority'] === 'low')
                        echo 'selected'; ?>>Low</option>
                </select>
                <button type="submit">Update Task</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>