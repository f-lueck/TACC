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
        <h1>Einsicht</h1>
        <br>
        <div class="flex-container">
            <a class="button" href="meineauftraege.php">Meine Aufträge</a>
            <a class="button" href="allelv.php">Alle LV</a>
            <a class="button" href="allesondera.php">Alle Sonderaufgaben</a>
            <a class="button" href="alledozenten.php">Alle Dozenten</a>
        </div>
    </div>
</div>
</body>
</html>