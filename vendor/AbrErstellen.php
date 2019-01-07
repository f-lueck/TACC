<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 26.11.2018
 * Time: 14:33
 */
include ("Benutzersitzung.php");
include_once ("libraries/tcpdf/tcpdf.php");

class AbrErstellen extends Benutzersitzung
{
    private $dozentID = 0;
    private $counter = 0;
    private $sws= 0;

    public function __construct()
    {
        parent::__construct();
        $this->preventOpen();
        $this->loadNav();

        $this->setDozentID();
        if (isset($_POST["submit"])){
            $this->createAbrLvDeputatPDF();
        }
    }

    private function setDozentID(){
        $this->dozentID = $this->getSession("IdDozent");
    }

    public function showOwnLv()
    {
        $output = '<table border="1">
<thead>
<tr>
<th></th>
<th>Lehrveranstaltung</th>
<th>Semester</th>
<th>SWS nach Plan</th>
<th>Wirkliche SWS</th>
</tr>
</thead>
<tbody>';

        $statement=$this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN semester ON dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER = semester.ID_SEMESTER 
INNER JOIN veranstaltung ON dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID"=>$this->dozentID));


        while ($data=$statement->fetch()){
            $this->counter++;
            $output .= '<tr>';
            $output .= '<td>';
            $output .= $this->counter.'.';
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[0];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[1];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[2];
            $output .= '</td>';
            $output .= '<td>';
            $output .= '<input type="number">';
            $output .= '</td>';
            $output .= '</tr>';

        }

        $output .= '
</tbody>
</table>';

        return $output;

    }

    public function showOwnSondera(){
        $output = '<table border="1">
<thead>
<tr>
<th></th>
<th>Sonderaufgabe</th>
<th>Semester</th>
<th>SWS nach Plan</th>
<th>Wirkliche SWS</th>
</tr>
</thead>
<tbody>';

        $statement=$this->dbh->prepare("SELECT sonderaufgabe.BEZEICHNUNG,semester.BEZEICHNUNG,dozent_hat_sonderaufgabe_in_s.WIRKLICHE_SWS FROM `dozent_hat_sonderaufgabe_in_s` 
INNER JOIN sonderaufgabe ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE=sonderaufgabe.ID_SONDERAUFGABE 
INNER JOIN semester ON dozent_hat_sonderaufgabe_in_s.SEMESTER_ID_SEMESTER=semester.ID_SEMESTER 
WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID"=>$this->dozentID));

        $this->counter=0;
        while ($data=$statement->fetch()){
            $this->counter++;

            $output .= '<tr>';
            $output .= '<td>';
            $output .= $this->counter.'.';
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[0];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[1];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[2];
            $output .= '</td>';
            $output .= '<td>';
            $output .= '<input type="number">';
            $output .= '</td>';
            $output .= '</tr>';

        }

        $output .= '
</tbody>
</table>';

        return $output;
    }

    public function showOwnZusatzaufgaben(){
        $statement=$this->dbh->prepare("SELECT dozent_hat_zusatzaufgabe_in_s.NAME, arten_von_zusatzaufgaben.KUERZEL ,dozent_hat_zusatzaufgabe_in_s.MATRIKELNUMMER 
FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART=arten_von_zusatzaufgaben.ID_ART 
WHERE `DOZENT_ID_DOZENT`= :DozentID");
        $result=$statement->execute(array("DozentID"=>$this->dozentID));

        $output = '<table border="1">
        <thead>
        <tr>
        <th></th>
            <th>Name</th>
            <th>P/B/M/D/T</th>
            <th>Matrikelnummer</th>
        </tr>
        </thead>
        <tbody>';


        $this->counter = 0;
        while ($data=$statement->fetch()){
            $this->counter++;

            $output .= '<tr>';
            $output .= '<td>';
            $output .= $this->counter.'.';
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[0];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[1];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[2];
            $output .= '</td>';
            $output .= '</tr>';

        }

        $output .= '
</tbody>
</table>';

        return $output;
    }

    public function getCounter(){
        echo $this->counter;
    }
    public function setCounter($i){
        $this->counter+=$i;
    }

    public function summeSWS(){
        $sws = $this->getSWSZusatz($this->dozentID);
        $sws += $this->getSWSSonder($this->dozentID);
        $sws += $this->getSWSLv($this->dozentID);
        echo $sws;
    }

    private function createAbrLvDeputatPDF(){
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION,PDF_UNIT,PDF_PAGE_FORMAT,true,'UTF-8',false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('TACC');
        $pdf->SetTitle('Abrechnung LV Deputat');
        $pdf->SetSubject('Abrechnung LV Deputat');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        $this->createCellPDFTop($pdf);
        $this->createCellPDFTableLv($pdf);
        $this->createCellPDFMid($pdf);
        $this->createCellPDFTableZusatz($pdf);
        $this->createCellPDFBottom($pdf);

        ob_end_clean();
       //$pdf->writeHTML($content,true,false,true,false,'');
        $pdf->Output('test.pdf','I');

    }

    private function createCellPDFTop(&$pdf){
        $pdf->Cell(0, 0, 'Ostfalia Hochschule für angewandte Wissenschaften', 0, false, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Wolfenbüttel, den '.$this->getCurrentDate(), 0, 1, 'R', 0, '', 0);
        $pdf->Cell(0, 0, 'Fakultät Informatik', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Der Dekan', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Abrechnung der Lehrveranstaltungen für das ', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Name: '.$this->dozentID, 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
    }

    private function createCellPDFTableLv(&$pdf)
    {
        $pdf->Cell(100, 0, 'Lehrveranstaltungen', 1, false, 'L', 0, '', 0);
        $pdf->Cell(20, 0, 'Semester', 1, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'SWS nach Plan', 1, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'SWS Geleistet', 1, 1, 'L', 0, '', 0);



        $statement = $this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN semester ON dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER = semester.ID_SEMESTER 
INNER JOIN veranstaltung ON dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        $this->counter=0;
        while ($data = $statement->fetch()) {
            $this->counter++;

            $pdf->Cell(5, 0, $this->counter.'.', 1, false, 'L', 0, '', 0);
            $pdf->Cell(95, 0, $data[0], 1, false, 'L', 0, '', 0);
            $pdf->Cell(20, 0, $data[1], 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, $data[2], 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, 'SWS', 1, 1, 'L', 0, '', 0);
        }
    }

    private function createCellPDFMid(&$pdf){
        $this->counter++;
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(5, 0, $this->counter.'.', 0, false, 'L', 0, '', 0);
        $pdf->Cell(150, 0, 'Praxisprojekte / Abschlussarbeiten', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, $this->getSWSZusatz($this->dozentID), 1, 1, 'L', 0, '', 0);
        $this->counter++;
        $pdf->Cell(5, 0, $this->counter.'.', 0, false, 'L', 0, '', 0);
        $pdf->Cell(150, 0, 'Verfügungsstunden für', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, $this->getSWSSonder($this->dozentID), 1, 1, 'L', 0, '', 0);
        $this->counter++;
        $pdf->Cell(5, 0, $this->counter.'.', 0, false, 'L', 0, '', 0);
        $pdf->Cell(150, 0, 'In anderen Fakultäten', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, '', 1, 1, 'L', 0, '', 0);

        for ($i=0;$i<3;$i++) {
            $this->counter++;
            $pdf->Cell(5, 0, $this->counter . '.', 0, false, 'L', 0, '', 0);
            $pdf->Cell(115, 0, '', 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, '', 0, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, '', 1, 1, 'L', 0, '', 0);
        }

        $pdf->Cell(120, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'Summe =', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'SWS', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Unter 8. sind die Praxisprojekte, die Mastertutorien und die Abschlussarbeiten einzusetzen, die Sie im', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Abrechnungssemester betreut haben,  je Praxisprojekt (P) 0,2 SWS,  je Bachelorarbeit (B) 0,3 SWS,  je', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Masterarbeit (M) 1,0 SWS, je Diplomarbeit (D) 0,4 SWS und je Mastertutorium (T) 0,2 SWS. Für', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Abschlussarbeiten dürfen höchstens 2 SWS abgerechnet werden.', 0, 1, 'L', 0, '', 0);
    }

    private function createCellPDFTableZusatz(&$pdf){
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Praxisprojekte / Abschlussarbeiten', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(120, 0, 'Name', 1, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'P/B/M/D/T', 1, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'Matrikelnummer', 1, 1, 'L', 0, '', 0);

        $statement=$this->dbh->prepare("SELECT dozent_hat_zusatzaufgabe_in_s.NAME, arten_von_zusatzaufgaben.KUERZEL ,dozent_hat_zusatzaufgabe_in_s.MATRIKELNUMMER 
FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART=arten_von_zusatzaufgaben.ID_ART 
WHERE `DOZENT_ID_DOZENT`= :DozentID");
        $result=$statement->execute(array("DozentID"=>$this->dozentID));

        $this->counter=0;
        while ($data = $statement->fetch()) {
            $this->counter++;

            $pdf->Cell(5, 0, $this->counter.'.', 1, false, 'L', 0, '', 0);
            $pdf->Cell(115, 0, $data[0], 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, $data[1], 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, $data[2], 1, 1, 'L', 0, '', 0);
        }

    }

    private function createCellPDFBottom(&$pdf){
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Datum, Unterschrift', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, $this->getCurrentDate().',', 1, 1, 'L', 0, '', 0);
        $pdf->Cell(110, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(20, 0, 'Name', 0, false, 'L', 0, '', 0);
        $pdf->Cell(60, 0, '', 1, 1, 'L', 0, '', 0);
    }
}