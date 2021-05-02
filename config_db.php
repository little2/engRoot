<?php
$dbtype="mysql";

$dbname="english";



$dbuser="jxroot";
$dbpasswd="FVG2Dv3Ad5vv59Ab";


$httphost=$_SERVER["HTTP_HOST"];

if($httphost=="root.little2.net")
{
    $dbhost="localhost";
}
else
{
    
    $dbhost="192.168.100.88";
  
}
$dbhost="localhost";
?>
