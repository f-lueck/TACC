<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 15:02
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class DozentAnlegen
 * Ermöglicht das Anlegen eines neuen Benutzers
 */
class DozentAnlegen extends Benutzersitzung
{
    /**
     * @var
     * Benachrichtigung von auftretenden Fehlern
     */
    private $message;

    /**
     * DozentAnlegen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht das Aufrufen von Methoden bei Kicken des Buttons
     */
    public function __construct()
    {
        //Konstruktoraufruf der Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Falls der Button geklickt wurde
        if (isset($_POST["submit"])) {

            if (!$this->checkUser()) {

                if ($this->checkPassword()) {
                    $this->insertDozentInDB();
                    $this->insertUserInDB();
                }
            }
        }
    }

    /**
     * @function insertDozentInDb
     * Legt einen neuen Benutzer in der Datenbank an
     */
    private function insertDozentInDB()
    {
        //Eigenschaften eines neuen Dozenten übernehmen
        $nachname = $this->getPOST("Nachname");
        $vorname = $this->getPOST("Vorname");
        $titel = $this->getPOST("Titel");
        $rolle = $this->getPOST("Rolle");
        $sws = $this->getPOST("SWS");
        $ueberstunden = $this->getPOST("Ueberstunden");

        //SQL-Statement für das Anlegen eines neuen Dozenten
        $statement = $this->dbh->prepare('INSERT INTO `dozent`(`NAME`, `VORNAME`, `TITEL`, `SWS_PRO_SEMESTER`, `UEBERSTUNDEN`, `ROLLE_BEZEICHNUNG`) VALUES (:name,:vorname,:titel,:sws,:ueberstunden,:rolle)');
        $result = $statement->execute(array('name' => $nachname, 'vorname' => $vorname, 'titel' => $titel, 'sws' => $sws, 'ueberstunden' => $ueberstunden, 'rolle' => $rolle));
    }

    /**
     * @function checkPassword
     * Prüft ob beide Passwörter übereinstimmen
     * @return bool
     * Wahrheitswert auf Basis der Übereinstimmung
     */
    private function checkPassword()
    {
        //Passwörter aus Formular übernehmen
        $passwort1 = $this->getPOST("Passwort1");
        $passwort2 = $this->getPOST("Passwort2");

        if ($passwort1 != $passwort2) {
            //Passwörter stimmen nicht überein
            $this->message = "Passwörter stimmen nicht überein";
            $this->showMessage();
            return false;
        } else {
            //Passwörter stimmen überein
            return true;
        }
    }

    /**
     * @function passwordCrypt
     * Verschlüsselt das Passwort mit BCrypt und Salt
     * @param $passwort
     * Zu verschlüsselndes Passwort
     * @return bool|string
     * Verschlüsseltes Passwort
     */
    private function passwordCrypt($passwort)
    {
        //12er Salt
        $option = ['cost=>12'];

        return password_hash($passwort, PASSWORD_BCRYPT, $option);
    }

    /**
     * @function insertUserInDb
     * Legt einen neuen Benuter in der Datenbank an
     */
    private function insertUserInDB()
    {
        //Übernehmen der Eingenschaften eines neuen Benutzers aus dem Formular
        $benutzername = $this->getPOST("Benutzername");
        $passwort = $this->getPOST("Passwort1");
        $nachname = $this->getPOST("Nachname");
        $vorname = $this->getPOST("Vorname");

        //Verschlüsseln des Passworts
        $passwortcrypt = $this->passwordCrypt($passwort);
        //Abfragen der Dozenten ID für Verlinkung Benutzer-Dozent
        $dozentID = $this->getDozentID($nachname, $vorname);

        //SQL-Statement zum Anlegen eines neuen Benutzers
        $statement = $this->dbh->prepare('INSERT INTO `benutzerkonto`(`BENUTZERNAME`, `DOZENT_ID_DOZENT`, `PASSWORT`) VALUES (:benutzername,:dozentID,:passwort)');
        $result = $statement->execute(array('benutzername' => $benutzername, 'dozentID' => $dozentID, 'passwort' => $passwortcrypt));

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

    /**
     * @function checkUser
     * Prüft ob schon ein Benutzer mit diesem Benutzernamen existiert
     * @return bool
     * Wahrheitswert auf Basis des Vorhandenseins des Benuternamens
     */
    private function checkUser()
    {
        $benutzername = $this->getPOST("Benutzername");
        //Abfrage des Benutzers in der Datenbank
        $data = $this->getBenutzer($benutzername);


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

    /**
     * @function showMessage
     * Liefert Meldungen über Javascript alert() zurück
     */
    public function showMessage()
    {
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }
}