<html>
<head>
  <title>Team Schedule</title>
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
$teams = array();
$sql0 = "SELECT * FROM team";
$res = $mysqli->query($sql0);
$res->data_seek(0);
  while ($row = $res->fetch_assoc()) {
    $teams[$row['team_name']] = $row['nickname'];
  }
  foreach ($teams as $key => $value) {
//Grab HTML page used to grep ESPN number
$html = file_get_html('http://espn.go.com/mlb/team/schedule/_/name/'.$value);

$link = array();
$bigDivs = $html->find('tr');
foreach($bigDivs as $div) {
  $found = NULL;
  $nobr = $div->find('nobr');
  if (isset($nobr[0])) {
    if (strpos($nobr[0], date('F j')) == true) {
      $found = 1;
    }
  }
  if ($found == 1) {
    ////Grab the games location
    $list = $div->find('li[class=game-status]');
    $location = $list[0]->innertext;
    ////Grab the opposing team
    $list2 = $div->find('li[class=team-name]');
    $href = $list2[0]->find('a');
    $opponent = $href[0]->innertext;
    $sql1 = "UPDATE team SET opponent = '$opponent' WHERE team_name = '$key'";
    $res = $mysqli->query($sql1);
}
}
}

?>
