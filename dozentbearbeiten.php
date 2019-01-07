<?php
include("vendor/DozentBearbeiten.php");

$obj = new DozentBearbeiten();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dozent Bearbeiten</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Dozent bearbeiten</h1>
        <form method="post">
            Dozent auswählen:
            <?php
            $obj->createDozentDropdown();
            ?>
            <br>
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