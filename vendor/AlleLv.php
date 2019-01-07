<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:20
 */

include("Benutzersitzung.php");

class AlleLv extends Benutzersitzung
{

    public function __construct()
    {
        parent::__construct();
        $this->preventOpen();
        $this->loadNav();
    }

    public function showLinkAllDozentLv()
{
    $output = '<table align="center" style="width:50%" border="1">
        <thead>
        <tr>
            <th>Lehrveranstaltung</th>
            <th>Dozent</th>
            <th>SWS</th>
        </tr>
        </thead>
        <tbody>';


    $statement = $this->dbh->prepare('SELECT `DOZENT_ID_DOZENT`, `VERANSTALTUNG_ID_VERANSTALTUNG`, `WIRKLICHE_SWS` FROM `dozent_hat_veranstaltung_in_s`');
    $result = $statement->execute();

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


    $output .= '</tbody>

    </table>';

    echo $output;

}


}