<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:43
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class MeineAuftraege
 * Ermöglicht das Anzeigen der aktuellen Verlinkungen von LV-Dozent und SA-Dozent an
 */
class MeineAuftraege extends Benutzersitzung
{
    /**
     * @var int
     * Variable zur Weiterverarbeitung
     */
    private $dozentID = 0;

    /**
     * MeineAuftraege constructor.
     * Erzeugt das Objekt der Klasse
     */
    public function __construct()
    {
        //Konstruktoraufruf der Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();
        //Setzen der DozentenID
        $this->setDozentID();
    }

    /**
     * @function setDozentID
     * Setzt die Variable $dozentID auf den Wert aus der Session
     */
    private function setDozentID()
    {
        $this->dozentID = $this->getSession("IdDozent");
    }

    /**
     * @function showOwnSondera
     * Zeigt die aktuell belegten Sonderaufgaben des jeweiligen Dozenten als Tabelle an
     */
    public function showOwnSondera()
    {
        //Tableheader
        $output = '<table align=\"center\" style=\"width: 50%\" border="1">
<thead>
<tr>
<th>Sonderaufgabe</th>
<th>SWS</th>
</tr>
</thead>
<tbody>';

        //SQL-Statement zum Laden der Sonderaufgaben auf Basis des Dozenten
        $statement = $this->dbh->prepare("SELECT * FROM `dozent_hat_sonderaufgabe_in_s` WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=DozentID
        //[1]=SonderaufgabeID
        //[2]=SemesterID
        //[3]=SWS

        while ($data = $statement->fetch()) {
            $output .= '<tr>';
            $output .= '<td>';
            //Name der Sonderaufgabe laden
            $output .= $this->getSonderaBezeichnung($data[1]);

            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[3];
            $output .= '</td>';
            $output .= '</tr>';

        }

        //Tabellenende
        $output .= '</tbody>
</table>';

        echo $output;
    }

    /**
     * @function showOwnLv
     * Zeigt die aktuellen LVs des jeweiligen Dozenten in einer Tabelle an
     */
    public function showOwnLv()
    {
        //Tabellenheader
        $output = '<table align=\"center\" style=\"width: 50%\" border="1">
<thead>
<tr>
<th>Lehrveranstaltung</th>
<th>Semester</th>
<th>SWS nach Plan</th>
</tr>
</thead>
<tbody>';

        //SQL-Statement für das Laden der LVs des jeweiligen Dozenten
        $statement = $this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN semester ON dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER = semester.ID_SEMESTER 
INNER JOIN veranstaltung ON dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=Name der LV
        //[1]=Name des Semesters
        //[2]=SWS

        while ($data = $statement->fetch()) {
            $output .= '<tr>';
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

        $output .= '</tbody>
</table>';

        echo $output;
    }
}
