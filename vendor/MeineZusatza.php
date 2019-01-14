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
 * Class MeineZusatza
 * Ermöglicht das Anzeigen der Zusatzaufgaben eines Dozenten
 */
class MeineZusatza extends Benutzersitzung
{
    /**
     * @var int
     * Variable zur Weiterverarbeitung
     */
    private $dozentID = 0;

    /**
     * MeineZusatza constructor.
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
     * @function showOwnZusatzaufgaben
     * Zeigt die aktuell betreuten Zusatzaufgaben eines Dozenten an
     */
    public function showOwnZusatzaufgaben()
    {
        //Tabellenheader
        $output = '<table align="center" style="width:50%" border="1">
<thead>
<tr>
<th>Art</th>
<th>Matrikelnummer</th>
<th>Name</th>
<th>SWS</th>
</tr>
</thead>
<tbody>';

        //SQL-Statement für das Laden der Zusatzaufgaben auf Basis der DozentenID
        $statement = $this->dbh->prepare("SELECT * FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART = arten_von_zusatzaufgaben.ID_ART WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result = $statement->execute(array("DozentID" => $this->dozentID));

        //fetched:
        //[0]=ZusatzaufgabeID
        //[1]=DozentID
        //[2]=Zusatzaufgabe Art ID
        //[3]=Name des Studenten
        //[4]=Matrikelnummer der Studenten
        //[5]=SemesterID
        //[6]=Zusatzaufgabe Art ID
        //[7]=Bezeichnung Zusatzaufgabe
        //[8]=Kuerzel Zusatzaufgabe
        //[9]=SWS

        while ($data = $statement->fetch()) {
            $output .= '<tr>';
            $output .= '<td>';
            $output .= $data[7];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[4];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[3];
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[9];
            $output .= '</td>';
            $output .= '</tr>';
        }

        //Tabellenende
        $output .= '</tbody>
</table>';

        echo $output;
    }
}