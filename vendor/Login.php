<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:26
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class Login
 * Ermöglicht das Einloggen und Abmelden eines Benutzers
 */
class Login extends Benutzersitzung
{
    /**
     * @var
     * Variablen für die Weiterverarbeitung
     */
    private $benutzername;
    private $passwort;
    private $message;

    /**
     * Login constructor.
     * Erzeugt ein Objekt der Klasse und ermöglicht Methodenaufruf durch Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Löschen einer bereits bestehenden Session (Abmelden)
        $this->cleanSession();

        //Anmeldeablauf bei Buttonclick
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

    /**
     * @function cleanSession
     * Überschreibt eine bereits vorhandene Session (Abmelden)
     */
    private function cleanSession()
    {
        $_SESSION["Rolle"] = NULL;
        $_SESSION["IdDozent"] = NULL;
    }

    /**
     * @function redirection
     * Leitet auf Basis der Rolle auf das korrekte Interface weiter
     * @param $rolle
     * Variable, welche die Weiterleitung bestimmt
     */
    private function redirection($rolle)
    {
        switch ($rolle) {
            case "Dozent":
                header("Location: dozenthome.php");
                break;
            case "Studiendekan":
                header("Location: studiendekanhome.php");
                break;
            case "Sekretariat":
                header("Location: sekretariathome.php");
                break;
                //Flasche Rolle
            default:
                header("Location: login.php");
        }
    }

    /**
     * @function initVar
     * Überschreibt vorhandene Session-Daten und setzt globale Variablen mit Werten aus dem Formular
     */
    private function initVar()
    {
        $_SESSION["IdDozent"] = NULL;
        $_SESSION["Rolle"] = NULL;

        $this->benutzername = $this->getPOST("Benutzername");
        $this->passwort = $this->getPOST("Passwort");
    }

    /**
     * @function checkExistingUser
     * Überprüft ob ein Benutzer mit dem Benutzernamen existiert
     * @param $benutzername
     * Zu prüfender Benutzername
     * @return bool
     * Wahrheitswert auf Basis der Existenz des Benutzers
     */
    private function checkExistingUser($benutzername)
    {
        //SQL-Statement für das Prüfen des Benutzers
        $statement = $this->dbh->prepare("SELECT * FROM `benutzerkonto` WHERE `BENUTZERNAME` = :benutzername");
        $result = $statement->execute(array("benutzername" => $benutzername));
        $data = $statement->fetch();

        //fetched:
        //[0]=ID des Dozenten
        //[1]=Nachname
        //[2]=Vorname
        //[3]=Titel
        //[4]=Deputat
        //[5]=Ueberstunden
        //[6]=Rollenname

        if ($data[0] == NULL) {
            //Benutzer existiert nicht
            $this->message = "Falsch";
            return false;
        } else {
            //Benutzer exisitiert
            $this->message = "Existiert";
            return true;
        }
    }

    /**
     * @function checkPasswort
     * Prüft die Übereinstimmung des Passworts aus dem Formular mit dem aus der Datenbank
     * @return bool
     * Wahrheitswert auf Basis der Übereinstimmung
     */
    private function checkPasswort()
    {
        //Passwort aus der Datenbank holen
        $passwortHash = $this->getPasswort($this->benutzername);

        if ((password_verify($_POST["Passwort"], $passwortHash))) {
            //Passwörter stimmen überein
            return true;
        } else {
            //Passwörter stimmen nicht überein
            $this->message = "Falsch";
            return false;
        }
    }

    /**
     * @function getPasswort
     * Liefer das Passwort auf Basis eines Benutzernamens aus der Datenbank zurück
     * @param $benutzername
     * Der Benutzername für welcher das Passwort gebraucht wird
     * @return mixed
     * Passwort aus der Datenbank
     */
    private function getPasswort($benutzername)
    {
        //SQL-Statement für das Passwort eines Benutzers
        $statement = $this->dbh->prepare("SELECT `PASSWORT` FROM `benutzerkonto` WHERE `BENUTZERNAME` = :benutzername");
        $result = $statement->execute(array("benutzername" => $benutzername));

        //fetched:
        //[0]=Passwort

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function setSession
     * Füllt die Session mit den wichtigen Eigenschaften des Benutzers
     * @param $benutzername
     * Benutzername, für welchen die Eigenschaften aus der Datenbank geladen werden sollen
     */
    private function setSession($benutzername)
    {
        //SQL-Statement für das Laden von DozentID und Rolle auf Basis eines Benutzers
        $statement = $this->dbh->prepare("SELECT `DOZENT_ID_DOZENT`, dozent.ROLLE_BEZEICHNUNG FROM `benutzerkonto` 
INNER JOIN dozent ON benutzerkonto.DOZENT_ID_DOZENT = dozent.ID_DOZENT WHERE `BENUTZERNAME` = :benutzername");
        $result = $statement->execute(array("benutzername" => $benutzername));

        //fetched:
        //[0]=DozentID
        //[1]=Rolle

        $data = $statement->fetch();
        //Füllen der Session
        $_SESSION["IdDozent"] = $data[0];
        $_SESSION["Rolle"] = $data[1];
        $_SESSION["IdSemester"] = $this->getCurrentSemester();
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