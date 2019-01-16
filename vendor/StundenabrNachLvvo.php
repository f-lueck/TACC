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
    private $summeUeberstunden = 0;

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

        $sheet->getStyle('A6:N7')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
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

        $sheet->mergeCells('F6:J6');
        $sheet->mergeCells('A7:E7');
        $sheet->mergeCells('K7:N7');
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
        $rolle = 'Sekretariat';

        //SQL-Statement zum Laden aller Dozenten aus der Datenbank
        $statement = $this->dbh->prepare("SELECT `ID_DOZENT`,`TITEL`, `VORNAME`, `NAME`, `SWS_PRO_SEMESTER`, `FE` FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` != :Rolle ORDER BY `NAME`");
        $result = $statement->execute(array('Rolle' => $rolle));

        //fetched:
        //[0]=ID des Dozenten
        //[1]=Titel des Dozenten
        //[2]=Vorname des Dozenten
        //[3]=Nachname des Dozenten
        //[4]=Deputat des Dozenten
        //[5]=F+E

        $anzDozenten = 1;


        while ($data = $statement->fetch()) {
            $sheet->setCellValue('A' . $counter, $anzDozenten);
            $sheet->setCellValue('B' . $counter, $data[1]);
            $sheet->setCellValue('C' . $counter, $data[3] . ', ' . $data[2]);
            $sheet->setCellValue('D' . $counter, $data[4]);
            $sheet->setCellValue('E' . $counter, $this->summeSWS($data[0]));
            $sheet->setCellValue('F' . $counter, $data[5]);


            $sheet->setCellValue('I' . $counter, $this->getSWSSonder($data[0]));
            $sheet->setCellValue('J' . $counter, $this->getAllSonderaBezFormat($data[0]));
            $sheet->setCellValue('K' . $counter, $this->calcNettoLehre($data[0]));

            $sheet->setCellValue('L' . $counter, $this->calcDeltaSWS($data[0]));
            $sheet->setCellValue('M' . $counter, $this->getCurrentUeberstunden($data[0]));
            $ueberstunden = $this->calcUeberstunden($data[0]);
            $sheet->setCellValue('N' . $counter, $ueberstunden);

            //Nach Beendigung für einen Dozenten Counter erhöhen
            $counter++;
            $anzDozenten++;
            $this->summeUeberstunden += $ueberstunden;
        }
        $sheet->getStyle('A8:N' . $counter)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }

    /**
     * @function summeSWS
     * Liefert die Summe aller aktuellen Veranstaltungen
     */
    public function summeSWS($dozentID)
    {
        $sws = $this->getSWSZusatz($dozentID);
        $sws += $this->getSWSSonder($dozentID);
        $sws += $this->getSWSLv($dozentID);
        $sws += $this->getFE($dozentID);
        $sws += $this->getSWSInAF($dozentID);
        return $sws;
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
     * @function calcNettoLehre
     * Berechnet die Netto SWS (Gesamt - Verfuegungsstunden)
     * @param $dozentID
     * ID des Dozenten
     * @return int|mixed|string
     * Netto SWS
     */
    private function calcNettoLehre($dozentID)
    {
        $sws = $this->getSWSZusatz($dozentID);
        $sws += $this->getSWSLv($dozentID);
        $sws += $this->getSWSInAF($dozentID);
        return $sws;
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
        $swsGeleistet = $this->summeSWS($dozentID);
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
        $sheet->mergeCells('A' . $counter . ':K' . $counter);
        $sheet->setCellValue('L' . $counter, $this->summeUeberstunden);
        $sheet->mergeCells('L' . $counter . ':N' . $counter);
        $counter += 2;
        $sheet->setCellValue('A' . $counter, 'Prof. Dr. U. Klages (Dekan)');
        $sheet->setCellValue('I' . $counter, 'Stand: ' . $this->getCurrentDate());
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
        $html .= '<th colspan="5"></th>';
        $html .= '<th>F+E</th>';
        $html .= '<th>herausgeh. Bedeutung</th>';
        $html .= '<th>besondere Aufgaben</th>';
        $html .= '<th>Sonstige</th>';
        $html .= '<th>Definition</th>';
        $html .= '<th colspan="4"></th>';
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
        $rolle = 'Sekretariat';

        //SQL-Statement zum Laden aller Dozenten
        $statement = $this->dbh->prepare("SELECT `ID_DOZENT`,`TITEL`, `VORNAME`, `NAME`, `SWS_PRO_SEMESTER`, `FE`, `SWS_I_A_F`  
FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` != :Rolle ORDER BY `NAME`");
        $result = $statement->execute(array('Rolle' => $rolle));

        //fetched:
        //[0]=ID des Dozenten
        //[1]=Titel des Dozenten
        //[2]=Vorname des Dozenten
        //[3]=Nachname des Dozenten
        //[4]=Deputat des Dozenten
        //[5]=SWS für F+E
        //[6]=SWS in anderen Fakultäten

        $counter = 1;
        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>' . $counter . '</td>';
            $html .= '<td>' . $data[1] . '</td>';
            $html .= '<td>' . $data[2] . ', ' . $data[3] . '</td>';
            $html .= '<td>' . $data[4] . '</td>';
            $html .= '<td>' . $this->summeSWS($data[0]) . '</td>';
            $html .= '<td>' . $data[5] . '</td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td>' . $this->getSWSSonder($data[0]) . '</td>';
            $html .= '<td>' . $this->getAllSonderaBezFormat($data[0]) . '</td>';
            $html .= '<td>' . $this->calcNettoLehre($data[0]) . '</td>';
            $html .= '<td>' . $this->calcDeltaSWS($data[0]) . '</td>';
            $html .= '<td>' . $this->getCurrentUeberstunden($data[0]) . '</td>';
            $ueberstunden = $this->calcUeberstunden($data[0]);
            $html .= '<td>' . $ueberstunden . '</td>';
            $html .= '</tr>';

            $counter++;
            $this->summeUeberstunden += $ueberstunden;
        }

    }

    /**
     * @function createTableFooter
     * Schließt die Tabelle mit Summe der Ueberstunden
     * @param $html
     * Referenz auf die gesamte Ausgabe
     */
    private function createTableFooter(&$html)
    {
        $html .= '<tr>';
        $html .= '<th colspan="11">Summe</th>';
        $html .= '<th colspan="3">' . $this->summeUeberstunden . '</th>';
        $html .= '</tr>';

        $html .= '</table>';
    }
}