<div class="container mt-4">
    <h2>Add Brand</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="brand_name">Brand Name</label>
            <input type="text" class="form-control" id="brand_name" name="brand_name" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Brand</button>
    </form>
</div>

<script>
    // Auto-capitalize brand name
    document.addEventListener('DOMContentLoaded', function () {
        const brandNameField = document.querySelector('input[name="brand_name"]');
        if (brandNameField) {
            brandNameField.addEventListener('blur', function () {
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