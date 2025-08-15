<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Category Management</h1>
            <p class="text-muted">Manage product categories for your inventory</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo URLROOT; ?>/categories/add" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Add New Category
            </a>
            <a href="<?php echo URLROOT; ?>/products" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php flash('category_message'); ?>

    <!-- Categories Card -->
    <div class="card theme-card-light">
        <div class="card-header theme-card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-tags text-primary mr-2"></i>Categories
            </h5>
            <div class="d-flex align-items-center gap-3">
                <span class="badge badge-info">
                    <?php echo count($data['categories'] ?? []); ?> Categories
                </span>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" class="form-control" id="searchCategories" placeholder="Search categories...">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($data['categories'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="categoriesTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="10%">ID</th>
                                <th width="50%">Category Name</th>
                                <th width="20%">Products Count</th>
                                <th width="15%">Status</th>
                                <th width="15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['categories'] as $category): ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-light">#<?php echo $category->category_id; ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="category-icon mr-2">
                                                <i class="fas fa-tag text-primary"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($category->category_name); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $productCount = $category->product_count ?? 0;
                                        if ($productCount > 0): ?>
                                            <span class="badge badge-success"><?php echo $productCount; ?> products</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">No products</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($category->is_active == 1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo URLROOT; ?>/categories/edit/<?php echo $category->category_id; ?>"
                                                class="btn btn-outline-primary btn-sm" title="Edit Category">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (($category->product_count ?? 0) == 0): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="confirmDelete(<?php echo $category->category_id; ?>, '<?php echo htmlspecialchars($category->category_name); ?>')"
                                                    title="Delete Category">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    title="Cannot delete - has products" disabled>
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-tags fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Categories Found</h5>
                    <p class="text-muted mb-4">Start by creating your first product category.</p>
                    <a href="<?php echo URLROOT; ?>/categories/add" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Add First Category
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats Card -->
    <?php if (!empty($data['categories'])): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card theme-card-light">
                    <div class="card-header theme-card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-info mr-2"></i>Category Statistics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border-right">
                                    <h4 class="text-primary"><?php echo count($data['categories']); ?></h4>
                                    <small class="text-muted">Total Categories</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-right">
                                    <h4 class="text-success">
                                        <?php
                                        $activeCategories = array_filter($data['categories'], function ($cat) {
                                            return ($cat->product_count ?? 0) > 0;
                                        });
                                        echo count($activeCategories);
                                        ?>
                                    </h4>
                                    <small class="text-muted">Active Categories</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-right">
                                    <h4 class="text-warning">
                                        <?php
                                        $emptyCategories = array_filter($data['categories'], function ($cat) {
                                            return ($cat->product_count ?? 0) == 0;
                                        });
                                        echo count($emptyCategories);
                                        ?>
                                    </h4>
                                    <small class="text-muted">Empty Categories</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-info">
                                    <?php
                                    $totalProducts = array_sum(array_map(function ($cat) {
                                        return $cat->product_count ?? 0;
                                    }, $data['categories']));
                                    echo $totalProducts;
                                    ?>
                                </h4>
                                <small class="text-muted">Total Products</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the category <strong id="categoryName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post" class="d-inline">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-2"></i>Delete Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize search functionality
        initializeSearch();

        // Initialize table interactions
        initializeTableInteractions();
    });

    function initializeSearch() {
        const searchInput = document.getElementById('searchCategories');
        const table = document.getElementById('categoriesTable');

        if (searchInput && table) {
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                for (let row of rows) {
                    const categoryName = row.cells[1].textContent.toLowerCase();
                    const categoryId = row.cells[0].textContent.toLowerCase();

                    if (categoryName.includes(searchTerm) || categoryId.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    }

    function initializeTableInteractions() {
        // Bootstrap table-hover class will handle hover effects automatically
        // No manual hover effects needed
    }

    function confirmDelete(categoryId, categoryName) {
        // Set the category name in the modal
        document.getElementById('categoryName').textContent = categoryName;

        // Set the form action
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = '<?php echo URLROOT; ?>/categories/delete/' + categoryId;

        // Show the modal
        $('#deleteModal').modal('show');
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