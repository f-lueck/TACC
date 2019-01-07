<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 15:46
 */

include("vendor/LvBearbeiten.php");
$obj = new LvBearbeiten();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LV Bearbeiten</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Lehrveranstaltung bearbeiten</h1>
        <form method="post">
            Lehrveranstaltung auswählen:
            <?php
            $obj->createLvDropdown();
            ?>
            <br>
            <div class="buttonholder">
                <button class="submitButtons" type="submit" id="submitSelect" name="submitSelect" value="Bearbeiten">
                    Bearbeiten
                </button>
                <button class="submitButtons" type="submit" name="submitLöschen" id="submitLöschen" value="Löschen">
                    Löschen
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
