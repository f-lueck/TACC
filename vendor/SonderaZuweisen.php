<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:44
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class SonderaZuweisen
 * Ermöglicht die Zuweisung von Sonderaufgaben
 */
class SonderaZuweisen extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $message;
    private $semesterID;

    /**
     * SonderaZuweisen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf nach Buttonclick
     */
    public function __construct()
    {
        //Kontruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        $this->setSemesterID();

        //Nach Buttonclick
        if (isset($_POST["submit"])) {
            $this->createLinkInDB();
            $this->showMessage();
        }
    }

    /**
     * @function setSemesterID
     * Setzt die Variable $semesterID auf den aktuellen Wert aus der Datenbank
     */
    private function setSemesterID()
    {
        $this->semesterID = $this->getCurrentSemester();
    }

    /**
     * @function createLinkInDB
     * Erstellt die Verbindung Sonderaufgabe/Dozent in der Datenbank
     */
    private function createLinkInDB()
    {
        //Variablen aus dem Formular laden
        $sonderaID = $this->getPOST("Sonderaufgabe");
        $dozentID = $this->getPOST("Dozent");
        $sws = $this->getPOST("SWS");

        //SQL-Statement für die Erstellung der Verlinkung
        $statement = $this->dbh->prepare("INSERT INTO `dozent_hat_sonderaufgabe_in_s`(`DOZENT_ID_DOZENT`, `SONDERAUFGABE_ID_SONDERAUFGABE`, 
`SEMESTER_ID_SEMESTER`, `WIRKLICHE_SWS`) VALUES (:DozentID,:SonderaID,:SemesterID,:SWS)");
        $result = $statement->execute(array("DozentID" => $dozentID, "SonderaID" => $sonderaID, "SemesterID" => $this->semesterID, "SWS" => $sws));

        if ($result) {
            //Erfolgreich erstellt
            $this->message = "Erstellt";
        } else {
            //Fehler beim Erstellen
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