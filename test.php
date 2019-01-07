<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 18:03
 */

include ("vendor/Datenbank.php");

class test extends Datenbank
{
    public function __construct()
    {

        parent::__construct();

        echo password_verify("123", password_hash("123", PASSWORD_BCRYPT));

        $statement = $this->dbh->prepare('INSERT INTO `test`(`pw`, `pwhash`) VALUES (:pw1,:pw2)');

    }
}