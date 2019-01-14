<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:20
 */

/**
 * Includes
 * Für die Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class AlleLv
 * Listet alle Dozenten mit ihren Lehrveranstaltungen auf
 */
class AlleLv extends Benutzersitzung
{

    /**
     * AlleLv constructor.
     * Erzeugt das Objekt der Klasse
     */
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();
    }

    /**
     * @function showLinkAllDozentLv
     * Gibt alle Dozenten mit ihren belegten Lehrveranstaltungen zurück
     */
    public function showLinkAllDozentLv()
    {
        //Tabellheader
        $output = '<table align="center" style="width:50%" border="1">
<thead>
<tr>
<th>Lehrveranstaltung</th>
<th>Dozent</th>
<th>SWS</th>
</tr>
</thead>
<tbody>';

        //SQL-Statement für die Dozenten und ihre Lehrveranstaltungen
        $statement = $this->dbh->prepare('SELECT `DOZENT_ID_DOZENT`, `VERANSTALTUNG_ID_VERANSTALTUNG`, `WIRKLICHE_SWS` FROM `dozent_hat_veranstaltung_in_s`');
        $result = $statement->execute();

        //fetched:
        //
        //
        //

        while ($data = $statement->fetch()) {

            $dozentID = $data[0];
            $lvID = $data[1];
            $sws = $data[2];

            $output .= '<tr>';
            $output .= '<td>' . $this->getlvBezeichnung($lvID) . '</td>';
            $output .= '<td>' . $this->formatDozent($this->getDozent($dozentID)) . '</td>';
            $output .= '<td>' . $sws . '</td>';
            $output .= '</tr>';
        }

        //Tabellenende
        $output .= '</tbody>
</table>';

        echo $output;
    }
}