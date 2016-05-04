<html>
<head>
  <title>Probable Players</title>
</head>
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
//END SQL CONNECTION   //

//Grab HTML page used to grep ESPN number
$html = file_get_html('https://rotogrinders.com/lineups/mlb?site=fanduel');

$sql0 = "UPDATE dk_main SET probable = 0";
$res = $mysqli->query($sql0);
//Test to see if page has player name; if so echo ESPN number.
$link = array();
$bigDivs = $html->find('div.pitcher');
foreach($bigDivs as $div) {
  $sql1 = "UPDATE dk_main SET probable = 1 WHERE name = ";
    $link = $div->find('a');
    if (isset($link[0])) {
        $href = $link[0]->innertext;
        $sql1 .= "'".$href."'";
        $res = $mysqli->query($sql1);
    }
  }
  $bigDivs = $html->find('div.info');
  foreach($bigDivs as $div) {
    $sql1 = "UPDATE dk_main SET probable = 1 WHERE name = ";
      $link = $div->find('a');
      if (isset($link[0])) {
          $href = $link[0]->innertext;
          $sql1 .= "'".$href."'";
          $res = $mysqli->query($sql1);
      }

    }

?>
