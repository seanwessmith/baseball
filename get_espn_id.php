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
// END SQL CONNECTION  //

// NEW LOOP   //
//30000 - 30392
//30394 - 30820
//30820 - 30920
//30820 - 31105
//31106 - 31290
//31290 - 31762
//31762 - 32302
//3202  - 32328
//32329 - 32826
//32826 - 32985
//32985 - 33319
//33319 - 33670
//33670 - 34863
//34863 - 35000
////////////////
$pageID = '30000';
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

$generalStats = $html->find('ul.general-info li');
$pos_num = $generalStats[0];
preg_match('~>(.*?)<~', $pos_num, $output);
$position = substr($output[1], -2);
$number   = substr($output[1], 1, 2);

$throw_bat = $generalStats[1];
preg_match('~Throws: (.*?),~', $throw_bat, $output2);
$throw = $output2[1];
preg_match('~Bats: (.*?)<~', $throw_bat, $output3);
$bat = $output3[1];

$teamArray = $generalStats[2];
preg_match('~<a(.*?)/a>~', $teamArray, $output4);
$input = $output4[1];
preg_match('~>(.*?)<~', $input, $output5);
$team = $output5[1];

$generalStats2 = $html->find('ul.player-metadata li');
$birthDate = $generalStats2[0];
preg_match('~<span>Birth Date</span>(.*?) \(Age~', $birthDate, $output6);
$date = $output6[1];
$date = str_replace(',', '', $date);
//$date = str_replace(' ', '-', $date);
$date =  date('Y/m/d', strtotime($date));

if (preg_match('~Ht/Wt~', $generalStats2[3])) {
  $ht_wt = $generalStats2[3];
  preg_match('~</span>(.*?),~', $ht_wt, $output7);
  $height = $output7[1];
  preg_match('~,(.*?)lbs.~', $ht_wt, $output8);
  $weight = trim($output8[1]);
} else {
  $ht_wt = $generalStats2[4];
  preg_match('~</span>(.*?),~', $ht_wt, $output7);
  $height = $output7[1];
  preg_match('~,(.*?)lbs.~', $ht_wt, $output8);
  $weight = trim($output8[1]);
}

//Check to see if player is alread in database
$id = NULL;
$sql0 = "SELECT player_id FROM players WHERE player_name = '$name' AND position = '$position'";
echo $pageID." ".$sql0;
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
  $id = $row['player_id'];
}
if ($id == NULL) {
$sql1 = "INSERT INTO players (`espn_id`,`player_name`, `position`, `number`, `team`, `throw`, `bat`, `height`,`weight`,`birth_date`,`added_on`) VALUES ('$pageID','$name','$position','$number','$team','$throw','$bat','$height','$weight','$date',curdate())";
$res = $mysqli->query($sql1);

$sql0 = "SELECT player_id FROM players WHERE player_name = '$name' AND position = '$position'";
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
  $id = $row['player_id'];
}

echo "Inserted new record ".$sql1."<br>";
}
//Grab Field Stats of Player
$table = array();
$table = $html->find('table',1);
if ($table != NULL) {
$sql3 = "INSERT INTO `pitcher_stats`(`player_id`,`game_date`, `opponent`, `win_result`, `score_result`, `innings_pitched`,
  `hits`, `runs`, `earned_runs`, `home_runs`, `walks`, `strikeouts`, `ground_balls`, `fly_balls`, `pitches`,
  `batters_faced`, `game_score`, `added_on`) VALUES ";

//Build table from from table
$headData     = array();
$mainTable    = 0;
$skipNextRow  = 0;
$cellCounter  = 0;
$rowCounter   = 0;
foreach(($table->find('tr')) as $row) {
  $rowCounter++;
  $newRow = 1;
    $rowData = array();
    foreach($row->find('td') as $cell) {
        $cellData = $cell->innertext;
        //End the table loop once end is reached, determined by the cell "Totals"
        if ($cellData == "Totals") {
          $sql3 .= ",curdate())";
          break 2;
        }
        //Skip the two header columns following the Regular header and Monthly header
        if (strpos($cellData, 'Regular') == TRUE || $cellData == "Monthly Totals") {
          $skipNextRow = 1;
        }
        if ($skipNextRow == 0) {
          //echo $counter." ".$cellData;

        //for column 2 select right 3 or right 2 characters
          if ($cellCounter == 0) {
            $cellData .= " 2016";
            $date =  date('Y/m/d', strtotime($cellData));
            if ($rowCounter != 3) {
              $sql3 .= ",curdate()),('".$id."','".$date."'";
            } else {
              $sql3 .= "('".$id."','".$date."'";
            }
            $newRow = 0;
          } elseif ($cellCounter == 1) {
            $sql3 .= ",'".trim(substr($cellData, -3))."'";
            //for column 3 echo win or loss as boolean and echo score as seperate column
          } elseif ($cellCounter == 2) {
            preg_match('~>(.*?)<~', $cellData, $output);
            if ($output[1] == "W") {
              $sql3 .= ",'1',";
            } else {
              $sql3 .= ",'0',";
            }
            preg_match('~<a(.*?)/a~', $cellData, $output);
            $input2 = $output[1];
            preg_match('~>(.*?)<~', $input2, $output2);
            $sql3 .= "'".$output2[1]."'";
          } elseif ($cellCounter == 15 || $cellCounter == 16 || $cellCounter == 17) {
          } else {
            $sql3 .= ",'".$cellData."'";
            $newRow = 0;
          }
        }
        $cellCounter++;
      }
      if ($skipNextRow == 1) {
        $skipNextRow = 2;
      } else {
        $skipNextRow = 0;
      }
    $cellCounter = 0;
}
//Input new game stats into pitcher_stats table
$gameDate = NULL;
$sql5 = "SELECT MAX(game_date) AS max_game_date FROM pitcher_stats WHERE player_id = '$id'";
$res = $mysqli->query($sql5);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
  $gameDate = $row['max_game_date'];
}
if ($gameDate == NULL) {
  $res = $mysqli->query($sql3);
  echo $pageID." ".time()." Inserted new record<br>";
  } else {
  echo time()." Already exists in the database.<br>";
}
}
}
}
$y++;
$pageID--;
}

?>
