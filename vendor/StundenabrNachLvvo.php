<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 27.11.2018
 * Time: 16:51
 */

/**
 * Includes
 * Für Polymorphie und Excel-Erzeugung
 */
include("Benutzersitzung.php");
include_once("libraries/PHPSpreadsheet/vendor/autoload.php");

//Namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class StundenabrNachLvvo
 * Ermöglicht die Erzeugung als xlsx und Ansicht der Stundenabrechnung nach LVVO
 */
class StundenabrNachLvvo extends Benutzersitzung
{
    /**
     * @var int
     * Variablen zur Weiterverarbeitung
     */
    private $praxisprojektID = 1;
    private $bachelorarbeitID = 2;
    private $masterarbeitID = 3;
    private $diplomarbeitID = 4;
    private $tutoriumID = 5;

    /**
     * StundenabrNachLvvo constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht den Methodenaufruf nach Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Erstellung der xlsx Datei
        if (isset($_POST["print"])) {
            $file = $this->createStundenabrNachLvvo();

            //Löschen des Ausgabepuffers
            ob_end_clean();
            //Herunterladen der xls
            $this->forceDownload($file);
        }
    }

    /**
     * @function createStundenabrNachLvvo
     * Erzeugt die xlsx Datei nach Vorlage
     * @return string
     * Rückgabe des Dateinamen
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function createStundenabrNachLvvo()
    {
        //Erstellung des sheets
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Füllen der xlsx
        $this->createStundenabrNachLvvoHeader($sheet);
        $this->createStundenabrNachLvvoTableHeader($sheet);

        //Counter für Abstände innerhalb der Zellen
        $counter = 8;

        $this->createStundenabrNachLvvoTableContent($sheet, $counter);
        $this->createStundenabrNachLvvoBottom($sheet, $counter);

        //Umwandeln des Sheets in eine xlsx Datei
        $writer = new Xlsx($spreadsheet);

        //Setzen des Dateinamens
        $filename = '01_Stundenabrechnung_nach_LVVO_Informatik.xlsx';
        //Speichern der Datei auf dem Server
        $writer->save($filename);
        return $filename;
    }

    /**
     * @function createStundenabrNachLvvoHeader
     * Ergänzt die xlsx Datei mit Informationen nach Vorlage
     * @param $sheet
     * Referenz auf die zu ergänzende Datei
     */
    private function createStundenabrNachLvvoHeader(&$sheet)
    {
        $cellA1 = 'Stundenabrechnung nach LVVO im ' . $this->formatSemester($this->getCurrentSemester());
        $sheet->setCellValue('A1', $cellA1);

        $sheet->setCellValue('A3', 'Fakultät: Informatik');
        $sheet->setCellValue('A4', 'Ansprechpartnerin Heidrun Rasch');
        $sheet->setCellValue('A5', 'Standort Wolfenbüttel');
    }

    /**
     * @function createStundenabrNachLvvoTableHeader
     * Ergänzt die xlsx Datei mit dem eigentlichen Tabellen-Header nach Vorlage
     * @param $sheet
     * Referenz auf die zu ergänzende Datei
     */
    private function createStundenabrNachLvvoTableHeader(&$sheet)
    {

        $sheet->setCellValue('A6', 'Lfd.N');
        $sheet->setCellValue('B6', 'Titel');
        $sheet->setCellValue('C6', 'Name');
        $sheet->setCellValue('D6', 'Lehrdeputat');
        $sheet->setCellValue('E6', 'abgerechnet');
        $sheet->setCellValue('F6', 'davon Entlastungsstunden');
        $sheet->setCellValue('K6', 'Netto Lehre');
        $sheet->setCellValue('L6', 'Delta lfd.');
        $sheet->setCellValue('M6', 'Übertrag Vorsemester');
        $sheet->setCellValue('N6', 'KtoStand +/-');

        $sheet->setCellValue('F7', 'F+E');
        $sheet->setCellValue('G7', 'herausgeh. Bedeutung');
        $sheet->setCellValue('H7', 'bes. Aufgaben');
        $sheet->setCellValue('I7', 'Sonstige');
        $sheet->setCellValue('J7', 'Definition');

    }

    /**
     * @function createStundenabrNachLvvoTableContent
     * Ergänzt die xlsx Datei mit Tabelleneinträgen zu den einzelnen Dozenten
     * @param $sheet
     * Referenz auf die zu ergänzende Datei
     * @param $counter
     * Für die Eintragung in die richtige Zeile
     */
    private function createStundenabrNachLvvoTableContent(&$sheet, &$counter)
    {
        //SQL-Statement zum Laden aller Dozenten aus der Datenbank
        $statement = $this->dbh->prepare("SELECT `ID_DOZENT`,`TITEL`, `VORNAME`, `NAME`, `SWS_PRO_SEMESTER` FROM `dozent`");
        $result = $statement->execute();

        //fetched:
        //[0]=ID des Dozenten
        //[1]=Titel des Dozenten
        //[2]=Vorname des Dozenten
        //[3]=Nachname des Dozenten
        //[4]=Deputat des Dozenten

        while ($data = $statement->fetch()) {
            $sheet->setCellValue('A' . $counter, $data[0]);
            $sheet->setCellValue('B' . $counter, $data[1]);
            $sheet->setCellValue('C' . $counter, $data[3] . ', ' . $data[2]);
            $sheet->setCellValue('D' . $counter, $data[4]);
            $sheet->setCellValue('E' . $counter, $this->calcSummeSWS($data[0]));


            $sheet->setCellValue('I' . $counter, $this->getSWSSonder($data[0]));
            $sheet->setCellValue('J' . $counter, $this->getAllSonderaBezFormat($data[0]));

            $sheet->setCellValue('L' . $counter, $this->calcDeltaSWS($data[0]));
            $sheet->setCellValue('M' . $counter, $this->getCurrentUeberstunden($data[0]));
            $sheet->setCellValue('N' . $counter, $this->calcUeberstunden($data[0]));

            //Nach Beendigung für einen Dozenten Counter erhöhen
            $counter++;
        }
    }

    /**
     * @function calcSummeSWS
     * Berechnet die Summe der SWS aller Veranstaltungen eines Dozenten
     * @param $dozentID
     * ID des Dozenten
     * @return float|int|string
     * SWS
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
     * @function calcAbschlussarbeitenSWS
     * Berechnet die SWS aller Abschlussarbeiten eines Dozenten
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
     * @function getAllSonderaBezFormat
     * Liefert eine Liste aller Sonderaufgaben eines Dozenten mit Kommata getrennt zurück
     * @param $dozentID
     * ID des Dozenten
     * @return string
     * Aufzählung aller Bezeichnungen
     */
    private function getAllSonderaBezFormat($dozentID)
    {
        //SQL-Statement für das Laden der Bezeichnung der Sonderaufgaben zurück
        $statement = $this->dbh->prepare("SELECT sonderaufgabe.BEZEICHNUNG FROM `dozent_hat_sonderaufgabe_in_s` 
INNER JOIN sonderaufgabe ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE=sonderaufgabe.ID_SONDERAUFGABE WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID" => $dozentID));

        //fetched:
        //[0]=Bezeichnung der Sonderaufgabe

        $output = '';
        //Für die Kommasetzung
        $counter = 0;
        while ($data = $statement->fetch()) {
            if ($counter != 0) {
                $output .= ', ';
            }
            $output .= $data[0];
            $counter++;
        }
        return $output;
    }

    /**
     * @function calcDeltaSWS
     * Berechnet die Differenz von Deputat und geleisteten SWS eines Dozenten
     * @param $dozentID
     * ID des Dozenten
     * @return float|int|mixed|string
     * SWS
     */
    private function calcDeltaSWS($dozentID)
    {
        $swsGeleistet = $this->calcSummeSWS($dozentID);
        $swsSoll = $this->getSWSProSemester($dozentID);
        $delta = $swsGeleistet - $swsSoll;

        return $delta;
    }

    /**
     * @function calcUeberstunden
     * Berechnet die Ueberstunden eines Dozenten
     * @param $dozentID
     * ID des Dozenten
     * @return float|int|mixed|string
     * SWS
     */
    private function calcUeberstunden($dozentID)
    {
        $ueberstundenAlt = $this->getCurrentUeberstunden($dozentID);
        $ueberstundenNeu = $this->calcDeltaSWS($dozentID);
        $ueberstundenGesamt = $ueberstundenAlt + $ueberstundenNeu;

        return $ueberstundenGesamt;
    }

    /**
     * @function createStundenabrNachLvvoBottom
     * Ergänzt die xlsx Datei mit Informationen nach Vorlage
     * @param $sheet
     * Referenz auf die zu ergänzende Datei
     * @param $counter
     * Für die Eintragung in die richtige Zeile
     */
    private function createStundenabrNachLvvoBottom(&$sheet, &$counter)
    {
        $sheet->setCellValue('A' . $counter, 'Summen');
        $counter += 2;
        $sheet->setCellValue('A' . $counter, 'Prof. Dr. U. Klages (Dekan)');
        $sheet->setCellValue('G' . $counter, $this->getCurrentDate());
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

    /**
     * @function showStundenabrNachLvvo
     * Zeigt die StundenabrNachLvvo in Tabellenform im Browser an
     * @return string
     * Gesamte Ausgabe
     */
    public function showStundenabrNachLvvo()
    {
        $html = '';

        $this->createTableHeader($html);
        $this->createTableContent($html);
        $this->createTableFooter($html);

        return $html;
    }

    /**
     * @function createTableHeader
     * Erzeugt den Tabellen-Header nach Vorlage
     * @param $html
     * Referenz auf die gesamte Ausgabe
     */
    private function createTableHeader(&$html)
    {
        $html .= '<table border="1">';

        $html .= '<tr>';
        $html .= '<th>Lfd.N</th>';
        $html .= '<th>Titel</th>';
        $html .= '<th>Name</th>';
        $html .= '<th>Lehrdeputat</th>';
        $html .= '<th>abgerechnet</th>';
        $html .= '<th colspan="5">davon Entlastungsstunden</th>';
        $html .= '<th>Netto Lehre</th>';
        $html .= '<th>Delta lfd.</th>';
        $html .= '<th>Übertrag Vorsemester</th>';
        $html .= '<th>Kontostand</th>';
        $html .= '</tr>';


        $html .= '<tr>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th>F+E</th>';
        $html .= '<th>herausgeh.Bedeutung</th>';
        $html .= '<th>besondere Aufgaben</th>';
        $html .= '<th>Sonstige</th>';
        $html .= '<th>Definition</th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '</tr>';
    }

    /**
     * @function createTableContent
     * Ergänzt die Ausgabe durch die Informationen der einzelnen Dozenten
     * @param $html
     * Referenz auf die gesamte Ausgabe
     */
    private function createTableContent(&$html)
    {
        //SQL-Statement zum Laden aller Dozenten
        $statement = $this->dbh->prepare("SELECT `ID_DOZENT`,`TITEL`, `VORNAME`, `NAME`, `SWS_PRO_SEMESTER` FROM `dozent`");
        $result = $statement->execute();

        //fetched:
        //[0]=ID des Dozenten
        //[1]=Titel des Dozenten
        //[2]=Vorname des Dozenten
        //[3]=Nachname des Dozenten
        //[4]=Deputat des Dozenten

        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>' . $data[0] . '</td>';
            $html .= '<td>' . $data[1] . '</td>';
            $html .= '<td>' . $data[2] . ', ' . $data[3] . '</td>';
            $html .= '<td>' . $data[4] . '</td>';
            $html .= '<td>' . $this->calcSummeSWS($data[0]) . '</td>';
            $html .= '<td>???</td>';
            $html .= '<td>???</td>';
            $html .= '<td>???</td>';
            $html .= '<td>' . $this->getSWSSonder($data[0]) . '</td>';
            $html .= '<td>' . $this->getAllSonderaBezFormat($data[0]) . '</td>';
            $html .= '<td>???</td>';
            $html .= '<td>' . $this->calcDeltaSWS($data[0]) . '</td>';
            $html .= '<td>' . $this->getCurrentUeberstunden($data[0]) . '</td>';
            $html .= '<td>' . $this->calcUeberstunden($data[0]) . '</td>';
            $html .= '</tr>';
        }

    }

    /**
     * @function createTableFooter
     * Schließt die Tabelle
     * @param $html
     * Referenz auf die gesamte Ausgabe
     */
    private function createTableFooter(&$html)
    {
        $html .= '</table>';
    }
}