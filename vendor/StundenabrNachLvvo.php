<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 27.11.2018
 * Time: 16:51
 */

include ("Benutzersitzung.php");
include_once ("libraries/PHPSpreadsheet/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StundenabrNachLvvo extends Benutzersitzung
{
    private $praxisprojektID=1;
    private $bachelorarbeitID=2;
    private $masterarbeitID=3;
    private $diplomarbeitID=4;
    private $tutoriumID=5;
    private $neueUeberstunden=0;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();

        if (isset($_POST["print"])){
            $file=$this->createStundenabrNachLvvo();
            ob_end_clean();
            $this->force_download($file);

        }
    }

    public function showStundenabrNachLvvo(){
        $html='';

        $this->createTableHeader($html);
        $this->createTableContent($html);

        $this->createTableFooter($html);

        return $html;
    }

    private function createTableHeader(&$html){
        $html.='<table border="1">';

        $html.='<tr>';
        $html.='<th>Lfd.N</th>';
        $html.='<th>Titel</th>';
        $html.='<th>Name</th>';
        $html.='<th>Lehrdeputat</th>';
        $html.='<th>abgerechnet</th>';
        $html.='<th colspan="5">davon Entlastungsstunden</th>';
        $html.='<th>Netto Lehre</th>';
        $html.='<th>Delta lfd.</th>';
        $html.='<th>Übertrag Vorsemester</th>';
        $html.='<th>Kontostand</th>';
        $html.='</tr>';


        $html.='<tr>';
        $html.='<th></th>';
        $html.='<th></th>';
        $html.='<th></th>';
        $html.='<th></th>';
        $html.='<th></th>';
        $html.='<th>F+E</th>';
        $html.='<th>herausgeh.Bedeutung</th>';
        $html.='<th>besondere Aufgaben</th>';
        $html.='<th>Sonstige</th>';
        $html.='<th>Definition</th>';
        $html.='<th></th>';
        $html.='<th></th>';
        $html.='<th></th>';
        $html.='<th></th>';
        $html.='</tr>';

    }

    private function createTableContent(&$html){

        $statement=$this->dbh->prepare("SELECT `ID_DOZENT`,`TITEL`, `VORNAME`, `NAME`, `SWS_PRO_SEMESTER` FROM `dozent`");
        $result=$statement->execute();

        while ($data=$statement->fetch()){
            $html.='<tr>';
            $html.='<td>'.$data[0].'</td>';
            $html.='<td>'.$data[1].'</td>';
            $html.='<td>'.$data[2].', '.$data[3].'</td>';
            $html.='<td>'.$data[4].'</td>';
            $html.='<td>'.$this->calcSummeSWS($data[0]).'</td>';
            $html.='<td>???</td>';
            $html.='<td>???</td>';
            $html.='<td>???</td>';
            $html.='<td>'.$this->getSWSSonder($data[0]).'</td>';
            $html.='<td>'.$this->getAllSonderaBezFormat($data[0]).'</td>';
            $html.='<td>???</td>';
            $html.='<td>'.$this->calcDeltaSWS($data[0]).'</td>';
            $html.='<td>'.$this->getCurrentUeberstunden($data[0]).'</td>';
            $html.='<td>'.$this->calcUeberstunden($data[0]).'</td>';
            $html.='</tr>';
        }

    }

    private function createTableFooter(&$html){
        $html.='</table>';
    }

    private function calcAbschlussarbeitenSWS($dozentID){

        $bachelorsws=$this->getSWSArt($dozentID,$this->bachelorarbeitID);
        $mastersws=$this->getSWSArt($dozentID,$this->masterarbeitID);
        $diplomsws=$this->getSWSArt($dozentID,$this->diplomarbeitID);

        return ($bachelorsws+$mastersws+$diplomsws);
    }

    private function calcSummeSWS($dozentID){
        $sws=0;
        $sws+=$this->getSWSLv($dozentID);
        $sws+=$this->getSWSArt($dozentID,$this->praxisprojektID);
        $sws+=$this->calcAbschlussarbeitenSWS($dozentID);
        $sws+=$this->getSWSArt($dozentID,$this->tutoriumID);
        //in anderen Fakultäten
        //Verfügungsstunden
        $sws+=$this->getSWSSonder($dozentID);

        return $sws;
    }

    private function getAllSonderaBezFormat($dozentID){

        $statement=$this->dbh->prepare("SELECT sonderaufgabe.BEZEICHNUNG FROM `dozent_hat_sonderaufgabe_in_s` 
INNER JOIN sonderaufgabe ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE=sonderaufgabe.ID_SONDERAUFGABE WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result=$statement->execute(array("DozentID"=>$dozentID));

        $output='';
        $counter=0;
        while ($data=$statement->fetch()){
            if ($counter!=0){
                $output.=', ';
            }
            $output.=$data[0];
            $counter++;
        }
        return $output;
    }

    private function calcDeltaSWS($dozentID){

        $swsGeleistet=$this->calcSummeSWS($dozentID);
        $swsSoll=$this->getSWSProSemester($dozentID);
        $delta=$swsGeleistet-$swsSoll;

        return $delta;
    }
    private function calcUeberstunden($dozentID){
        $ueberstundenAlt=$this->getCurrentUeberstunden($dozentID);
        $ueberstundenNeu=$this->calcDeltaSWS($dozentID);
        $ueberstundenGesamt=$ueberstundenAlt+$ueberstundenNeu;

        return $ueberstundenGesamt;
    }

    private function createStundenabrNachLvvo(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $this->createStundenabrNachLvvoHeader($sheet);
        $this->createStundenabrNachLvvoTableHeader($sheet);

        $counter=8;
        $this->createStundenabrNachLvvoTableContent($sheet,$counter);
        $this->createStundenabrNachLvvoBottom($sheet, $counter);


        $writer = new Xlsx($spreadsheet);

        $filename='01_Stundenabrechnung_nach_LVVO_Informatik.xlsx';
        $writer->save($filename);
        return $filename;
    }

    private function createStundenabrNachLvvoHeader(&$sheet){
        $cellA1 = 'Stundenabrechnung nach LVVO im '.$this->formatSemester($this->getCurrentSemester());
        $sheet->setCellValue('A1', $cellA1);

        $sheet->setCellValue('A3', 'Fakultät: Informatik');
        $sheet->setCellValue('A4', 'Ansprechpartnerin Heidrun Rasch');
        $sheet->setCellValue('A5', 'Standort Wolfenbüttel');
    }

    private function createStundenabrNachLvvoTableHeader(&$sheet){

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


    private function createStundenabrNachLvvoTableContent(&$sheet,&$counter){

        $statement=$this->dbh->prepare("SELECT `ID_DOZENT`,`TITEL`, `VORNAME`, `NAME`, `SWS_PRO_SEMESTER` FROM `dozent`");
        $result=$statement->execute();

        while ($data=$statement->fetch()){
            $sheet->setCellValue('A'.$counter, $data[0]);
            $sheet->setCellValue('B'.$counter, $data[1]);
            $sheet->setCellValue('C'.$counter, $data[3].', '.$data[2]);
            $sheet->setCellValue('D'.$counter, $data[4]);
            $sheet->setCellValue('E'.$counter, $this->calcSummeSWS($data[0]));



            $sheet->setCellValue('I'.$counter, $this->getSWSSonder($data[0]));
            $sheet->setCellValue('J'.$counter, $this->getAllSonderaBezFormat($data[0]));

            $sheet->setCellValue('L'.$counter, $this->calcDeltaSWS($data[0]));
            $sheet->setCellValue('M'.$counter, $this->getCurrentUeberstunden($data[0]));
            $sheet->setCellValue('N'.$counter, $this->calcUeberstunden($data[0]));

            $counter++;
        }

    }

    private function createStundenabrNachLvvoBottom(&$sheet, &$counter){
        $sheet->setCellValue('A'.$counter, 'Summen');
        $counter +=2;
        $sheet->setCellValue('A'.$counter, 'Prof. Dr. U. Klages (Dekan)');
        $sheet->setCellValue('G'.$counter, $this->getCurrentDate());
    }


    function force_download($filename) {
        $filedata = @file_get_contents($filename);

        // SUCCESS
        if ($filedata)
        {
            // GET A NAME FOR THE FILE
            $basename = basename($filename);

            // THESE HEADERS ARE USED ON ALL BROWSERS
            header("Content-Type: application-x/force-download");
            header("Content-Disposition: attachment; filename=$basename");
            header("Content-length: " . (string)(strlen($filedata)));
            header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
            header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

            // THIS HEADER MUST BE OMITTED FOR IE 6+
            if (FALSE === strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE '))
            {
                header("Cache-Control: no-cache, must-revalidate");
            }

            // THIS IS THE LAST HEADER
            header("Pragma: no-cache");

            // FLUSH THE HEADERS TO THE BROWSER
            flush();

            // CAPTURE THE FILE IN THE OUTPUT BUFFERS - WILL BE FLUSHED AT SCRIPT END
            ob_start();
            echo $filedata;
            unlink($filename);
        }

        // FAILURE
        else
        {
            die("ERROR: UNABLE TO OPEN $filename");
        }
    }

}