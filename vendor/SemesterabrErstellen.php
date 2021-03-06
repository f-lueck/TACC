<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 27.11.2018
 * Time: 14:06
 */

/**
 * Includes
 * Für Polymorphie und PDF-Erzeugung
 */
include("Benutzersitzung.php");
include_once("libraries/tcpdf/tcpdf.php");

/**
 * Class SemesterabrErstellen
 * Ermöglicht die Erstellung der Semesterabrechnung nach Vorlage
 */
class SemesterabrErstellen extends Benutzersitzung
{
    /**
     * @var
     * Variablen zur Weiterverarbeitung
     */
    private $dozentID;
    private $praxisprojektID = 1;
    private $bachelorarbeitID = 2;
    private $masterarbeitID = 3;
    private $diplomarbeitID = 4;
    private $tutoriumID = 5;
    private $neueUeberstunden = 0;
    private $semesterID;

    /**
     * SemesterabrErstellen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf nach Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Nabar
        $this->loadNav();

        $this->semesterID = $this->getCurrentSemester();

        //Drucken der Abrechnungen
        if (isset($_POST["print"])) {
            $this->createFor();
        }
    }

    /**
     * @function createFor
     * Iteriert durch die Checkboxen und erstellt die PDFs
     */
    private function createFor(){
        $max = $this->getMaxDozentID();
        for ($i = 0; $i < ($max + 1); $i++ ){
            if (isset($_POST['check'.$i])){
                $this->setDozentID($this->getPOST('check'.$i));
                $this->createSemesterabrPDF();
            }
        }
        ob_end_clean();
        $this->zipFolder();
        $this->forceDownload('semesterabr.zip');
        $this->deleteSemesterabr();
    }

    /**
     * @function zipFolder
     * Generiert eine .zip Datei aus dem Ordner semesterabr
     */
    private function zipFolder(){
        $rootPath = realpath('semesterabr');

        //Neues Archiv erstellen
        $zip = new ZipArchive();
        $zip->open('semesterabr.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE );
        //Dateien auswählen
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip archive will be created only after closing object
        $zip->close();
    }

    /**
     * @function deleteSemesterabr
     * Löscht alle generierten Semesterabrechnungen und das Archiv
     */
    private function deleteSemesterabr(){
        $dir = 'semesterabr';
        $files = glob($dir. '/*');
        foreach ($files as $file){
            if (is_file($file)){
                unlink($file);
            }
        }

        unlink('semesterabr.zip');
    }

    /**
     * @function setDozentID
     * Setzt den Wert der Variable
     * @param $post
     */
    private function setDozentID($post)
    {
        $this->dozentID = $post;
    }

    /**
     * @function showSemesterabrDozent
     * Liefert die Semesterabrechnung des jeweiligen Dozenten in Tabellenform zurück
     * @return string
     * Semesterabrechnung
     */
    private function showSemesterabrDozent()
    {
        $output = '<div class=main>';
        //Formular-Header
        $output .= '<form method="post">';


        $this->createSemesterabrHeader($output);
        $this->createSemesterabrTop($output);

        $this->createSemesterabrTableTop($output);
        $this->createSemesterabrTableLV($output);
        $this->createSemesterabrTableZusatza($output);
        $this->createSemesterabrTableSondera($output);
        $this->createSemesterabrTableSumme($output);

        $this->createSemesterabrTableBot($output);

        $output .= '<br>';
        $output .= '<div class="buttonholder">';
        $output .= '<button class="submitButtons" type="submit" name="print" id="print">Als PDF</button>';
        $output .= '</div>';
        //Formular-Ende
        $output .= '</form>';

        return $output;
    }

    /**
     * @function createSemesterabrHeader
     * Ergänzt das Formular durch Text
     * @param $html
     * Zu ergänzendes HTML Formular
     */
    private function createSemesterabrHeader(&$html)
    {
        $html .= 'Ostfalia - Hochschule für angewandte Wissenschaften<br>';
        $html .= 'Fakultät Informatik<br>';
        $html .= 'Der Dekan<br>';
        $html .= '<br><br>';
    }

    /**
     * @function createSemesterabrTop
     * Ergänzt das Formular durch Informationen zu Semester, Datum und Dozenten
     * @param $html
     * Zu ergänzendes HTML Formular
     */
    private function createSemesterabrTop(&$html)
    {
        $html .= 'Semesterabrechnung ' . $this->formatSemester($this->getCurrentSemester());
        $html .= '<span style="float: right">' . $this->getCurrentDate() . '</span><br>';
        $html .= '<br>';
        $html .= $this->formatDozent($this->getDozent($this->dozentID)) . '<br>';
        $html .= '<br>';
        $html .= 'Lehrdeputat: ' . $this->getDeputat($this->dozentID) . ' SWS<br>';
    }

    /**
     * @function getDeputat
     * Lädt das Lehrdeputat eines Dozenten aus der Datenbank
     * @param $dozentID
     * ID des Dozenten
     * @return mixed
     * Deputat in SWS
     */
    private function getDeputat($dozentID)
    {

        $statement = $this->dbh->prepare('SELECT `SWS_PRO_SEMESTER` FROM `dozent` WHERE `ID_DOZENT` = :DozentID');
        $result = $statement->execute(array('DozentID' => $dozentID));

        //fetched:
        //[0]=Deputat

        $data = $statement->fetch();
        return $data[0];
    }

    /**
     * @function createSemesterabrHeader
     * Ergänzt das Formular durch Hinzufügen des Tabellen-Headers und den aktuellen Ueberstunden des Dozenten
     * @param $html
     * Zu ergänzendes HTML Formular
     */
    private function createSemesterabrTableTop(&$html)
    {
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<th></th>';
        $html .= '<th>Sem.</th>';
        $html .= '<th>SWS</th>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>bisher aufgelaufene Mehr-/Minderarbeit</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->getCurrentUeberstunden($this->dozentID) . '</td>';
        $html .= '</tr>';

    }

    /**
     * @function createSemesterabrTableLv
     * Ergänzt das Formular durch Tabelleneinträge zu den Lehrveranstaltungen
     * @param $html
     * Zu ergänzendes HTML Formular
     */
    private function createSemesterabrTableLV(&$html)
    {
        //SQL-Statement für das Laden der LV-Bezeichnung, der Semesterbezeichnung und den wirklichen SWS
        $statement = $this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN veranstaltung ON veranstaltung.ID_VERANSTALTUNG = dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG 
INNER JOIN semester ON semester.ID_SEMESTER=dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Bezeichnung der Lehrveranstaltung
        //[1]=Bezeichnung des Semesters
        //[2]=SWS

        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>' . $data[0] . '</td>';
            $html .= '<td>' . $data[1] . '</td>';
            $html .= '<td>' . $data[2] . '</td>';
            $html .= '</tr>';
        }
        $this->createBlankTableEntries($html);
    }

    /**
     * @function createBlankTableEntries
     * Erzeugt leere Zeilen auf Basis der Anzahl
     * @param $html
     * Zu ergänzendes HTML Formular
     * @param $anz
     * Anzahl wie viele leere Zeilen hinzugefügt werden sollen
     */
    private function createBlankTableEntries(&$html)
    {
        $html .= '<td colspan="3"></td>';
    }

    /**
     * @function createSemesterabrTableZusata
     * Ergänzt das Formular durch Tabelleneinträge zu den Zusatzaufgaben
     * @param $html
     * Zu ergänzendes HTML Formular
     */
    private function createSemesterabrTableZusatza(&$html)
    {
        $html .= '<tr>';
        $html .= '<td>Praxisprojekte</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->getSWSArt($this->dozentID, $this->praxisprojektID) . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>Abschlussarbeiten</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->calcAbschlussarbeitenSWS($this->dozentID) . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>Tutorien</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->getSWSArt($this->dozentID, $this->tutoriumID) . '</td>';
        $html .= '</tr>';

        $this->createBlankTableEntries($html);
    }

    /**
     * @function calcAbschlussarbeitenSWS
     * Liefert die Summe der SWS aller Abschlussarbeiten eines Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return float|int
     * SWS
     */
    private function calcAbschlussarbeitenSWS($dozentID)
    {

        $bachelorsws = $this->getSWSArt($dozentID, $this->bachelorarbeitID);
        $mastersws = $this->getSWSArt($dozentID, $this->masterarbeitID);
        $diplomsws = $this->getSWSArt($dozentID, $this->diplomarbeitID);

        return ($bachelorsws + $mastersws + $diplomsws);
    }

    /**
     * @function createSemesterabrTableSondera
     * Ergänzt das Formular durch Tabelleneinträge zu Sonderaufgaben des Dozenten
     * @param $html
     * Zu ergänzendes HTML Formular
     */
    private function createSemesterabrTableSondera(&$html)
    {
        $html .= '<tr>';
        $html .= '<td>in anderen Fakultäten</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->getSWSInAF($this->dozentID) . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>Verfügungsstunden F+E</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->getFE($this->dozentID) . '</td>';
        $html .= '</tr>';

        //SQL-Statement zum Laden der Sonderaufgabenbezeichnung und den SWS
        $statement = $this->dbh->prepare("SELECT sonderaufgabe.BEZEICHNUNG, dozent_hat_sonderaufgabe_in_s.WIRKLICHE_SWS FROM `dozent_hat_sonderaufgabe_in_s` 
INNER JOIN sonderaufgabe ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE=sonderaufgabe.ID_SONDERAUFGABE
WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Bezeichnung der Sonderaufgabe
        //[1]=SWS

        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>' . $data[0] . '</td>';
            $html .= '<td></td>';
            $html .= '<td>' . $data[1] . '</td>';
            $html .= '</tr>';
        }
    }

    /**
     * @function createSemesterabrTableHeader
     * Ergänzt das Formular durch den Tabelleneintrag der Summe
     * @param $html
     * Zu ergänzendes HTML Formular
     */
    private function createSemesterabrTableSumme(&$html)
    {

        $this->createBlankTableEntries($html);

        $html .= '<tr>';
        $html .= '<th colspan="2">Summe</th>';
        $html .= '<th>' . $this->summeSWS($this->dozentID) . '</th>';
        $html .= '</tr>';
    }

    /**
     * @function summeSWS
     * Liefert die Summe aller aktuellen Veranstaltungen
     */
    public function summeSWS()
    {
        $sws = $this->getSWSZusatz($this->dozentID);
        $sws += $this->getSWSSonder($this->dozentID);
        $sws += $this->getSWSLv($this->dozentID);
        $sws += $this->getFE($this->dozentID);
        $sws += $this->getSWSInAF($this->dozentID);
        return $sws;
    }

    /**
     * @function createSemesterabrTableBot
     * Ergänzt das Formular durch Tabelleneinträge zu Ueberstunden
     * @param $html
     * Zu ergänzendes HTML Formular
     */
    private function createSemesterabrTableBot(&$html)
    {

        $this->createBlankTableEntries($html);

        $html .= '<tr>';
        $html .= '<th colspan="2">Mehr-/Minderarbeit im Semester</th>';
        $html .= '<th>' . $this->calcDeltaSWS($this->dozentID) . '</th>';
        $html .= '</tr>';

        $this->createBlankTableEntries($html);

        $html .= '<tr>';
        $html .= '<th colspan="2">aufgelaufene Mehr-/Minderarbeit</th>';
        $html .= '<th>' . $this->calcUeberstunden($this->dozentID) . '</th>';
        $html .= '</tr>';

        $html .= '<input type="hidden" name="DozentID" id="DozentID" value="' . $this->dozentID . '">';

        //Tabellenende
        $html .= '</table>';
        $html .= '</div>';
    }

    /**
     * @function calcDeltaSWS
     * Berechnet die neu hinzugekommenen Ueberstunden
     * @param $dozentID
     * ID des Dozenten
     * @return float|int|mixed|string
     * Ueberstunden in SWS
     */
    private function calcDeltaSWS($dozentID)
    {
        $swsGeleistet = $this->summeSWS($dozentID);
        $swsSoll = $this->getSWSProSemester($dozentID);
        $delta = $swsGeleistet - $swsSoll;

        $this->setNeueUeberstunden($delta);
        return $delta;
    }

    /**
     * @function setNeueUeberstunden
     * Setzt die Variable $neueUeberstunden auf den gewünschten Wert
     * @param $ueberstunden
     * Neue Ueberstunden
     */
    private function setNeueUeberstunden($ueberstunden)
    {
        $this->neueUeberstunden = $ueberstunden;
    }

    /**
     * @function calcUeberstunden
     * Berechnet die gesamten Ueberstunden eines Dozenten
     * @param $dozentID
     * ID des Dozenten
     * @return int|mixed
     * Ueberstunden in SWS
     */
    private function calcUeberstunden($dozentID)
    {
        $ueberstundenAlt = $this->getCurrentUeberstunden($dozentID);
        $ueberstundenNeu = $this->neueUeberstunden;
        $ueberstundenGesamt = $ueberstundenAlt + $ueberstundenNeu;

        return $ueberstundenGesamt;
    }

    /**
     * @function createSemesterabrPDF
     * Erzeugt ein PDF Dokument mit Angabe aller Informationen nach Vorlage, zeigt diese direkt im Browser an
     */
    private function createSemesterabrPDF()
    {
        //Erzeugen einer neuen PDF-Datei
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        //Meta-Daten
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('TACC');
        $pdf->SetTitle('Semesterabrechnung');
        $pdf->SetSubject('Semesterabrechnung');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        //Füllen der PDF Datei
        $this->createCellPDFHeader($pdf);
        $this->createCellPDFTop($pdf);
        $this->createCellPDFTableTop($pdf);
        $this->createCellPDFTableLV($pdf);
        $this->createCellPDFTableZusatza($pdf);
        $this->createCellPDFTableFakVer($pdf);
        $this->createCellPDFTableSondera($pdf);
        $this->createCellPDFTableSumme($pdf);
        $this->createCellPDFTableBottom($pdf);


        //Speichern im Ordner semesterabr
        //getDozent[0] = Nachname
        $pdf_string = $pdf->Output('Semesterabrechnung_'.$this->dozentID.'.pdf', 'S');
        file_put_contents('./semesterabr/Semesterabrechnung_'.$this->getDozent($this->dozentID)[0].'.pdf', $pdf_string);
    }

    /**
     * @function createCellPDFTop
     * Schreibt den Header der PDF Datei
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFHeader(&$pdf)
    {
        $pdf->Cell(0, 0, 'Ostfalia - Hochschule für angewandte Wissenschaften', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Fakultät Informatik', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Der Dekan', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTop
     * Schreibt Informationen zu Datum und Dozenten in die PDF Datei
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTop(&$pdf)
    {
        $pdf->Cell(45, 0, 'Semesterabrechnung ', 0, false, 'L', 0, '', 0);
        $pdf->Cell(20, 0, $this->formatSemester($this->getCurrentSemester()), 0, 0, 'L', 0, '', 0);
        $pdf->Cell(0, 0, $this->getCurrentDate(), 0, 1, 'R', 0, '', 0);
        $pdf->Cell(0, 0, $this->formatDozent($this->getDozent($this->dozentID)), 0, 1, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'Lehrdeputat: ', 0, 0, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getSWSProSemester($this->dozentID) . ' SWS', 0, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableTop
     * Schreibt den Tabellenkopf und die bisherigen Ueberstunden in die PDF Datei
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableTop(&$pdf)
    {
        $pdf->Cell(100, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'Semester', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'SWS', 1, 1, 'L', 0, '', 0);
        $pdf->Cell(100, 0, 'bisher aufgelaufene Mehr-/Minderarbeit', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 1, 0, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getCurrentUeberstunden($this->dozentID), 1, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableLV
     * Ergänzt die PDF Datei mit den Lehrveranstaltungen des Dozenten
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableLV(&$pdf)
    {
        //SQL-Statement um den Namen der Veranstaltung, den Namen des Semesters und die SWS zu laden
        $statement = $this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN veranstaltung ON veranstaltung.ID_VERANSTALTUNG = dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG 
INNER JOIN semester ON semester.ID_SEMESTER=dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Name der Lehrveranstaltung
        //[1]=Name des Semesters
        //[2]=SWS

        $pdf->Cell(0, 0, '', 1, 1, 'L', 0, '', 0);

        while ($data = $statement->fetch()) {
            $pdf->Cell(100, 0, $data[0], 1, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[1], 1, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[2], 1, 1, 'L', 0, '', 0);
        }
    }

    /**
     * @function createCellPDFTableZusatza
     * Ergänzt die PDF Datei mit Informationen zu den Zusatzaufgaben eines Dozenten
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableZusatza(&$pdf)
    {
        $pdf->Cell(0, 0, '', 1, 1, 'L', 0, '', 0);

        $pdf->Cell(145, 0, 'Praxisprojekte', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getSWSArt($this->dozentID, $this->praxisprojektID), 1, 1, 'L', 0, '', 0);

        $pdf->Cell(145, 0, 'Abschlussarbeiten', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcAbschlussarbeitenSWS($this->dozentID), 1, 1, 'L', 0, '', 0);

        $pdf->Cell(145, 0, 'Tutorien', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getSWSArt($this->dozentID, $this->tutoriumID), 1, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableFakVer
     * Ergänzt die PDF Datei mit SWS für Veranstaltungen in anderen Fakultäten und F+E
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableFakVer(&$pdf)
    {
        $pdf->Cell(0, 0, '', 1, 1, 'L', 0, '', 0);

        $pdf->Cell(145, 0, 'in anderen Fakultäten', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getSWSInAF($this->dozentID), 1, 1, 'L', 0, '', 0);

        $pdf->Cell(145, 0, 'Verfügungsstunden F+E', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getFE($this->dozentID), 1, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableSondera
     * Ergänzt die PDF Datei mit Informationen zu den Sonderaufgaben eines Dozenten
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableSondera(&$pdf)
    {
        //SQL-Statement für das Laden der Bezeichnungen und SWS der Sonderaufgaben
        $statement = $this->dbh->prepare("SELECT sonderaufgabe.BEZEICHNUNG, dozent_hat_sonderaufgabe_in_s.WIRKLICHE_SWS FROM `dozent_hat_sonderaufgabe_in_s` 
INNER JOIN sonderaufgabe ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE=sonderaufgabe.ID_SONDERAUFGABE
WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Name der Sonderaufgabe
        //[1]=SWS

        while ($data = $statement->fetch()) {
            $pdf->Cell(145, 0, $data[0], 1, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[1], 1, 1, 'L', 0, '', 0);
        }
    }

    /**
     * @function createCellPDFTableSumme
     * Ergänzt die PDF Datei mit der Summe der SWS aus dem aktuellen Semester
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableSumme(&$pdf)
    {
        $pdf->Cell(0, 0, '', 1, 1, 'L', 0, '', 0);

        $pdf->Cell(145, 0, 'Summe', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->summeSWS($this->dozentID), 1, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableBottom
     * Ergänzt die PDF Datei mit den Ueberstunden des Dozenten
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableBottom(&$pdf)
    {
        $pdf->Cell(145, 0, 'Mehr-/Minderarbeit im Semester', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcDeltaSWS($this->dozentID), 1, 1, 'L', 0, '', 0);
        $pdf->Cell(145, 0, 'aufgelaufene Mehr-/Minderarbeit', 1, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcUeberstunden($this->dozentID), 1, 1, 'L', 0, '', 0);

        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(0, 0, 'Datum, Unterschrift', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 1, 1, 'L', 0, '', 0);
        $pdf->Cell(110, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(15, 0, 'Name', 0, false, 'L', 0, '', 0);
        $pdf->Cell(65, 0, '', 1, 1, 'L', 0, '', 0);
    }

    /**
     * @function createDozentTable
     * Liefert die komplette Tabelle mit Dozenten, Status und Auswahl zurück
     * @return string
     * Die Tabelle
     */
    public function createDozentTable()
    {
        $html = '';
        $this->createTableHeader($html);
        $this->createTableContent($html);

        $html .= '</table>';

        return $html;
    }

    /**
     * @function createTableHeader
     * Erzeugt den Tabellen Header
     * @param $html
     * Zu ergänzender HTML Code
     */
    private function createTableHeader(&$html)
    {
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<th>Titel</th>';
        $html .= '<th>Nachname</th>';
        $html .= '<th>Vorname</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Auswahl</th>';
        $html .= '</tr>';
    }

    /**
     * @function createTableContent
     * Ergänzt die Tabelle um Informationen Titel, Name, Vorname, Status des jeweiligen Dozenten
     * @param $html
     * Zu ergänzender HTML Code
     */
    private function createTableContent(&$html)
    {
        $rolle = 'Sekretariat';

        //SQL-Statement für das Laden der Titel, Nachnamen, Vornamen, IDs der Dozenten, sortiert nach Nachnamen
        $statement = $this->dbh->prepare('SELECT `TITEL`, `NAME`, `VORNAME`, `ID_DOZENT` FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` != :Rolle ORDER BY `NAME`');
        $result = $statement->execute(array('Rolle' => $rolle));

        //fetched:
        //[0]=Titel
        //[1]=Nachname
        //[2]=Vorname
        //[3]=ID des Dozenten

        while ($data = $statement->fetch()) {
            $html .= '<tr>';

            $html .= '<td>' . $data[0] . '</td>';
            $html .= '<td>' . $data[1] . '</td>';
            $html .= '<td>' . $data[2] . '</td>';

            $dozentID = $data[3];

            $html .= '<td><div id="rectangle" style="background: ' . $this->getColorForChange($dozentID) . '"></div></td>';
            $html .= '<td><input type="checkbox" name="check' . $dozentID . '" id="check' . $dozentID . '" value="' . $dozentID . '"></td>';

            $html .= '<tr>';
        }
    }

    /**
     * @function getColorForChange
     * Ermittelt die jeweilige Farbe für den Veränderungsgrad
     * @param $dozentID
     * ID des zu prüfenden Dozenten
     * @return string
     * Farbe
     */
    private function getColorForChange($dozentID)
    {

        //SQL-Statement für das Laden des höchsten Veränderungsgrads einer Veranstaltung eines Dozenten
        $statement = $this->dbh->prepare('SELECT MAX(`VERAENDERUNG`) FROM `dozent_hat_veranstaltung_in_s` 
WHERE `SEMESTER_ID_SEMESTER` = :SemesterID AND `DOZENT_ID_DOZENT` = :DozentID');
        $statement->execute(array('SemesterID' => $this->semesterID, 'DozentID' => $dozentID));
        $data = $statement->fetch();

        //fetched:
        //[0]=Maximaler Veränderungsgrad

        switch ($data[0]) {
            case 1:
                return 'orange';
            case 2:
                return 'red';
            default:
                return 'green';
        }
    }

}