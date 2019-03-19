<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 16:44
 */

/**
 * Includes
 * Für Polymorphies
 */
include("Benutzersitzung.php");

/**
 * Class lvZuweisen
 * Ermöglicht das Erzeugen von der Verbindung LV-Dozent
 */
class lvZuweisen extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $message;
    private $semester;
    private $prozent = 1.0;

    /**
     * lvZuweisen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf bei Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Nach Clicken des Buttons
        if (isset($_POST["submit"])) {

            $this->createLinkInDB();
        }
    }

    /**
     * @function createLinkInDB
     * Erstellt die Verbindung LV-Dozent in der Datenbank
     */
    private function createLinkInDB()
    {
        $dozentID = $this->getSession('IdDozent');
        $this->semester = $this->getCurrentSemester();
        $anz = $this->setAnzLV();

        for ($i = 1; $i < $anz + 1; $i++) {
            if (isset($_POST['check' . $i])) {
                $lvID = $this->getPOST('check' . $i);
                $sws = $this->getPOST('sws' . $i);

                //SQL-Statement zu Erstellung der Verbindung LV-Dozent
                $statement = $this->dbh->prepare('INSERT INTO `dozent_hat_veranstaltung_in_s`(`DOZENT_ID_DOZENT`, 
`VERANSTALTUNG_ID_VERANSTALTUNG`, `SEMESTER_ID_SEMESTER`, `ANTEIL_PROZENT`, `WIRKLICHE_SWS`) VALUES (:DozentID,:LvID,:Semester,:Prozent,:SWS)');
                $result = $statement->execute(array("DozentID" => $dozentID, "LvID" => $lvID, "Semester" => $this->semester, "Prozent" => $this->prozent, "SWS" => $sws));

                if ($result) {
                    //Verbindung erstellt
                    $this->message = "Erstellt";
                    $this->showMessage();
                } else {
                    //Fehler bei der Erstellung
                    $this->message = "Fehler";
                    $this->showMessage();
                }
            }
        }
    }

    /**
     * @function setAnzLV
     * Lädt den höchsten ID Wert aus der Datenbank
     */
    private function setAnzLV()
    {

        //SQL-Statement um die höchste ID zu laden
        $statement = $this->dbh->prepare('SELECT MAX(`ID_VERANSTALTUNG`) FROM `veranstaltung`');
        $result = $statement->execute();

        //fetched:
        //[0]=höchste ID

        $data = $statement->fetch();

        return $data[0];
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
     * @function showLVInformatik
     * Zeigt die LVs des Studiengangs Informatik in Tabellenform an
     * @return string
     * Tabelle
     */
    public function showLVInformatik()
    {
        $html = '';

        $html .= '<form method="post">
<table>
<caption>Informatik</caption>
<tr>
<th>Name Lehrveranstaltung</th>
<th>SWS (muss)</th>
<th>Belegt von</th>
<th>Auswahl</th>
<th>SWS gewünscht</th>
</tr>';

        //SQL-Statement zum Laden aller Veranstaltungen der Informatik
        $statement = $this->dbh->prepare('SELECT `ID_VERANSTALTUNG`, `BEZEICHNUNG`, `SWS` FROM `veranstaltung` WHERE `ID_VERANSTALTUNG`>1000 AND `ID_VERANSTALTUNG`<2000');
        $result = $statement->execute();

        //fetched:
        //[0]=ID der Veranstaltung
        //[1]=Name der Veranstaltung
        //[2]=SWS

        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>' . $data[1] . '</td>';
            $html .= '<td>' . $data[2] . '</td>';
            $html .= '<td>'.$this->getAllSWSLv($data[0]).'</td>';
            $html .= '<td><input type="checkbox" name="check' . $data[0] . '" id="check' . $data[0] . '" value="' . $data[0] . '"></td>';
            $html .= '<td><input type="number" name="sws' . $data[0] . '" id="sws' . $data[0] . '" min="0"></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '<button class="submitButtons" type="submit" name="submit" id="submit" value="Speichern">Speichern
                </button>';
        return $html;
    }

    /**
     * @function showLVEigene
     * Zeigt die selbst erstellten LVs an
     * @return string
     * Tabelle
     */
    public function showLVEigene()
    {
        $html = '';

        $html .= '<table>
<caption>Eigene</caption>
<tr>
<th>Name Lehrveranstaltung</th>
<th>SWS (muss)</th>
<th>Belegt von</th>
<th>Auswahl</th>
<th>SWS gewünscht</th>
</tr>';

        //SQL-Statement zum Laden aller Veranstaltungen der Informatik
        $statement = $this->dbh->prepare('SELECT `ID_VERANSTALTUNG`, `BEZEICHNUNG`, `SWS` FROM `veranstaltung` WHERE `ID_VERANSTALTUNG`>6000');
        $result = $statement->execute();

        //fetched:
        //[0]=ID der Veranstaltung
        //[1]=Name der Veranstaltung
        //[2]=SWS

        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>' . $data[1] . '</td>';
            $html .= '<td>' . $data[2] . '</td>';
            $html .= '<td>'.$this->getAllSWSLv($data[0]).'</td>';
            $html .= '<td><input type="checkbox" name="check' . $data[0] . '" id="check' . $data[0] . '" value="' . $data[0] . '"></td>';
            $html .= '<td><input type="number" name="sws' . $data[0] . '" id="sws' . $data[0] . '" min="0" width="5 %"></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '<button class="submitButtons" type="submit" name="submit" id="submit" value="Speichern">Speichern
                </button>';
        $html .= '</form>';
        return $html;
    }

    private function getAllSWSLv($lvID){
        $semseterID = $this->getSession('IdSemester');
        $statement = $this->dbh->prepare('SELECT dozent.NAME, `WIRKLICHE_SWS` FROM `dozent_hat_veranstaltung_in_s` 
  INNER JOIN dozent ON dozent_hat_veranstaltung_in_s.DOZENT_ID_DOZENT = dozent.ID_DOZENT WHERE `VERANSTALTUNG_ID_VERANSTALTUNG` = :LvID AND `SEMESTER_ID_SEMESTER` = :SemesterID');
        $result = $statement->execute(array('LvID'=>$lvID, 'SemesterID'=>$semseterID));
        $sws = '';
        $counter = 0;
        while ($data = $statement->fetch()){
            if ($counter == 0){
                $sws .= $data[0] . '/' . $data[1];
            } else {
                $sws .= '; '.$data[0] . '/' . $data[1];
            }
            $counter++;
        }
        return $sws;
    }
}