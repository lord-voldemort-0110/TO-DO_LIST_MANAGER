<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Retrieve user's tasks from the database
$mysqli = new mysqli('localhost', 'root', '', 'MySQL');

if (isset($_GET['search'])) {
    // Get the search query
    $search = $_GET['search'];

    // SQL query to retrieve tasks filtered by user_id and title
    $query = "SELECT * FROM tasks WHERE user_id = ? AND title LIKE ? ORDER BY due_date ASC";

    // Prepare the query
    $stmt = $mysqli->prepare($query);

    // Bind parameters
    //$searchTerm = $search;
    $stmt->bind_param("is", $user_id, $search);

    // Execute the query
    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result) {
        die("Error: " . $mysqli->error);
    }
} else {
    // If search is not submitted, retrieve all tasks
    $query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC";

    // Prepare the query
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user_id);

    // Execute the query
    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result) {
        die("Error: " . $mysqli->error);
    }
} ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        a {
            text-decoration: none;
            color: #007BFF;
        }

        .add-button {
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            text-align: center;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .completed {
            background-color: #d9edf7;
            /* Light blue for completed tasks */
        }

        /* Default styles for search container */
        .search-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container label {
            margin-right: 10px;
        }

        .search-container input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-container button[type="submit"] {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Responsive styles for smaller screens */
        @media (max-width: 600px) {
            .search-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-container label {
                margin-bottom: 5px;
            }

            .search-container input[type="text"] {
                width: 100%;
                max-width: none;
            }

            th,
            td {
                padding: 8px 10px;
            }

            .container {
                padding: 10px;
            }

            table {
                font-size: 14px;
            }

            .add-button {
                max-width: 150px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Your Task List</h2>
        <div class="search-container">
            <label for="search">Search Task:</label>
            <input type="text" id="search" name="search" placeholder="Enter task title...">
            <button type="submit">Search</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="<?= $row['status'] === 'Completed' ? 'completed' : '' ?>">
                        <td><?= $row['title'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['due_date'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><a href="edit_task.php?id=<?= $row['id'] ?>">Edit</a></td>
                        <td><a href="delete_task.php?id=<?= $row['id'] ?>">Delete</a></td>
                        <td>
    <select name="priority">
        <option value="high" <?php if ($row['priority'] === 'high') echo 'selected'; ?>>High</option>
        <option value="medium" <?php if ($row['priority'] === 'medium') echo 'selected'; ?>>Medium</option>
        <option value="low" <?php if ($row['priority'] === 'low') echo 'selected'; ?>>Low</option>
    </select>
</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a class="add-button" href="add_task.php">Add New Task</a>
    </div>

    <script>
        // JavaScript code to dynamically change row color when status changes
        const rows = document.querySelectorAll("tr");

        rows.forEach(row => {
            const statusCell = row.querySelector("td:nth-child(4)");

            // Assuming you have a way to change the status dynamically (e.g., through AJAX)
            // You can add an event listener to update the row's class when the status changes
            // Example:
            statusCell.addEventListener("click", () => {
                if (statusCell.textContent.trim() === "completed") {
                    row.classList.add("completed");
                } else {
                    row.classList.remove("completed");
                }
            });
        });
    </script>
</body>

</html>