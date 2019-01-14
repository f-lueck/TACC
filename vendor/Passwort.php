<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:45
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class Passwort
 * Ermöglicht das Ändern des Passworts eines Benutzers
 */
class Passwort extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $pw1;
    private $pw2;
    private $message;
    private $dozentID = 0;

    /**
     * Passwort constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf über Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Nach Buttonclick
        if (isset($_POST["submit"])) {
            $this->setVar();
            if ($this->checkGleichheit()) {
                $this->updatePasswort();
            }
            $this->showMessage();
        }
    }

    /**
     * @function setVar
     * Initialisiert die Variablen mit den Werten aus dem Formular und der Session
     */
    private function setVar()
    {
        $this->dozentID = $this->getSession("IdDozent");
        $this->pw1 = $this->getPOST("PasswortNeu1");
        $this->pw2 = $this->getPOST("PasswortNeu2");
    }

    /**
     * @function updatePasswort
     * Aktualisiert das Passwort in der Datenbank
     */
    private function updatePasswort()
    {
        //Verschlüsselung
        $passwortNeu = $this->cryptPasswort($this->pw1);

        //SQL-Statement zum Aktualisieren des Passworts
        $statement = $this->dbh->prepare("UPDATE `benutzerkonto` SET `PASSWORT`=:PasswortNeu WHERE `DOZENT_ID_DOZENT` =:BenutzerID");
        $result = $statement->execute(array("PasswortNeu" => $passwortNeu, "BenutzerID" => $this->dozentID));
        $this->message = $result;
    }

    /**
     * @function passwordCrypt
     * Verschlüsselt das Passwort mit BCrypt und Salt
     * @param $passwort
     * Zu verschlüsselndes Passwort
     * @return bool|string
     * Verschlüsseltes Passwort
     */
    private function cryptPasswort($passwort)
    {
        //12er Salt
        $option = ['cost=>12'];

        return password_hash($passwort, PASSWORD_BCRYPT, $option);
    }

    /**
     * @function checkGleichheit
     * Prüft ob die eingegebenen Passwörter übereinstimmen
     * @return bool
     * Wahrheitswert auf Basis der Gleicheit
     */
    private function checkGleichheit()
    {
        if (($this->pw1) == ($this->pw2)) {
            //Passwörter stimmen überein
            return true;
        } else {
            //Passwörter stimmen nicht überein
            $this->message = "Passwörter stimmen nicht überein";
            return false;
        }
    }

    /**
     * @function showMessage
     * Liefert Meldungen über Javascript alert() zurück
     */
    public function showMessage()
    {
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }
}