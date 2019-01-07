<?php
include("vendor/MeineAuftraege.php");

$obj = new MeineAuftraege();
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8"/>
    <title>Lehrveranstaltungen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <br>
        <h1>Meine Lehrveranstaltungen</h1>
        <br>
        <div class="lv">
            <?php
            $obj->showOwnLv();
            ?>
        </div>
        <br>
        <h1>Meine Sonderaufgaben</h1>
        <br>
        <div class="sondera">
            <?php
            $obj->showOwnSondera();
            ?>
        </div>
    </div>
</div>
</body>
</html>