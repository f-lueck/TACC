<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 13:16
 */


class Datenbank  {
    protected $dbh; // Instanz der Datenbankverbindung

    //Konstruktor: Datenbankverbindung aufbauen
    public function __construct() {

        // DB Anmeldeldedaten
        $host = "127.0.0.1";
        $port = "3306";
        $dbname = "curriculum_planung";
        $user = ""; //local
        $pass = "";
        //$user = "phoenix"; //server
        //$pass = "bNs20OCt18";

        // Verbindung herstellen
        try {
            $this->dbh = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
        } catch(PDOException $e) {
            print "Fehler: " . $e->getMessage() . "<br>";
        }
    }

    // Destruktor: Verbindung schließen
    public function __destruct() {
        $this->dbh = null;
    }
}
