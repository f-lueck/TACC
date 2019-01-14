<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 13:16
 */

/**
 * Class Datenbank
 * Erzeugt die Datenbankverbindung
 */
class Datenbank
{
    //Instanz der Datenbankverbindung
    protected $dbh;

    /**
     * Datenbank constructor.
     * Baut die Datenbankverbindung auf
     */
    public function __construct()
    {

        //DB Anmeldeldedaten
        $host = "127.0.0.1";
        $port = "3306";
        $dbname = "curriculum_planung";

        //Offline
        $user = ""; //local
        $pass = "";

        //Server
        //$user = "phoenix";
        //$pass = "bNs20OCt18";

        // Verbindung herstellen
        try {
            $this->dbh = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
        } catch (PDOException $e) {
            print "Fehler: " . $e->getMessage() . "<br>";
        }
    }

    /**
     * Datenbank destructor.
     * Schließt die aktuelle Datenbankverbindung
     */
    public function __destruct()
    {
        $this->dbh = null;
    }
}
