<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:43
 */

include ("Benutzersitzung.php");

class AlleSondera extends Benutzersitzung
{
    public function __construct()
    {
        parent::__construct();
        $this->preventOpen();
        $this->loadNav();
    }

    public function showLinkAllDozentSondera(){
        $output = '<table align="center" style="width:50%" border="1">
        <thead>
        <tr>
            <th>Sonderaufgabe</th>
            <th>Dozent</th>
            <th>SWS</th>
        </tr>
        </thead>
        <tbody>';


        $statement = $this->dbh->prepare('SELECT `DOZENT_ID_DOZENT`, `SONDERAUFGABE_ID_SONDERAUFGABE`, `WIRKLICHE_SWS` FROM `dozent_hat_sonderaufgabe_in_s`');
        $result = $statement->execute();

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


        $output .= '</tbody>

    </table>';

        echo $output;
    }


}