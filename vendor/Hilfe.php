<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 16.01.2019
 * Time: 15:23
 */

/**
 * Includes
 * Für Polymorphie
 */
include ("Benutzersitzung.php");

/**
 * Class Hilfe
 * Ermöglicht die Navigationseinbindung bei der Hilfe Seite
 */
class Hilfe extends Benutzersitzung
{
    public function __construct()
    {
        //Konstruktoraufruf für Parent-Klassen
        parent::__construct();
        //Zugriffsbeschränkung
        $this->preventOpen();
        //Laden der Navbar
        $this->loadNav();
    }
}