<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 14:48
 */
include ("Benutzersitzung.php");

class SonderaBearbeiten extends Benutzersitzung
{
    private $message;
    private $sonderaID;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset ($_POST["submitSelect"])){
            $this->setSonderaID();
            $this->createEditSonderaufgaben();
        }

        if (isset($_POST["submitLöschen"])){
            $this->setSonderaID();
            $this->deleteSonderaFormDB();
            $this->showMessage();

        }

        if (isset($_POST["submitSpeichern"])){
            $this->updateSonderaInDB();
            $this->showMessage();

        }
    }

    private function setSonderaID(){
        $this->sonderaID=$this->getPOST("Sonderaufgabe");
        $this->message=$this->sonderaID;
    }

    private function createEditSonderaufgaben(){

        $output="<form method='post'>";

        $statement=$this->dbh->prepare('SELECT `BEZEICHNUNG`,`SWS` FROM `sonderaufgabe` WHERE `ID_SONDERAUFGABE` =:SonderaufgabeID ');
        $result=$statement->execute(array("SonderaufgabeID"=>$this->sonderaID));
        $data=$statement->fetch();

        $output.="<input type='text' name='Bezeichnung' id='Bezeichnung' value='".$data[0]."'><br>";
        $output.="<input type='text' name='SWS' id='SWS' value='".$data[1]."'><br>";
        $output.="<input type='hidden' name='SonderaID' id='SonderaID' value='".$this->sonderaID."'><br>";
        $output.="<input type='submit' name='submitSpeichern' id='submitSpeichern' value='Speichern'>";

        $output.="</form>";

        echo $output;
    }

    private function deleteSonderaFormDB(){
        $statement = $this->dbh->prepare("DELETE FROM `sonderaufgabe` WHERE `ID_SONDERAUFGABE` = :SonderaufgabeID");
        $result = $statement->execute(array("SonderaufgabeID"=>$this->sonderaID));
        if ($result){
            $this->message="Gelöscht";
        } else {
            $this->message="Fehler";
        }
    }

    private function updateSonderaInDB(){
        $bezeichnung = $this->getPOST("Bezeichnung");
        $sws = $this->getPOST("SWS");
        $sonderaID=$this->getPOST("SonderaID");

        $statement = $this->dbh->prepare("UPDATE `sonderaufgabe` SET `BEZEICHNUNG`=:Bezeichnung,`SWS`=:SWS WHERE `ID_SONDERAUFGABE` =:SonderaufgabeID");
        $result = $statement->execute(array("Bezeichnung"=>$bezeichnung,"SWS"=>$sws,"SonderaufgabeID"=>$sonderaID));

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