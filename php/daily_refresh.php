<html>
<head>
  <title>Player Update</title>
</head>
<?php
//Send updates while script is running
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

//Prevent timing out
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

////CONNECT TO SQL        ////
$mysqli = new mysqli("localhost", "root", "root", "dkings");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
//// END SQL CONNECTION  ////
/*
////Update the probable players for the day////
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
    ////END update the probable players////

    ////Update the team's opponents for the day////
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
    ////END the teams opponents////
*/
////INPUT: SELECT statement that selects players needing updating////
$sqlSelect = "SELECT * FROM players WHERE refreshed_on <> curdate() + 1 ORDER BY player_id DESC";
/////////////////////////////////////////////////////////////////////

//Grab record count
$sql0 = "SELECT count(*) AS rec_count FROM ($sqlSelect) a";
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
$rec_count = $row['rec_count'];
}
$step        = 0;
$updateCount = 0;
$newRecords  = 0;
$noTable     = 0;
$i           = 0;
$runSQL      = 0;
$noTablePlayer = array();
for ($y = 0; $y < $rec_count;) {
//Reset PHP script processing time to prevent script ending after 30 seconds//
set_time_limit(0);
//////////////////////////////////////////////////////////////////////////////

//Select one player that needs updating
$sql0 = $sqlSelect." LIMIT ".$step.",1";
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
$espnID     = $row['espn_id'];
$playerID = $row['player_id'];
}
  echo "<br>ESPN_ID: ".$espnID;
  echo " Player_ID: ".$playerID;
$html = file_get_html('http://espn.go.com/mlb/player/gamelog/_/id/'.$espnID.'/year/2016');

//    THIS UPDATES PLAYERS STATIC INFO
//Test to see if page is the standard format needed to grab relevant info//
$name     = NULL;
$position = NULL;
$generalStats = $html->find('ul.general-info li');
if ($generalStats != NULL) {
  $pos_num = $generalStats[0];
  preg_match('~>(.*?)<~', $pos_num, $output);
  $position = substr($output[1], -2);

  if ($position !== NULL) {
  //Grab General Stats of Player

  $generalStats2 = $html->find('h1');
  $name = $generalStats2[0];
  preg_match('~>(.*?)<~', $name, $output);
  $name = str_replace('\'', '\\\'', $output[1]);

  $generalStats2 = $html->find('ul.general-info li');
  $pos_num = $generalStats2[0];
  preg_match('~>(.*?)<~', $pos_num, $output);
  $position = substr($output[1], -2);
  if (strpos($position, 'F')) {
    $position = "OF";
  }
  $number   = substr($output[1], 1, 2);

  $throw_bat = preg_replace('~[,:]~', '', $generalStats2[1]->innertext);
  $arr = explode(' ', $throw_bat);
  $how_many = count($arr);
  for($i = 0; $i < $how_many; $i = $i + 2){
    if ($arr[$i] == "Bats") {
      $bat = $arr[$i+1];
    } elseif ($arr[$i] == "Throws") {
      $throw = $arr[$i+1];
    }
  }
  
  $teamArray = $generalStats2[2];
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
} else {
  $generalStats = $html->find('ul.player-metadata li');
  if ($generalStats != NULL) {
  foreach ($generalStats as $key => $value) {
    if (strpos($value->innertext, 'Date')) {
      foreach($value->find('span') as $e) {
        $e->outertext = '';
    }
    $date = $value->innertext;
    $date = str_replace('Birth Date', '', $date);
    $date =  date('Y/m/d', strtotime($date));
  } elseif (strpos($value->innertext, 'Position')) {
      $position = str_replace('Position', '', $value->innertext) ;
      if (strpos($position, 'Field')) {
        $position = "OF";
      } elseif (strpos($position, 'Catcher')) {
        $position = "C";
      } elseif (strpos($position, 'Second')) {
        $position = "2B";
      } elseif (strpos($position, 'First')) {
        $position = "1B";
      } elseif (strpos($position, 'Third')) {
        $position = "3B";
      }
      if (strpos($position, 'Short')) {
        $position = "SS";
      }
    }
}
  $generalStats = $html->find('h1');
  $name = $generalStats[0];
  preg_match('~>(.*?)<~', $name, $output);
  $name = str_replace('\'', '\\\'', $output[1]);

  //Set defaults for incomplete data
  $number = 0;
  $team   = NULL;
  $throw  = NULL;
  $bat    = NULL;
  $height = NULL;
  $weight = NULL;
}
}

if ($name != NULL && $date !== NULL) {
echo "<br>Name: ".$name." Position: ".$position." Number: ".$number." Team: ".$team." Throw: ".$throw." Bat: ".$bat." Height: ".$height." Birth Date".$date;
$sql1 = "UPDATE players SET `espn_id` = '$espnID',`player_name` = '$name', `position` = '$position', `number` = '$number', `team` = '$team', `throw` = '$throw', `bat` = '$bat', `height` = '$height',
                              `weight` = '$weight',`birth_date` = '$date',`changed_on` = curdate() WHERE player_id = $playerID";
$res = $mysqli->query($sql1);
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
$date         = 0;
$cellSQL      = NULL;
foreach(($table->find('tr')) as $row) {
  $rowCounter++;
    $rowData = array();
    foreach($row->find('td') as $cell) {
        $cellData = $cell->innertext;
        //End the table loop once end is reached, determined by the cell "Totals"
        if ($cellData == "Totals" || $cellData == "&nbsp;") {
          break 2;
        }
        //Skip the two header columns following the Regular header and Monthly header
        if (strpos($cellData, 'Regular') == TRUE || $cellData == "Monthly Totals") {
          $skipNextRow = 1;
        }
        if ($cellData == "DATE") {
          $skipNextRow = 2;
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
          }
        }
        $cellCounter++;
      }
    $cellCounter = 0;
    //See if this players game has already been recorded in pitcher_stats
    $sql4 = "SELECT count(*) as p_count FROM pitcher_stats WHERE player_id = '$playerID' AND game_date = '$date'";
    if ($date !== 0 && $skipNextRow == 0) {
      $res = $mysqli->query($sql4);
      $res->data_seek(0);
      while ($row = $res->fetch_assoc()) {
        $pCount = $row['p_count'];
      }
      if ($pCount == 0) {
        $sql3 .= $cellSQL;
        $runSQL = 1;
        $cellSQL = NULL;
      }
    }
    //Used to skip the two header rows
    if ($skipNextRow == 1) {
      $skipNextRow = 2;
    } else {
      $skipNextRow = 0;
    }
}
} else {
  $noTable++;
  $noTablePlayer[] = $playerID;
}
//Input new game stats into pitcher_stats table
if ($runSQL == 1) {
  $res = $mysqli->query($sql3);
  $newRecords++;
}
$updateCount++;
//Send updates while script is running
$i++;
if($i %1 == 0) {
send_message($startTime, $i, $updateCount . ' of '.$rec_count, round(($updateCount / $rec_count) * 100 ,2).'%');
}
//Set refrehsed_on date for the newlyupdated player
$sql5 = "UPDATE players SET `refreshed_on` = curdate() WHERE `player_id` = $playerID ";
$res = $mysqli->query($sql5);
$y++;
$step++;
}
$totalTime = time() - $startTime;
echo " Total Time Taken: ".$totalTime;
echo " Total Players updated: ".$updateCount;
echo " Total Players with new records: ".$newRecords;
echo " PlayerID of players that haven't played in 2016: ".$noTable;
foreach ($noTablePlayer as $key => $value) {
  echo $value;
}
send_message('CLOSE', 'Process complete', '100%');
?>
