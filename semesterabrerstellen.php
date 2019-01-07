<?php
include("vendor/SemesterabrErstellen.php");
$obj = new SemesterabrErstellen();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Semesterabrechnung erstellen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Semesterabrechnung</h1><br>
        <form method="post">
            <?php
            $obj->createDozentDropdown();
            ?>
            <div class="buttonholder">
                <button class="submitButtons" type="submit" name="selectDozent" id="selectDozent">Erzeugen</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>