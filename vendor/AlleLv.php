<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:20
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
 * Class AlleLv
 * Listet alle Dozenten mit ihren Lehrveranstaltungen auf
 */
class AlleLv extends Benutzersitzung
{

    /**
     * AlleLv constructor.
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

        if (isset($_POST['print'])) {
            $file = $this->createAlleLVXls();

            //Löschen des Ausgabepuffers
            ob_end_clean();
            //Herunterladen der xls
            $this->forceDownload($file);
        }
    }

    /**
     * @function createAlleLVXls
     * Erzeugt die xlsx Datei nach Vorlage
     * @return string
     * Rückgabe des Dateinamen
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function createAlleLVXls()
    {
        //Erstellung des sheets
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Füllen der xlsx

        $this->createAlleLVXlsHeader($sheet);
        $this->createAlleLVXlsTable($sheet);

        //Umwandeln des Sheets in eine xlsx Datei
        $writer = new Xlsx($spreadsheet);

        //Setzen des Dateinamens
        $filename = 'Alle_LV.xlsx';
        //Speichern der Datei auf dem Server
        $writer->save($filename);
        return $filename;
    }

    /**
     * @function createAlleLVXlsHeader
     * Ergänzt die xlsx Datei um den Tabellenheader
     * @param $sheet
     * Die zu bearbeitende Datei
     */
    private function createAlleLVXlsHeader(&$sheet)
    {
        $sheet->setCellValue('A1', $this->getCurrentDate());
        $sheet->mergeCells('A1:J1');

        $sheet->setCellValue('A3', 'Dozent');
        $sheet->setCellValue('B3', 'Lehrveranstaltung');
        $sheet->setCellValue('C3', 'SWS');
        $sheet->setCellValue('D3', 'B I');
        $sheet->setCellValue('E3', 'B ITM');
        $sheet->setCellValue('F3', 'B WI');
        $sheet->setCellValue('G3', 'M Alt');
        $sheet->setCellValue('H3', 'M');
        $sheet->setCellValue('I3', 'VFH');
        $sheet->setCellValue('J3', 'P-Form');
        $sheet->getStyle('A3:J3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }

    /**
     * @function createAlleLVXlsTable
     * Ergänzt die xlsx Datei um Tabelleneinträge
     * @param $sheet
     * Die zu bearbeitende Datei
     */
    private function createAlleLVXlsTable(&$sheet)
    {
        $rolle = 'Sekretariat';

        //SQL-Statement um alle Dozenten IDs zu laden
        $statement_outer = $this->dbh->prepare('SELECT `ID_DOZENT` FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` != :Rolle ORDER BY `NAME`');
        $result = $statement_outer->execute(array("Rolle" => $rolle));

        //fetched:
        //[0]=ID Dozent

        //Reihen
        $counter = 4;

        while ($data_outer = $statement_outer->fetch()) {
            $dozentID = $data_outer[0];
            $sheet->setCellValue('A' . $counter, $this->formatDozent($this->getDozent($dozentID)));

            //SQL-Statement um den Namen und die SWS der Veranstaltungen des Dozenten zu laden
            $statement_inner = $this->dbh->prepare('SELECT veranstaltung.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS 
FROM `dozent_hat_veranstaltung_in_s` INNER JOIN veranstaltung 
ON dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE `DOZENT_ID_DOZENT` = :DozentID');
            $result = $statement_inner->execute(array("DozentID" => $dozentID));

            //fetched:
            //[0]=Name der Veranstaltung
            //[1]=Wirkliche SWS

            //Counter für Dozent
            $dozentlv = 0;
            while ($data_inner = $statement_inner->fetch()) {
                $sheet->setCellValue('B' . $counter, $data_inner[0]);
                $sheet->setCellValue('C' . $counter, $data_inner[1]);

                $dozentlv++;
                $counter++;
            }
            $sheet->setCellValue('A' . $counter, 'Summe');
            $sheet->mergeCells('A' . $counter . ':B' . $counter);
            $sheet->setCellValue('C' . $counter, $this->getSWSLv($dozentID));
            $counter++;
        }
        $sheet->getStyle('A4:J' . ($counter - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }

    /**
     * @function showLinkAllDozentLV
     * Zeigt alle Dozenten mit deren Veranstaltungen an
     */
    public function showLinkAllDozentLV()
    {
        $output = $this->createTableHeader();
        $rolle = 'Sekretariat';

        //SQL-Statement um alle Dozenten IDs zu laden
        $statement_outer = $this->dbh->prepare('SELECT `ID_DOZENT` FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` != :Rolle ORDER BY `NAME`');
        $result = $statement_outer->execute(array("Rolle" => $rolle));

        //fetched:
        //[0]=Dozent ID

        while ($data_outer = $statement_outer->fetch()) {
            $output .= '<tr>';
            $output .= '<td>' . $this->formatDozent($this->getDozent($data_outer[0])) . '</td>';

            $dozentID = $data_outer[0];

            //SQL-Statement um den Namen und die SWS der Veranstaltungen des Dozenten zu laden
            $statement_inner = $this->dbh->prepare('SELECT veranstaltung.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS 
FROM `dozent_hat_veranstaltung_in_s` INNER JOIN veranstaltung 
ON dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE `DOZENT_ID_DOZENT` = :DozentID');
            $result = $statement_inner->execute(array("DozentID" => $dozentID));

            //fetched:
            //[0]=Name der Veranstaltung
            //[1]=Wirkliche SWS

            $counter = 0;

            while ($data_inner = $statement_inner->fetch()) {
                if ($counter > 0) {
                    $output .= '<tr>';
                    $output .= '<td></td>';
                }
                $output .= '<td>' . $data_inner[0] . '</td>';
                $output .= '<td>' . $data_inner[1] . '</td>';
                $output .= '<td></td>';
                $output .= '<td></td>';
                $output .= '<td></td>';
                $output .= '<td></td>';
                $output .= '<td></td>';
                $output .= '<td></td>';
                $output .= '<td></td>';
                $output .= '</tr>';

                $counter++;
            }

            $output .= '<tr>';
            $output .= '<th colspan="2">Summe</th>';
            $output .= '<th>' . $this->getSWSLv($dozentID) . '</th>';
            $output .= '<td colspan="7"></td>';
            $output .= '</tr>';

        }

        $output .= '</tbody>';
        $output .= '</table>';

        echo $output;
    }

    /**
     * @function createTableHeader
     * Erzeugt den Tabellen-Header für die Ausgabe
     * @return string
     * Tabellenheader
     */
    private function createTableHeader()
    {
        $output = '<table align="center" style="width:70%" border="1">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th>Dozent</th>';
        $output .= '<th>Lehrveranstaltung</th>';
        $output .= '<th>SWS</th>';
        $output .= '<th>B I</th>';
        $output .= '<th>B ITM</th>';
        $output .= '<th>B WI</th>';
        $output .= '<th>M Alt</th>';
        $output .= '<th>M</th>';
        $output .= '<th>VFH</th>';
        $output .= '<th>P-Form</th>';
        $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        return $output;
    }

    public function buttonDownload()
    {
        $rolle = $this->getSession('Rolle');
        $button = '<div class="buttonholder">';
        $button .= '<form method="post">';
        $button .= '<button class="submitButtons" type="submit" name="print" id="print">Herunterladen</button>';
        $button .= '</form>';
        $button .= '</div>';
        if ($rolle == 'Studiendekan' || $rolle == 'Sekretariat') {
            echo $button;
        }
    }
}