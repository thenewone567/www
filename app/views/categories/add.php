<!DOCTYPE html>
<html>

<head>
    <title>Add Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid p-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add New Category</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo URLROOT; ?>/categories/add" method="post" class="needs-validation" novalidate>
                    <div class="form-group mb-3">
                        <label for="category_name" class="form-label">Category Name <span
                                class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control <?php echo (!empty($data['category_name_err'])) ? 'is-invalid' : ''; ?>"
                            id="category_name" name="category_name" value="<?php echo $data['category_name'] ?? ''; ?>"
                            required>
                        <div class="invalid-feedback">
                            <?php echo $data['category_name_err'] ?? 'Please enter a category name'; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Add Category</button>
                        <button type="button" class="btn btn-secondary" onclick="window.close()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-formatting for category name
            const categoryNameField = document.querySelector('input[name="category_name"]');

            if (categoryNameField) {
                categoryNameField.addEventListener('input', function () {
                    if (this.value) {
                        this.value = capitalizeWords(this.value);
                    }
                });
            }
        });

        // Capitalize first letter of each word
        function capitalizeWords(str) {
            return str.toLowerCase().replace(/\b\w/g, function (letter) {
                return letter.toUpperCase();
            });
        }
    </script>
</body>

</html>