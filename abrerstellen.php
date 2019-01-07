<?php
include("vendor/AbrErstellen.php");

$obj = new AbrErstellen();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Abrechnung erstellen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Übersicht</h1><br>
        <p>Bitten überprüfen Sie folgenden Angaben:</p><br>

        <p>Ostfalia Hochschule für angewandte Wissenschaften<br> <span
                    style="float: right">Wolfenbüttel, den <?php echo $obj->getCurrentDate() ?></span>
            Fakultät Informatik <br>
            Der Dekan </p>
        <br>
        Abrechnung der Lehrveranstaltungen für das SOMMER/WINTERSEMESTER 2018<br>
        <br><br>
        Name: DOZENT<br>

        <form method="post">
            <div class="lv">
                <?php
                echo $obj->showOwnLv();
                ?>
            </div>
            <br>
            <?php $obj->setCounter(1);
            $obj->getCounter() ?>. Praxisprojekte / Abschlussarbeiten <input type="number"
                                                                             value="<?php echo $obj->getSWSZusatz($obj->getSession("IdDozent")) ?>"><br>
            <?php $obj->setCounter(1);
            $obj->getCounter() ?>.Verfügungsstunden für <input type="number"
                                                               value="<?php echo $obj->getSWSSonder($obj->getSession("IdDozent")) ?>"><br>
            <?php $obj->setCounter(1);
            $obj->getCounter() ?>.In anderen Fakultäten <input type="text"><br>
            <input type="text"><input type="text"><br>
            <input type="text"><input type="text"><br>
            <input type="text"><input type="text"><br>
            Summe: <?php $obj->summeSWS() ?>
            <br><br>
            <div class="sondera">
                <?php
                echo $obj->showOwnSondera();
                ?>
            </div>
            <br>
            Unter 8. sind die Praxisprojekte, die Mastertutorien und die Abschlussarbeiten einzusetzen, die Sie im <br>
            Abrechnungssemester betreut haben, je Praxisprojekt (P) 0,2 SWS, je Bachelorarbeit (B) 0,3 SWS, je<br>
            Masterarbeit (M) 1,0 SWS, je Diplomarbeit (D) 0,4 SWS und je Mastertutorium (T) 0,2 SWS. Für<br>
            Abschlussarbeiten dürfen höchstens 2 SWS abgerechnet werden.
            <br><br>
            Praxisprojekte / Abschlussarbeiten
            <br>
            <div class="meinezusatza">

                <?php
                echo $obj->showOwnZusatzaufgaben();
                ?>

            </div>
            <br>
            Sonstige Anmerkungen<br>
            <textarea id="text" name="text" rows="10" cols="30"></textarea>
            <br><br>
            <br><br>
            <div class="buttonholder">
                <button class="submitButtons" type="submit" name="submit" id="submit" value="Speichern und Drucken">
                    Speichern und Drucken
                </button>
            </div>
        </form>
    </div>
</div>
<!-- popup mit frage: "Ist alles korrekt?" -->
</body>
</html>