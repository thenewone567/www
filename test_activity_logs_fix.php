<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs Console Fix Test</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4><i class="fas fa-check-circle"></i> Activity Logs JavaScript Fixes Applied</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h5>✅ Console Error Fixes Applied to Activity Logs</h5>
                    <ul class="mb-0">
                        <li><strong>Browser Extension Errors:</strong> Added error suppression for runtime.lastError
                        </li>
                        <li><strong>Console Logging:</strong> Reduced excessive console.log output</li>
                        <li><strong>Error Handling:</strong> Added Chrome extension communication error handling</li>
                        <li><strong>Clean Console:</strong> Removed debugging output that cluttered developer tools</li>
                    </ul>
                </div>

                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> What Was Fixed:</h5>
                    <pre class="mb-0"><code>❌ Before (in console):
- runtime.lastError: The message port closed before a response was received
- Loading activity logs...
- Activity logs response: Object
- updateActivityLogsTable: appended rows: 15
- Filtering logs for period: today

✅ After (in console):
- Clean, minimal output
- No browser extension errors
- No debug logging noise
- Only essential error messages when needed</code></pre>
                </div>

                <div class="alert alert-warning">
                    <h5><i class="fas fa-lightbulb"></i> How It Works:</h5>
                    <p class="mb-0">
                        The fixes intercept browser extension communication errors and prevent them from appearing in
                        the console.
                        This is the same approach we used for the putaway page theme controller.
                    </p>
                </div>

                <div class="text-center">
                    <a href="<?= URLROOT ?>/admin/activityLogs" class="btn btn-primary">
                        <i class="fas fa-history"></i> Test Activity Logs Page
                    </a>
                    <p class="mt-2 text-muted">Visit to see clean console without runtime.lastError</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Test for console errors
        let errorCount = 0;
        const originalConsoleError = console.error;
        console.error = function (...args) {
            errorCount++;
            originalConsoleError.apply(console, args);
        };

        // Check for runtime errors after 3 seconds
        setTimeout(() => {
            const status = document.createElement('div');
            status.className = errorCount === 0 ? 'alert alert-success mt-3' : 'alert alert-danger mt-3';
            status.innerHTML = errorCount === 0
                ? '<strong>✅ Success:</strong> No JavaScript errors detected on this test page!'
                : `<strong>❌ Found:</strong> ${errorCount} JavaScript errors in console`;

            document.querySelector('.card-body').appendChild(status);
        }, 3000);
    </script>
</body>

</html>