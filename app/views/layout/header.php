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
    <title><?php echo SITENAME; ?></title>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'sidebar.php'; ?>
        <div id="page-content-wrapper">
            <?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'navbar.php'; ?>
            <div class="container-fluid">