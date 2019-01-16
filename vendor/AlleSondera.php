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
 * Class AlleSondera
 * Zeigt alle Sonderaufgaben in Kombination mit den bearbeitenden Dozenten an
 */
class AlleSondera extends Benutzersitzung
{
    /**
     * AlleSondera constructor.
     * Erzeugt das Objekt der Klasse
     */
    public function __construct()
    {
        //Konstruktoraufruf für die Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();
    }

    /**
     * @function showLinkAllDozentSondera
     * Zeigt die Dozenten mit ihren jeweiligen Sonderaufgaben an
     */
    public function showLinkAllDozentSondera()
    {
        $output = $this->createTableHeader();

        $rolle = 'Sekretariat';
        $statement_outer = $this->dbh->prepare('SELECT `ID_DOZENT` FROM `dozent` 
INNER JOIN dozent_hat_sonderaufgabe_in_s ON dozent_hat_sonderaufgabe_in_s.DOZENT_ID_DOZENT=dozent.ID_DOZENT 
WHERE `ROLLE_BEZEICHNUNG` != :Rolle ORDER BY `NAME`');
        $result = $statement_outer->execute(array("Rolle" => $rolle));

        //fetched:
        //[0]=DozentID

        while ($data_outer = $statement_outer->fetch()) {
            $output .= '<tr>';
            $output .= '<td>' . $this->formatDozent($this->getDozent($data_outer[0])) . '</td>';

            $dozentID = $data_outer[0];

            $statement_inner = $this->dbh->prepare('SELECT sonderaufgabe.BEZEICHNUNG, dozent_hat_sonderaufgabe_in_s.WIRKLICHE_SWS 
FROM `dozent_hat_sonderaufgabe_in_s` INNER JOIN sonderaufgabe 
ON dozent_hat_sonderaufgabe_in_s.SONDERAUFGABE_ID_SONDERAUFGABE = sonderaufgabe.ID_SONDERAUFGABE
WHERE `DOZENT_ID_DOZENT` = :DozentID');
            $result = $statement_inner->execute(array("DozentID" => $dozentID));

            //fetched:
            //[0]=Bezeichnung der Sonderaufgabe
            //[1]=SWS

            //Reihenzähler für einzelnen Dozenten
            $counter = 0;

            while ($data_inner = $statement_inner->fetch()) {
                //Neue Reihe anfangen
                if ($counter > 0) {
                    $output .= '<tr>';
                    $output .= '<td></td>';
                }
                $output .= '<td>' . $data_inner[0] . '</td>';
                $output .= '<td>' . $data_inner[1] . '</td>';
                $output .= '</tr>';

                $counter++;
            }
            //Summenbildung
            $output .= '<tr>';
            $output .= '<th colspan="2">Summe</th>';
            $output .= '<th>' . $this->getSWSSonder($dozentID) . '</th>';
            $output .= '</tr>';
        }
        //Tabellenende
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
        $output = '<table align="center" style="width:50%" border="1">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th>Dozent</th>';
        $output .= '<th>Lehrveranstaltung</th>';
        $output .= '<th>SWS</th>';
        $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        return $output;
    }
}