<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 16:44
 */

include ("Benutzersitzung.php");
class lvZuweisen extends Benutzersitzung
{

    private $message;
    private $semester = 1;
    private $prozent = 1.0;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset($_POST["submit"])){

            $this->createLinkInDB();
            $this->showMessage();
        }


    }

    private function createLinkInDB(){

        $lvID=$this->getPOST("Lv");
        $dozentID=$this->getPOST("Dozent");
        $sws=$this->getPOST("SWS");

        $statement=$this->dbh->prepare('INSERT INTO `dozent_hat_veranstaltung_in_s`(`DOZENT_ID_DOZENT`, 
`VERANSTALTUNG_ID_VERANSTALTUNG`, `SEMESTER_ID_SEMESTER`, `ANTEIL_PROZENT`, `WIRKLICHE_SWS`) VALUES (:DozentID,:LvID,:Semester,:Prozent,:SWS)');
        $result =$statement->execute(array("DozentID"=>$dozentID,"LvID"=>$lvID,"Semester"=>$this->semester,"Prozent"=>$this->prozent,"SWS"=>$sws));

        if ($result){
            $this->message="Erstellt";
        } else {
            $this->message="Fehler";
        }
    }

    public function showMessage()
    {
        //Meldung über javascript alert() ausgeben
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }
}