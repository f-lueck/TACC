<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 18.12.2018
 * Time: 17:15
 */
include ("Benutzersitzung.php");

class Home extends Benutzersitzung
{

    public function __construct()
    {
        parent::__construct();

        $this->preventOpen();
        $this->loadNav();
    }
}