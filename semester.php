<?php
include ('vendor/Semester.php');
$obj = new Semester();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Semester</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Semester - Administration</h1>

        <?php
        echo $obj->showSemesterData();
        ?>

        <form method="post">
            <h1>Neues Semester anlegen</h1>
            <table>
                <tr>
                    <th>Bezeichnung</th>
                    <th>Sommersemester</th>
                </tr>
                <tr>
                    <td><input type="text" name="Bezeichnung" id="Bezeichnung" required="required"></td>
                    <td><input type="number" name="Sommersemester" id="Sommersemester" required="required" min="0" max="1"></td>
                </tr>
            </table>
            <div class="buttonholder">
                <button class="submitButtons" id="Create" name="Create">Anlegen</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>