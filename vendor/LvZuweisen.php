<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 16:44
 */

/**
 * Includes
 * Für Polymorphies
 */
include("Benutzersitzung.php");

/**
 * Class lvZuweisen
 * Ermöglicht das Erzeugen von der Verbindung LV-Dozent
 */
class lvZuweisen extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $message;
    private $semester = 1;
    private $prozent = 1.0;

    /**
     * lvZuweisen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf bei Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Nach Clicken des Buttons
        if (isset($_POST["submit"])) {

            $this->createLinkInDB();
            $this->showMessage();
        }
    }

    /**
     * @function createLinkInDB
     * Erstellt die Verbindung LV-Dozent in der Datenbank
     */
    private function createLinkInDB()
    {
        //IDs von Veranstaltung und Dozent laden
        $lvID = $this->getPOST("Lv");
        $dozentID = $this->getPOST("Dozent");
        $sws = $this->getPOST("SWS");

        //SQL-Statement zu Erstellung der Verbindung LV-Dozent
        $statement = $this->dbh->prepare('INSERT INTO `dozent_hat_veranstaltung_in_s`(`DOZENT_ID_DOZENT`, 
`VERANSTALTUNG_ID_VERANSTALTUNG`, `SEMESTER_ID_SEMESTER`, `ANTEIL_PROZENT`, `WIRKLICHE_SWS`) VALUES (:DozentID,:LvID,:Semester,:Prozent,:SWS)');
        $result = $statement->execute(array("DozentID" => $dozentID, "LvID" => $lvID, "Semester" => $this->semester, "Prozent" => $this->prozent, "SWS" => $sws));

        if ($result) {
            //Verbindung erstellt
            $this->message = "Erstellt";
        } else {
            //Fehler bei der Erstellung
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