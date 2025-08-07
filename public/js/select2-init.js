/**
 * Global Select2 Initialization for Hardware Store App
 * Automatically initializes searchable dropdowns across all forms
 */

$(document).ready(function () {
    // Global Select2 configuration (moved outside to be accessible)
    window.initializeSelect2 = function () {
        $('.searchable-dropdown').each(function () {
            const $dropdown = $(this);

            // Skip if already initialized
            if ($dropdown.hasClass('select2-hidden-accessible')) {
                return;
            }

            const hasAddNew = $dropdown.find('option[value^="add_new"]').length > 0;

            // Start with basic configuration
            const config = {
                theme: 'bootstrap4',
                placeholder: $(this).data('placeholder') || 'Search...',
                allowClear: true,
                width: '100%'
            };

            // Only add advanced features for dropdowns with "Add New" options
            if (hasAddNew) {
                // Custom matcher to keep "Add New" options visible during search
                config.matcher = function (params, data) {
                    // If there are no search terms, return all of them
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // If this is an "Add New" option, always show it
                    if (data.id && data.id.toString().startsWith('add_new')) {
                        return data;
                    }

                    // Standard text matching for regular options
                    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                        return data;
                    }

                    // Return null if no match
                    return null;
                };

                // Custom sorter to keep "Add New" options at the bottom
                config.sorter = function (data) {
                    return data.sort(function (a, b) {
                        const aIsAddNew = a.id && a.id.toString().startsWith('add_new');
                        const bIsAddNew = b.id && b.id.toString().startsWith('add_new');

                        if (aIsAddNew && !bIsAddNew) return 1;
                        if (!aIsAddNew && bIsAddNew) return -1;

                        return a.text.localeCompare(b.text);
                    });
                };
            }

            try {
                $dropdown.select2(config);
            } catch (error) {
                console.error('Error initializing Select2 for dropdown:', error);
                // Fallback to very basic initialization
                try {
                    $dropdown.select2({
                        theme: 'bootstrap4',
                        width: '100%'
                    });
                } catch (fallbackError) {
                    console.error('Fallback Select2 initialization also failed:', fallbackError);
                }
            }
        });
    };

    // Initialize Select2 on page load
    initializeSelect2();

    // Also initialize after a short delay to ensure DOM is ready
    setTimeout(function () {
        initializeSelect2();
    }, 500);

    // Re-initialize Select2 when new elements are added to the DOM
    // Using MutationObserver instead of deprecated DOMNodeInserted
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) { // Element node
                    if ($(node).find('.searchable-dropdown').length || $(node).hasClass('searchable-dropdown')) {
                        initializeSelect2();
                    }
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Add event listener for enabling/disabling form controls
    $('.searchable-dropdown').on('select2:select select2:unselect', function () {
        if (typeof enableFormControls === 'function') {
            enableFormControls();
        }
    });

    // Common "Add New" handlers for all forms
    $('.searchable-dropdown').on('select2:select', function (e) {
        var selectedValue = e.params.data.id;
        var dropdown = $(this);

        if (selectedValue === 'add_new_category') {
            showAddCategoryModal(dropdown);
            $(this).val('').trigger('change');
        } else if (selectedValue === 'add_new_supplier') {
            showAddSupplierModal(dropdown);
            $(this).val('').trigger('change');
        } else if (selectedValue === 'add_new_brand') {
            showAddBrandModal(dropdown);
            $(this).val('').trigger('change');
        } else if ($(this).attr('id') === 'supplierSelect') {
            // Update supplier code when supplier is selected
            if (typeof updateSupplierCode === 'function') {
                updateSupplierCode(selectedValue);
            }
        }
    });

    // Handle clearing supplier selection
    $('.searchable-dropdown').on('select2:clear', function () {
        if ($(this).attr('id') === 'supplierSelect') {
            // Clear supplier code when supplier is cleared
            if (typeof updateSupplierCode === 'function') {
                updateSupplierCode('');
            }
        }
    });
});

// Modal functions for adding new items
function showAddCategoryModal(dropdown) {
    // Create modal if it doesn't exist
    if ($('#addCategoryModal').length === 0) {
        createCategoryModal();
    }

    // Clear form and show modal
    $('#addCategoryModal form')[0].reset();
    $('#addCategoryModal .is-invalid').removeClass('is-invalid');
    $('#addCategoryModal .invalid-feedback').hide();
    $('#addCategoryModal').data('dropdown', dropdown);
    $('#addCategoryModal').modal('show');
}

function createCategoryModal() {
    const modalHtml = `
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addCategoryForm">
                            <div class="form-group">
                                <label for="modalCategoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modalCategoryName" name="category_name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitCategoryForm()">Add Category</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('body').append(modalHtml);

    // Auto-formatting for category name
    $('#modalCategoryName').on('input', function () {
        if (this.value) {
            this.value = capitalizeWords(this.value);
        }
    });
}

function submitCategoryForm() {
    const form = $('#addCategoryForm');
    const categoryName = $('#modalCategoryName').val().trim();
    const submitBtn = $('.modal-footer .btn-primary');

    // Reset previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    // Validate
    if (!categoryName) {
        $('#modalCategoryName').addClass('is-invalid');
        $('#modalCategoryName').siblings('.invalid-feedback').text('Please enter a category name').show();
        return;
    }

    // Show loading state
    submitBtn.prop('disabled', true).text('Adding...');

    // Submit via AJAX to direct API endpoint
    $.ajax({
        url: window.location.origin + '/api/addCategory.php',
        method: 'POST',
        data: {
            category_name: categoryName
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (response) {
            // Hide modal
            $('#addCategoryModal').modal('hide');

            // Get the dropdown that triggered this modal
            const dropdown = $('#addCategoryModal').data('dropdown');

            // Refresh the dropdown options via AJAX
            refreshDropdownOptions(dropdown, 'categories');

            // Show success message
            showSuccessMessage('Category added successfully!');
        },
        error: function (xhr) {
            let errorMessage = 'An error occurred';

            try {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                if (xhr.responseText.includes('already exists')) {
                    errorMessage = 'Category name already exists';
                } else if (xhr.status === 401) {
                    errorMessage = 'Please log in to continue';
                } else {
                    errorMessage = 'Failed to add category. Please try again.';
                }
            }

            $('#modalCategoryName').addClass('is-invalid');
            $('#modalCategoryName').siblings('.invalid-feedback').text(errorMessage).show();
        },
        complete: function () {
            // Reset button state
            submitBtn.prop('disabled', false).text('Add Category');
        }
    });
}

function refreshDropdownOptions(dropdown, type) {
    let endpoint;
    if (type === 'categories') {
        endpoint = '/api/getCategories.php';
    } else if (type === 'suppliers') {
        endpoint = '/api/getSuppliers.php';
    } else if (type === 'brands') {
        endpoint = '/api/getBrands.php';
    }

    $.ajax({
        url: window.location.origin + endpoint,
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (data) {
            const currentValue = dropdown.val();
            dropdown.empty();
            dropdown.append('<option value="">Select ' + type.slice(0, -1) + '</option>');

            data.forEach(function (item) {
                let value, text;
                if (type === 'categories') {
                    value = item.category_id;
                    text = item.category_name;
                } else if (type === 'suppliers') {
                    value = item.supplier_id;
                    text = item.supplier_name;
                } else if (type === 'brands') {
                    value = item.brand_id;
                    text = item.brand_name;
                }
                dropdown.append('<option value="' + value + '">' + text + '</option>');
            });

            // Add the "Add New" option back
            if (type === 'categories') {
                dropdown.append('<option value="add_new_category">+ Add Category</option>');
            } else if (type === 'suppliers') {
                dropdown.append('<option value="add_new_supplier">+ Add Supplier</option>');
            } else if (type === 'brands') {
                dropdown.append('<option value="add_new_brand">+ Add Brand</option>');
            }

            // Re-initialize Select2 to apply custom matcher for "Add New" options
            dropdown.select2('destroy');
            dropdown.removeClass('select2-hidden-accessible');

            // Re-initialize with our custom configuration
            if (typeof window.initializeSelect2 === 'function') {
                window.initializeSelect2();
            }

            dropdown.trigger('change');
        },
        error: function (xhr) {
            console.error('Failed to refresh dropdown options:', xhr);
            showSuccessMessage('Failed to refresh dropdown options');
        }
    });
}

function showAddSupplierModal(dropdown) {
    // Create modal if it doesn't exist
    if ($('#addSupplierModal').length === 0) {
        createSupplierModal();
    }

    // Clear form and show modal
    $('#addSupplierModal form')[0].reset();
    $('#addSupplierModal .is-invalid').removeClass('is-invalid');
    $('#addSupplierModal .invalid-feedback').hide();
    $('#addSupplierModal').data('dropdown', dropdown);
    $('#addSupplierModal').modal('show');
}

function createSupplierModal() {
    const modalHtml = `
        <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addSupplierForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="modalSupplierName" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="modalSupplierName" name="supplier_name" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="modalSupplierCode" class="form-label">Supplier Code <span class="text-muted">(Auto-generated)</span></label>
                                        <input type="text" class="form-control bg-light" id="modalSupplierCode" name="supplier_code" readonly>
                                        <small class="form-text text-muted">Generated from company name + phone number</small>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="modalContactPerson" class="form-label">Contact Person</label>
                                        <input type="text" class="form-control" id="modalContactPerson" name="contact_person">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="modalPhone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="modalPhone" name="phone">
                                        <small class="form-text text-muted">Used for supplier code generation</small>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="modalEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="modalEmail" name="email">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="modalGstNumber" class="form-label">GST Number</label>
                                        <input type="text" class="form-control" id="modalGstNumber" name="gst_number" maxlength="15">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="modalAddress" class="form-label">Address</label>
                                        <textarea class="form-control" id="modalAddress" name="address" rows="2"></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitSupplierForm()">Add Supplier</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('body').append(modalHtml);

    // Auto-formatting for supplier fields
    $('#modalSupplierName').on('input', function () {
        if (this.value) {
            this.value = capitalizeWords(this.value);
        }
        generateSupplierCodePreview();
    });

    $('#modalContactPerson').on('input', function () {
        if (this.value) {
            this.value = capitalizeWords(this.value);
        }
    });

    $('#modalPhone').on('input', function () {
        generateSupplierCodePreview();
    });

    $('#modalGstNumber').on('input', function () {
        if (this.value) {
            this.value = this.value.toUpperCase();
        }
    });
}

// Function to generate supplier code preview
function generateSupplierCodePreview() {
    const supplierName = $('#modalSupplierName').val().trim();
    const phone = $('#modalPhone').val().trim();

    if (supplierName || phone) {
        // Get first 4 letters from company name (remove spaces and special characters)
        const cleanName = supplierName.replace(/[^A-Za-z]/g, '');
        let nameCode = cleanName.substring(0, 4).toUpperCase();

        // Pad with X if less than 4 characters
        nameCode = nameCode.padEnd(4, 'X');

        // Get last 4 digits from phone number
        const cleanPhone = phone.replace(/[^0-9]/g, '');
        let phoneCode = cleanPhone.slice(-4);

        // Pad with 0 if less than 4 digits
        phoneCode = phoneCode.padStart(4, '0');

        const supplierCode = nameCode + phoneCode;
        $('#modalSupplierCode').val(supplierCode);
    } else {
        $('#modalSupplierCode').val('');
    }
}

function submitSupplierForm() {
    const form = $('#addSupplierForm');
    const supplierName = $('#modalSupplierName').val().trim();
    const contactPerson = $('#modalContactPerson').val().trim();
    const phone = $('#modalPhone').val().trim();
    const email = $('#modalEmail').val().trim();
    const address = $('#modalAddress').val().trim();
    const gstNumber = $('#modalGstNumber').val().trim();
    const submitBtn = $('.modal-footer .btn-primary');

    // Reset previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    // Validate required fields
    let hasErrors = false;

    if (!supplierName) {
        $('#modalSupplierName').addClass('is-invalid');
        $('#modalSupplierName').siblings('.invalid-feedback').text('Please enter supplier name').show();
        hasErrors = true;
    }

    // Email validation (if provided)
    if (email && !isValidEmail(email)) {
        $('#modalEmail').addClass('is-invalid');
        $('#modalEmail').siblings('.invalid-feedback').text('Please enter a valid email address').show();
        hasErrors = true;
    }

    // GST number validation (if provided)
    if (gstNumber && !isValidGSTNumber(gstNumber)) {
        $('#modalGstNumber').addClass('is-invalid');
        $('#modalGstNumber').siblings('.invalid-feedback').text('Please enter a valid GST number (15 characters)').show();
        hasErrors = true;
    }

    if (hasErrors) {
        return;
    }

    // Show loading state
    submitBtn.prop('disabled', true).text('Adding...');

    // Submit via AJAX to direct API endpoint
    $.ajax({
        url: window.location.origin + '/api/addSupplier.php',
        method: 'POST',
        data: {
            supplier_name: supplierName,
            contact_person: contactPerson,
            phone: phone,
            email: email,
            address: address,
            gst_number: gstNumber
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (response) {
            // Hide modal
            $('#addSupplierModal').modal('hide');

            // Get the dropdown that triggered this modal
            const dropdown = $('#addSupplierModal').data('dropdown');

            // Refresh the dropdown options via AJAX
            refreshDropdownOptions(dropdown, 'suppliers');

            // Show success message with supplier code
            let message = 'Supplier added successfully!';
            if (response.supplier_code) {
                message += ' Supplier Code: ' + response.supplier_code;
            }
            showSuccessMessage(message);
        },
        error: function (xhr) {
            let errorMessage = 'An error occurred';

            try {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                if (xhr.responseText.includes('already exists')) {
                    errorMessage = 'Supplier name already exists';
                } else if (xhr.status === 401) {
                    errorMessage = 'Please log in to continue';
                } else {
                    errorMessage = 'Failed to add supplier. Please try again.';
                }
            }

            // Show error in the first field or general area
            if (errorMessage.includes('GST')) {
                $('#modalGstNumber').addClass('is-invalid');
                $('#modalGstNumber').siblings('.invalid-feedback').text(errorMessage).show();
            } else {
                $('#modalSupplierName').addClass('is-invalid');
                $('#modalSupplierName').siblings('.invalid-feedback').text(errorMessage).show();
            }
        },
        complete: function () {
            // Reset button state
            submitBtn.prop('disabled', false).text('Add Supplier');
        }
    });
}

function showAddBrandModal(dropdown) {
    // Create modal if it doesn't exist
    if ($('#addBrandModal').length === 0) {
        createBrandModal();
    }

    // Clear form and show modal
    $('#addBrandModal form')[0].reset();
    $('#addBrandModal .is-invalid').removeClass('is-invalid');
    $('#addBrandModal .invalid-feedback').hide();
    $('#addBrandModal').data('dropdown', dropdown);
    $('#addBrandModal').modal('show');
}

function createBrandModal() {
    const modalHtml = `
        <div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBrandModalLabel">Add New Brand</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addBrandForm">
                            <div class="form-group">
                                <label for="modalBrandName" class="form-label">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modalBrandName" name="brand_name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitBrandForm()">Add Brand</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('body').append(modalHtml);

    // Auto-formatting for brand name
    $('#modalBrandName').on('input', function () {
        if (this.value) {
            this.value = capitalizeWords(this.value);
        }
    });
}

function submitBrandForm() {
    const form = $('#addBrandForm');
    const brandName = $('#modalBrandName').val().trim();
    const submitBtn = $('.modal-footer .btn-primary');

    // Reset previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    // Validate
    if (!brandName) {
        $('#modalBrandName').addClass('is-invalid');
        $('#modalBrandName').siblings('.invalid-feedback').text('Please enter a brand name').show();
        return;
    }

    // Show loading state
    submitBtn.prop('disabled', true).text('Adding...');

    // Submit via AJAX to direct API endpoint
    $.ajax({
        url: window.location.origin + '/api/addBrand.php',
        method: 'POST',
        data: {
            brand_name: brandName
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (response) {
            // Hide modal
            $('#addBrandModal').modal('hide');

            // Get the dropdown that triggered this modal
            const dropdown = $('#addBrandModal').data('dropdown');

            // Refresh the dropdown options via AJAX
            refreshDropdownOptions(dropdown, 'brands');

            // Show success message
            showSuccessMessage('Brand added successfully!');
        },
        error: function (xhr) {
            let errorMessage = 'An error occurred';

            try {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                if (xhr.responseText.includes('already exists')) {
                    errorMessage = 'Brand name already exists';
                } else if (xhr.status === 401) {
                    errorMessage = 'Please log in to continue';
                } else {
                    errorMessage = 'Failed to add brand. Please try again.';
                }
            }

            $('#modalBrandName').addClass('is-invalid');
            $('#modalBrandName').siblings('.invalid-feedback').text(errorMessage).show();
        },
        complete: function () {
            // Reset button state
            submitBtn.prop('disabled', false).text('Add Brand');
        }
    });
}

function showSuccessMessage(message) {
    // Create a simple toast notification for Bootstrap 4
    const toast = $('<div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">' +
        '<span>' + message + '</span>' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        '</div>');

    $('body').append(toast);

    // Auto-remove after 3 seconds
    setTimeout(function () {
        toast.alert('close');
    }, 3000);
}

function capitalizeWords(str) {
    return str.toLowerCase().replace(/\b\w/g, function (letter) {
        return letter.toUpperCase();
    });
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidGSTNumber(gst) {
    // Basic GST validation - should be 15 characters
    const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/;
    return gst.length === 15 && gstRegex.test(gst);
}
