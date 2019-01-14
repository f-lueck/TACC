<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 26.11.2018
 * Time: 14:33
 */

/**
 * Includes
 * Befassen sich mit der Polymorphie und Bibliothekeneinbindung für die PDF-Erzeugung
 */
include("Benutzersitzung.php");
include_once("libraries/tcpdf/tcpdf.php");

/**
 * Class AbrErstellen
 * Befasst sich mit der Erstellung der Dozentenabrechnung
 */
class AbrErstellen extends Benutzersitzung
{
    /**
     * @var
     * Globale Variablen für die Weiterverarbeitung
     */
    private $dozentID = 0;
    private $counter = 0;

    /**
     * AbrErstellen constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht aufrufen von Methoden bei Klicken des Buttons
     */
    public function __construct()
    {
        //Konstruktoraufruf von Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Erstellung der PDF auf Tastendruck
        $this->setDozentID();
        if (isset($_POST["submit"])) {
            $this->createAbrLvDeputatPDF();
        }
    }

    /**
     * @function setDozentID
     * Setzt die globale Variable $dozentID, gelesen aus der Session
     */
    private function setDozentID()
    {
        $this->dozentID = $this->getSession("IdDozent");
    }

    /**
     * @function showOwnLv
     * Zeigt die aktuell belegten Lehrveranstaltungen des jeweiligen Dozenten an
     * @return string
     * Rückgabe der Informationen in Tabellenform
     */
    public function showOwnLv()
    {
        //Tabellenheader
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

        //SQL-Statement für die Lehrveranstaltungen für den jeweiligen Dozenten
        $statement = $this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN semester ON dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER = semester.ID_SEMESTER 
INNER JOIN veranstaltung ON dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Name der Veranstaltung
        //[1]=Name des Semesters
        //[2]=Anzahl der vom Dozenten belegten SWS

        while ($data = $statement->fetch()) {
            //Nummerierung
            $this->counter++;

            $output .= '<tr>';
            $output .= '<td>';
            $output .= $this->counter . '.';
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

            //Eingabemöglichkeit für Veränderungen der SWS
            $output .= '<input type="number">';
            $output .= '</td>';
            $output .= '</tr>';

        }

        //Tabellenende
        $output .= '
</tbody>
</table>';

        return $output;
    }

    /**
     * @function showOwnSondera
     * Zeigt die aktuell zugewiesenen Sonderaufgaben des jeweiligen Dozenten an
     * @return string
     * Rückgabe der Informationen in Tabellenform
     */
    public function showOwnSondera()
    {
        //Tabellenheader
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

        //SQL-Statement für die Sonderaufgaben des jeweiligen Dozenten
        $statement = $this->dbh->prepare("SELECT sonderaufgabe.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_sonderaufgabe_in_s.WIRKLICHE_SWS FROM `dozent_hat_sonderaufgabe_in_s` 
INNER JOIN sonderaufgabe ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE=sonderaufgabe.ID_SONDERAUFGABE 
INNER JOIN semester ON dozent_hat_sonderaufgabe_in_s.SEMESTER_ID_SEMESTER=semester.ID_SEMESTER 
WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Name der Sonderaufgabe
        //[1]=Name des Semesters
        //[2]=Anzahl der vom Dozenten belegten SWS

        //Nummerierung
        $this->counter = 0;
        while ($data = $statement->fetch()) {
            $this->counter++;

            $output .= '<tr>';
            $output .= '<td>';
            $output .= $this->counter . '.';
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

            //Eingabemöglichkeit für Veränderung der SWS
            $output .= '<input type="number">';
            $output .= '</td>';
            $output .= '</tr>';

        }

        //Tabellenende
        $output .= '
</tbody>
</table>';

        return $output;
    }

    /**
     * @function showOwnZusatzaufgaben
     * Zeigt die aktuell betreuten Zusatzaufgaben des Dozenten an
     * @return string
     * Rückgabe der Informationen in Tabellenform
     */
    public function showOwnZusatzaufgaben()
    {
        //Tabellenheader
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

        //SQL-Statement für die aktuell betreuten Zusatzaufgaben des jeweiligen Dozenten
        $statement = $this->dbh->prepare("SELECT dozent_hat_zusatzaufgabe_in_s.NAME, arten_von_zusatzaufgaben.KUERZEL ,dozent_hat_zusatzaufgabe_in_s.MATRIKELNUMMER 
FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART=arten_von_zusatzaufgaben.ID_ART 
WHERE `DOZENT_ID_DOZENT`= :DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Name des Studenten
        //[1]=Kürzel der Zusatzaufgabe
        //[2]=Matrikelnummer des Studenten

        //Counter für die Nummerierung
        $this->counter = 0;
        while ($data = $statement->fetch()) {
            //Nummerierung
            $this->counter++;

            $output .= '<tr>';
            $output .= '<td>';
            $output .= $this->counter . '.';
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

        //Tabellenende
        $output .= '
</tbody>
</table>';

        return $output;
    }

    /**
     * @function getCounter
     * Liefert den aktuellen Wert von counter
     */
    public function getCounter()
    {
        echo $this->counter;
    }

    /**
     * @function setCounter
     * Setzt counter auf den gewünschten Wert
     * @param $i
     * Gewünschter Wert
     */
    public function setCounter($i)
    {
        $this->counter += $i;
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
        echo $sws;
    }

    /**
     * @function createAbrLvDeputatPDF
     * Erzeugt ein PDF Dokument mit Angabe aller Veranstaltungen nach Vorlage, zeigt diese direkt im Browser an
     */
    private function createAbrLvDeputatPDF()
    {
        //Erstellung einer neuen PDF Datei
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        //Meta-Daten
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('TACC');
        $pdf->SetTitle('Abrechnung LV Deputat');
        $pdf->SetSubject('Abrechnung LV Deputat');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        //Füllen der PDF Datei
        $this->createCellPDFTop($pdf);
        $this->createCellPDFTableLv($pdf);
        $this->createCellPDFMid($pdf);
        $this->createCellPDFTableZusatz($pdf);
        $this->createCellPDFBottom($pdf);

        //Löschung des Ausgabepuffers für Herunterladen
        ob_end_clean();
        //Anzeigen im Browser
        $pdf->Output('Abr_LV_Deputat.pdf', 'I');

    }

    /**
     * @function createCellPDFTop
     * Schreibt den Header der PDF Datei
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTop(&$pdf)
    {
        $pdf->Cell(0, 0, 'Ostfalia Hochschule für angewandte Wissenschaften', 0, false, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Wolfenbüttel, den ' . $this->getCurrentDate(), 0, 1, 'R', 0, '', 0);
        $pdf->Cell(0, 0, 'Fakultät Informatik', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Der Dekan', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Abrechnung der Lehrveranstaltungen für das ', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Name: ' . $this->dozentID, 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableLv
     * Schreibt eine Tabelle mit den belegten Lvs in die PDF Datei
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableLv(&$pdf)
    {
        $pdf->Cell(100, 0, 'Lehrveranstaltungen', 1, false, 'L', 0, '', 0);
        $pdf->Cell(20, 0, 'Semester', 1, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'SWS nach Plan', 1, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'SWS Geleistet', 1, 1, 'L', 0, '', 0);

        //SQL-Statement für die Lehrveranstaltungen des jeweiligen Dozenten
        $statement = $this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN semester ON dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER = semester.ID_SEMESTER 
INNER JOIN veranstaltung ON dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Name der Veranstaltung
        //[1]=Name des Semester
        //[2]=Anzahl der vom Dozenten belegten SWS

        $this->counter = 0;
        while ($data = $statement->fetch()) {
            //Nummerierung
            $this->counter++;

            $pdf->Cell(5, 0, $this->counter . '.', 1, false, 'L', 0, '', 0);
            $pdf->Cell(95, 0, $data[0], 1, false, 'L', 0, '', 0);
            $pdf->Cell(20, 0, $data[1], 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, $data[2], 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, 'SWS', 1, 1, 'L', 0, '', 0);
        }
    }

    /**
     * @function createCellPDFMid
     * Schreibt die SWS für Zusatz- und Sonderaufgaben in die PDF Datei
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFMid(&$pdf)
    {
        //Nummerierung
        $this->counter++;

        //SWS für Zusatzaufgaben
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(5, 0, $this->counter . '.', 0, false, 'L', 0, '', 0);
        $pdf->Cell(150, 0, 'Praxisprojekte / Abschlussarbeiten', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, $this->getSWSZusatz($this->dozentID), 1, 1, 'L', 0, '', 0);

        $this->counter++;

        //SWS für Verügungsstunden
        $pdf->Cell(5, 0, $this->counter . '.', 0, false, 'L', 0, '', 0);
        $pdf->Cell(150, 0, 'Verfügungsstunden für', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, $this->getSWSSonder($this->dozentID), 1, 1, 'L', 0, '', 0);

        $this->counter++;

        //SWS für LV in anderen Fakultäten
        $pdf->Cell(5, 0, $this->counter . '.', 0, false, 'L', 0, '', 0);
        $pdf->Cell(150, 0, 'In anderen Fakultäten', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, '', 1, 1, 'L', 0, '', 0);

        //Namen der LV in anderen Fakultäten
        for ($i = 0; $i < 3; $i++) {
            $this->counter++;
            $pdf->Cell(5, 0, $this->counter . '.', 0, false, 'L', 0, '', 0);
            $pdf->Cell(115, 0, '', 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, '', 0, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, '', 1, 1, 'L', 0, '', 0);
        }

        //Summenerstellung der SWS
        $pdf->Cell(120, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'Summe =', 0, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'SWS', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Unter 8. sind die Praxisprojekte, die Mastertutorien und die Abschlussarbeiten einzusetzen, die Sie im', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Abrechnungssemester betreut haben,  je Praxisprojekt (P) 0,2 SWS,  je Bachelorarbeit (B) 0,3 SWS,  je', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Masterarbeit (M) 1,0 SWS, je Diplomarbeit (D) 0,4 SWS und je Mastertutorium (T) 0,2 SWS. Für', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Abschlussarbeiten dürfen höchstens 2 SWS abgerechnet werden.', 0, 1, 'L', 0, '', 0);
    }

    /**
     * @function createCellPDFTableZusatz
     * Schreibt eine Tabelle mit den betreuten Zusatzaufgaben in die PDF Datei
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFTableZusatz(&$pdf)
    {
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Praxisprojekte / Abschlussarbeiten', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);

        $pdf->Cell(120, 0, 'Name', 1, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'P/B/M/D/T', 1, false, 'L', 0, '', 0);
        $pdf->Cell(35, 0, 'Matrikelnummer', 1, 1, 'L', 0, '', 0);

        //SQL-Statement für die betreuten Zusatzaufgaben des jeweiligen Dozenten
        $statement = $this->dbh->prepare("SELECT dozent_hat_zusatzaufgabe_in_s.NAME, arten_von_zusatzaufgaben.KUERZEL ,dozent_hat_zusatzaufgabe_in_s.MATRIKELNUMMER 
FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART=arten_von_zusatzaufgaben.ID_ART 
WHERE `DOZENT_ID_DOZENT`= :DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Name des Studenten
        //[1]=Kürzel der Zusatzaufgabe
        //[2]=Matrikelnummer des Studenten

        //Nummerierung
        $this->counter = 0;
        while ($data = $statement->fetch()) {
            $this->counter++;

            $pdf->Cell(5, 0, $this->counter . '.', 1, false, 'L', 0, '', 0);
            $pdf->Cell(115, 0, $data[0], 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, $data[1], 1, false, 'L', 0, '', 0);
            $pdf->Cell(35, 0, $data[2], 1, 1, 'L', 0, '', 0);
        }

    }

    /**
     * @function createCellPDFBottom
     * Schreibt das Ende der PDF Datei nach Vorlage
     * @param $pdf
     * Referenz auf zu bearbeitende PDF Datei
     */
    private function createCellPDFBottom(&$pdf)
    {
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, 'Datum, Unterschrift', 0, 1, 'L', 0, '', 0);
        $pdf->Cell(0, 0, $this->getCurrentDate() . ',', 1, 1, 'L', 0, '', 0);
        $pdf->Cell(110, 0, '', 0, false, 'L', 0, '', 0);
        $pdf->Cell(20, 0, 'Name', 0, false, 'L', 0, '', 0);
        $pdf->Cell(60, 0, '', 1, 1, 'L', 0, '', 0);
    }
}