<?php
include_once ('class_translate.php');
$keyword="test";
$ts = new Translate();
$outputStrArr=$ts->exec($keyword); 
print_r($outputStrArr);
?>
