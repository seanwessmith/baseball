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
$html = file_get_html('http://mlb.mlb.com/news/probable_pitchers');

//Test to see if page has player name; if so echo ESPN number.
$bigDivs = $html->find('div');
foreach($bigDivs as $div) {
    $sql1 = "UPDATE dk_main SET probable = 1 WHERE name = ";
    $link = $div->find('a');
    $href = $link[0]->innertext;
    if (strpos($href, 'img') == false) {
      $sql1 .= "'".$href."'";
      echo "<br>".$sql1;
      $res = $mysqli->query($sql1);
    }
  }


?>
