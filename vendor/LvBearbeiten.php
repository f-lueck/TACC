<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 15:46
 */
include ("Benutzersitzung.php");

class LvBearbeiten extends Benutzersitzung
{

    private $message;
    private $lvID;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset ($_POST["submitSelect"])){
            $this->setLvID();
            $this->createEditLv();
        }

        if (isset($_POST["submitLöschen"])){
            $this->setLvID();
            $this->deleteLvFromDB();
            $this->showMessage();

        }

        if (isset($_POST["submitSpeichern"])){
            $this->updateLvInDB();
            $this->showMessage();

        }
    }

    private function setLvID(){
        $this->lvID=$this->getPOST("Lv");
    }

    private function createEditLv(){

        $output="<form method='post'>";

        $statement=$this->dbh->prepare('SELECT * FROM `veranstaltung` WHERE `ID_VERANSTALTUNG` =:LvID ');
        $result=$statement->execute(array("LvID"=>$this->lvID));
        $data=$statement->fetch();

        $output.="<input type='text' name='Bezeichnung' id='Bezeichnung' value='".$data[1]."'><br>";
        $output.="<input type='text' name='SWS' id='SWS' value='".$data[2]."'><br>";
        $output.="<input type='text' name='Credits' id='Credits' value='".$data[3]."'><br>";
        $output.="<input type='text' name='Haeufigkeit' id='Haeufigkeit' value='".$data[4]."'><br>";
        $output.="<input type='text' name='Doppelung' id='Doppelung' value='".$data[5]."'><br>";
        $output.="<input type='text' name='Sommersemester' id='Sommersemester' value='".$data[6]."'><br>";
        $output.="<input type='text' name='Kosten' id='Kosten' value='".$data[7]."'><br>";



        $output.="<input type='hidden' name='LvID' id='LvID' value='".$this->lvID."'><br>";
        $output.="<input type='submit' name='submitSpeichern' id='submitSpeichern' value='Speichern'>";

        $output.="</form>";

        echo $output;
    }

    private function deleteLvFromDB(){
        $statement = $this->dbh->prepare("DELETE FROM `veranstaltung` WHERE `ID_VERANSTALTUNG` = :LvID");
        $result = $statement->execute(array("LvID"=>$this->lvID));
        if ($result){
            $this->message="Gelöscht";
        } else {
            $this->message="Fehler";
        }
    }

    private function updateLvInDB(){
        $bezeichnung = $this->getPOST("Bezeichnung");
        $sws = $this->getPOST("SWS");
        $credits =$this->getPOST("Credits");
        $haeufigkeit = $this->getPOST("Haeufigkeit");
        $doppelung =$this->getPOST("Doppelung");
        $sommersemester =$this->getPOST("Sommersemester");
        $kosten = $this->getPOST("Kosten");

        $LvID=$this->getPOST("LvID");

        $statement = $this->dbh->prepare("UPDATE `veranstaltung` SET `BEZEICHNUNG`= :Bezeichnung,`SWS`= :SWS,
`CREDITS`= :Credits,`HAUEFIGKEIT_PA`= :Haeufigkeit,`FAKTOR_DOPPELUNG`= :Doppelung,`SOMMERSEMESTER`= :Sommersemester,`KOSTEN_PA`= :Kosten WHERE `ID_VERANSTALTUNG` = :LvID");
        $result = $statement->execute(array("Bezeichnung"=>$bezeichnung,"SWS"=>$sws,"Credits"=>$credits,"Haeufigkeit"=>$haeufigkeit,"Doppelung"=>$doppelung,
            "Sommersemester"=>$sommersemester,"Kosten"=>$kosten,"LvID"=>$LvID));

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