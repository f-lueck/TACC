<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:03
 */

include ("Benutzersitzung.php");

class alleDozenten extends Benutzersitzung
{

    public function __construct()
    {
        parent::__construct();
        $this->preventOpen();
        $this->loadNav();
    }

    public function showAllDozenten(){

       $statement=$this->dbh->prepare("SELECT * FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` = 'Dozent' OR `ROLLE_BEZEICHNUNG` = 'Studiendekan' ORDER BY `NAME`");
        $result = $statement->execute();

        $output="<table border='1'>
                <tr>
                <th>Name</th>
                <th>Vorname</th>
                <th>Titel</th>
                <th>SWS nach Plan</th>
                <th>Überstunden</th>
</tr>";

        while ($data=$statement->fetch()){
            $output.="<tr>";
            $output.="<td>".$data[1]."</td>";
            $output.="<td>".$data[2]."</td>";
            $output.="<td>".$data[3]."</td>";
            $output.="<td>".$data[4]."</td>";
            $output.="<td>".$data[5]."</td>";

            $output.="</tr>";
        }


        $output .="</table>";
        echo $output;
}
}