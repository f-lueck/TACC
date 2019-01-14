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
     * Liefert alle Sonderaufgaben in Kombination mit den bearbeitenden Dozenten als Tabelle
     */
    public function showLinkAllDozentSondera()
    {
        //Tabellenheader
        $output = '<table align="center" style="width:50%" border="1">
<thead>
<tr>
<th>Sonderaufgabe</th>
<th>Dozent</th>
<th>SWS</th>
</tr>
</thead>
<tbody>';

        //SQL-Statement für die Sonderaufgaben mit den Dozenten
        $statement = $this->dbh->prepare('SELECT `DOZENT_ID_DOZENT`, `SONDERAUFGABE_ID_SONDERAUFGABE`, `WIRKLICHE_SWS` FROM `dozent_hat_sonderaufgabe_in_s`');
        $result = $statement->execute();

        //fetched:
        //
        //
        //
        //

        while ($data = $statement->fetch()) {

            $dozentID = $data[0];
            $sonderaID = $data[1];
            $sws = $data[2];

            $output .= '<tr>';
            $output .= '<td>' . $this->getSonderaBezeichnung($sonderaID) . '</td>';
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