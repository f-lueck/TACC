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
        if (isset($_POST["submitSelect"])) {
            echo $this->selectedLVDetails();
        }

        if (isset($_POST["submitSave"])) {
            $this->createLinkInDB();
        }
    }

    private function selectedLVDetails() {
        $html = '';
        $html .= '<div class="main">';
        $html .= '<div class="center">';
        $html .= '<h1>Ausgewählte Lehrveranstaltungen</h1>';
        $html .= '<form method="post">';

        $this->createTableDetailHeader($html);
        $this->getSelectedLVs($html);


        $html .= '</form>';
        $html .= '</table>';
        $html .= '<div class="buttonholder">';
        $html .= '<button class="submitButtons" type="submit" name="submitSave" id="submitSave" value="Speichern">Bestätigen</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private function getSelectedLVs(&$html){
        $max = $this->getMaxAnzLV();
        for ($i = 1000; $i < $max+1; $i++) {
            if (isset($_POST['check'.$i])) {
                $lvID = $this->getPOST('check'.$i);

                $statement = $this->dbh->prepare('SELECT `BEZEICHNUNG`, `SWS`, `CREDITS` FROM `veranstaltung` WHERE `ID_VERANSTALTUNG` = :LvID');
                $result = $statement->execute(array("LvID" => $lvID));
                $data = $statement->fetch();

                $html .= '<tr>';
                $html .= '<td>'.$data[0].'</td>';
                $html .= '<td>'.$this->getAllSWSLv($lvID).'</td>';
                $html .= '<td>'.$data[1].'</td>';
                $html .= '<td>'.$data[2].'</td>';
                $html .= '<td><input type="number" name="sws'.$lvID.'" id="sws'.$lvID.'"></td>';
                $html .= '<td><input type="text" name="pForm'.$lvID.'" id="pForm'.$lvID.'"></td>';
                $html .= '<td><input type="checkbox" name="confirmed'.$lvID.'" id="confirmed'.$lvID.'" value="'.$lvID.'" checked="checked"></td>';
                $html .= '</tr>';
            }
        }
    }

    private function createTableDetailHeader(&$html) {
        $html .= '<table align="center">';
        $html .= '<tr>';
        $html .= '<th>Bezeichnung</th>';
        $html .= '<th>SWS (Belegt)</th>';
        $html .= '<th>SWS (Muss)</th>';
        $html .= '<th>Credits</th>';
        $html .= '<th>SWS (Gewünscht)</th>';
        $html .= '<th>Prüfungsform</th>';
        $html .= '<th>Auswahl</th>';
        $html .= '<tr>';
    }


    /**
     * @function createLinkInDB
     * Erstellt die Verbindung LV-Dozent in der Datenbank
     */
    private function createLinkInDB()
    {
        $dozentID = $this->getSession('IdDozent');
        $this->semester = $this->getCurrentSemester();
        $anz = $this->getMaxAnzLV();
        $wirklicheSWS = 0;
        $veraenderung = 0;

        for ($i = 1; $i < $anz + 1; $i++) {
            if (isset($_POST['confirmed' . $i])) {
                $lvID = $this->getPOST('confirmed' . $i);
                $gebuchteSWS = $this->getPOST('sws' . $i);
                $pForm = $this->getPOST('pForm'.$i);

                //SQL-Statement zu Erstellung der Verbindung LV-Dozent
                $statement = $this->dbh->prepare('INSERT INTO `dozent_hat_veranstaltung_in_s`(`DOZENT_ID_DOZENT`, 
                `VERANSTALTUNG_ID_VERANSTALTUNG`, `SEMESTER_ID_SEMESTER`, `ANTEIL_PROZENT`, `WIRKLICHE_SWS`, `GEBUCHTE_SWS`, `VERAENDERUNG`, `P_FORM`) 
                VALUES (:DozentID, :LvID ,:SemesterID , :Prozent, :WirklicheSWS, :GebuchteSWS, :Veraenderung, :PForm)');
                $result = $statement->execute(array("DozentID" => $dozentID, "LvID" => $lvID, "SemesterID" => $this->semester, "Prozent" => $this->prozent, "WirklicheSWS" => $wirklicheSWS,"GebuchteSWS" => $gebuchteSWS, "Veraenderung" => $veraenderung, "PForm" => $pForm));

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
    private function getMaxAnzLV()
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
     * @param
     * ID der Vertiefung
     * @return string
     * Tabelle
     */
    public function showLVInformatik($VertiefungID)
    {
        $html = '';
        $html .= '<tr>';
        $html .= '<th colspan="3">' . $this->getVertiefungBezeichnung($VertiefungID) . '</th>';
        $html .= '</tr>';

        //SQL-Statement zum Laden aller Veranstaltungen der Informatik
        $statement = $this->dbh->prepare('SELECT `ID_VERANSTALTUNG`, `BEZEICHNUNG`, `SWS` FROM `veranstaltung` 
    INNER JOIN vertiefung_hat_veranstaltung ON vertiefung_hat_veranstaltung.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE vertiefung_hat_veranstaltung.VERTIEFUNG_ID_VERTIEFUNG = :VertiefungID ORDER BY `BEZEICHNUNG`');
        $result = $statement->execute(array("VertiefungID" => $VertiefungID));

        //fetched:
        //[0]=ID der Veranstaltung
        //[1]=Name der Veranstaltung
        //[2]=SWS

        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>' . utf8_encode($data[1]) .'</td>';
            $html .= '<td>' . $this->getLvSWS($data[0]) . '/' . $data[2] . '</td>';
            $html .= '<td><input type="checkbox" name="check' . $data[0] . '" id="check' . $data[0] . '" value="' . $data[0] . '"></td>';
            $html .= '</tr>';
        }
        return $html;
    }

    private function getVertiefungBezeichnung($vertiefungID)
    {
        $statement = $this->dbh->prepare('SELECT `BEZEICHNUNG` FROM `vertiefung` WHERE `ID_VERTIEFUNG` = :VertiefungID');
        $result = $statement->execute(array("VertiefungID" => $vertiefungID));
        $data = $statement->fetch();

        return $data[0];
    }

    private function getAllSWSLv($lvID)
    {
        $semseterID = $this->getSession('IdSemester');
        $statement = $this->dbh->prepare('SELECT dozent.NAME, `WIRKLICHE_SWS` FROM `dozent_hat_veranstaltung_in_s` 
  INNER JOIN dozent ON dozent_hat_veranstaltung_in_s.DOZENT_ID_DOZENT = dozent.ID_DOZENT WHERE `VERANSTALTUNG_ID_VERANSTALTUNG` = :LvID AND `SEMESTER_ID_SEMESTER` = :SemesterID');
        $result = $statement->execute(array('LvID' => $lvID, 'SemesterID' => $semseterID));
        $sws = '';
        $counter = 0;
        while ($data = $statement->fetch()) {
            if ($counter == 0) {
                $sws .= $data[0] . ' / ' . $data[1];
            } else {
                $sws .= '; ' . $data[0] . ' / ' . $data[1];
            }
            $counter++;
        }
        return $sws;
    }

    private function getLvSWS($lvID) {
        $semseterID = $this->getSession('IdSemester');
        $statement = $this->dbh->prepare('SELECT SUM(`GEBUCHTE_SWS`) FROM `dozent_hat_veranstaltung_in_s` 
WHERE `VERANSTALTUNG_ID_VERANSTALTUNG` = :LvID AND `SEMESTER_ID_SEMESTER` = :SemesterID');
        $result = $statement->execute(array('LvID' => $lvID, 'SemesterID' => $semseterID));
        $data = $statement->fetch();

        if ($data[0] === null) {
            return 0;
        }
        return $data[0];
    }
}
