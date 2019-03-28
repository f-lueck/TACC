<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 28.03.2019
 * Time: 17:15
 */

include("vendor/ZusatzaBearbeiten.php");

$obj = new ZusatzaBearbeiten();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zusatzaufgaben Bearbeiten</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <h1>Zusatzaufgaben bearbeiten</h1>
        <form method="post">
            <?php
            echo $obj->showAllZusatza();
            ?>
        </form>
    </div>
</div>
</body>
</html>