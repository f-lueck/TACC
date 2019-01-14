<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 19.11.2018
 * Time: 14:27
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class DozentBearbeiten
 * Ermöglicht das Bearbeiten eines Dozenten
 */
class DozentBearbeiten extends Benutzersitzung
{
    /**
     * @var
     * Variablen für die Weiterverarbeitung
     */
    private $message;
    private $dozentID;

    /**
     * DozentBearbeiten constructor.
     * Erzeugt ein Objekt der Klasse und ermöglicht den Aufruf von Methoden durch Buttonclicks
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Auswählen eines Dozenten
        if (isset ($_POST["submitSelect"])) {
            $this->setDozentID();
            $this->createEditDozent();
        }

        //Löschen eines Dozenten
        if (isset($_POST["submitLöschen"])) {
            $this->setDozentID();
            $this->deleteDozentFromDB();
            $this->showMessage();

        }

        //Übernehmen der neuen Daten
        if (isset($_POST["submitSpeichern"])) {
            $this->updateDozentInDB();
            $this->showMessage();

        }
    }

    /**
     * @function setDozentID
     * Setzt die Variable $dozentID
     */
    private function setDozentID()
    {
        $this->dozentID = $this->getPOST("Dozent");
    }

    /**
     * @function createEditDozent
     * Erzeugt ein Bearbeitungsformular für einen ausgewählten Dozenten
     */
    private function createEditDozent()
    {
        //Formularbegin
        $output = "<form method='post'>";

        //SQL-Statement für die Bearbeitung eines Dozenten
        $statement = $this->dbh->prepare('SELECT * FROM `dozent` WHERE `ID_DOZENT` =:DozentID ');
        $result = $statement->execute(array("DozentID" => $this->dozentID));
        $data = $statement->fetch();

        //fetched:
        //[0]=ID des Dozenten
        //[1]=Nachname
        //[2]=Vorname
        //[3]=Titel
        //[4]=Deputat
        //[5]=Ueberstunden
        //[6]=Rollenname

        $output .= "<input type='text' name='Name' id='Name' value='" . $data[1] . "'><br>";
        $output .= "<input type='text' name='Vorname' id='Vorname' value='" . $data[2] . "'><br>";
        $output .= "<input type='text' name='Titel' id='Titel' value='" . $data[3] . "'><br>";
        $output .= "<input type='text' name='SWSSemester' id='SWSSemester' value='" . $data[4] . "'><br>";
        $output .= "<input type='text' name='Ueberstunden' id='Ueberstunden' value='" . $data[5] . "'><br>";
        //Zwischenspeicherung der ID für das Aktualisieren
        $output .= "<input type='hidden' name='DozentID' id='DozentID' value='" . $this->dozentID . "'><br>";

        $output .= "<input type='submit' name='submitSpeichern' id='submitSpeichern' value='Speichern'>";
        //Formularende
        $output .= "</form>";

        echo $output;
    }

    /**
     * @function deleteDozentFromDB
     * Löscht einen ausgewählten Dozenten aus der Datenbank
     */
    private function deleteDozentFromDB()
    {
        //SQL-Statement für das Löschen eines Dozenten
        $statement = $this->dbh->prepare("DELETE FROM `dozent` WHERE `ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));
        if ($result) {
            //Löschen erfolgreich
            $this->message = "Gelöscht";
        } else {
            //Fehler beim Löschen aufgetreten
            $this->message = "Fehler";
        }
    }

    /**
     * @function updateDozentInDB
     * Aktualisiert einen Dozenten in der Datenbank
     */
    private function updateDozentInDB()
    {
        //Neue Eigenschaften eines Dozenten aus dem Formular übernehmen
        $name = $this->getPOST("Name");
        $vorname = $this->getPOST("Vorname");
        $titel = $this->getPOST("Titel");
        $swsSemester = $this->getPOST("SWSSemester");
        $ueberstunden = $this->getPOST("Ueberstunden");

        //DozentID aus dem hidden-field
        $dozentID = $this->getPOST("DozentID");

        //SQL-Statement für das Aktualisieren des Dozenten
        $statement = $this->dbh->prepare("UPDATE `dozent` SET `NAME`= :Name,`VORNAME`= :Vorname,`TITEL`=:Titel,
`SWS_PRO_SEMESTER`= :SWSSemester,`UEBERSTUNDEN`= :Ueberstunden WHERE `ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("Name" => $name, "Vorname" => $vorname, "Titel" => $titel, "SWSSemester" => $swsSemester, "Ueberstunden" => $ueberstunden, "DozentID" => $dozentID));

        if ($result) {
            //Erfolgreich aktualisiert
            $this->message = "Update";
        } else {
            //Fehler beim aktualisieren
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