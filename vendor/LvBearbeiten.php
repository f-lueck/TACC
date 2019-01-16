<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 15:46
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class LvBearbeiten
 * Ermöglicht die Bearbeitung von Lehrveranstaltungen
 */
class LvBearbeiten extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $message;
    private $lvID;

    /**
     * LvBearbeiten constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf durch Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Auswählen einer LV
        if (isset ($_POST["submitSelect"])) {
            $this->setLvID();
            $this->createEditLv();
        }

        //Löschen einer LV
        if (isset($_POST["submitLöschen"])) {
            $this->setLvID();
            $this->deleteLvFromDB();
            $this->showMessage();
        }

        //Aktualisieren einer LV
        if (isset($_POST["submitSpeichern"])) {
            $this->updateLvInDB();
            $this->showMessage();
        }
    }

    /**
     * @function setLvID
     * Setzt die LvID aus dem Formular
     */
    private function setLvID()
    {
        $this->lvID = $this->getPOST("Lv");
    }

    /**
     * @function createEditLv
     * Erzeugt ein Formular für die Bearbeitung
     */
    private function createEditLv()
    {
        //Formularheader
        $output = "<form method='post'>";

        //SQL-Statement um die Eigenschaften der LV zu laden
        $statement = $this->dbh->prepare('SELECT * FROM `veranstaltung` WHERE `ID_VERANSTALTUNG` =:LvID ');
        $result = $statement->execute(array("LvID" => $this->lvID));
        $data = $statement->fetch();

        //fetched:
        //[0]=ID der LV
        //[1]=Name der LV
        //[2]=SWS
        //[3]=Credits
        //[4]=Haeufigkeit pro Jahr
        //[5]=Faktor Doppelung
        //[6]=Sommersemester Ja/Nein
        //[7]=Kosten pro Jahr

        $output .= "<input type='text' name='Bezeichnung' id='Bezeichnung' value='" . $data[1] . "'><br>";
        $output .= "<input type='text' name='SWS' id='SWS' value='" . $data[2] . "'><br>";
        $output .= "<input type='text' name='Credits' id='Credits' value='" . $data[3] . "'><br>";
        $output .= "<input type='text' name='Haeufigkeit' id='Haeufigkeit' value='" . $data[4] . "'><br>";
        $output .= "<input type='text' name='Doppelung' id='Doppelung' value='" . $data[5] . "'><br>";
        $output .= "<input type='text' name='Sommersemester' id='Sommersemester' value='" . $data[6] . "'><br>";
        $output .= "<input type='text' name='Kosten' id='Kosten' value='" . $data[7] . "'><br>";

        //ID der LV in hidden-field
        $output .= "<input type='hidden' name='LvID' id='LvID' value='" . $this->lvID . "'><br>";
        $output .= "<input type='submit' name='submitSpeichern' id='submitSpeichern' value='Speichern'>";

        $output .= "</form>";

        echo $output;
    }

    /**
     * @function deleteLvFromDB
     * Löscht eine Veranstaltung auf Basis der ID aus der Datenbank
     */
    private function deleteLvFromDB()
    {
        //SQL-Statement für das Löschen einer Veranstaltung
        $statement = $this->dbh->prepare("DELETE FROM `veranstaltung` WHERE `ID_VERANSTALTUNG` = :LvID");
        $result = $statement->execute(array("LvID" => $this->lvID));
        if ($result) {
            //Veranstaltung wurde gelöscht
            $this->message = "Gelöscht";
        } else {
            //Veranstaltung konnte nicht gelöscht werden
            $this->message = "Fehler";
        }
    }

    /**
     * @function updateLvInDB
     * Ermöglicht das Aktualisieren der Veranstaltung
     */
    private function updateLvInDB()
    {
        //Eigenschaften der LV aus dem Formular
        $bezeichnung = $this->getPOST("Bezeichnung");
        $sws = $this->getPOST("SWS");
        $credits = $this->getPOST("Credits");
        $haeufigkeit = $this->getPOST("Haeufigkeit");
        $doppelung = $this->getPOST("Doppelung");
        $sommersemester = $this->getPOST("Sommersemester");
        $kosten = $this->getPOST("Kosten");

        //ID aus dem hidden-field
        $LvID = $this->getPOST("LvID");

        //SQL-Statement für das Aktualisieren einer Veranstaltung
        $statement = $this->dbh->prepare("UPDATE `veranstaltung` SET `BEZEICHNUNG`= :Bezeichnung,`SWS`= :SWS,
`CREDITS`= :Credits,`HAEUFIGKEIT_PA`= :Haeufigkeit,`FAKTOR_DOPPELUNG`= :Doppelung,`SOMMERSEMESTER`= :Sommersemester,`KOSTEN_PA`= :Kosten WHERE `ID_VERANSTALTUNG` = :LvID");
        $result = $statement->execute(array("Bezeichnung" => $bezeichnung, "SWS" => $sws, "Credits" => $credits, "Haeufigkeit" => $haeufigkeit, "Doppelung" => $doppelung,
            "Sommersemester" => $sommersemester, "Kosten" => $kosten, "LvID" => $LvID));

        if ($result) {
            //Erfolgreiches Aktualisieren
            $this->message = "Update";
        } else {
            //Fehler beim Aktualisieren
            $this->message = "Fehler";
        }
    }

    /**
     * @function showMessage
     * Liefert Meldungen über Javascript alert() zurück
     */
    public function showMessage()
    {
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }
}