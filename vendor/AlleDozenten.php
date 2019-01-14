<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:03
 */

/**
 * Includes
 * Für die Polymorphie
 */
include("Benutzersitzung.php");

/**
 * Class alleDozenten
 * Zeigt die Belegung aller Dozenten für das jeweilige Semester
 */
class alleDozenten extends Benutzersitzung
{

    /**
     * alleDozenten constructor.
     * Erzeugt das Objekt der Klasse
     */
    public function __construct()
    {
        //Konstruktoraufruf der Parent-Klasen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();
    }

    /**
     * @function showAllDozenten
     * Gibt alle Dozenten mit ihren aktuellen Eigenschaften in einer Tabelle zurück
     */
    public function showAllDozenten()
    {
        //Tabellenheader
        $output = "<table border='1'>
<tr>
<th>Name</th>
<th>Vorname</th>
<th>Titel</th>
<th>SWS nach Plan</th>
<th>Überstunden</th>
</tr>";
        //SQL-Statement für alle Dozenten
        $statement = $this->dbh->prepare("SELECT * FROM `dozent` WHERE `ROLLE_BEZEICHNUNG` = 'Dozent' OR `ROLLE_BEZEICHNUNG` = 'Studiendekan' ORDER BY `NAME`");
        $result = $statement->execute();

        //fetched:
        //[0]=ID des Dozenten
        //[1]=Nachname
        //[2]=Vorname
        //[3]=Titel
        //[4]=Deputat
        //[5]=Ueberstunden
        //[6]=Rollenname

        while ($data = $statement->fetch()) {
            $output .= "<tr>";
            $output .= "<td>" . $data[1] . "</td>";
            $output .= "<td>" . $data[2] . "</td>";
            $output .= "<td>" . $data[3] . "</td>";
            $output .= "<td>" . $data[4] . "</td>";
            $output .= "<td>" . $data[5] . "</td>";

            $output .= "</tr>";
        }

        //Tabellenende
        $output .= "</table>";
        echo $output;
    }
}