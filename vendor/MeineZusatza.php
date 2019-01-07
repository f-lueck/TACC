<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:43
 */

include ("Benutzersitzung.php");
class MeineZusatza extends Benutzersitzung
{

    private $dozentID=0;

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

    public function showOwnZusatzaufgaben(){
        $statement=$this->dbh->prepare("SELECT * FROM `dozent_hat_zusatzaufgabe_in_s` 
INNER JOIN arten_von_zusatzaufgaben ON dozent_hat_zusatzaufgabe_in_s.ARTEN_VON_ZUSATZAUFGABEN_ID_ART = arten_von_zusatzaufgaben.ID_ART WHERE `DOZENT_ID_DOZENT` = :DozentID");
        $result=$statement->execute(array("DozentID"=>$this->dozentID));

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


        while ($data=$statement->fetch()){
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

        $output .= '
</tbody>
</table>';

        echo $output;
    }


}