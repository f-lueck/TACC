<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:02
 */

include("vendor/AlleDozenten.php");

$obj = new AlleDozenten();
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
        <h1>Alle Dozenten</h1>
        <br>
        <div class="alledozenten">
            <?php
            $obj->showAllDozenten();
            ?>
        </div>
    </div>
</div>
</body>
</html>