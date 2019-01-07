<?php

include_once ("libraries/PHPSpreadsheet/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello.xlsx');
force_download('hello.xlsx');


 function force_download($filename) {
     $filedata = @file_get_contents($filename);

     // SUCCESS
     if ($filedata)
     {
         // GET A NAME FOR THE FILE
         $basename = basename($filename);

         // THESE HEADERS ARE USED ON ALL BROWSERS
         header("Content-Type: application-x/force-download");
         header("Content-Disposition: attachment; filename=$basename");
         header("Content-length: " . (string)(strlen($filedata)));
         header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
         header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

         // THIS HEADER MUST BE OMITTED FOR IE 6+
         if (FALSE === strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE '))
         {
             header("Cache-Control: no-cache, must-revalidate");
         }

         // THIS IS THE LAST HEADER
         header("Pragma: no-cache");

         // FLUSH THE HEADERS TO THE BROWSER
         flush();

         // CAPTURE THE FILE IN THE OUTPUT BUFFERS - WILL BE FLUSHED AT SCRIPT END
         ob_start();
         echo $filedata;
         unlink('hello.xlsx');
     }

     // FAILURE
     else
     {
         die("ERROR: UNABLE TO OPEN $filename");
     }
 }
?>