<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:44
 */
include ("Benutzersitzung.php");

class SonderaZuweisen extends Benutzersitzung
{

    private $message;
    private $semesterID;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();
        $this->setSemesterID();

        if (isset($_POST["submit"])){

            $this->createLinkInDB();
            $this->showMessage();

        }

    }

    private function setSemesterID(){
        $this->semesterID=$this->getCurrentSemester();
    }

    private function createLinkInDB(){
        $sonderaID=$this->getPOST("Sonderaufgabe");
        $dozentID=$this->getPOST("Dozent");
        $sws=$this->getPOST("SWS");


        $statement=$this->dbh->prepare("INSERT INTO `dozent_hat_sonderaufgabe_in_s`(`DOZENT_ID_DOZENT`, `SONDERAUFGABE_ID_SONDERAUFGABE`, 
`SEMESTER_ID_SEMESTER`, `WIRKLICHE_SWS`) VALUES (:DozentID,:SonderaID,:SemesterID,:SWS)");
        $result=$statement->execute(array("DozentID"=>$dozentID,"SonderaID"=>$sonderaID,"SemesterID"=>$this->semesterID,"SWS"=>$sws));

        if ($result){
            $this->message="Erstellt";
        } else {
            $this->message = "Fehler";
        }
    }

    public function showMessage()
    {
        //Meldung über javascript alert() ausgeben
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }
}