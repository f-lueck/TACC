<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:45
 */

include ("Benutzersitzung.php");
class Passwort extends Benutzersitzung
{
    private $pw1;
    private $pw2;
    private $message;
    private $dozentID = 0;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset($_POST["submit"])){
            $this->setVar();
            if ($this->checkGleichheit()){
                $this->updatePasswort();
            }
            $this->showMessage();

        }
    }

    private function setVar(){
        $this->dozentID = $this->getSession("IdDozent");
        $this->pw1=$this->getPOST("PasswortNeu1");
        $this->pw2=$this->getPOST("PasswortNeu2");
    }

    private function updatePasswort(){

        $passwortNeu=$this->cryptPasswort($this->pw1);

        $statement=$this->dbh->prepare("UPDATE `benutzerkonto` SET `PASSWORT`=:PasswortNeu WHERE `DOZENT_ID_DOZENT` =:BenutzerID");
        $result = $statement->execute(array("PasswortNeu"=>$passwortNeu,"BenutzerID"=>$this->dozentID));
        $this->message=$result;

    }

    private function cryptPasswort($passwort){


        $option = ['cost=>12'];
        //Rückgabe des verschlüsselten Passworts mit BCrypt und 12er SALT
        return password_hash($passwort, PASSWORD_BCRYPT, $option);
    }

    private function checkGleichheit(){
        if (($this->pw1)==($this->pw2)){
            return true;
        } else {
            $this->message="Passwörter stimmen nicht überein";
            return false;
        }
    }

    public function showMessage()
    {
        //Meldung über javascript alert() ausgeben
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }
}