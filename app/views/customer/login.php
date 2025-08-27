<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - Hardware Store</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">
</head>

<body class="bg-primary">
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row w-100">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0">Customer Portal Login</h3>
                        <p class="text-muted">Access your account and orders</p>
                    </div>
                    <div class="card-body">
                        <!-- Flash Messages -->
                        <?php if (isset($_SESSION['flash_message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show">
                                <?php echo $_SESSION['flash_message']; ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                        <?php endif; ?>

                        <form id="customerLoginForm" action="<?php echo URLROOT; ?>/customer/login" method="POST">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Don't have an account? Contact us to register as a customer.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('customerLoginForm').addEventListener('submit', function (e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.textContent = 'Logging in...';
            submitBtn.disabled = true;

            // Re-enable after 3 seconds in case of error
            setTimeout(() => {
                submitBtn.textContent = 'Login';
                submitBtn.disabled = false;
            }, 3000);
        });
    </script>
</body>

</html>