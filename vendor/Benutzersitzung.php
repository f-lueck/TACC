<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 15:03
 */

/**
 * Includes
 * Für Polymorphie
 */
include("Hilfsmethoden.php");

/**
 * Class Benutzersitzung
 * Beschäftigt sich mit dem Laden der Navbar und der Zugriffsbeschränkung
 */
class Benutzersitzung extends Hilfsmethoden
{

    /**
     * Benutzersitzung constructor.
     * Erzeugt das Objekt der Klasse
     */
    public function __construct()
    {
        //Konstruktoraufruf der Parent-Klassen
        parent::__construct();
        //Starten der Session damit diese verwendet werden kann
        session_start();

    }

    /**
     * @function loadNav
     * Lädt die Navbar auf Basis der jeweiligen Rolle durch includes
     */
    public function loadNav()
    {
        if (isset($_SESSION["Rolle"])) {
            //Prüfen der Rolle aus der Session
            switch ($this->getSession("Rolle")) {
                case "Dozent":
                    include_once("dozentsb.php");
                    break;
                case "Studiendekan":
                    include_once("studiendekansb.php");
                    break;
                case "Sekretariat":
                    include_once("sekretariatsb.php");
                    break;

                //Fehler bei der Sessioninitialisierung
                default:
                    header("Location: login.php");
                    break;
            }
        }
    }

    /**
     * @function preventOpen
     * Zugriffsbeschränkung falls kein Benutzer angemeldet ist
     */
    public function preventOpen()
    {
        if ($this->getSession("Rolle") == NULL) {
            header("Location: login.php");
        }
    }
}