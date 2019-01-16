<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 14:48
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class SonderaBearbeiten
 * Ermöglicht das Bearbeiten von Sonderaufgaben
 */
class SonderaBearbeiten extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $message;
    private $sonderaID;

    /**
     * SonderaBearbeiten constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht den Methodenaufruf nach Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Auswählen einer Sonderaufgabe
        if (isset ($_POST["submitSelect"])) {
            $this->setSonderaID();
            $this->createEditSonderaufgaben();
        }

        //Löschen einer Sonderaufgabe
        if (isset($_POST["submitLöschen"])) {
            $this->setSonderaID();
            $this->deleteSonderaFromDB();
            $this->showMessage();
        }

        //Aktualisieren einer Sonderaufgabe
        if (isset($_POST["submitSpeichern"])) {
            $this->updateSonderaInDB();
            $this->showMessage();
        }
    }

    /**
     * @function setSonderaID
     * Setzt die Variable $sonderaID auf den Wert aus dem Formular
     */
    private function setSonderaID()
    {
        $this->sonderaID = $this->getPOST("Sonderaufgabe");
    }

    /**
     * @function createEditSonderaufgaben
     * Erzeugt ein Formular für die Bearbeitung der Sonderaufgabe
     */
    private function createEditSonderaufgaben()
    {
        $output = '<div class="main">';
        //Formularheader
        $output .= "<form method='post'>";

        //SQL-Statement für Eigenschaften der Sonderaufgabe auf Basis der ID
        $statement = $this->dbh->prepare('SELECT `BEZEICHNUNG`,`SWS` FROM `sonderaufgabe` WHERE `ID_SONDERAUFGABE` =:SonderaufgabeID ');
        $result = $statement->execute(array("SonderaufgabeID" => $this->sonderaID));

        $output .= '<table>';

        //fetched:
        //[0]=Name der Sonderaufgabe
        //[1]=SWS

        $data = $statement->fetch();

        $output .= "<tr><td>Bezeichnung</td>";
        $output .= "<td><input type='text' name='Bezeichnung' id='Bezeichnung' value='" . $data[0] . "'></td></tr>";
        $output .= "<tr><td>SWS</td>";
        $output .= "<td><input type='text' name='SWS' id='SWS' value='" . $data[1] . "'></td></tr>";
        $output .= "<input type='hidden' name='SonderaID' id='SonderaID' value='" . $this->sonderaID . "'><br>";
        $output .= '<tr><td colspan="2"><button class="submitButtons" type="submit" name="submitSpeichern" id="submitSpeichern" value="Speichern">Speichern
                </button></td></tr>';

        $output .= '</table>';
        //Formularende
        $output .= "</form>";
        $output .= '</div>';

        echo $output;
    }

    /**
     * @function deleteSonderaFromDB
     * Löscht eine Sonderaufgabe auf Basis der ID aus der Datenbank
     */
    private function deleteSonderaFromDB()
    {
        //SQL-Statement zum Löschen einer Sonderaufgabe
        $statement = $this->dbh->prepare("DELETE FROM `sonderaufgabe` WHERE `ID_SONDERAUFGABE` = :SonderaufgabeID");
        $result = $statement->execute(array("SonderaufgabeID" => $this->sonderaID));

        if ($result) {
            //Erfolgreich Gelöscht
            $this->message = "Gelöscht";
        } else {
            //Fehler beim Löschen
            $this->message = "Fehler";
        }
    }

    /**
     * @function updateSonderaInDB
     * Aktualisiert eine Sonderaufgabe auf Basis ihrer ID
     */
    private function updateSonderaInDB()
    {
        //Eingeschaften aus dem Formular holen
        $bezeichnung = $this->getPOST("Bezeichnung");
        $sws = $this->getPOST("SWS");
        $sonderaID = $this->getPOST("SonderaID");

        //SQL-Statement zur Aktualisierung einer Sonderaufgabe
        $statement = $this->dbh->prepare("UPDATE `sonderaufgabe` SET `BEZEICHNUNG`=:Bezeichnung,`SWS`=:SWS WHERE `ID_SONDERAUFGABE` =:SonderaufgabeID");
        $result = $statement->execute(array("Bezeichnung" => $bezeichnung, "SWS" => $sws, "SonderaufgabeID" => $sonderaID));

        if ($result) {
            //Aktualisiert
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