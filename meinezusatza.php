<?php
include ("vendor/MeineZusatza.php");

$obj=new MeineZusatza();
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8" />
    <title>Meine Zusatzaufgaben</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <br>
        <h1>Meine Zusatzaufgaben</h1>
        <br>
        <div class="meinezusatza">
            <?php
            $obj->showOwnZusatzaufgaben();
            ?>
        </div>
    </div>
</div>
</body>
</html>