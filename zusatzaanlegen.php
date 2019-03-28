<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 28.03.2019
 * Time: 18:12
 */

include("vendor/ZusatzaAnlegen.php");

$obj = new ZusatzaAnlegen();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zusatzaufgaben Anlegen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Zusatzaufgaben anlegen</h1>
        <form method="post">
            <table>
                <thead>
                <tr>
                    <th>Bezeichnung</th>
                    <th>Kuerzel</th>
                    <th>SWS</th>
                </tr>
                </thead>
                <tr>
                    <td><input type="text" id="Bezeichnung" name="Bezeichnung" required="required"></td>
                    <td><input type="text" id="Kuerzel" name="Kuerzel" maxlength="2" required="required"></td>
                    <td><input type="number" id="SWS" name="SWS" required="required"></td>
                </tr>
            </table>
            <div class="buttonholder">
                <button class="submitButtons" type="submit" id="submit" name="submit">Speichern</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
