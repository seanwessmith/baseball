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

//Grab record count
$sql0 = "SELECT count(*) AS rec_count FROM (SELECT * FROM players WHERE position NOT IN ('SP','RP')) a";
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
$rec_count = $row['rec_count'];
}
$step = 0;
for ($y = 0; $y < 1) {
//Reset PHP script processing time to prevent script ending after 30 seconds
set_time_limit(0);

$sql0 = "SELECT * FROM players WHERE position NOT IN ('SP','RP') LIMIT $step,1";
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
$espnID     = $row['espn_id'];
$playerID = $row['player_id'];
}

$html = file_get_html('http://espn.go.com/mlb/player/gamelog/_/id/'.$espnID.'/year/2016');

//Test to see if page is the standard format needed to grab relevant info//
$generalStats = $html->find('ul.general-info li');
if ($generalStats != NULL) {
  $pos_num = $generalStats[0];
  preg_match('~>(.*?)<~', $pos_num, $output);
  $position = substr($output[1], -2);
  echo $position;
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
}
}
//Test to see if page is the second format needed to grab relevant info//
if ($generalStats == NULL) {
  $generalStats = $html->find('ul.player-metadata li');
  //End this page if not one of the two defaults
  if ($generalStats == NULL) {
    break 2;
  }
  $pos_num = $generalStats[4];
  preg_match('~\/span>(.*?)<\/li~', $pos_num, $output);
  $position = $output[1];
  if ($position == "Starting Pitcher") {
    $position = "SP";
  }
  if ($position == "Relief Pitcher") {
    $position = "RP";
  }

  if ($position == 'SP' || $position == 'RP') {
  $generalStats = $html->find('h1');
  $name = $generalStats[0];
  preg_match('~>(.*?)<~', $name, $output);
  $name = str_replace('\'', '\\\'', $output[1]);

  $generalStats = $html->find('ul.player-metadata li');
  $birthDate = $generalStats[0];
  preg_match('~\/span>(.*?)<\/li~', $birthDate, $output6);
  $date = $output6[1];
  $date = str_replace(',', '', $date);
  $date =  date('Y/m/d', strtotime($date));

  //Set defaults for incomplete data
  $number = 0;
  $team   = NULL;
  $throw  = NULL;
  $bat    = NULL;
  $height = NULL;
  $weight = NULL;
}
}

$sql1 = "UPDATE players SET `espn_id` = '$espnID',`player_name` = '$name', `position` = '$position', `number` = '$number', `team` = '$team', `throw` = '$throw', `bat` = '$bat', `height` = '$height',
                              `weight` = '$weight',`birth_date` = '$date',`added_on` = curdate() WHERE position_id = $playerID";
echo $sql1."<br>";
$res = $mysqli->query($sql1);

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
$date         = 0;
$cellSQL      = NULL;
foreach(($table->find('tr')) as $row) {
  $rowCounter++;
  echo "<br>".$rowCounter;
  $newRow = 1;
    $rowData = array();
    foreach($row->find('td') as $cell) {
        $cellData = $cell->innertext;
        //End the table loop once end is reached, determined by the cell "Totals"
        if ($cellData == "Totals") {
          break 2;
        }
        //Skip the two header columns following the Regular header and Monthly header
        if (strpos($cellData, 'Regular') == TRUE || $cellData == "Monthly Totals") {
          $skipNextRow = 1;
        }
        if ($skipNextRow == 0) {
          if ($cellCounter == 0) {
            $cellData .= " 2016";
            $date =  date('Y/m/d', strtotime($cellData));
            if ($rowCounter == 3) {
              $cellSQL .= "('".$playerID."','".$date."'";
            } else {
              $cellSQL .= ",('".$playerID."','".$date."'";
            }
            $newRow = 0;
          } elseif ($cellCounter == 1) {
            $cellSQL .= ",'".trim(substr($cellData, -3))."'";
          } elseif ($cellCounter == 2) {
            preg_match('~>(.*?)<~', $cellData, $output);
            if ($output[1] == "W") {
              $cellSQL .= ",'1',";
            } else {
              $cellSQL .= ",'0',";
            }
            preg_match('~<a(.*?)/a~', $cellData, $output);
            $input2 = $output[1];
            preg_match('~>(.*?)<~', $input2, $output2);
            $cellSQL .= "'".$output2[1]."'";
          } elseif ($cellCounter == 15 || $cellCounter == 16 || $cellCounter == 17) {
          } elseif ($cellCounter == 14) {
            $cellSQL .= ",'".$cellData."',curdate())";
          }else {
            $cellSQL .= ",'".$cellData."'";
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
    //See if this players game has already been recorded in pitcher_stats
    $sql4 = "SELECT count(*) as p_count FROM pitcher_stats WHERE player_id = '$playerID' AND game_date = '$date'";
    $res = $mysqli->query($sql4);
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
      $pCount = $row['p_count'];
    }
    if ($pCount == 0) {
      $sql3 .= $cellSQL;
      $cellSQL = NULL;
    }
}
//Input new game stats into pitcher_stats table
echo "<br>".$sql3;
$res = $mysqli->query($sql3);
}
$y++;
$step++;
}

?>
