<?php
include("vendor/Home.php");

$obj = new Home();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Einsicht</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Administration</h1>
        <br>
        <div class="flex-container">
            <a class="button" href="lvzuweisen.php">LV zuweisen</a>
            <a class="button" href="sonderazuweisen.php">Sonderaufgaben zuweisen</a>
        </div>
    </div>
</div>
</body>
</html>