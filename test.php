<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('simple_html_dom.php');

//CONNECT TO SQL        //
$mysqli = new mysqli("localhost", "root", "root", "dkings");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$pageID = '30378';
for ($y = 0; $y < 10000;) {
//Reset PHP script processing time
set_time_limit(0);
$html = file_get_html('http://espn.go.com/mlb/player/gamelog/_/id/'.$pageID.'/year/2016');
//$html = file_get_html('http://espn.go.com/mlb/player/gamelog/_/id/30393/year/2016');


//Test to see if page is the standard pitcher page
$generalStats = $html->find('ul.general-info li');
if ($generalStats != NULL) {
  $pos_num = $generalStats[0];
  preg_match('~>(.*?)<~', $pos_num, $output);
  $position = substr($output[1], -2);
  if ($position == 'SP' || $position == 'RP') {

//Grab General Stats of Player
$generalStats = NULL;
$generalStats = $html->find('h1');
$name = $generalStats[0];
preg_match('~>(.*?)<~', $name, $output);
$name = str_replace('\'', '\\\'', $output[1]);

//Check to see if player is alread in database
$id = NULL;
$sql0 = "SELECT player_id FROM players WHERE player_name = '$name' AND position = '$position'";
echo $sql0;
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
  $id = $row['player_id'];
}
if ($id != NULL) {
$sql1 = "UPDATE players SET `espn_id` = '$pageID' WHERE `player_id` = '$id'";
echo $sql1;
$res = $mysqli->query($sql1);
echo "Inserted new record ".$sql1."<br>";
}
}
}
$y++;
$pageID++;
}

?>
