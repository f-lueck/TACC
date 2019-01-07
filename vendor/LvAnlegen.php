<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 17:09
 */
include ("Benutzersitzung.php");
class lvAnlegen extends Benutzersitzung
{

    private $message;
    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset($_POST["submit"])){
            $this->insertLvInDB();
        }
    }

    private function insertLvInDB(){
        $Bezeichnung=$this->getPOST("Name");
        $SWS=$this->getPOST("SWS");
        $credits=$this->getPOST("Credits");
        $haeufigkeit=$this->getPOST("Haeufigkeit");
        $doppelt=$this->getPOST("Doppelt");
        $sommersemester=$this->getPOST("Sommersemester");
        $kosten=$this->getPOST("Kosten");
        $statement=$this->dbh->prepare('INSERT INTO `veranstaltung`(`BEZEICHNUNG`, `SWS`, `CREDITS`, `HAUEFIGKEIT_PA`,
 `FAKTOR_DOPPELUNG`, `SOMMERSEMESTER`, `KOSTEN_PA`) VALUES (:Bezeichnung,:SWS,:Credits,:Haeufigkeit,:Doppelt,:Sommer,:Kosten)');
        $result=$statement->execute(array('Bezeichnung'=>$Bezeichnung,'SWS'=>$SWS,'Credits'=>$credits,'Haeufigkeit'=>$haeufigkeit,'Doppelt'=>$doppelt,'Sommer'=>$sommersemester,'Kosten'=>$kosten));

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
    public function showMessage()
    {
        //Meldung über javascript alert() ausgeben
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }

}