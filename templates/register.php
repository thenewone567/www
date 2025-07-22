<?php require_once '../src/includes/header.php'; ?>

<h2>Register</h2>
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

<?php require_once '../src/includes/footer.php'; ?>
