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
            <div class="buttonholder">
                <?php
                echo $obj->createDozentTable();
                ?>
                <div class="buttonholder">
                    <button class="submitButtons" type="submit" id="print" name="print">Erzeugen</button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>