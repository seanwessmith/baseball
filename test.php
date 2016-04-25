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

$html = file_get_html('http://espn.go.com/mlb/player/gamelog/_/id/30981/year/2015/corey-kluber');

//Grab Field Stats of Player
$table = array();
$table = $html->find('table',1);

//Build table from from table
$headData     = array();
$mainTable    = 0;
$skipNextRow  = 0;
foreach(($table->find('tr')) as $row) {
    $rowData = array();
    foreach($row->find('td') as $cell) {
        $cellData = $cell->innertext;
        if ($cellData == "Totals") {
          break 2;
        }
        if (strpos($cellData, 'Regular') == TRUE || $cellData == "Monthly Totals") {
          $skipNextRow = 1;
        }
        //End Player loop if cell = Monthly Totals (needs to be updated to end if $cellData = Totals)
        if ($skipNextRow == 0) {
          echo " ".$cellData." ";
        }
  }
      if ($skipNextRow == 1) {
        $skipNextRow = 2;
      } else {
        $skipNextRow = 0;
        echo "<br>";
      }

}

?>
