<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 19.11.2018
 * Time: 14:27
 */

include ("Benutzersitzung.php");
class DozentBearbeiten extends Benutzersitzung
{

    private $message;
    private $dozentID;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset ($_POST["submitSelect"])){
            $this->setDozentID();
            $this->createEditDozent();
        }

        if (isset($_POST["submitLöschen"])){
            $this->setDozentID();
            $this->deleteDozentFromDB();
            $this->showMessage();

        }

        if (isset($_POST["submitSpeichern"])){
            $this->updateDozentInDB();
            $this->showMessage();

        }
    }

    private function setDozentID(){
        $this->dozentID=$this->getPOST("Dozent");
    }

    private function createEditDozent(){

        $output="<form method='post'>";

        $statement=$this->dbh->prepare('SELECT * FROM `dozent` WHERE `ID_DOZENT` =:DozentID ');
        $result=$statement->execute(array("DozentID"=>$this->dozentID));
        $data=$statement->fetch();

        $output.="<input type='text' name='Name' id='Name' value='".$data[1]."'><br>";
        $output.="<input type='text' name='Vorname' id='Vorname' value='".$data[2]."'><br>";
        $output.="<input type='text' name='Titel' id='Titel' value='".$data[3]."'><br>";
        $output.="<input type='text' name='SWSSemester' id='SWSSemester' value='".$data[4]."'><br>";
        $output.="<input type='text' name='Ueberstunden' id='Ueberstunden' value='".$data[5]."'><br>";


        $output.="<input type='hidden' name='DozentID' id='DozentID' value='".$this->dozentID."'><br>";
        $output.="<input type='submit' name='submitSpeichern' id='submitSpeichern' value='Speichern'>";

        $output.="</form>";

        echo $output;
    }

    private function deleteDozentFromDB(){
        $statement = $this->dbh->prepare("DELETE FROM `dozent` WHERE `ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID"=>$this->dozentID));
        if ($result){
            $this->message="Gelöscht";
        } else {
            $this->message="Fehler";
        }
    }

    private function updateDozentInDB(){
        $name = $this->getPOST("Name");
        $vorname = $this->getPOST("Vorname");
        $titel =$this->getPOST("Titel");
        $swsSemester = $this->getPOST("SWSSemester");
        $ueberstunden =$this->getPOST("Ueberstunden");


        $dozentID=$this->getPOST("DozentID");

        $statement = $this->dbh->prepare("UPDATE `dozent` SET `NAME`= :Name,`VORNAME`= :Vorname,`TITEL`=:Titel,
`SWS_PRO_SEMESTER`= :SWSSemester,`UEBERSTUNDEN`= :Ueberstunden WHERE `ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("Name"=>$name,"Vorname"=>$vorname,"Titel"=>$titel,"SWSSemester"=>$swsSemester,"Ueberstunden"=>$ueberstunden,"DozentID"=>$dozentID));

        if ($result){
            $this->message="Update";
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