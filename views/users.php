<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Users</h1>
        <hr>
        <div class="row mb-3">
            <div class="col">
                <a href="/users/login-activity" class="btn btn-info">View Login Activity</a>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">Add User</button>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo $user['Username']; ?></td>
                        <td><?php echo $user['Role']; ?></td>
                        <td><?php echo $user['LastLogin']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editUserModal" data-user='<?php echo json_encode($user); ?>'>Edit</button>
                            <a href="/users/delete?id=<?php echo $user['UserID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/users/add" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="Warehouse Associate">Warehouse Associate</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Manager">Manager</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/users/edit" method="POST">
                    <input type="hidden" name="userID" id="editUserID">
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" name="username" id="editUsername" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Password (leave blank to keep current password)</label>
                        <input type="password" name="password" id="editPassword" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="editRole">Role</label>
                        <select name="role" id="editRole" class="form-control" required>
                            <option value="Warehouse Associate">Warehouse Associate</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Manager">Manager</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#editUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var user = button.data('user');
        var modal = $(this);
        modal.find('#editUserID').val(user.UserID);
        modal.find('#editUsername').val(user.Username);
        modal.find('#editRole').val(user.Role);
    });
</script>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
