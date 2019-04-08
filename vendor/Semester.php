<?php

/**
 * Includes
 * Für Polymorphies
 */
include("Benutzersitzung.php");

class Semester extends Benutzersitzung
{
    /**
     * Semester constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadNav();

        if (isset($_POST['Create'])){
            $this->createSemesterInDB();
        }

        if (isset($_POST['Update'])){
            $this->updateSemester();
        }
    }

    public function showSemesterData() {
        $statement = $this->dbh->prepare('SELECT `ID_SEMESTER`, `BEZEICHNUNG`, `SOMMERSEMESTER`, `AKTIV`, `SPERRUNG` FROM `semester`');
        $result = $statement->execute();

        $html = '';
        $html .= '<form method="post">';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Bezeichnung</th>';
        $html .= '<th>Sommersemester</th>';
        $html .= '<th>Aktiv</th>';
        $html .= '<th>Sperrung</th>';
        $html .= '</tr>';

        while ($data = $statement->fetch()) {
            $html .= '<tr>';
            $html .= '<td>'.$data[1].'</td>';
            $html .= '<td>'.$data[2].'</td>';
            $html .= '<td><input type="radio" name="Aktiv" id="Aktiv" value="'.$data[0].'"';
            if ($data[3]) {
                $html .= ' checked = "checked" ';
            }
            $html .= '></td>';
            $html .= '<td><input type="checkbox" name="Sperrung'.$data[0].'" id="Sperrung'.$data[0].'"';
            if ($data[4]) {
                $html .= ' checked = "checked" ';
            }
            $html .= '></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        $html .= '<div class="buttonholder">';
        $html .= '<button class="submitButtons" id="Update" name="Update">Speichern</button>';
        $html .= '</div>';
        $html .= '</form>';

        return $html;
    }

    private function createSemesterInDB() {
        $bezeichnung = $this->getPOST('Bezeichnung');
        $sommersemester = $this->getPOST('Sommersemester');
        $aktiv = 1;
        $sperrung = 0;

        $this->resetOtherSemester();

        $statement = $this->dbh->prepare('INSERT INTO `semester`(`BEZEICHNUNG`, `SOMMERSEMESTER`, `AKTIV`, `SPERRUNG`) VALUES (:Bezeichnung, :Sommersemester, :Aktiv, :Sperrung)');
        $result = $statement->execute(array("Bezeichnung" => $bezeichnung, "Sommersemester" => $sommersemester, "Aktiv" => $aktiv, "Sperrung" => $sperrung));

    }

    private function resetOtherSemester(){
        $statement_outer = $this->dbh->prepare('SELECT `ID_SEMESTER` FROM `semester`');
        $result = $statement_outer->execute();

        while ($data_outer = $statement_outer->fetch()){
            $aktiv = 0;
            $sperrung = 0;
            $statement_inner = $this->dbh->prepare('UPDATE `semester` SET `AKTIV`=:Aktiv,`SPERRUNG`=:Sperrung WHERE `ID_SEMESTER` = :SemesterID');
            $result = $statement_inner->execute(array("Aktiv" => $aktiv, "Sperrung" => $sperrung, "SemesterID" => $data_outer[0]));
        }
    }

    private function updateSemester() {
        $maxSemesterID = $this->getMaxSemesterID();
        $aktivValue = $this->getPOST('Aktiv');
        for ($i = 0; $i < $maxSemesterID+1; $i++){
            if ($aktivValue == $i)  {
                $aktiv = 1;
            } else {
                $aktiv = 0;
            }
            if (isset($_POST['Sperrung'.$i])) {
            $sperrung = 1; }
            else {
                $sperrung = 0;
            }

            $statement = $this->dbh->prepare('UPDATE `semester` SET `AKTIV`=:Aktiv,`SPERRUNG`=:Sperrung WHERE `ID_SEMESTER` = :SemesterID');
            $result = $statement->execute(array('Aktiv' => $aktiv, 'Sperrung' => $sperrung, 'SemesterID' => $i));
        }
    }
}