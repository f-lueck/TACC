<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 16:43
 */

include("vendor/LvZuweisen.php");

$obj = new LvZuweisen();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LV zuweisen</title>
    <link rel="stylesheet" href="stylesheet.css">
    <style>
        .flex-container-tabelle {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-around;
        }
    </style>
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Lehrveranstaltung zuweisen</h1>

        <div class="flex-container-tabelle">
            <div>
                <?php echo $obj->showLVInformatik() ?>
            </div>
            <div>
                <?php echo $obj->showLVEigene() ?>
            </div>
            <div>
                <table>
                    <caption>IT Management</caption>
                    <tr>
                        <th>Name Lehrveranstaltung</th>
                        <th>SWS (muss)</th>
                        <th>Auswahl</th>
                        <th>SWS gewünscht</th>
                    </tr>
                </table>
                <button class="submitButtons" type="submit" name="submit" id="submit" value="Speichern">Speichern
                </button>
                <button class="submitButtons" type="reset">Reset</button>
            </div>
            <div>
                <table>
                    <caption>Wirtschaftsinformatik</caption>
                    <tr>
                        <th>Name Lehrveranstaltung</th>
                        <th>SWS (muss)</th>
                        <th>Auswahl</th>
                        <th>SWS gewünscht</th>
                    </tr>
                </table>
                <button class="submitButtons" type="submit" name="submit" id="submit" value="Speichern">Speichern
                </button>
                <button class="submitButtons" type="reset">Reset</button>
            </div>
            <div>
                <table>
                    <caption>Master (alt)</caption>
                    <tr>
                        <th>Name Lehrveranstaltung</th>
                        <th>SWS (muss)</th>
                        <th>Auswahl</th>
                        <th>SWS gewünscht</th>
                    </tr>
                </table>
                <button class="submitButtons" type="submit" name="submit" id="submit" value="Speichern">Speichern
                </button>
                <button class="submitButtons" type="reset">Reset</button>
            </div>
            <div>
                <table>
                    <caption>Master (neu)</caption>
                    <tr>
                        <th>Name Lehrveranstaltung</th>
                        <th>SWS (muss)</th>
                        <th>Auswahl</th>
                        <th>SWS gewünscht</th>
                    </tr>
                </table>
                <div class="buttonholder">
                    <button class="submitButtons" type="submit" name="submit" id="submit" value="Speichern">Speichern
                    </button>
                    <button class="submitButtons" type="reset">Reset</button>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
