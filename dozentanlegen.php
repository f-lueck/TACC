<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 14:59
 */
include("vendor/DozentAnlegen.php");
$obj = new DozentAnlegen();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dozent</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body>
<div class="main">
    <div class="center">
        <h1>Dozenten anlegen</h1>
        <form method="post">
            <table>
                <tr>
                    <td>Titel:</td>
                    <td><input type="text" name="Titel" id="Titel"></td>
                </tr>
                <tr>
                    <td>Vorname:</td>
                    <td><input type="text" name="Vorname" id="Vorname" required="required"></td>
                </tr>
                <tr>
                    <td>Nachname:</td>
                    <td><input type="text" name="Nachname" id="Nachname" required="required"></td>
                </tr>
                <tr>
                    <td>Rolle:</td>
                    <td><?php
                        $obj->createRollenDropdown();
                        ?></td>
                </tr>
                <tr>
                    <td>Benutzername:</td>
                    <td><input type="text" name="Benutzername" id="Benutzername" required="required"></td>
                </tr>
                <tr>
                    <td>Passwort:</td>
                    <td><input type="password" name="Passwort1" id="Passwort1" required="required"></td>
                </tr>
                <tr>
                    <td>Passwort wiederholen:</td>
                    <td><input type="password" name="Passwort2" id="Passwort2" required="required"></td>
                </tr>
                <tr>
                    <td>SWS pro Semester:</td>
                    <td><input type="number" name="SWS" id="SWS" step="0.5" min="0" required="required"></td>
                </tr>
                <tr>
                    <td>Überstunden:</td>
                    <td><input type="number" name="Ueberstunden" id="Ueberstunden" step="0.1" required="required"></td>
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
