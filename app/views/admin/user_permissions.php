<!-- PERMISSIONS TAB (moved to separate view) -->
<div class="tab-pane fade admin-section" id="permissions" role="tabpanel">
    <div class="card-theme">
        <div class="card-header bg-white border-bottom">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-shield mr-2"></i>User Permissions Management
            </h5>
            <small class="text-muted">Control which pages each user can access</small>
        </div>
        <div class="card-body">
            <div id="message-container"></div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th width="50%">Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['users'])): ?>
                            <?php foreach ($data['users'] as $user): ?>
                                <tr id="user-row-<?php echo $user->user_id; ?>">
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($user->name ?? $user->username); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($user->email ?? ''); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $user->role_name === 'admin' ? 'danger' : ($user->role_name === 'super_admin' ? 'dark' : 'primary'); ?>">
                                            <?php echo ucfirst($user->role_name); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $user->status === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($user->status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form class="permissions-form" data-user-id="<?php echo $user->user_id; ?>">
                                            <div class="row">
                                                <?php foreach ($data['available_pages'] as $page => $info): ?>
                                                    <div class="col-md-6 col-sm-6 mb-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input permission-checkbox" 
                                                                   type="checkbox" 
                                                                   id="<?php echo $user->user_id; ?>_<?php echo $page; ?>" 
                                                                   name="permissions[<?php echo $page; ?>]" 
                                                                   value="1"
                                                                   <?php echo (isset($user->permissions[$page]) && $user->permissions[$page]) ? 'checked' : ''; ?>
                                                                   <?php echo ($user->role_name === 'super_admin') ? 'disabled' : ''; ?> />
                                                            <label class="form-check-label small" for="<?php echo $user->user_id; ?>_<?php echo $page; ?>">
                                                                <?php echo $info['label']; ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php if ($user->role_name === 'super_admin'): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> Super admin has all permissions by default
                                                </small>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if ($user->role_name !== 'super_admin'): ?>
                                            <button type="button" class="btn btn-sm btn-primary save-permissions" data-user-id="<?php echo $user->user_id; ?>">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary reset-permissions ml-1" data-user-id="<?php echo $user->user_id; ?>">
                                                <i class="fas fa-undo"></i> Reset
                                            </button>
                                        <?php else: ?>
                                            <small class="text-muted">No action needed</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fas fa-users"></i> No users found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h6>Available Permissions:</h6>
                <div class="row">
                    <?php foreach ($data['available_pages'] as $page => $info): ?>
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="card-theme border-left-primary py-2">
                                <div class="card-body py-2">
                                    <strong><?php echo $info['label']; ?></strong>
                                    <p class="small text-muted mb-0"><?php echo $info['description']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Save permissions
    $('.save-permissions').off('click').on('click', function() {
        const userId = $(this).data('user-id');
        const form = $(`.permissions-form[data-user-id="${userId}"]`);
        const formData = new FormData();
        formData.append('user_id', userId);
        
        // Get all checked permissions
        form.find('.permission-checkbox:checked').each(function() {
            const permissionName = $(this).attr('name').match(/permissions\[(.+)\]/)[1];
            formData.append(`permissions[${permissionName}]`, '1');
        });
        
        // Disable save button and show loading
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '<?php echo URLROOT; ?>/admin/userPermissions',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        showMessage('success', result.message);
                    } else {
                        showMessage('error', result.message);
                    }
                } catch (e) {
                    showMessage('error', 'Invalid response from server');
                }
            },
            error: function() {
                showMessage('error', 'An error occurred while saving permissions');
            },
            complete: function() {
                // Re-enable save button
                $(`.save-permissions[data-user-id="${userId}"]`).prop('disabled', false).html('<i class="fas fa-save"></i> Save');
            }
        });
    });
    
    // Reset permissions
    $('.reset-permissions').off('click').on('click', function() {
        const userId = $(this).data('user-id');
        const form = $(`.permissions-form[data-user-id="${userId}"]`);
        
        if (confirm('Are you sure you want to reset all permissions for this user?')) {
            form.find('.permission-checkbox').prop('checked', false);
            showMessage('info', 'Permissions reset. Don\'t forget to save the changes.');
        }
    });
    
    function showMessage(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const messageHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('#message-container').html(messageHtml);
        
        // Auto-dismiss success messages after 3 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('#message-container .alert').alert('close');
            }, 3000);
        }
    }
});
</script>
