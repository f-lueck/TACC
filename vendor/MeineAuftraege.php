<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:43
 */

include("Benutzersitzung.php");

class MeineAuftraege extends Benutzersitzung
{
    private $dozentID = 0;

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();
        $this->setDozentID();
    }

    private function setDozentID(){
        $this->dozentID = $this->getSession("IdDozent");
    }

    public function showOwnSondera(){
        $output = '<table align=\"center\" style=\"width: 50%\" border="1">
<thead>
<tr>
<th>Sonderaufgabe</th>
<th>SWS</th>
</tr>
</thead>
<tbody>';

        $statement=$this->dbh->prepare("SELECT * FROM `dozent_hat_sonderaufgabe_in_s` WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID"=>$this->dozentID));

        while ($data=$statement->fetch()){
            $output .= '<tr>';
            $output .= '<td>';
            $output .= $this->getSonderaBezeichnung($data[1]);
            $output .= '</td>';
            $output .= '<td>';
            $output .= $data[3];
            $output .= '</td>';
            $output .= '</tr>';

        }

        $output .= '
</tbody>
</table>';

        echo $output;
    }

    public function showOwnLv()
    {
        $output = '<table align=\"center\" style=\"width: 50%\" border="1">
<thead>
<tr>
<th>Lehrveranstaltung</th>
<th>Semester</th>
<th>SWS nach Plan</th>
</tr>
</thead>
<tbody>';

        $statement=$this->dbh->prepare("SELECT veranstaltung.BEZEICHNUNG, semester.BEZEICHNUNG, dozent_hat_veranstaltung_in_s.WIRKLICHE_SWS FROM `dozent_hat_veranstaltung_in_s` 
INNER JOIN semester ON dozent_hat_veranstaltung_in_s.SEMESTER_ID_SEMESTER = semester.ID_SEMESTER 
INNER JOIN veranstaltung ON dozent_hat_veranstaltung_in_s.VERANSTALTUNG_ID_VERANSTALTUNG = veranstaltung.ID_VERANSTALTUNG 
WHERE `DOZENT_ID_DOZENT` =:DozentID");
        $result = $statement->execute(array("DozentID"=>$this->dozentID));

        while ($data=$statement->fetch()){
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

        $output .= '
</tbody>
</table>';

        echo $output;

    }

}
