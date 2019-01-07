<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:26
 */

include("Benutzersitzung.php");

class Login extends Benutzersitzung
{
    private $benutzername;
    private $passwort;
    private $message;

    public function __construct()
    {
        parent::__construct();

        $this->cleanSession();

        if (isset($_POST["submit"])) {
            $this->initVar();

            if ($this->checkExistingUser($this->benutzername)) {
                if ($this->checkPasswort()) {
                    $this->setSession($this->benutzername);
                    $this->redirection($this->getSession("Rolle"));
                }
            }
            $this->showMessage();
        }
    }

    private function cleanSession(){
        $_SESSION["Rolle"]=NULL;
        $_SESSION["IdDozent"]=NULL;
    }

    private function redirection($rolle){
        switch ($rolle){
            case "Dozent": header("Location: dozenthome.php");
                break;
            case "Studiendekan": header("Location: studiendekanhome.php");
                break;
            case "Sekretariat": header("Location: sekretariathome.php");
                break;
            default: header("Location: login.php");
        }
    }

    private function initVar()
    {
        $_SESSION["IdDozent"]=NULL;
        $_SESSION["Rolle"]=NULL;

        $this->benutzername = $this->getPOST("Benutzername");
        $this->passwort = $this->getPOST("Passwort");
    }

    private function checkExistingUser($benutzername)
    {
        $statement = $this->dbh->prepare("SELECT * FROM `benutzerkonto` WHERE `BENUTZERNAME` = :benutzername");
        $result = $statement->execute(array("benutzername" => $benutzername));
        $data = $statement->fetch();

        if ($data[0] == NULL) {
            $this->message = "Falsch";
            return false;
        } else {
            $this->message = "Existiert";
            return true;
        }
    }

    private function checkPasswort()
    {

        $passwortHash = $this->getPasswort($this->benutzername);
        $this->message = password_verify($this->getPOST("Passwort"), $passwortHash);
        if ((password_verify($_POST["Passwort"], $passwortHash))) {
            return true;
        } else {
            $this->message = "Falsch";
            return false;
        }
    }

    private function getPasswort($benutzername)
    {
        $statement = $this->dbh->prepare("SELECT `PASSWORT` FROM `benutzerkonto` WHERE `BENUTZERNAME` = :benutzername");
        $result = $statement->execute(array("benutzername" => $benutzername));
        $data = $statement->fetch();
        return $data[0];
    }

    private function setSession($benutzername)
    {
        $statement = $this->dbh->prepare("SELECT `DOZENT_ID_DOZENT`, dozent.ROLLE_BEZEICHNUNG FROM `benutzerkonto` 
INNER JOIN dozent ON benutzerkonto.DOZENT_ID_DOZENT = dozent.ID_DOZENT WHERE `BENUTZERNAME` = :benutzername");
        $result = $statement->execute(array("benutzername" => $benutzername));
        $data = $statement->fetch();
        $_SESSION["IdDozent"] = $data[0];
        $_SESSION["Rolle"] = $data[1];

        echo "<script type='text/javascript'>alert('".$data[1].$data[0]."');</script>";
    }

    public function showMessage()
    {
        //Meldung über javascript alert() ausgeben
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }

}