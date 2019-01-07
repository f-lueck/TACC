<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 17:26
 */

include ("Benutzersitzung.php");
class SonderaAnlegen extends Benutzersitzung
{
    private $message;
    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset($_POST["submit"])){

            $this->insertSonderaInDB();
        }
    }

    private function insertSonderaInDB (){
        $name = $this->getPOST("NameS");
        $sws = $this->getPOST("SWS");

        $statement=$this->dbh->prepare('INSERT INTO `sonderaufgabe`(`BEZEICHNUNG`, `SWS`) VALUES (:Bezeichnung,:SWS)');
        $result=$statement->execute(array('Bezeichnung'=>$name,'SWS'=>$sws));

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