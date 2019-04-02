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

        <form class="flex-container-tabelle" method="post">
            <div>
                    <table>
                        <caption>Informatik</caption>
                        <tr>
                            <th>Name Lehrveranstaltung</th>
                            <th>SWS</th>
                            <th>Auswahl</th>
                        </tr>
                        <?php echo $obj->showLVInformatik(6) ?>
                        <?php echo $obj->showLVInformatik(1) ?>
                        <?php echo $obj->showLVInformatik(2) ?>
                        <?php echo $obj->showLVInformatik(3) ?>
                        <?php echo $obj->showLVInformatik(4) ?>
                        <?php echo $obj->showLVInformatik(5) ?>
                    </table>
            </div>
            <div>
                <table>
                    <caption>Wirtschaftsinformatik</caption>
                    <tr>
                        <th>Name Lehrveranstaltung</th>
                        <th>SWS</th>
                        <th>Auswahl</th>
                    </tr>
                    <?php echo $obj->showLVInformatik(11) ?>
                    <?php echo $obj->showLVInformatik(12) ?>
                </table>
            </div>
            <div>
                <table>
                    <caption>Master</caption>
                    <tr>
                        <th>Name Lehrveranstaltung</th>
                        <th>SWS</th>
                        <th>Auswahl</th>
                    </tr>
                    <?php echo $obj->showLVInformatik(21) ?>
                    <?php echo $obj->showLVInformatik(22) ?>
                    <?php echo $obj->showLVInformatik(23) ?>
                </table>
            </div>
            <div>
                <table>
                    <caption>Master (alt)</caption>
                    <tr>
                        <th>Name Lehrveranstaltung</th>
                        <th>SWS</th>
                        <th>Auswahl</th>
                    </tr>
                    <?php echo $obj->showLVInformatik(32) ?>
                    <?php echo $obj->showLVInformatik(33) ?>
                    <?php echo $obj->showLVInformatik(34) ?>
                </table>
            </div>
            <div>
                <table>
                    <caption>IT-Management</caption>
                    <tr>
                        <th>Name Lehrveranstaltung</th>
                        <th>SWS</th>
                        <th>Auswahl</th>
                    </tr>
                    <?php echo $obj->showLVInformatik(31) ?>
                </table>
                <div class="buttonholder">
                    <button class="submitButtons" type="submit" name="submitSelect" id="submitSelect" value="Speichern">Auswählen
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</body>
</html>
