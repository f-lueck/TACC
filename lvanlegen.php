<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 17:04
 */

include("vendor/LvAnlegen.php");
$obj = new lvAnlegen();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lehrveranstaltung</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Lehrveranstaltung anlegen</h1>
        <form method="post">
            <table>
                <tr>
                    <td>Name:</td>
                    <td><input type="text" name="Name" id="Name" required="required"></td>
                <tr/>
                <tr>
                    <td>Semesterwochenstunden:</td>
                    <td><input type="number" name="SWS" id="SWS" step="0.1" min="0" required="required"></td>
                </tr>
                <tr>
                    <td>Credits:</td>
                    <td><input type="number" name="Credits" id="Credits" step="0.1" min="0" required="required"></td>
                </tr>
                <tr>
                    <td>Häufigkeit im Jahr:</td>
                    <td><input type="number" name="Haeufigkeit" id="Hauefigkeit" required="required"></td>
                </tr>
                <tr>
                    <td>Faktor Doppelung:</td>
                    <td><input type="number" name="Doppelt" id="Doppelt" step="0.1" required="required"></td>
                </tr>
                <tr>
                    <td>Sommersemester:</td>
                    <td><input type="radio" value="1" name="Sommersemester" id="Sommersemester" checked> Ja
                        <input type="radio" value="0" name="Sommersemester" id="Sommersemester"> Nein
                    </td>
                </tr>
                <tr>
                    <td>Kosten im Jahr:</td>
                    <td><input type="number" name="Kosten" id="Kosten" required="required"></td>
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