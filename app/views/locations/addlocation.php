<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified">
    <!-- Header -->
    <div class="row align-items-center theme-header">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/locations/locations" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Locations
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1><i class="fas fa-plus"></i> Add Warehouse Location</h1>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('location_message'); ?>

    <!-- Add Location Form -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> New Location Details</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/locations/addlocation" method="post"
                        style="text-transform: none;">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="location_type" class="form-label">
                                    <strong>Location Type:</strong> <span class="text-danger">*</span>
                                </label>
                                <select name="location_type" id="location_type" class="form-control" required>
                                    <option value="">Choose location type...</option>
                                    <option value="bulk">Bulk Storage (B- prefix)</option>
                                    <option value="receiving">Receiving Area (B-RECV-)</option>
                                    <option value="temporary">Temporary Storage (B-TEMP-)</option>
                                    <option value="regular">Regular Storage (A1-, B1-, C1-, etc.)</option>
                                    <option value="secure">Secure Storage (SEC-)</option>
                                    <option value="display">Display Area (DISP-)</option>
                                    <option value="overflow">Overflow Storage (OVER-)</option>
                                    <option value="custom">Custom Location</option>
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Select the type to auto-generate naming format
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location_name" class="form-label">
                                    <strong>Location Name:</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="location_name" id="location_name"
                                    class="form-control <?php echo (!empty($data['location_name_err'])) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $data['location_name']; ?>"
                                    placeholder="e.g., A1-1, B-BULK-01, Main Floor - A1"
                                    style="text-transform: uppercase;" required>
                                <span class="invalid-feedback"><?php echo $data['location_name_err']; ?></span>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Location names will be automatically converted to
                                    uppercase
                                </small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="rack" class="form-label">
                                    <strong>Rack:</strong>
                                </label>
                                <input type="text" name="rack" id="rack" class="form-control"
                                    value="<?php echo $data['rack']; ?>" placeholder="e.g., A, B, C"
                                    style="text-transform: uppercase;" maxlength="10">
                                <small class="form-text text-muted">Rack identifier</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="shelf" class="form-label">
                                    <strong>Shelf:</strong>
                                </label>
                                <input type="text" name="shelf" id="shelf" class="form-control"
                                    value="<?php echo $data['shelf']; ?>" placeholder="e.g., 1, 2, 3" maxlength="10">
                                <small class="form-text text-muted">Shelf identifier</small>
                            </div>
                        </div>

                        <!-- Location Type Helper -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-lightbulb"></i> Location Naming Guidelines:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Regular Storage:</strong>
                                            <ul class="mb-0 small">
                                                <li>Format: RACK-SECTION (e.g., A1-1, A1-2)</li>
                                                <li>Use for individual product storage</li>
                                                <li>Easy to locate specific items</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Bulk Storage:</strong>
                                            <ul class="mb-0 small">
                                                <li>Format: B-NAME (e.g., B-BULK-01, B-TEMP-A)</li>
                                                <li>Use for temporary/bulk storage</li>
                                                <li>Receiving and overflow areas</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-asterisk text-danger"></i> Required fields
                                        </small>
                                    </div>
                                    <div>
                                        <a href="<?php echo URLROOT; ?>/locations/locations"
                                            class="btn btn-secondary mr-2">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Create Location
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Presets -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-magic"></i> Quick Add Presets</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Click a preset to auto-fill the form:</p>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm btn-block"
                                onclick="fillPresetWithType('regular', 'A1-', 'A1', '1')">
                                <i class="fas fa-cube"></i> Regular A1-x
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm btn-block"
                                onclick="fillPresetWithType('bulk', 'B-BULK-', 'BULK', '01')">
                                <i class="fas fa-boxes"></i> Bulk Storage
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-warning btn-sm btn-block"
                                onclick="fillPresetWithType('receiving', 'B-RECV-', 'RECV', '01')">
                                <i class="fas fa-truck"></i> Receiving Area
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-info btn-sm btn-block" onclick="clearForm()">
                                <i class="fas fa-eraser"></i> Clear Form
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for form enhancements -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-capitalize inputs
        const inputs = document.querySelectorAll('input[style*="text-transform: uppercase"]');
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });

        // Location type dropdown handler
        const locationTypeSelect = document.getElementById('location_type');
        const locationNameInput = document.getElementById('location_name');
        const rackInput = document.getElementById('rack');
        const shelfInput = document.getElementById('shelf');

        locationTypeSelect.addEventListener('change', function () {
            const selectedType = this.value;
            let namePrefix = '';
            let rackValue = '';
            let shelfValue = '';

            switch (selectedType) {
                case 'bulk':
                    namePrefix = 'B-BULK-';
                    rackValue = 'BULK';
                    shelfValue = '01';
                    break;
                case 'receiving':
                    namePrefix = 'B-RECV-';
                    rackValue = 'RECV';
                    shelfValue = '01';
                    break;
                case 'temporary':
                    namePrefix = 'B-TEMP-';
                    rackValue = 'TEMP';
                    shelfValue = '01';
                    break;
                case 'regular':
                    namePrefix = 'A1-';
                    rackValue = 'A1';
                    shelfValue = '1';
                    break;
                case 'secure':
                    namePrefix = 'SEC-';
                    rackValue = 'SEC';
                    shelfValue = '01';
                    break;
                case 'display':
                    namePrefix = 'DISP-';
                    rackValue = 'DISP';
                    shelfValue = 'A';
                    break;
                case 'overflow':
                    namePrefix = 'OVER-';
                    rackValue = 'OVER';
                    shelfValue = '1';
                    break;
                case 'custom':
                    // Clear all fields for custom entry
                    locationNameInput.value = '';
                    rackInput.value = '';
                    shelfInput.value = '';
                    locationNameInput.focus();
                    return;
            }

            if (namePrefix) {
                locationNameInput.value = namePrefix;
                rackInput.value = rackValue;
                shelfInput.value = shelfValue;

                // Focus on location name for completion
                locationNameInput.focus();
                locationNameInput.setSelectionRange(namePrefix.length, namePrefix.length);
            }
        });

        // Auto-generate suggestions for rack input
        rackInput.addEventListener('input', function () {
            if (this.value && !locationNameInput.value && !locationTypeSelect.value) {
                locationNameInput.value = this.value.toUpperCase() + '1-';
            }
        });

        // Update preset buttons to also set the dropdown
        window.fillPresetWithType = function (type, namePrefix, rack, shelf) {
            locationTypeSelect.value = type;
            locationNameInput.value = namePrefix.toUpperCase();
            rackInput.value = rack.toUpperCase();
            shelfInput.value = shelf;

            // Focus on location name for completion
            locationNameInput.focus();
            const input = locationNameInput;
            input.setSelectionRange(input.value.length, input.value.length);
        };
    });

    function fillPreset(namePrefix, rack, shelf) {
        document.getElementById('location_name').value = namePrefix.toUpperCase();
        document.getElementById('rack').value = rack.toUpperCase();
        document.getElementById('shelf').value = shelf;

        // Focus on location name for completion
        document.getElementById('location_name').focus();
        const input = document.getElementById('location_name');
        input.setSelectionRange(input.value.length, input.value.length);
    }

    function clearForm() {
        document.getElementById('location_type').value = '';
        document.getElementById('location_name').value = '';
        document.getElementById('rack').value = '';
        document.getElementById('shelf').value = '';
        document.getElementById('location_type').focus();
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>