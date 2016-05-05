<html>
<head>
  <title>Unknown Players</title>
</head>
<?php
ini_set( 'default_socket_timeout', 120 );
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('simple_html_dom.php');

$startTime = time();
//Send updates while script is running
function send_message($startTime, $id, $message, $progress) {
    $d = array('Iteration: ' => $message , 'progress' => $progress);

    echo "<pre>Seconds: ";
    echo time() - $startTime. PHP_EOL;
    echo json_encode($d) . PHP_EOL;
    echo PHP_EOL;
    echo "</pre>";
    flush();
}

//CONNECT TO SQL        //
$mysqli = new mysqli("localhost", "root", "root", "dkings");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
//END SQL CONNECTION   //


//Grab record count
$sql0 = "SELECT count(*) AS rec_count FROM (SELECT a.* FROM dk_main a LEFT JOIN players b on a.name = b.player_name
         WHERE b.player_name IS NULL) a";
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
$rec_count = $row['rec_count'];
}

//Cycle through 1 player
$i = 0;
for ($y = 0; $y < $rec_count;) {
$sql1 = "SELECT a.* FROM dk_main a LEFT JOIN players b on a.name = b.player_name
         WHERE b.player_name IS NULL LIMIT 0,1";
$res = $mysqli->query($sql1);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
  $unmatchedPlayer = $row['name'];
}

//Get player name ready for URL
$encodedPlayer = urlencode($unmatchedPlayer);
$hrefPlayer = strtolower(str_replace(' ', '\-', $unmatchedPlayer));

//Get player name ready for SQL
$name = str_replace('\'', '\\\'', $unmatchedPlayer);
//Grab HTML page used to grep ESPN number
$html = file_get_html('https://www.google.com/search?safe=off&site=&source=hp&q='.$encodedPlayer.'+espn+mlb');

//Test to see if page has player name; if so echo ESPN number.
$bigDivs = $html->find('h3.r');
foreach($bigDivs as $div) {
    $link = $div->find('a');
    $href = $link[0]->href;
    $pattern = '#(?<=id/)[^/'.$hrefPlayer.']+#';
    preg_match($pattern,$href, $espnID);
    if ($espnID[0] !== NULL){
      //Insert Player Name and ESPN ID into players table
      $sql0 = "INSERT INTO `players`(`espn_id`, `player_name`,`added_on`) VALUES ('$espnID[0]','$name',curdate())";
      $mysqli->query($sql0);
      break;
    }
}
$y++;

$i++;
if($i %20 == 0) {
send_message($startTime, $i, $y . ' of '.$rec_count, round(($y / $rec_count) * 100 ,2).'%');
}
}
$totalTime = time() - $startTime;
echo " Total Time Taken: ".$totalTime;
?>
