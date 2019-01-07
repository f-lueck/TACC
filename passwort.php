<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:44
 */
include("vendor/Passwort.php");

$obj = new Passwort();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Passwort Ändern</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Passwort ändern</h1>
        <br>
        <form method="post">
            <table>
                <tr>
                    <td>Altes Passwort:</td>
                    <td><input type="password" name="PasswortAlt" id="PasswortAlt"></td>
                </tr>
                <tr>
                    <td>Neues Passwort:</td>
                    <td><input type="password" name="PasswortNeu1" id="PasswortNeu1"></td>
                </tr>
                <tr>
                    <td>Neues Passwort wiederholen:</td>
                    <td><input type="password" name="PasswortNeu2" id="PasswortNeu2"></td>
                </tr>
            </table>
            <br>
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
