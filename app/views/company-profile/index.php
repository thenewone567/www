<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<h1>Company Profile</h1>
<?php flash('company_profile_message'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var f = document.getElementById('msg-flash');
        if (f) {
            var txt = (f.textContent || f.innerText || '').trim();
            try { console.log('Submission result: ' + txt); } catch (e) { }
            try { alert('Submission successful! ' + txt); } catch (e) { }
        }
    });
</script>
<?php if (!empty($data['errors']['general'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($data['errors']['general']); ?></div>
<?php endif; ?>

<?php
// Fallback: if no logo set but legacy mlogo.png exists
if (empty($data['company_logo']) && file_exists(APPROOT . DS . 'uploads' . DS . 'logo' . DS . 'mlogo.png')) {
    $data['company_logo'] = 'uploads/logo/mlogo.png';
}
?>
<div class="card mb-3">
    <div class="card-body">
        <!-- Company Logo Section -->
        <div class="d-flex align-items-center mb-4">
            <div class="logo-section">
                <?php if (!empty($data['company_logo'])): ?>
                    <img id="company-logo-display" src="<?php echo URLROOT . '/' . htmlspecialchars($data['company_logo']); ?>" alt="Logo"
                        style="max-height:80px;max-width:160px;object-fit:contain;border:1px solid #ddd;padding:4px;background:#fff;">
                <?php else: ?>
                    <div id="company-logo-display"
                        style="height:80px;width:160px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;border:1px dashed #ccc;color:#888;">
                        No Logo</div>
                <?php endif; ?>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editField('logo')">
                        <i class="fa fa-edit"></i> Edit Logo
                    </button>
                </div>
            </div>
            <div class="ml-4">
                <div class="d-flex align-items-center">
                    <span id="company-name-display" class="h3 mb-0"><?php echo htmlspecialchars($data['company_name'] ?: company_name()); ?></span>
                    <button type="button" class="btn btn-sm btn-outline-primary ml-2" onclick="editField('company_name')">
                        <i class="fa fa-edit"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Company Details Grid -->
        <div class="row">
            <div class="col-md-6">
                <!-- GST Field -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>GST:</strong>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editField('company_gst')">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                    <span id="company-gst-display"><?php echo htmlspecialchars($data['company_gst'] ?: 'Not set'); ?></span>
                </div>

                <!-- Email Field -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Email:</strong>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editField('company_email')">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                    <span id="company-email-display"><?php echo htmlspecialchars($data['company_email'] ?: 'Not set'); ?></span>
                </div>

                <!-- Phone Field -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Phone:</strong>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editField('company_phone')">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                    <span id="company-phone-display"><?php echo htmlspecialchars($data['company_phone'] ?: 'Not set'); ?></span>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Currency Field -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Currency:</strong>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editField('currency')">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                    <span id="currency-display"><?php echo htmlspecialchars($data['currency'] ?: 'INR'); ?></span>
                </div>

                <!-- Address Field -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <strong>Address:</strong>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editField('company_address')">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                    <span id="company-address-display"><?php echo nl2br(htmlspecialchars($data['company_address'] ?: 'Not set')); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Individual Field Edit Modals/Forms -->
<div id="edit-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modal-title">Edit Field</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-field-form" action="<?php echo URLROOT; ?>/company-profile" method="post" enctype="multipart/form-data"
                onsubmit="try{console.log('Initiating submission...');}catch(e){}">
                <div class="modal-body" id="edit-modal-body">
                    <!-- Dynamic content will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editField(fieldName) {
    const modal = $('#edit-modal');
    const modalTitle = $('#edit-modal-title');
    const modalBody = $('#edit-modal-body');
    
    let title = '';
    let content = '';
    
    switch(fieldName) {
        case 'company_name':
            title = 'Edit Company Name';
            content = `
                <div class="form-group">
                    <label for="company_name">Company Name:</label>
                    <input type="text" name="company_name" class="form-control" 
                           value="<?php echo htmlspecialchars($data['company_name']); ?>">
                </div>
            `;
            break;
            
        case 'logo':
            title = 'Edit Company Logo';
            content = `
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
                    <small class="form-text text-muted">Allowed: png, jpg, jpeg, gif, webp. Max 5MB.</small>
                </div>
                <div class="form-group">
                    <label for="company_logo_url">OR Logo URL:</label>
                    <input type="text" name="company_logo_url" class="form-control"
                           value="<?php echo htmlspecialchars($data['company_logo']); ?>">
                </div>
            `;
            break;
            
        case 'company_gst':
            title = 'Edit GST Number';
            content = `
                <div class="form-group">
                    <label for="company_gst">Company GST:</label>
                    <input type="text" name="company_gst" class="form-control"
                           value="<?php echo htmlspecialchars($data['company_gst']); ?>">
                </div>
            `;
            break;
            
        case 'company_email':
            title = 'Edit Email';
            content = `
                <div class="form-group">
                    <label for="company_email">Company Email:</label>
                    <input type="email" name="company_email" class="form-control"
                           value="<?php echo htmlspecialchars($data['company_email']); ?>">
                </div>
            `;
            break;
            
        case 'company_phone':
            title = 'Edit Phone';
            content = `
                <div class="form-group">
                    <label for="company_phone">Company Phone:</label>
                    <input type="text" name="company_phone" class="form-control"
                           value="<?php echo htmlspecialchars($data['company_phone']); ?>">
                </div>
            `;
            break;
            
        case 'currency':
            title = 'Edit Currency';
            content = `
                <div class="form-group">
                    <label for="currency">Currency:</label>
                    <input type="text" name="currency" class="form-control"
                           value="<?php echo htmlspecialchars($data['currency']); ?>">
                </div>
            `;
            break;
            
        case 'company_address':
            title = 'Edit Address';
            content = `
                <div class="form-group">
                    <label for="company_address">Company Address:</label>
                    <textarea name="company_address" class="form-control" rows="3"><?php echo htmlspecialchars($data['company_address']); ?></textarea>
                </div>
            `;
            break;
    }
    
    modalTitle.text(title);
    modalBody.html(content);
    modal.modal('show');
}</script>

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