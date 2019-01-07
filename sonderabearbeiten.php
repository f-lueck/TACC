<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 14:46
 */

include("vendor/SonderaBearbeiten.php");

$obj = new SonderaBearbeiten();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SonderaufgabenBearbeiten</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Sonderaufgabe bearbeiten</h1>
        <form method="post">
            Sonderaufgabe auswählen:
            <?php
            $obj->createSonderaDropdown();
            ?>
            <br>
            <div class="buttonholder">
                <button class="submitButtons" type="submit" name="submitSelect" id="submitSelect" value="Bearbeiten">
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
