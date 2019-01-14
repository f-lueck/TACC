<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 18.12.2018
 * Time: 17:15
 */

/**
 * Includes
 * Für Polymorphie
 */
include ("Benutzersitzung.php");

class Home extends Benutzersitzung
{

    /**
     * Home constructor.
     * Erzeugt das Objekt der Klasse
     */
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