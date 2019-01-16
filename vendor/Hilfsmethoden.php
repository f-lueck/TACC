<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 15:01
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Datenbank.php");

/**
 * Class Hilfsmethoden
 * Eine Sammlung von Methoden, welche von den erbenden Klassen häufiger verwendet werden
 */
class Hilfsmethoden extends Datenbank
{
    /**
     * @var
     * Variable für Meldungen
     */
    private $message;

    /**
     * Hilfsmethoden constructor.
     * Erzeugt das Objekt der Klasse
     */
    public function __construct()
    {
        //Konstruktoraufruf der Parent-Klasse
        parent::__construct();
    }

    /**
     * @function createRollenDropdown
     * Erzeugt ein Dropdown-Menü mit den Rollen aus der Datenbank
     */
    public function createRollenDropdown()
    {
        //SQL-Statement für das Laden der Rollen
        $statement = $this->dbh->prepare('SELECT * FROM rolle');
        $result = $statement->execute();

        //fetched:
        //[0]=Rollenname

        //Select-Menü-Header
        $output = "<select name='Rolle' id='Rolle'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[0] . '</option>';

        }
        //Select-Menü-Ende
        $output .= "</select>";

        echo $output;
    }

    /**
     * @function createSonderaDropdown
     * Erzeugt ein Dropdown-Menü mit den Sonderaufgaben aus der Datenbank
     */
    public function createSonderaDropdown()
    {
        //SQL-Statement für das Laden der Sonderaufgaben
        $statement = $this->dbh->prepare('SELECT `ID_SONDERAUFGABE`,`BEZEICHNUNG` FROM `sonderaufgabe`');
        $result = $statement->execute();

        //fetched:
        //[0]=SonderaufgabeID
        //[1]=Name der Sonderaufgabe

        //Select-Menü-Header
        $output = "<select name='Sonderaufgabe' id='Sonderaufgabe'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[1] . '</option>';
        }
        //Select-Menü-Ende
        $output .= "</select>";

        echo $output;
    }

    /**
     * @function createLvDropdown
     * Erzeugt ein Dropdown-Menü mit den Lehrveranstaltungen aus der Datenbank
     */
    public function createLvDropdown()
    {
        //SQL-Statement für das Laden der Lehrveranstaltungen
        $statement = $this->dbh->prepare('SELECT `ID_VERANSTALTUNG`,`BEZEICHNUNG` FROM `veranstaltung`');
        $result = $statement->execute();

        //fetched:
        //[0]=LehrveranstaltungID
        //[1]=Name der Lehrveranstaltung

        //Select-Menü-Header
        $output = "<select name='Lv' id='Lv'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[1] . '</option>';
        }
        //Select-Menü-Ende
        $output .= "</select>";

        echo $output;
    }

    /**
     * @function createDozentDropdown
     * Erzeugt ein Dropdown-Menü mit den Dozenten aus der Datenbank
     */
    public function createDozentDropdown()
    {
        //Variable für Auswahlbeschränlung
        $rolle = "Sekretariat";
        $statement = $this->dbh->prepare('SELECT `ID_DOZENT`, `VORNAME`, `NAME` FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` != :Rolle');
        $result = $statement->execute(array("Rolle" => $rolle));

        //fetched:
        //[0]=DozentID
        //[1}=Vorname
        //[2]=Nachname

        //Select-Menü-Header
        $output = "<select name='Dozent' id='Dozent'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[1] . ' ' . $data[2] . '</option>';
        }
        //Select-Menü-Ende
        $output .= "</select>";

        echo $output;
    }

    /**
     * @function createArtVonZusatzaufgabeDropdown
     * Erzeugt ein Dropdown-Menü mit den Arten von Zusatzaufgaben aus der Datenbank
     */
    public function createArtVonZusatzaufgabeDropdown()
    {
        //SQL-Statement zum Laden der Arten der Zusatzaufgaben
        $statement = $this->dbh->prepare("SELECT `ID_ART`,`BEZEICHNUNG` FROM `arten_von_zusatzaufgaben`");
        $result = $statement->execute();

        //fetched:
        //[0]=ArtID
        //[1]=Bezeichnung der Zusatzaufgabe

        //Select-Menü-Header
        $output = "<select name='Art' id='Art'>";

        while ($data = $statement->fetch()) {
            $output .= '<option value="' . $data[0] . '">' . $data[1] . '</option>';
        }
        //Select-Menü-Ende
        $output .= "</select>";

        echo $output;
    }

    /**
     * @function getDozentID
     * Liefert die DozentenID auf Basis von Nachname und Vorname zurück
     * @param $nachname
     * Nachname des Dozenten
     * @param $vorname
     * Vorname des Dozenten
     * @return mixed
     * DozentID
     */
    public function getDozentID($nachname, $vorname)
    {
        //SQL-Statement zum Laden der DozentenID
        $statement = $this->dbh->prepare('SELECT `ID_DOZENT` FROM `dozent` WHERE `NAME` = :nachname AND `VORNAME` = :vorname');
        $result = $statement->execute(array('nachname' => $nachname, 'vorname' => $vorname));

        //fetched:
        //[0]= DozentID

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function getBenutzer
     * Liefert alle Eigenschaften eines Benutzers auf Basis seines Benutzernamens zurück
     * @param $benutzername
     * Benutzername für den die Eigenschaften geladen werden sollen
     * @return mixed
     * Array mit Eigenschaften des Benutzers
     */
    public function getBenutzer($benutzername)
    {
        //SQL-Statement zum Laden der Eigenschaften eines Benutzers auf Basis seines Benutzernamens
        $statement = $this->dbh->prepare('SELECT `BENUTZERNAME`,`DOZENT_ID_DOZENT` FROM `benutzerkonto` WHERE `BENUTZERNAME` = :benutzername');
        $result = $statement->execute(array('benutzername' => $benutzername));

        //fetched:
        //[0]=Benutzername
        //[1]=DozentID

        $data = $statement->fetch();
        return $data;
    }

    /**
     * @function getlvBezeichnung
     * Liefert die Bezeichnung der Lehrveranstaltung auf Basis der ID zurück
     * @param $lvID
     * ID der Lehrveranstaltung
     * @return mixed
     * Bezeichnung der Lehrveranstaltung
     */
    public function getlvBezeichnung($lvID)
    {
        //SQL-Statement zum Laden der Bezeichung der Lehrveranstaltung
        $statement = $this->dbh->prepare('SELECT `BEZEICHNUNG` FROM `veranstaltung` WHERE `ID_VERANSTALTUNG` = :LvID');
        $result = $statement->execute(array("LvID" => $lvID));

        //fetched:
        //[0]=Bezeichnung der Lehrveranstaltung

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function getDozent
     * Liefert Name, Vorname und Titel eines Dozenten auf Basis der ID zurück
     * @param $dozentID
     * ID des Dozenten
     * @return mixed
     * Array mit Name, Vorname und Titel
     */
    public function getDozent($dozentID)
    {
        //SQL-Statement zum Laden der Eigenschaften des Dozenten
        $statement = $this->dbh->prepare('SELECT `NAME`, `VORNAME`, `TITEL`FROM `dozent` WHERE `ID_DOZENT` = :DozentID');
        $result = $statement->execute(array("DozentID" => $dozentID));

        //fetched:
        //[0]=Nachname des Dozenten
        //[1]=Vorname des Dozenten
        //[2]=Titel des Dozenten

        $data = $statement->fetch();

        return $data;
    }

    /**
     * @function getSonderaBezeichnung
     * Liefert die Bezeichnung der Sonderaufgabe zurück
     * @param $sonderaID
     * ID der Sonderaufgabe
     * @return mixed
     * Bezeichnung der Sonderaufgabe
     */
    public function getSonderaBezeichnung($sonderaID)
    {
        //SQL-Statement zum Laden der Bezeichnung der Sonderaufgabe
        $statement = $this->dbh->prepare('SELECT `BEZEICHNUNG` FROM `sonderaufgabe` WHERE `ID_SONDERAUFGABE` = :SonderaufgabeID');
        $result = $statement->execute(array("SonderaufgabeID" => $sonderaID));

        //fetched:
        //[0]=Bezeichnung der Sonderaufgabe

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function formatDozent
     * Erzeugt einen String mit den zusammengesetzten Eigenschaften eines Dozenten
     * @param $data
     * Array mit Nachname, Vorname, Titel
     * @return string
     * Formatierter Dozent
     */
    public function formatDozent($data)
    {
        //Variablen extrahieren
        $titel = $data[2];
        $vorname = $data[1];
        $name = $data[0];

        $dozent = $titel . " " . $vorname . " " . $name;
        return $dozent;
    }

    /**
     * @function getPOST
     * Liefert den Wert von einem Feld aus einem Formular über den Key zurück
     * @param $POST
     * Key der Feldes
     * @return mixed
     * Rückgabe des jeweiligen Wertes
     */
    public function getPOST($POST)
    {
        return $_POST["$POST"];
    }

    /**
     * @function showMessage
     * Liefert Meldungen über Javascript alert() zurück
     */
    public function showMessage()
    {
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }

    /**
     * @function getCurrentDate
     * Liefert den aktuellen Tag zurück
     * @return false|string
     * Aktueller Tag (Systemzeit)
     */
    public function getCurrentDate()
    {
        $timestamp = time();
        $date = date("d.m.Y", $timestamp);
        return $date;
    }

    /**
     * @function getSWSZusatz
     * Liefert die Summe der SWS der Zusatzaufgaben eines Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return int|string
     * Summe der SWS
     */
    public function getSWSZusatz($dozentID)
    {
        //SQL-Statement für das Laden der Summe der SWS der Zusatzaufgaben
        $statement = $this->dbh->prepare('SELECT SUM(arten_von_zusatzaufgaben.SWS) FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART=arten_von_zusatzaufgaben.ID_ART 
WHERE `DOZENT_ID_DOZENT` = :DozentID');
        $result = $statement->execute(array("DozentID" => $dozentID));

        //fetched:
        //[0]=Summe der SWS

        $data = $statement->fetch();
        //Formatieren der Summe auf eine Nachkommastelle
        $sws = number_format($data[0], 1);
        //Falls die Summe 2 SWS überschreitet
        if ($sws > 2) {
            $sws = 2;
        }
        return $sws;
    }

    /**
     * @function getSWSSonder
     * Liefert die Summe der SWS der Zusatzaufgaben eines Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return string
     * Summe der SWS
     */
    public function getSWSSonder($dozentID)
    {
        //SQL-Statement für das Laden der Summe der SWS der Sonderaufgaben
        $statement = $this->dbh->prepare('SELECT SUM(`WIRKLICHE_SWS`) FROM `dozent_hat_sonderaufgabe_in_s` WHERE `DOZENT_ID_DOZENT` = :DozentID');
        $result = $statement->execute(array("DozentID" => $dozentID));

        //fetched:
        //[0]=Summe der SWS

        $data = $statement->fetch();
        //Formatieren der Summe auf eine Nachkommastelle
        $sws = number_format($data[0], 1);

        return $sws;
    }

    /**
     * @function getSWSLv
     * Liefert die Summe der SWS der Lehrveranstaltungen eines Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return string
     * Summe der SWS
     */
    public function getSWSLv($dozentID)
    {
        //SQL-Statement für das Laden der Summe der SWS der Lehrveranstaltungen
        $statement = $this->dbh->prepare('SELECT SUM(`WIRKLICHE_SWS`) FROM `dozent_hat_veranstaltung_in_s` WHERE `DOZENT_ID_DOZENT` = :DozentID');
        $result = $statement->execute(array("DozentID" => $dozentID));

        //fetched:
        //[0]=Summe der SWS

        $data = $statement->fetch();
        //Formatieren der Summe auf eine Nachkommastelle
        $sws = number_format($data[0], 1);

        return $sws;
    }

    /**
     * @function getCurrentSemester
     * Liefert das aktuelle Semester auf Basis der höchsten ID zurück
     * @return mixed
     * ID des Semesters
     */
    public function getCurrentSemester()
    {
        //SQL-Statement zum Laden der höchsten SemesterID
        $statement = $this->dbh->prepare("SELECT MAX(ID_SEMESTER) FROM `semester`");
        $result = $statement->execute();

        //fetched:
        //[0}=SemesterID

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function formatSemester
     * Liefert die Bezeichnung des Semesters auf Basis der ID zurück
     * @param $semesterID
     * ID des Semesters
     * @return mixed
     * Bezeichnung des Semesters
     */
    public function formatSemester($semesterID)
    {
        //SQL-Statement für das Laden der Semesterbezeichnung
        $statement = $this->dbh->prepare("SELECT `BEZEICHNUNG` FROM `semester` WHERE `ID_SEMESTER` = :SemesterID");
        $result = $statement->execute(array("SemesterID" => $semesterID));

        //fetched:
        //[0]=Bezeichnung des Semesters

        $data = $statement->fetch();
        return $data[0];

    }

    /**
     * @function getCurrentUeberstunden
     * Liefert die aktuellen Ueberstunden eines Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return mixed
     * Aktuelle Ueberstunden
     */
    public function getCurrentUeberstunden($dozentID)
    {
        //SQL-Statement für das Laden der Ueberstunden
        $statement = $this->dbh->prepare("SELECT `UEBERSTUNDEN` FROM `dozent` WHERE `ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID" => $dozentID));

        //fetched:
        //[0]=Ueberstunden

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function getSWSArt
     * Liefert die Summe der SWS einer bestimmten Art von Zusatzaufgaben eines Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @param $artID
     * ID der Art der Zusatzaufgabe
     * @return float|int
     * Summe der SWS
     */
    public function getSWSArt($dozentID, $artID)
    {
        //SQL-Statement für das Laden der Anzahl der Zusatzaufgabe und der jeweiligen SWS
        $statement = $this->dbh->prepare("SELECT COUNT(`ID_ZUSATZAUFGABE`),arten_von_zusatzaufgaben.SWS FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART=arten_von_zusatzaufgaben.ID_ART 
WHERE `ARTEN_VON_ZUSATZAUFGABEN_ID_ART` = :ArtPraxisprojektID AND `DOZENT_ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("ArtPraxisprojektID" => $artID, "DozentID" => $dozentID));

        //fetched:
        //[0]=Anzahl der Zusatzaufgabe
        //[1]=SWS der einzelnen Zusatzaufgabe

        $data = $statement->fetch();

        //Gesamte SWS berechnen
        $sws = $data[0] * $data[1];
        return $sws;
    }

    /**
     * @function getSWSProSemester
     * Liefert das Deputat eines Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return mixed
     * Deputat in SWS
     */
    public function getSWSProSemester($dozentID)
    {
        //SQL-Statement zum Laden des Deputats
        $statement = $this->dbh->prepare("SELECT `SWS_PRO_SEMESTER` FROM `dozent` WHERE `ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID" => $dozentID));

        //fetched:
        //[0]=SWS

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function getSession
     * Liefert den Wert in der Session zu dem Key
     * @param $key
     * Key
     * @return mixed
     * Wert
     */
    public function getSession($key)
    {
        return $_SESSION["$key"];
    }

    /**
     * @function getFE
     * Liefert den Wert von F+E aus der Datenbank für den Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return mixed
     * F+E in SWS
     */
    public function getFE($dozentID)
    {
        $statement = $this->dbh->prepare('SELECT `FE` FROM `dozent` WHERE `ID_DOZENT` = :DozentID');
        $result = $statement->execute(array('DozentID' => $dozentID));

        //fetched:
        //[0]=SWS für F+E

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function getSWSInAF
     * Liefert den Wert von SWS in anderen Fakultäten aus der Datenbank für den Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return mixed
     * SWS in anderen Fakultäten
     */
    public function getSWSInAF($dozentID)
    {
        $statement = $this->dbh->prepare('SELECT `SWS_I_A_F` FROM `dozent` WHERE `ID_DOZENT` = :DozentID');
        $result = $statement->execute(array('DozentID' => $dozentID));

        //fetched:
        //[0]=SWS in anderen Fakultäten

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function forceDownload
     * Lädt eine Datei durch ihren Dateinamen herunter
     * @param $filename
     * Dateiname
     */
    function forceDownload($filename)
    {
        $filedata = @file_get_contents($filename);

        //Datei existiert
        if ($filedata) {
            $basename = basename($filename);

            //Meta-Daten und Mime-Type
            header("Content-Type: application-x/force-download");
            header("Content-Disposition: attachment; filename=$basename");
            header("Content-length: " . (string)(strlen($filedata)));
            header("Expires: " . gmdate("D, d M Y H:i:s", mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y"))) . " GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

            //Kompatibilität für IE
            if (FALSE === strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE ')) {
                header("Cache-Control: no-cache, must-revalidate");
            }

            header("Pragma: no-cache");
            flush();

            //Laden der Datei in den Output-Puffer
            ob_start();
            echo $filedata;
            //Löschen der Datei
            unlink($filename);
        } //Datei existiert nicht
        else {
            die("Datei $filename existiert nicht");
        }
    }
}