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
include ("Benutzersitzung.php");

/**
 * Class ZusatzaZuweisen
 * Ermöglicht das Anlegen von Zusatzaufgaben für einen Dozenten
 */
class ZusatzaZuweisen extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $semesterID;
    private $dozentID;
    private $message;

    /**
     * ZusatzaZuweisen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf nach Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        $this->setVar();

        //Nach Buttonclick
        if (isset($_POST["submit"])){
            $this->createLinkInDB();
            $this->showMessage();
        }
    }

    /**
     * @function setVar
     * Initialisiert die Variablen mit Werten aus dem Formular und der Session
     */
    private function setVar(){
        $this->semesterID=$this->getCurrentSemester();
        $this->dozentID=$this->getSession("IdDozent");
    }

    /**
     * @function createLinkInDB
     * Erstellt die Verbindung Zusatzaufgabe/Dozent in der Datenbank
     */
    private function createLinkInDB(){
        //Variablen aus dem Formular laden
        $matrikelnummer=$this->getPOST("Matrikelnummer");
        $zusatzaufgabeID=$this->getPOST("Art");
        $name=$this->getPOST("Name");

        //SQL-Statement für das Anlegen der Verbindung Zusatzaufgabe/Dozent
        $statement=$this->dbh->prepare("INSERT INTO `dozent_hat_zusatzaufgabe_in_s`(`DOZENT_ID_DOZENT`, `ARTEN_VON_ZUSATZAUFGABEN_ID_ART`, `NAME`, 
`MATRIKELNUMMER`, `SEMESTER_ID_SEMESTER`) VALUES (:DozentID,:ZusatzaufgabeID,:NameZ,:Matrikelnummer,:SemesterID)");
        $result=$statement->execute(array("DozentID"=>$this->dozentID,"ZusatzaufgabeID"=>$zusatzaufgabeID,"NameZ"=>$name,"Matrikelnummer"=>$matrikelnummer,"SemesterID"=>$this->semesterID));

        if ($result){
            //Erfolgreich erstellt
            $this->message="Erstellt";
        } else {
            //Fehler beim Erstellen
            $this->message="Fehler";
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