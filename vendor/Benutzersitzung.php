<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 11.11.2018
 * Time: 15:03
 */
include("Hilfsmethoden.php");

class Benutzersitzung extends Hilfsmethoden
{

    public function __construct()
    {
        parent::__construct();
        session_start();

    }

    public function loadNav()
    {

        if (isset($_SESSION["Rolle"])){
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
                default:
                    header("Location: login.php");
                    break;
            }
            }
    }

    public function preventOpen(){
        if ($this->getSession("Rolle")==NULL){
            header("Location: login.php");
        }
    }

}