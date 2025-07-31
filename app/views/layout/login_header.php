<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <title><?php echo SITENAME; ?> - Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .facebook-container {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .facebook-left {
            flex: 1;
            max-width: 500px;
            color: white;
            margin-right: 50px;
        }

        .facebook-logo {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .facebook-tagline {
            font-size: 1.5rem;
            line-height: 1.4;
            opacity: 0.9;
        }

        .facebook-right {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e1e5e9;
            padding: 12px 15px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .facebook-container {
                flex-direction: column;
                text-align: center;
            }

            .facebook-left {
                margin-right: 0;
                margin-bottom: 30px;
                max-width: none;
            }

            .facebook-logo {
                font-size: 2.5rem;
            }

            .facebook-tagline {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>