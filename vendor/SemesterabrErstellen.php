<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 27.11.2018
 * Time: 14:06
 */

include ("Benutzersitzung.php");
include_once ("libraries/tcpdf/tcpdf.php");

class SemesterabrErstellen extends Benutzersitzung
{
    private $dozentID;
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

        if (isset($_POST["selectDozent"])) {
            $this->setDozentID($this->getPOST("Dozent"));
            echo $this->showSemesterabrDozent();
        }

        if (isset($_POST["print"])){
            $this->setDozentID($this->getPOST("DozentID"));
            $this->createSemesterabrPDF();
        }
    }

    private function showSemesterabrDozent(){
        $output = '';
        $output.='<form method="post">';


        $this->createSemesterabrHeader($output);
        $this->createSemesterabrTop($output);

        $this->createSemesterabrTableTop($output);
        $this->createSemesterabrTableLV($output);
        $this->createSemesterabrTableZusatza($output);
        $this->createSemesterabrTableSondera($output);
        $this->createSemesterabrTableSumme($output);

        $this->createSemesterabrTableBot($output);

        $output.='<br>';
        $output.='<input type="submit" name="print" id="print" value="Als PDF">';
        $output.='</form>';

        return $output;
    }

    private function createSemesterabrHeader(&$html){
        $html.='Ostfalia - Hochschule für angewandte Wissenschaften<br>';
        $html.='Fakultät Informatik<br>';
        $html.='Der Dekan<br>';
        $html.='<br><br>';
    }

    private function createSemesterabrTop(&$html){
        $html.='Semesterabrechnung '.$this->formatSemester($this->getCurrentSemester());
        $html.='<span style="float: right">'.$this->getCurrentDate().'</span><br>';
        $html.='<br>';
        $html.=$this->formatDozent($this->getDozent($this->dozentID)).'<br>';
        $html.='<br>';
        $html.='Lehrdeputat: <> SWS<br>';
    }

    private function createSemesterabrTableTop(&$html){
        $html.='<table border="1">';

        $html.='<tr>';
        $html.='<td></td>';
        $html.='<td>Sem.</td>';
        $html.='<td>SWS</td>';
        $html.='</tr>';

        $html.='<tr>';
        $html.='<td>bisher aufgelaufene Mehr-/Minderarbeit</td>';
        $html.='<td></td>';
        $html.='<td>'.$this->getCurrentUeberstunden($this->dozentID).'</td>';
        $html.='</tr>';

    }

    private function createSemesterabrTableLV(&$html){

        $statement=$this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN veranstaltung ON veranstaltung.ID_VERANSTALTUNG = dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG 
INNER JOIN semester ON semester.ID_SEMESTER=dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result=$statement->execute(array("DozentID"=>$this->dozentID));

        while ($data=$statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>'.$data[0].'</td>';
            $html .= '<td>'.$data[1].'</td>';
            $html .= '<td>'.$data[2].'</td>';
            $html .= '</tr>';
        }
    }

    private function createSemesterabrTableZusatza(&$html){

        $html.='<tr>';
        $html.='<td>Praxisprojekte</td>';
        $html.='<td></td>';
        $html.='<td>'.$this->getSWSArt($this->dozentID,$this->praxisprojektID).'</td>';
        $html.='</tr>';

        $html.='<tr>';
        $html.='<td>Abschlussarbeiten</td>';
        $html.='<td></td>';
        $html.='<td>'.$this->calcAbschlussarbeitenSWS($this->dozentID).'</td>';
        $html.='</tr>';

        $html.='<tr>';
        $html.='<td>Tutorien</td>';
        $html.='<td></td>';
        $html.='<td>'.$this->getSWSArt($this->dozentID,$this->tutoriumID).'</td>';
        $html.='</tr>';

        $html.='<tr>';
        $html.='<td>in anderen Fakultäten</td>';
        $html.='<td></td>';
        $html.='<td>SWS</td>';
        $html.='</tr>';
    }

    private function createSemesterabrTableSondera(&$html){

        $html .= '<tr>';
        $html .= '<td>Verfügungsstunden F+E</td>';
        $html .= '<td></td>';
        $html .= '<td>SWS</td>';
        $html .= '</tr>';

        $statement=$this->dbh->prepare("SELECT sonderaufgabe.BEZEICHNUNG, dozent_hat_sonderaufgabe_in_s.WIRKLICHE_SWS FROM `dozent_hat_sonderaufgabe_in_s` 
INNER JOIN sonderaufgabe ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE=sonderaufgabe.ID_SONDERAUFGABE
WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result=$statement->execute(array("DozentID"=>$this->dozentID));

        while ($data=$statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>'.$data[0].'</td>';
            $html .= '<td></td>';
            $html .= '<td>'.$data[1].'</td>';
            $html .= '</tr>';
        }
    }

    private function createSemesterabrTableSumme(&$html){

        $this->createBlankTableEntries($html,2);

        $html .= '<tr>';
        $html .= '<td>Summe</td>';
        $html .= '<td></td>';
        $html .= '<td>'.$this->calcSummeSWS($this->dozentID).'</td>';
        $html .= '</tr>';
    }

    private function createSemesterabrTableBot(&$html){

        $this->createBlankTableEntries($html,2);

        $html .= '<tr>';
        $html .= '<td>Mehr-/Minderarbeit im Semester</td>';
        $html .= '<td></td>';
        $html .= '<td>'.$this->calcDeltaSWS($this->dozentID).'</td>';
        $html .= '</tr>';

        $this->createBlankTableEntries($html,1);

        $html .= '<tr>';
        $html .= '<td>aufgelaufene Mehr-/Minderarbeit</td>';
        $html .= '<td></td>';
        $html .= '<td>'.$this->calcUeberstunden($this->dozentID).'</td>';
        $html .= '</tr>';

        $html .= '<input type="hidden" name="DozentID" id="DozentID" value="'.$this->dozentID.'">';


        $html.='</table>';
    }

    private function createBlankTableEntries(&$html,$anz){
        for ($i=0;$i<$anz;$i++) {
            $html .= '<tr>';
            $html .= '<td>&nbsp</td>';
            $html .= '<td>&nbsp</td>';
            $html .= '<td>&nbsp</td>';
            $html .= '</tr>';
        }
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

    private function calcDeltaSWS($dozentID){

        $swsGeleistet=$this->calcSummeSWS($dozentID);
        $swsSoll=$this->getSWSProSemester($dozentID);
        $delta=$swsGeleistet-$swsSoll;

        $this->setNeueUeberstunden($delta);
        return $delta;
    }

    private function calcUeberstunden($dozentID){
        $ueberstundenAlt=$this->getCurrentUeberstunden($dozentID);
        $ueberstundenNeu=$this->neueUeberstunden;
        $ueberstundenGesamt=$ueberstundenAlt+$ueberstundenNeu;

        return $ueberstundenGesamt;
    }

    private function setDozentID($post){
        $this->dozentID=$post;
    }

    private function setNeueUeberstunden($ueberstunden){
        $this->neueUeberstunden=$ueberstunden;
    }

    private function createSemesterabrPDF(){
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION,PDF_UNIT,PDF_PAGE_FORMAT,true,'UTF-8',false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('TACC');
        $pdf->SetTitle('Semesterabrechnung');
        $pdf->SetSubject('Semesterabrechnung');

        $pdf->AddPage();

        $this->createCellPDFHeader($pdf);
        $this->createCellPDFTop($pdf);
        $this->createCellPDFTableTop($pdf);
        $this->createCellPDFTableLV($pdf);
        $this->createCellPDFTableZusatza($pdf);
        $this->createCellPDFTableFakVer($pdf);
        $this->createCellPDFTableSondera($pdf);
        $this->createCellPDFTableSumme($pdf);
        $this->createCellPDFTableBottom($pdf);

        ob_end_clean();
        $pdf->Output('test.pdf','I');
    }

    private function createCellPDFHeader(&$pdf){
        $pdf->Cell(0, 0, 'Ostfalia - Hochschule für angewandte Wissenschaften', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Fakultät Informatik', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Der Dekan', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
    }

    private function createCellPDFTop(&$pdf){
        $pdf->Cell(160, 0, 'Semesterabrechnung '.$this->formatSemester($this->getCurrentSemester()), 0, false, 'L', 0, '', 0);
        $pdf->Cell(30, 0, $this->getCurrentDate(), 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, $this->formatDozent($this->getDozent($this->dozentID)), 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(60, 0, 'Lehrdeputat', 0, false, 'L', 0, '', 0);
        $pdf->Cell(20, 0, 'Deputat', 0, false, 'L', 0, '', 0);
        $pdf->Cell(30, 0, $this->getSWSProSemester($this->dozentID), 0, 1, 'L', 0, '', 0);
    }

    private function createCellPDFTableTop(&$pdf){
        $pdf->Cell(100, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'Semester', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'SWS', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(100, 0, 'bisher aufgelaufene Mehr-/Minderarbeit', 0, false, 'L', 0, '', 0);
        $pdf->Cell(60, 0, $this->getCurrentUeberstunden($this->dozentID), 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
    }

    private function createCellPDFTableLV(&$pdf){
        $statement=$this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN veranstaltung ON veranstaltung.ID_VERANSTALTUNG = dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG 
INNER JOIN semester ON semester.ID_SEMESTER=dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result=$statement->execute(array("DozentID"=>$this->dozentID));

        while ($data=$statement->fetch()) {
            $pdf->Cell(100, 0, $data[0], 0, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[1], 0, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[2], 0, 1, 'L', 0, '', 0);
        }
    }

    private function createCellPDFTableZusatza(&$pdf){
        $pdf->Cell(100, 0, 'Praxisprojekte', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getSWSArt($this->dozentID,$this->praxisprojektID), 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Abschlussarbeiten', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcAbschlussarbeitenSWS($this->dozentID), 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Tutorien', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->getSWSArt($this->dozentID,$this->tutoriumID), 0, 1, 'L', 0, '', 0);
    }

    private function createCellPDFTableFakVer(&$pdf){
        $pdf->Cell(100, 0, 'in anderen Fakultäten', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'SWS', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Verfügungsstunden F+E', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, 'SWS', 0, 1, 'L', 0, '', 0);
    }

    private function createCellPDFTableSondera(&$pdf){
        $statement=$this->dbh->prepare("SELECT sonderaufgabe.BEZEICHNUNG, dozent_hat_sonderaufgabe_in_s.WIRKLICHE_SWS FROM `dozent_hat_sonderaufgabe_in_s` 
INNER JOIN sonderaufgabe ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE=sonderaufgabe.ID_SONDERAUFGABE
WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result=$statement->execute(array("DozentID"=>$this->dozentID));

        while ($data=$statement->fetch()) {
            $pdf->Cell(100, 0, $data[0], 0, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
            $pdf->Cell(45, 0, $data[1], 0, 1, 'L', 0, '', 0);
        }

    }

    private function createCellPDFTableSumme(&$pdf)
    {
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(100, 0, 'Summe', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(45, 0, $this->calcSummeSWS($this->dozentID), 0, 1, 'L', 0, '', 0);
    }

    private function createCellPDFTableBottom(&$pdf){
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