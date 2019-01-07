<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:44
 */

include ("Benutzersitzung.php");

class ZusatzAnlegen extends Benutzersitzung
{

    private $semesterID;
    private $dozentID;
    private $message;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();
        $this->setVar();

        if (isset($_POST["submit"])){
            $this->createLinkInDB();
            $this->showMessage();
        }
    }

    private function setVar(){
        $this->semesterID=$this->getCurrentSemester();
        $this->dozentID=$this->getSession("IdDozent");
    }

    private function createLinkInDB(){
        $matrikelnummer=$this->getPOST("Matrikelnummer");
        $zusatzaufgabeID=$this->getPOST("Art");
        $name=$this->getPOST("Name");

        $statement=$this->dbh->prepare("INSERT INTO `dozent_hat_zusatzaufgabe_in_s`(`DOZENT_ID_DOZENT`, `ARTEN_VON_ZUSATZAUFGABEN_ID_ART`, `NAME`, 
`MATRIKELNUMMER`, `SEMESTER_ID_SEMESTER`) VALUES (:DozentID,:ZusatzaufgabeID,:NameZ,:Matrikelnummer,:SemesterID)");
        $result=$statement->execute(array("DozentID"=>$this->dozentID,"ZusatzaufgabeID"=>$zusatzaufgabeID,"NameZ"=>$name,"Matrikelnummer"=>$matrikelnummer,"SemesterID"=>$this->semesterID));

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