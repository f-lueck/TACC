<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 17:09
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class lvAnlegen
 * Ermöglicht das Anlegen von Lehrveranstaltungen
 */
class lvAnlegen extends Benutzersitzung
{
    /**
     * @var
     * Variable für Meldungen
     */
    private $message;

    /**
     * lvAnlegen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht den Methodenaufruf über Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf der Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Nach Klicken des Buttons
        if (isset($_POST["submit"])) {
            $this->insertLvInDB();
        }
    }

    /**
     * @function insertLvInDB
     * Erzeugt eine neue Lv in der Datenbank
     */
    private function insertLvInDB()
    {
        //Eigenschaften einer neuen Lv aus dem Formular übernehmen
        $Bezeichnung = $this->getPOST("Name");
        $SWS = $this->getPOST("SWS");
        $credits = $this->getPOST("Credits");
        $haeufigkeit = $this->getPOST("Haeufigkeit");
        $doppelt = $this->getPOST("Doppelt");
        $sommersemester = $this->getPOST("Sommersemester");
        $kosten = $this->getPOST("Kosten");

        //SQL-Statement für die Erstellung einer neuen LV
        $statement = $this->dbh->prepare('INSERT INTO `veranstaltung`(`BEZEICHNUNG`, `SWS`, `CREDITS`, `HAEUFIGKEIT_PA`,
 `FAKTOR_DOPPELUNG`, `SOMMERSEMESTER`, `KOSTEN_PA`) VALUES (:Bezeichnung,:SWS,:Credits,:Haeufigkeit,:Doppelt,:Sommer,:Kosten)');
        $result = $statement->execute(array('Bezeichnung' => $Bezeichnung, 'SWS' => $SWS, 'Credits' => $credits, 'Haeufigkeit' => $haeufigkeit, 'Doppelt' => $doppelt, 'Sommer' => $sommersemester, 'Kosten' => $kosten));

        if ($result) {
            //Erfolgreich registriert
            $this->message = 'Angelegt';
            $this->showMessage();
        } else {
            //Fehler bei der Eintragung
            $this->message = 'Fehler';
            $this->showMessage();
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