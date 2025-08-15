<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Add New Category</h1>
            <p class="text-muted">Create a new product category for your inventory</p>
        </div>
        <a href="<?php echo URLROOT; ?>/categories" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Categories
        </a>
    </div>

    <!-- Alert Messages -->
    <?php flash('category_message'); ?>

    <div class="row">
        <!-- Add Form -->
        <div class="col-lg-6">
            <div class="card theme-card-light">
                <div class="card-header theme-card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus text-primary mr-2"></i>Category Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/categories/add" method="post" id="addCategoryForm">
                        <div class="form-group mb-4">
                            <label for="category_name" class="form-label">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control <?php echo (!empty($data['category_name_err'])) ? 'is-invalid' : ''; ?>"
                                id="category_name" name="category_name"
                                value="<?php echo $data['category_name'] ?? ''; ?>" placeholder="Enter category name"
                                required>
                            <?php if (!empty($data['category_name_err'])): ?>
                                <div class="invalid-feedback"><?php echo $data['category_name_err']; ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                Choose a descriptive name for this category (e.g., "Power Tools", "Plumbing",
                                "Electrical")
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i>Add Category
                            </button>
                            <a href="<?php echo URLROOT; ?>/categories" class="btn btn-outline-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category Preview -->
        <div class="col-lg-6">
            <div class="card theme-card-light">
                <div class="card-header theme-card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-eye text-info mr-2"></i>Category Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="preview-box border rounded p-4 bg-light">
                        <div class="d-flex align-items-center mb-3">
                            <div class="category-icon mr-3">
                                <i class="fas fa-tag fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 id="previewName" class="mb-1 text-muted">Category Name</h5>
                                <small class="text-muted">New Category</small>
                            </div>
                        </div>
                        <div class="border-top pt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                This is how your category will appear in product forms and listings.
                            </small>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">Category Tips</h6>
                        <div class="tips-list">
                            <div class="tip-item mb-2">
                                <i class="fas fa-lightbulb text-warning mr-2"></i>
                                <small>Use clear, descriptive names that customers understand</small>
                            </div>
                            <div class="tip-item mb-2">
                                <i class="fas fa-hierarchy text-info mr-2"></i>
                                <small>Categories help organize products and improve searchability</small>
                            </div>
                            <div class="tip-item mb-2">
                                <i class="fas fa-edit text-success mr-2"></i>
                                <small>You can edit or reorganize categories anytime</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card theme-card-light mt-4">
                <div class="card-header theme-card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning mr-2"></i>Next Steps
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/categories" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list mr-2"></i>View All Categories
                        </a>
                        <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus mr-2"></i>Add Product After Creating Category
                        </a>
                    </div>
                </div>
            </div>

            <!-- JavaScript -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Initialize live preview
                    initializeLivePreview();

                    // Initialize form validation
                    initializeFormValidation();

                    // Auto-focus on category name field
                    document.getElementById('category_name').focus();
                });

                function initializeLivePreview() {
                    const categoryNameInput = document.getElementById('category_name');
                    const previewName = document.getElementById('previewName');

                    if (categoryNameInput && previewName) {
                        categoryNameInput.addEventListener('input', function () {
                            const value = this.value.trim();
                            previewName.textContent = value || 'Category Name';

                            // Add some visual feedback
                            if (value) {
                                previewName.classList.remove('text-muted');
                                previewName.classList.add('text-dark');
                            } else {
                                previewName.classList.add('text-muted');
                                previewName.classList.remove('text-dark');
                            }
                        });

                        // Format category name properly
                        categoryNameInput.addEventListener('input', function () {
                            if (this.value) {
                                // Capitalize first letter of each word
                                const formatted = this.value.replace(/\b\w/g, l => l.toUpperCase());
                                if (formatted !== this.value) {
                                    const cursorPosition = this.selectionStart;
                                    this.value = formatted;
                                    this.setSelectionRange(cursorPosition, cursorPosition);
                                }
                            }
                        });
                    }
                }

                function initializeFormValidation() {
                    const form = document.getElementById('addCategoryForm');

                    form.addEventListener('submit', function (e) {
                        let isValid = true;

                        // Validate category name
                        const categoryName = document.getElementById('category_name');
                        const categoryNameValue = categoryName.value.trim();

                        // Remove previous validation classes
                        categoryName.classList.remove('is-invalid', 'is-valid');

                        if (!categoryNameValue) {
                            categoryName.classList.add('is-invalid');
                            showValidationError(categoryName, 'Category name is required');
                            isValid = false;
                        } else if (categoryNameValue.length < 2) {
                            categoryName.classList.add('is-invalid');
                            showValidationError(categoryName, 'Category name must be at least 2 characters long');
                            isValid = false;
                        } else if (categoryNameValue.length > 50) {
                            categoryName.classList.add('is-invalid');
                            showValidationError(categoryName, 'Category name cannot exceed 50 characters');
                            isValid = false;
                        } else {
                            categoryName.classList.add('is-valid');
                            removeValidationError(categoryName);
                        }

                        if (!isValid) {
                            e.preventDefault();

                            // Scroll to first error
                            const firstError = form.querySelector('.is-invalid');
                            if (firstError) {
                                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                firstError.focus();
                            }
                        }
                    });
                }

                function showValidationError(element, message) {
                    // Remove existing error message
                    removeValidationError(element);

                    // Create error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback custom-error';
                    errorDiv.textContent = message;

                    // Insert after the element
                    element.parentNode.insertBefore(errorDiv, element.nextSibling);
                }

                function removeValidationError(element) {
                    const errorDiv = element.parentNode.querySelector('.custom-error');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }

                // Auto-hide alerts after 5 seconds
                document.addEventListener('DOMContentLoaded', function () {
                    const alerts = document.querySelectorAll('.alert-dismissible');
                    alerts.forEach(alert => {
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.style.transition = 'opacity 0.5s';
                                alert.style.opacity = '0';
                                setTimeout(() => {
                                    if (alert.parentNode) {
                                        alert.parentNode.removeChild(alert);
                                    }
                                }, 500);
                            }
                        }, 5000);
                    });
                });
            </script>

            <?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>