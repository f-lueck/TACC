<?php
include("vendor/AlleSondera.php");

$obj = new AlleSondera();
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
        <h1>Alle Sonderaufgaben</h1>
        <br>
        <div class="allesondera">
            <?php
            $obj->showLinkAllDozentSondera();
            ?>
        </div>
    </div>
</div>
</body>
</html>