<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Unified CSS - Ultra lean 4.8KB (91% smaller!) -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Theme script for immediate theme application -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('preferred-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <title><?php echo htmlspecialchars(company_name()); ?> - Login</title>
    <script>
        // Apply login-page class for theme-aware styling
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.add('login-page');
        });
    </script>
</head>

<body data-theme="light">