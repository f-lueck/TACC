<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 15:01
 */
include("Datenbank.php");

class Hilfsmethoden extends Datenbank
{
    private $message;



    public function __construct()
    {

        parent::__construct();
    }

    public function createRollenDropdown()
    {

        $statement = $this->dbh->prepare('SELECT * FROM rolle');

        $result = $statement->execute();

        $output = "<select name='Rolle' id='Rolle'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[0] . '</option>';

        }

        $output .= "</select>";

        echo $output;

    }

    public function createSonderaDropdown()
    {
        $statement = $this->dbh->prepare('SELECT `ID_SONDERAUFGABE`,`BEZEICHNUNG` FROM `sonderaufgabe`');
        $result = $statement->execute();

        $output = "<select name='Sonderaufgabe' id='Sonderaufgabe'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[1] . '</option>';
        }
        $output .= "</select>";
        echo $output;
    }

    public function createLvDropdown()
    {
        $statement = $this->dbh->prepare('SELECT `ID_VERANSTALTUNG`,`BEZEICHNUNG` FROM `veranstaltung`');
        $result = $statement->execute();

        $output = "<select name='Lv' id='Lv'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[1] . '</option>';
        }
        $output .= "</select>";
        echo $output;
    }

    public function createDozentDropdown()
    {
        $rolle = "Dozent";
        $statement = $this->dbh->prepare('SELECT `ID_DOZENT`, `VORNAME`, `NAME` FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` = :Rolle');
        $result = $statement->execute(array("Rolle" => $rolle));

        $output = "<select name='Dozent' id='Dozent'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[1] . ' ' . $data[2] . '</option>';
        }
        $output .= "</select>";
        echo $output;
    }

    public function createArtVonZusatzaufgabeDropdown()
    {
        $statement = $this->dbh->prepare("SELECT * FROM `arten_von_zusatzaufgaben`");
        $result = $statement->execute();

        $output = "<select name='Art' id='Art'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[1] . '</option>';
        }
        $output .= "</select>";
        echo $output;

    }


    public function getDozentID($nachname, $vorname)
    {
        $statement = $this->dbh->prepare('SELECT `ID_DOZENT` FROM `dozent` WHERE `NAME` = :nachname AND `VORNAME` = :vorname');
        $result = $statement->execute(array('nachname' => $nachname, 'vorname' => $vorname));
        $data = $statement->fetch();
        return $data[0];
    }

    public function getBenutzer($benutzername)
    {
        $statement = $this->dbh->prepare('SELECT * FROM `benutzerkonto` WHERE `BENUTZERNAME` = :benutzername');
        $result = $statement->execute(array('benutzername' => $benutzername));
        $data = $statement->fetch();
        return $data;
    }

    public function getlvBezeichnung($lvID)
    {
        $statement = $this->dbh->prepare('SELECT `BEZEICHNUNG` FROM `veranstaltung` WHERE `ID_VERANSTALTUNG` = :LvID');
        $result = $statement->execute(array("LvID" => $lvID));
        $data = $statement->fetch();
        return $data[0];
    }

    public function getDozent($dozentID)
    {
        $statement = $this->dbh->prepare('SELECT `NAME`, `VORNAME`, `TITEL`FROM `dozent` WHERE `ID_DOZENT` = :DozentID');
        $result = $statement->execute(array("DozentID" => $dozentID));
        $data = $statement->fetch();

        return $data;

    }

    public function getLv($lvID)
    {
        $statement = $this->dbh->prepare("SELECT `BEZEICHNUNG` FROM `veranstaltung` WHERE `ID_VERANSTALTUNG` = :lvID");
        $result = $statement->execute(array("lvID" => $lvID));
        $data = $statement->fetch();

        return $data[0];
    }

    public function getSonderaBezeichnung($sonderaID)
    {
        $statement = $this->dbh->prepare('SELECT `BEZEICHNUNG`,`ID_SONDERAUFGABE` FROM `sonderaufgabe` WHERE `ID_SONDERAUFGABE` = :SonderaufgabeID');
        $result = $statement->execute(array("SonderaufgabeID" => $sonderaID));
        $data = $statement->fetch();
        return $data[0];
    }

    public function formatDozent($data)
    {
        $titel = $data[2];
        $vorname = $data[1];
        $name = $data[0];

        $dozent = $titel . " " . $vorname . " " . $name;
        return $dozent;


    }

    public function getZusatzaufgabeArt($artID)
    {
        $statement = $this->dbh->prepare("SELECT * FROM `arten_von_zusatzaufgaben` WHERE `ID_ART` = :ArtID");
        $result = $statement->execute(array("ArtID" => $artID));
        $data = $statement->fetch();
        return $data[1];
    }


    public function getPOST($POST)
    {
        return $_POST["$POST"];
    }

    public function showMessage()
    {
        //Meldung über javascript alert() ausgeben
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }

    public function getCurrentDate()
    {
        $timestamp = time();
        $date = date("d.m.Y", $timestamp);
        return $date;
    }

    public function getSWSZusatz($dozentID)
    {
        $statement = $this->dbh->prepare('SELECT SUM(arten_von_zusatzaufgaben.SWS) FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART=arten_von_zusatzaufgaben.ID_ART 
WHERE `DOZENT_ID_DOZENT` = :DozentID');
        $result=$statement->execute(array("DozentID"=>$dozentID));
        $data=$statement->fetch();
        $sws=number_format($data[0],1);
        if ($sws>2){
            $sws=2;
        }
        return $sws;
    }

    public function getSWSSonder($dozentID){
        $statement=$this->dbh->prepare('SELECT SUM(`WIRKLICHE_SWS`) FROM `dozent_hat_sonderaufgabe_in_s` WHERE `DOZENT_ID_DOZENT` = :DozentID');
        $result=$statement->execute(array("DozentID"=>$dozentID));
        $data=$statement->fetch();
        $sws=number_format($data[0],1);
        return $sws;
    }

    public function getSWSLv($dozentID){
        $statement=$this->dbh->prepare('SELECT SUM(`WIRKLICHE_SWS`) FROM `dozent_hat_veranstaltung_in_s` WHERE `DOZENT_ID_DOZENT` = :DozentID');
        $result=$statement->execute(array("DozentID"=>$dozentID));
        $data=$statement->fetch();
        $sws=number_format($data[0],1);
        return $sws;
    }

    public function getCurrentSemester(){
        $statement=$this->dbh->prepare("SELECT MAX(ID_SEMESTER) FROM `semester`");
        $result=$statement->execute();
        $data=$statement->fetch();
        return $data[0];
    }

    public function formatSemester($semesterID){
        $statement=$this->dbh->prepare("SELECT `BEZEICHNUNG` FROM `semester` WHERE `ID_SEMESTER` = :SemesterID");
        $result=$statement->execute(array("SemesterID"=>$semesterID));
        $data=$statement->fetch();
        return $data[0];

    }

    public function getCurrentUeberstunden($dozentID){
        $statement=$this->dbh->prepare("SELECT `UEBERSTUNDEN` FROM `dozent` WHERE `ID_DOZENT` = :DozentID");
        $result=$statement->execute(array("DozentID"=>$dozentID));
        $data=$statement->fetch();
        return $data[0];
    }

    public function getSWSArt($dozentID, $artID){;
        $statement=$this->dbh->prepare("SELECT COUNT(`ID_ZUSATZAUFGABE`),arten_von_zusatzaufgaben.SWS FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART=arten_von_zusatzaufgaben.ID_ART 
WHERE `ARTEN_VON_ZUSATZAUFGABEN_ID_ART` = :ArtPraxisprojektID AND `DOZENT_ID_DOZENT` = :DozentID");
        $result=$statement->execute(array("ArtPraxisprojektID"=>$artID,"DozentID"=>$dozentID));
        $data=$statement->fetch();

        $sws = $data[0]*$data[1];
        return $sws;
    }

    public function getSWSProSemester($dozentID){
        $statement=$this->dbh->prepare("SELECT `SWS_PRO_SEMESTER` FROM `dozent` WHERE `ID_DOZENT` = :DozentID");
        $result=$statement->execute(array("DozentID"=>$dozentID));
        $data=$statement->fetch();
        return $data[0];
    }

    public function getSession($key){
        return $_SESSION["$key"];
    }
}