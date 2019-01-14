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

        //Auswählen eines Dozenten
        if (isset($_POST["selectDozent"])) {
            $this->setDozentID($this->getPOST("Dozent"));
            echo $this->showSemesterabrDozent();
        }

        //Drucken der Abrechnung
        if (isset($_POST["print"])) {
            $this->setDozentID($this->getPOST("DozentID"));
            $this->createSemesterabrPDF();
        }
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
        $output .= '<input type="submit" name="print" id="print" value="Als PDF">';
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
        $html .= 'Lehrdeputat: <> SWS<br>';
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

        $html .= '<tr>';
        $html .= '<td>in anderen Fakultäten</td>';
        $html .= '<td></td>';
        $html .= '<td>SWS</td>';
        $html .= '</tr>';
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
        $html .= '<td>Verfügungsstunden F+E</td>';
        $html .= '<td></td>';
        $html .= '<td>SWS</td>';
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

        $this->createBlankTableEntries($html, 2);

        $html .= '<tr>';
        $html .= '<td>Summe</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->calcSummeSWS($this->dozentID) . '</td>';
        $html .= '</tr>';
    }

    /**
     * @function createBlankTableEntries
     * Erzeugt leere Zeilen auf Basis der Anzahl
     * @param $html
     * Zu ergänzendes HTML Formular
     * @param $anz
     * Anzahl wie viele leere Zeilen hinzugefügt werden sollen
     */
    private function createBlankTableEntries(&$html, $anz)
    {
        for ($i = 0; $i < $anz; $i++) {
            $html .= '<tr>';
            $html .= '<td>&nbsp</td>';
            $html .= '<td>&nbsp</td>';
            $html .= '<td>&nbsp</td>';
            $html .= '</tr>';
        }
    }

    /**
     * @function calcSummeSWS
     * Liefert die Summe der SWS aller Veranstaltungen und Aufgaben eines Dozenten zurück
     * @param $dozentID
     * ID des Dozenten
     * @return float|int|string
     * Summe der SWS
     */
    private function calcSummeSWS($dozentID)
    {
        $sws = 0;
        $sws += $this->getSWSLv($dozentID);
        $sws += $this->getSWSArt($dozentID, $this->praxisprojektID);
        $sws += $this->calcAbschlussarbeitenSWS($dozentID);
        $sws += $this->getSWSArt($dozentID, $this->tutoriumID);
        //in anderen Fakultäten
        //Verfügungsstunden
        $sws += $this->getSWSSonder($dozentID);

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

        $this->createBlankTableEntries($html, 2);

        $html .= '<tr>';
        $html .= '<td>Mehr-/Minderarbeit im Semester</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->calcDeltaSWS($this->dozentID) . '</td>';
        $html .= '</tr>';

        $this->createBlankTableEntries($html, 1);

        $html .= '<tr>';
        $html .= '<td>aufgelaufene Mehr-/Minderarbeit</td>';
        $html .= '<td></td>';
        $html .= '<td>' . $this->calcUeberstunden($this->dozentID) . '</td>';
        $html .= '</tr>';

        $html .= '<input type="hidden" name="DozentID" id="DozentID" value="' . $this->dozentID . '">';

        //Tabellenende
        $html .= '</table>';
        //Formularende
        $html .= '</form>';
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
        $swsGeleistet = $this->calcSummeSWS($dozentID);
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

        //Löschung des Ausgabepuffers für Herunterladen
        ob_end_clean();
        //Anzeigen im Browser
        $pdf->Output('Semesterabrechnung.pdf', 'I');
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
        $pdf->Cell(160, 0, 'Semesterabrechnung ' . $this->formatSemester($this->getCurrentSemester()), 0, false, 'L', 0, '', 0);
        $pdf->Cell(30, 0, $this->getCurrentDate(), 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, $this->formatDozent($this->getDozent($this->dozentID)), 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(60, 0, 'Lehrdeputat', 0, false, 'L', 0, '', 0);
        $pdf->Cell(20, 0, 'Deputat', 0, false, 'L', 0, '', 0);
        $pdf->Cell(30, 0, $this->getSWSProSemester($this->dozentID), 0, 1, 'L', 0, '', 0);
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
        $pdf->Cell(45, 0, 'Semester', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'SWS', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(100, 0, 'bisher aufgelaufene Mehr-/Minderarbeit', 0, false, 'L', 0, '', 0);
        $pdf->Cell(60, 0, $this->getCurrentUeberstunden($this->dozentID), 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
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

        while ($data = $statement->fetch()) {
            $pdf->Cell(100, 0, $data[0], 0, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[1], 0, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[2], 0, 1, 'L', 0, '', 0);
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
        $pdf->Cell(100, 0, 'Praxisprojekte', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getSWSArt($this->dozentID, $this->praxisprojektID), 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Abschlussarbeiten', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcAbschlussarbeitenSWS($this->dozentID), 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Tutorien', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getSWSArt($this->dozentID, $this->tutoriumID), 0, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableFakVer
     * Ergänzt die PDF Datei mit SWS für Veranstaltungen in anderen Fakultäten und F+E
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableFakVer(&$pdf)
    {
        $pdf->Cell(100, 0, 'in anderen Fakultäten', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'SWS', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Verfügungsstunden F+E', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'SWS', 0, 1, 'L', 0, '', 0);
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
            $pdf->Cell(100, 0, $data[0], 0, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[1], 0, 1, 'L', 0, '', 0);
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
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Summe', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcSummeSWS($this->dozentID), 0, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableBottom
     * Ergänzt die PDF Datei mit den Ueberstunden des Dozenten
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableBottom(&$pdf)
    {
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Mehr-/Minderarbeit im Semester', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcDeltaSWS($this->dozentID), 0, 1, 'L', 0, '', 0);

        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'aufgelaufene Mehr-/Minderarbeit', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcUeberstunden($this->dozentID), 0, 1, 'L', 0, '', 0);

    }
}