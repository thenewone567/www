<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/customers" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body theme-card-light mt-5">
    <h2>Edit Customer</h2>
    <p>Edit the customer with this form</p>
    <form action="<?php echo URLROOT; ?>/customers/edit/<?php echo $data['id']; ?>" method="post">
        <div class="form-group">
            <label for="customer_name">Customer Name: <sup>*</sup></label>
            <input type="text" name="customer_name"
                class="form-control form-control-lg <?php echo (!empty($data['customer_name_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['customer_name']; ?>">
            <span class="invalid-feedback"><?php echo $data['customer_name_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="contact_info">Contact Info:</label>
            <input type="text" name="contact_info" class="form-control form-control-lg"
                value="<?php echo $data['contact_info']; ?>" placeholder="Phone, Email, Address">
        </div>
        <div class="form-group">
            <label for="credit_limit">Credit Limit (₹):</label>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" step="0.01" name="credit_limit" class="form-control form-control-lg currency-input"
                    value="<?php echo $data['credit_limit']; ?>" placeholder="0.00">
            </div>
            <small class="form-text text-muted">Maximum credit allowed for this customer</small>
        </div>
        <input type="submit" class="btn btn-success" value="Submit">
    </form>
</div>

<script>
    // Auto-formatting for customer edit form
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-capitalize customer name
        const customerNameField = document.querySelector('input[name="customer_name"]');
        if (customerNameField) {
            customerNameField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = capitalizeWords(this.value);
                }
            });
        }

        // Auto-format contact info (could contain phone, email, address)
        const contactInfoField = document.querySelector('input[name="contact_info"]');
        if (contactInfoField) {
            contactInfoField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = formatContactInfo(this.value);
                }
            });
        }

        // Format credit limit in Indian currency
        const creditLimitField = document.querySelector('input[name="credit_limit"]');
        if (creditLimitField) {
            creditLimitField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = formatINR(this.value);
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

    // Format contact info intelligently
    function formatContactInfo(str) {
        // If it looks like a phone number (digits only), format it
        if (/^\d{10}$/.test(str.replace(/\D/g, ''))) {
            const cleaned = str.replace(/\D/g, '');
            if (cleaned.length === 10) {
                return `+91 ${cleaned.slice(0, 5)} ${cleaned.slice(5)}`;
            }
        }

        // If it looks like an email, make it lowercase
        if (str.includes('@')) {
            return str.toLowerCase();
        }

        // Otherwise, capitalize as address/general info
        return capitalizeWords(str);
    }

    // Format number in Indian numbering system
    function formatINR(num) {
        num = num.toString().replace(/[^0-9.]/g, '');
        let [integer, decimal] = num.split('.');
        let lastThree = integer.substring(integer.length - 3);
        let otherNumbers = integer.substring(0, integer.length - 3);
        if (otherNumbers !== '')
            lastThree = ',' + lastThree;
        let formatted = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
        if (decimal) formatted += '.' + decimal;
        return formatted;
    }
</script>

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