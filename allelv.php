<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 13.11.2018
 * Time: 17:14
 */

include("vendor/AlleLv.php");
$obj = new AlleLv();
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8"/>
    <title>Lehrveranstaltungen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="main">
    <div class="center">
        <br>
        <h1>Alle Lehrveranstaltungen</h1>
        <br>

        <div class="allelv">
            <?php
            $obj->showLinkAllDozentLv();
            ?>
            <div class="buttonholder">
                <form method="post">
                    <button class="submitButtons" type="submit" name="print" id="print">Herunterladen</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
