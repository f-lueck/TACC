<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 15:02
 */
include ("Benutzersitzung.php");

class DozentAnlegen extends Benutzersitzung
{
    private $message;
    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset($_POST["submit"])){

            if (!$this->checkUser()){

            if ($this->checkPassword()) {
                $this->insertDozentInDB();
                $this->insertUserInDB();
            }
        }}
    }

    private function insertDozentInDB()
    {
        $nachname = $this->getPOST("Nachname");
        $vorname = $this->getPOST("Vorname");
        $titel = $this->getPOST("Titel");
        $rolle = $this->getPOST("Rolle");
        $sws = $this->getPOST("SWS");
        $ueberstunden = $this->getPOST("Ueberstunden");

        $statement = $this->dbh->prepare('INSERT INTO `dozent`(`NAME`, `VORNAME`, `TITEL`, `SWS_PRO_SEMESTER`, `UEBERSTUNDEN`, `ROLLE_BEZEICHNUNG`) VALUES (:name,:vorname,:titel,:sws,:ueberstunden,:rolle)');
        $result = $statement->execute(array('name' => $nachname, 'vorname' => $vorname, 'titel' => $titel, 'sws' => $sws, 'ueberstunden' => $ueberstunden, 'rolle' => $rolle));

    }
private function checkPassword(){
        $passwort1=$this->getPOST("Passwort1");
        $passwort2=$this->getPOST("Passwort2");

        if ($passwort1!=$passwort2){
            $this->message="Passwörter stimmen nicht überein";
            $this->showMessage();
            return false;
        } else {
            return true;
        }
}

private function passwordCrypt($passwort){
    $option = ['cost=>12'];
    //Rückgabe des verschlüsselten Passworts mit BCrypt und 12er SALT
    return password_hash($passwort, PASSWORD_BCRYPT);
}

private function insertUserInDB(){
    $benutzername = $this->getPOST("Benutzername");
    $passwort = $this->getPOST("Passwort1");
    $nachname =$this->getPOST("Nachname");
    $vorname =$this->getPOST("Vorname");

    $passwortcrypt = $this->passwordCrypt($passwort);
    $dozentID=$this->getDozentID($nachname,$vorname);

    $statement=$this->dbh->prepare('INSERT INTO `benutzerkonto`(`BENUTZERNAME`, `DOZENT_ID_DOZENT`, `PASSWORT`) VALUES (:benutzername,:dozentID,:passwort)');
    $result=$statement->execute(array('benutzername'=>$benutzername,'dozentID'=>$dozentID,'passwort'=>$passwortcrypt));

    if ($result) {
        //Erfolgreich registriert
        $this->message = 'Registriert';
        $this->showMessage();
    } else {
        //Fehler bei der Eintragung
        $this->message = 'Fehler';
        $this->showMessage();
    }

}

private function checkUser(){
        $benutzername = $this->getPOST("Benutzername");
        $data=$this->getBenutzer($benutzername);


    //Prüfung, ob Daten zurückgegeben wurden
    if ($data == false) {
        //Nutzer existiert nicht (Keine Daten vorhanden)
        $this->message = 'Nicht registriert';
        $this->showMessage();
        return false;
    }
    //Nutzer existiert (Daten vorhanden)
    $this->message = 'Nutzername bereits in Verwendung';
    $this->showMessage();
    return true;
}

    public function showMessage()
    {
        //Meldung über javascript alert() ausgeben
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }

}