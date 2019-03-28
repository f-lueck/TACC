<!DOCTYPE html>
<html lang="de">
<?php include("header.php"); ?>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8"/>
    <title>T-ACC</title>
    <link rel="stylesheet" href="../../stylesheet.css">
</head>

<body>
<div class="nav">
    <nav class="menu">
        <ul>
            <li><a href="sekretariathome.php">Home</a></li>
            <li><a class="dropdown-btn">Einsicht<i class="fa fa-caret-down"></i></a>
                <ul class="dropmenu">
                    <li><a href="allelv.php">Alle LV</a></li>
                    <li><a href="allesondera.php">Alle Sonderaufgaben</a></li>
                    <li><a href="alledozenten.php">Alle Dozenten</a></li>
                </ul>
            </li>
            <li><a class="dropdown-btn">Administration<i class="fa fa-caret-down"></i></a>
                <ul class="dropmenu">
                    <li><a href="lvanlegen.php">LV anlegen</a></li>
                    <li><a href="lvbearbeiten.php">LV bearbeiten</a></li>
                    <li><a href="lvzuweisen.php">LV zuweisen</a></li>
                    <li><a href="sonderaanlegen.php">Sonderaufgaben anlegen</a></li>
                    <li><a href="sonderabearbeiten.php">Sonderaufgaben bearbeiten</a></li>
                    <li><a href="sonderazuweisen.php">Sonderaufgaben zuweisen</a></li>
                    <li><a href="dozentanlegen.php">Dozenten anlegen</a></li>
                    <li><a href="dozentbearbeiten.php">Dozenten bearbeiten</a></li>
                    <li><a href="zusatzaanlegen.php">Zusatzaufgaben anlegen</a></li>
                    <li><a href="zusatzabearbeiten.php">Zusatzaufgaben bearbeiten</a></li>
                </ul>
            </li>
            <li><a href="semesterabrerstellen.php">Semesterabrechnung erstellen</a></li>
            <li><a href="stundenabrnachlvvo.php">Stundenabrechnung nach LVVO erstellen</a></li>
            <li><a href="passwort.php">Passwort ändern</a></li>
            <li><a href="hilfe.php">Hilfe</a></li>
            <li><a href="login.php">Abmelden</a></li>
        </ul>
    </nav>
</div>
</body>
</html>