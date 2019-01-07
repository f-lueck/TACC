<?php
include("vendor/Home.php");

$obj = new Home();
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8"/>
    <title>T-ACC</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <header>
            <h1>T-ACC</h1>
            <br><br><br><br><br><br>
            <div class="flex-container">
                <a class="button" href="meineauftraege.php">Meine Aufträge</a>
                <a class="button" href="abrerstellen.php">Abrechnung erstellen</a>
                <a class="button" href="lvzuweisen.php">LV zuweisen</a>
                <a class="button" href="allelv.php">Alle LV</a>
            </div>
        </header>
    </div>
</div>
</body>
</html>