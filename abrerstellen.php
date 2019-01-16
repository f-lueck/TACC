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
        Abrechnung der Lehrveranstaltungen für das <?php echo $obj->formatSemester($obj->getCurrentSemester()) ?><br>
        <br><br>
        Name: <?php echo $obj->formatDozent($obj->getDozent($obj->getSession('IdDozent'))) ?><br>

        <form method="post">
            <div class="lv">
                <?php
                echo $obj->showOwnLv();
                ?>
            </div>
            <br>
            <?php $obj->addCounter(1);
            $obj->getCounter() ?>. Praxisprojekte / Abschlussarbeiten <input type="number"
                                                                             value="<?php echo $obj->getSWSZusatz($obj->getSession("IdDozent")) ?>"><br>
            <?php $obj->addCounter(1);
            $obj->getCounter() ?>.Verfügungsstunden für <input type="number"
                                                               value="<?php echo $obj->getSWSSonder($obj->getSession("IdDozent")) ?>"><br>
            <?php $obj->addCounter(1);
            $obj->getCounter() ?>.Verfügungsstunden für F+E<input type="number" name="F+E" id="F+E"
                                                                  value="<?php echo $obj->getFE($obj->getSession("IdDozent")) ?>"><br>
            <?php $obj->addCounter(1);
            $obj->getCounter() ?>.In anderen Fakultäten <input type="number" value="<?php echo $obj->getSWSInAF($obj->getSession("IdDozent")) ?>"><br>
            <input type="text" name="LVAFN1" id="LVAFN1"><input type="number" name="LVAFSWS1" id="LVAFSWS1"><br>
            <input type="text" name="LVAFN2" id="LVAFN2"><input type="number" name="LVAFSWS2" id="LVAFSWS2"><br>
            <input type="text" name="LVAFN3" id="LVAFN3"><input type="number" name="LVAFSWS3" id="LVAFSWS3"><br>
            Zwischensumme (ohne andere Fakultäten): <?php echo $obj->summeSWS() ?>
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
            Sonstige Anmerkungen (max. 320 Zeichen)<br>
            <textarea id="text" name="text" rows="10" cols="30" maxlength="320"></textarea>
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
</body>
</html>