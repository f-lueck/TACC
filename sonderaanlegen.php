<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 17:26
 */
include("vendor/SonderaAnlegen.php");

$obj = new SonderaAnlegen();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sonderaufgaben anlegen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Sonderaufgaben</h1>
        <form method="post">
            <table>
                <tr>
                    <td>Sonderaufgabe:</td>
                    <td><input type="text" name="NameS" id="NameS" required="required"></td>
                </tr>
                <tr>
                    <td>Semesterwochenstunden:</td>
                    <td><input type="number" name="SWS" id="SWS" step="0.1" min="0" required="required"></td>
                </tr>
            </table>
            <div class="buttonholder">
                <button class="submitButtons" type="submit" name="submit" id="submit" value="Speichern">Speichern
                </button>
                <button class="submitButtons" type="reset">Reset</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
