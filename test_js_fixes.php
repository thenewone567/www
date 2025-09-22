<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaScript Error Fix Test</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-bug-slash"></i> JavaScript Error Fix Test</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h5>✅ JavaScript Fixes Applied</h5>
                    <ul class="mb-0">
                        <li><strong>Theme Controller:</strong> Fixed duplicate toggle creation</li>
                        <li><strong>Icon Updates:</strong> Prevented empty className conflicts</li>
                        <li><strong>Console Logging:</strong> Reduced excessive output</li>
                        <li><strong>Error Handling:</strong> Added browser extension error suppression</li>
                    </ul>
                </div>

                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> What Was Fixed:</h5>
                    <pre class="mb-0"><code>❌ Before:
- Multiple theme toggles created
- Icon className set to empty string
- Excessive console.log output
- Runtime.lastError from browser extensions

✅ After:
- Single theme toggle with initialization flag
- Smart icon class updates (only when different)
- Minimal console output
- Browser extension error handling</code></pre>
                </div>

                <div class="text-center">
                    <button id="theme-toggle" class="btn btn-primary">
                        <i class="fas fa-moon"></i> Test Theme Toggle
                    </button>
                    <p class="mt-2 text-muted">Click to test theme switching without errors</p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= URLROOT ?>/public/js/theme-controller.js"></script>
    <script>
        // Initialize theme controller
        const themeController = new ThemeController();

        // Test for console errors
        let errorCount = 0;
        const originalConsoleError = console.error;
        console.error = function (...args) {
            errorCount++;
            originalConsoleError.apply(console, args);
        };

        // Report after 2 seconds
        setTimeout(() => {
            const status = document.createElement('div');
            status.className = errorCount === 0 ? 'alert alert-success' : 'alert alert-danger';
            status.innerHTML = errorCount === 0
                ? '<strong>✅ Success:</strong> No JavaScript errors detected!'
                : `<strong>❌ Found:</strong> ${errorCount} JavaScript errors in console`;

            document.querySelector('.card-body').appendChild(status);
        }, 2000);
    </script>
</body>

</html>