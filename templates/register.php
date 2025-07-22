<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hardware Shop Management</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Register</h2>
        <?php if (isset($_GET['error'])): ?>
            <p class="error">
                <?php
                if ($_GET['error'] === 'username_taken') {
                    echo 'Username is already taken.';
                } else {
                    echo 'An error occurred during registration.';
                }
                ?>
            </p>
        <?php endif; ?>
        <form action="../src/actions/register_action.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <br>
            <label for="role_id">Role:</label>
            <select name="role_id" id="role_id">
                <option value="1">Admin</option>
                <option value="2">Manager</option>
                <option value="3">Supervisor</option>
                <option value="4">Warehouse Associate</option>
            </select>
            <br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
