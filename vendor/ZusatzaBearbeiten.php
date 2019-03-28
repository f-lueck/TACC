<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 28.03.2019
 * Time: 17:16
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Benutzersitzung.php");

class ZusatzaBearbeiten extends Benutzersitzung
{
    /**
     * @var
     * Variablen für Weiterverarbeitung
     */
    private $message;

    /**
     * ZusatzaBearbeiten constructor.
     * Erzeugt das Objekt der Klasse und ermöglicht Methodenaufruf nach Buttonclick
     */
    public function __construct()
    {
        //Konstruktoraufruf
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();

        //Nach Buttonclick
        if (isset($_POST["submit"])) {
            $this->updateAllZusatzaInDB();
        }
    }

    /**
     * @function updateAllZusatzaInDB
     * Aktualisiert alle Zusatzaufgaben in der Datenbank
     */
    private function updateAllZusatzaInDB()
    {
        //Laden der höchsten ID
        $max = $this->getMaxZusatzaID();

        for ($i = 1; $i < ($max + 1); $i++) {
            if (isset($_POST['Bezeichnung' . $i]) & isset($_POST['SWS' . $i])) {

                //Neue Werte für die jeweilige Zusatzaufgabe
                $bezeichnung = $this->getPOST('Bezeichnung' . $i);
                $kuerzel = $this->getPOST('Kuerzel' . $i);
                $sws = $this->getPOST('SWS' . $i);

                //SQL-Statement zum Aktualisieren der Zusatzaufgabe
                $statement = $this->dbh->prepare('UPDATE `arten_von_zusatzaufgaben` SET `ID_ART`= :ID_Art,`BEZEICHNUNG`= :Bezeichnung,`KUERZEL`= :Kuerzel,`SWS`= :SWS WHERE `ID_ART` = :ID_Art');
                $result = $statement->execute(array('ID_Art' => $i, 'Bezeichnung' => $bezeichnung, 'Kuerzel' => $kuerzel, 'SWS' => $sws));
            }
            $this->message = 'Alle Zusatzaufgaben aktualisiert';
        }
        $this->showMessage();
    }

    /**
     * @function showMessage
     * Liefert Meldungen über Javascript alert() zurück
     */
    public function showMessage()
    {
        echo "<script type='text/javascript'>alert('$this->message');</script>";
    }

    public function showAllZusatza()
    {
        $html = '';
        $this->createTableHeader($html);
        $this->createTableContent($html);
        $this->createTableFooter($html);

        return $html;
    }

    /**
     * @function createTableHeader
     * Erzeugt den Tabellen Header
     * @param $html
     * Referenz auf zu ergänzenden HTML Code
     */
    private function createTableHeader(&$html)
    {
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Bezeichnung</th>';
        $html .= '<th>Kuerzel</th>';
        $html .= '<th>SWS</th>';
        $html .= '</thead>';
    }

    /**
     * @function createTableContent
     * Erweitert die Tabelle um Inhalt aus der Datenbank
     * @param $html
     * Referenz auf zu ergänzenden HTML Code
     */
    private function createTableContent(&$html)
    {
        //SQL-Statement zum Laden von ID_Art, Bezeichnung, Kuerzel und SWS aus der Datenbank
        $statement = $this->dbh->prepare('SELECT `ID_ART`, `BEZEICHNUNG`, `KUERZEL`, `SWS` FROM `arten_von_zusatzaufgaben`');
        $result = $statement->execute();

        //fetched:
        //[0]=ID_Art
        //[1]=Bezeichnung
        //[2]=Kuerzel
        //[3]=SWS

        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td><input type="text" name="Bezeichnung' . $data[0] . '" id="Bezeichnung' . $data[0] . '" value="' . $data[1] . '"></td>';
            $html .= '<td><input type="text" name="Kuerzel' . $data[0] . '" id="Kuerzel' . $data[0] . '" value="' . $data[2] . '"></td>';
            $html .= '<td><input type="number" name="SWS' . $data[0] . '" id="SWS' . $data[0] . '" value="' . $data[3] . '"></td>';
            $html .= '</tr>';
        }
    }

    /**
     * @function createTableFooter
     * Erzeugt den Tabellen Footer mit dem Button
     * @param $html
     * Referenz auf zu ergänzenden HTML Code
     */
    private function createTableFooter(&$html)
    {
        $html .= '</table>';
        $html .= '<div class="buttonholder">';
        $html .= '<button class="submitButtons" type="submit" id="submit" name="submit">Speichern</button>';
        $html .= '</div>';
    }
}