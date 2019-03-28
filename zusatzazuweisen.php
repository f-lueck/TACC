<?php

include("vendor/ZusatzaZuweisen.php");

$obj = new ZusatzaZuweisen();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zusatzaufgabe Zuweisen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <div class="zusatzaufgabe">
            <h1>Zusatzaufgaben zuweisen</h1>
            Hier sind die Praxisprojekte, die Mastertutorien und die Abschlussarbeiten einzusetzen,<br>
            die Sie im Abrechnungssemester betreut haben,<br>
            je Praxisprojekt 0,2 SWS,<br>
            je Bachelorarbeit 0,3 SWS,<br>
            je Masterarbeit 1,0 SWS,<br>
            je Diplomarbeit 0,4 SWS<br>
            und je Mastertutorium 0,2 SWS.<br>
            Für Abschlussarbeiten dürfen höchstens 2 SWS abgerechnet werden.<br>
            <form method="post">
                <br>
                <table>
                    <tr>
                        <td>Art:</td>
                        <td><?php
                            $obj->createArtVonZusatzaufgabeDropdown();
                            ?></td>
                    </tr>
                    <tr>
                        <td>Matrikelnummer:</td>
                        <td><input type="number" name="Matrikelnummer" id="Matrikelnummer"></td>
                    </tr>
                    <tr>
                        <td>Name:</td>
                        <td><input type="text" name="Name" id="Name"></td>
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
</div>
</body>
</html>