<?php require_once '../src/includes/header.php'; ?>

<h2>Login</h2>
<form action="../src/actions/login_action.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <br>
    <button type="submit">Login</button>
</form>

<?php require_once '../src/includes/footer.php'; ?>
