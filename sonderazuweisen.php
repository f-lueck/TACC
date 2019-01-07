<?php
include("vendor/SonderaZuweisen.php");

$obj = new SonderaZuweisen();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sonderaufgaben zuweisen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Sonderaufgaben zuweisen</h1>
        <form method="post">
            Dozent auswählen:
            <?php
            $obj->createDozentDropdown();
            ?>
            Sonderaufgabe auswählen:
            <?php
            $obj->createSonderaDropdown();
            ?>
            <br>
            SWS: <input type="number" name="SWS" id="SWS">
            <div class="buttonholder">
                <button class="submitButtons" type="submit" name="submit" id="submit" value="Bestätigen">Bestätigen
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>