<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$array = array("0"=>"April 10 1986");
$date = $array[0];
echo $date;
echo "<br>".gettype($date);
echo "<br>".$date;
echo "<br>".date('m/d/Y', strtotime($date));
?>
