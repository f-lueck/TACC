<?php
include("vendor/StundenabrNachLvvo.php");

$obj = new StundenabrNachLvvo();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stundenabrechnung nach LVVO erstellen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Stundenabrechnung nach LVVO im SS 2018</h1><br>
        <p>Fakultät: Informatik</p>
        <p>Ansprechpartnerin: Heidrun Rasch</p>
        <p>Standort: Wolfenbüttel</p>

        <?php
        echo $obj->showStundenabrNachLvvo();
        ?>

        <br>
        <form method="post">
            <div class="buttonholder">
                <button class="submitButtons" type="submit" id="print" name="print">Drucken</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>