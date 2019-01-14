<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 17:26
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class SonderaAnlegen
 * Ermöglicht das Anlegen von Sonderaufgaben
 */
class SonderaAnlegen extends Benutzersitzung
{
    /**
     * @var
     * Variable für Meldungen
     */
    private $message;

    /**
     * SonderaAnlegen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht den Methodenaufruf nach Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffskontrolle
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Nach Buttonclick
        if (isset($_POST["submit"])) {
            $this->insertSonderaInDB();
            $this->showMessage();
        }
    }

    /**
     * @function insertSonderaInDB
     * Legt eine neue Sonderaufgabe in der Datenbank an
     */
    private function insertSonderaInDB()
    {
        //Laden der Variablen aus dem Formular
        $name = $this->getPOST("NameS");
        $sws = $this->getPOST("SWS");

        //SQL-Statement für das Anlegen einer neuen Sonderaufgabe
        $statement = $this->dbh->prepare('INSERT INTO `sonderaufgabe`(`BEZEICHNUNG`, `SWS`) VALUES (:Bezeichnung,:SWS)');
        $result = $statement->execute(array('Bezeichnung' => $name, 'SWS' => $sws));

        if ($result) {
            //Erfolgreich angelegt
            $this->message = 'Angelegt';
        } else {
            //Fehler beim Anlegen
            $this->message = 'Fehler';
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