<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 12.11.2018
 * Time: 17:26
 */

include("header.php");
include("vendor/Login.php");

$obj = new Login();
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login T-ACC</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>
<h1 style="text-align:center">Teaching-Accounting-System</h1>
<div class="login">
    <div class="form">
        <form class="login-form" method="post">
            <input type="text" placeholder="Benutzername" id="Benutzername" name="Benutzername" required="required"/>
            <input type="password" placeholder="Kennwort" id="Passwort" name="Passwort" required="required"/>
            <button type="submit" id="submit" name="submit">Anmelden</button>
            <div class="message">Bei Anmeldeproblemen wenden Sie sich bitte an das Sekretariat.</div>
        </form>
    </div>
</div>
</body>
</html>
