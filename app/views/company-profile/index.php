<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<h1>Company Profile</h1>
<?php flash('company_profile_message'); ?>
<?php if (!empty($data['errors']['general'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($data['errors']['general']); ?></div>
<?php endif; ?>

<?php
// Fallback: if no logo set but legacy mlogo.png exists
if (empty($data['company_logo']) && file_exists(APPROOT . DS . 'uploads' . DS . 'logo' . DS . 'mlogo.png')) {
    $data['company_logo'] = 'uploads/logo/mlogo.png';
}
?>
<?php if ($data['mode'] === 'view'): ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <?php if (!empty($data['company_logo'])): ?>
                    <img src="<?php echo URLROOT . '/' . htmlspecialchars($data['company_logo']); ?>" alt="Logo"
                        style="max-height:80px;max-width:160px;object-fit:contain;margin-right:20px;border:1px solid #ddd;padding:4px;background:#fff;">
                <?php else: ?>
                    <div
                        style="height:80px;width:160px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;border:1px dashed #ccc;color:#888;">
                        No Logo</div>
                <?php endif; ?>
                <h3 class="mb-0"><?php echo htmlspecialchars($data['company_name'] ?: 'Company Name Not Set'); ?></h3>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>GST:</strong> <?php echo htmlspecialchars($data['company_gst']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($data['company_email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($data['company_phone']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Currency:</strong> <?php echo htmlspecialchars($data['currency']); ?></p>
                    <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($data['company_address'])); ?></p>
                </div>
            </div>
            <a href="<?php echo URLROOT; ?>/company-profile?edit=1" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
<?php else: ?>
    <form action="<?php echo URLROOT; ?>/company-profile" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="company_name">Company Name:</label>
            <input type="text" name="company_name"
                class="form-control <?php echo !empty($data['errors']['company_name']) ? 'is-invalid' : ''; ?>"
                value="<?php echo htmlspecialchars($data['company_name']); ?>">
            <?php if (!empty($data['errors']['company_name'])): ?>
                <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['company_name']); ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Current Logo:</label><br>
            <?php if (!empty($data['company_logo'])): ?>
                <img src="<?php echo URLROOT . '/' . htmlspecialchars($data['company_logo']); ?>" alt="Logo"
                    style="max-height:80px;max-width:160px;object-fit:contain;border:1px solid #ddd;padding:4px;background:#fff;">
            <?php else: ?>
                <span class="text-muted">No logo uploaded</span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="company_logo_file">Upload New Logo:</label>
            <input type="file" name="company_logo_file" class="form-control-file">
            <?php if (!empty($data['errors']['company_logo'])): ?>
                <div class="text-danger small mt-1"><?php echo htmlspecialchars($data['errors']['company_logo']); ?></div>
            <?php endif; ?>
            <small class="form-text text-muted">Allowed: png, jpg, jpeg, gif, webp. Max 5MB.</small>
            <?php
            $uMax = ini_get('upload_max_filesize');
            $pMax = ini_get('post_max_size');
            ?>
            <div class="text-muted mt-1" style="font-size:12px;">Server limits:
                upload_max_filesize=<?php echo htmlspecialchars($uMax); ?>,
                post_max_size=<?php echo htmlspecialchars($pMax); ?></div>
        </div>
        <div class="form-group">
            <label for="company_logo_url">OR Logo URL:</label>
            <input type="text" name="company_logo_url" class="form-control"
                value="<?php echo htmlspecialchars($data['company_logo']); ?>">
        </div>
        <div class="form-group">
            <label for="company_gst">Company GST:</label>
            <input type="text" name="company_gst" class="form-control"
                value="<?php echo htmlspecialchars($data['company_gst']); ?>">
        </div>
        <div class="form-group">
            <label for="currency">Currency:</label>
            <input type="text" name="currency" class="form-control"
                value="<?php echo htmlspecialchars($data['currency']); ?>">
        </div>
        <div class="form-group">
            <label for="company_address">Company Address:</label>
            <textarea name="company_address" class="form-control"
                rows="3"><?php echo htmlspecialchars($data['company_address']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="company_email">Company Email:</label>
            <input type="email" name="company_email"
                class="form-control <?php echo !empty($data['errors']['company_email']) ? 'is-invalid' : ''; ?>"
                value="<?php echo htmlspecialchars($data['company_email']); ?>">
            <?php if (!empty($data['errors']['company_email'])): ?>
                <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['company_email']); ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="company_phone">Company Phone:</label>
            <input type="text" name="company_phone" class="form-control"
                value="<?php echo htmlspecialchars($data['company_phone']); ?>">
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="<?php echo URLROOT; ?>/company-profile" class="btn btn-secondary ml-2">Cancel</a>
    </form>
<?php endif; ?>

</div> <!-- End container-fluid -->
</div> <!-- End page-content-wrapper -->
</div> <!-- End wrapper -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>

</html>