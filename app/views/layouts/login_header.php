<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <title><?php echo SITENAME; ?> - Login</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: white;
            padding: 48px 40px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            color: #718096;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 2;
        }

        .form-control.has-icon {
            padding-left: 48px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            z-index: 2;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #4f46e5;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 32px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
        }

        .form-check-label {
            color: #374151;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 32px 0;
            position: relative;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: white;
            padding: 0 16px;
            position: relative;
        }

        .btn-secondary {
            width: 100%;
            padding: 14px;
            background: #f8fafc;
            color: #374151;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            text-decoration: none;
            color: #1e293b;
        }

        .forgot-password {
            text-align: center;
            margin-bottom: 24px;
        }

        .forgot-password a {
            color: #4f46e5;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #3730a3;
            text-decoration: none;
        }

        .login-footer {
            text-align: center;
            margin-top: 32px;
            color: #718096;
            font-size: 0.875rem;
        }

        .dev-tools {
            margin-top: 24px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .dev-tools small {
            color: #6b7280;
        }

        .btn-dev {
            width: 100%;
            padding: 8px 12px;
            margin: 4px 0;
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-dev:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.95rem;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 16px;
            }

            .login-card {
                padding: 32px 24px;
            }

            .login-logo {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>