<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 28.03.2019
 * Time: 18:12
 */

include("Benutzersitzung.php");

class ZusatzaAnlegen extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $message;

    /**
     * ZusatzaAnlegen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf nach Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Nach Buttonclick
        if (isset ($_POST["submit"])) {
            $this->insertIntoDB();
            $this->showMessage();
        }

    }

    /**
     * @function insertIntoDB
     * Legt eine neue Zusatzaufgabe in der Datenbank an
     */
    private function insertIntoDB(){
        //Laden der neuen Werte
        $bezeichnung = $this->getPOST('Bezeichnung');
        $kuerzel = $this->getPOST('Kuerzel');
        $sws = $this->getPOST('SWS');

        //SQL-Statement zum Erstellen einer neuen Zusatzaufgabe
        $statement = $this->dbh->prepare('INSERT INTO `arten_von_zusatzaufgaben`(`BEZEICHNUNG`, `KUERZEL`, `SWS`) VALUES (:Bezeichnung, :Kuerzel, :SWS)');
        $result = $statement->execute(array('Bezeichnung'=>$bezeichnung, 'Kuerzel'=>$kuerzel, 'SWS'=> $sws));

        if ($result) {
            $this->message='Erfolgreich erstellt';
        } else {
            $this->message='Fehler';
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