<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0" id="viewport" name="viewport">
	<link rel="icon" href="favicon.ico">
	<link rel="stylesheet" type="text/css" href="bootstrap.css">
	<link href="http://fonts.googleapis.com/css?family=Raleway:300" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="wynd.css">
	<script src="jquery-1.11.3.min.js"></script>
	<script src="migrate.js"></script>
	<script src="bootstrap.min.js"></script>
	<script src="main.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<link rel="shortcut icon" href="img/favicon.ico">
	<!--[if lt IE 9]>
	        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
	<title>Fantasy Baseball Quant</title>
</head>
<body>

<!-- Make a connection to localhost DKings Database-->
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("localhost", "root", "root", "dkings");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

////Build new table and view from CSV URL////

//Change this when using new draft kings link//
$csvLink = "https://www.draftkings.com/lineup/getavailableplayerscsv?contestTypeId=28&draftGroupId=9576";
///////////////////////////////////////////////

$file = file_get_contents('https://www.draftkings.com/lineup/getavailableplayerscsv?contestTypeId=28&draftGroupId=9523');
file_put_contents('DKSalaries.csv', $file);
$viewName = NULL;
$csv_link = NULL;
$sql0 = "SELECT csv_link, table_name FROM link_table WHERE csv_link = '$csvLink'";
$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
  $viewName = $row['table_name']."_view";
	$csv_link = $row['csv_link'];
}

if ($csv_link != $csvLink) {

$tableName = "baseball_".strtotime("now");

$sql1 = "INSERT INTO link_table (csv_link, table_name) VALUES ('$csvLink', '$tableName');";
$res = $mysqli->query($sql1);

$sql2 = "CREATE TABLE $tableName (position VARCHAR(10), name VARCHAR(100), salary INT, game_info VARCHAR(50), avg_points DECIMAL(5,3), team varchar(50))";
$res = $mysqli->query($sql2);

$sql3 = "
LOAD DATA LOCAL INFILE 'DKSalaries.csv'
INTO TABLE $tableName
FIELDS TERMINATED BY ','
ENCLOSED BY '\"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(position, name, salary, game_info, avg_points, team) ;";
$res = $mysqli->query($sql3);

$sql4 = "ALTER TABLE $tableName ADD salary_key INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
$res = $mysqli->query($sql4);

$sql5 = "ALTER TABLE $tableName ADD value INT";
$res = $mysqli->query($sql5);

$sql6 = "UPDATE $tableName SET value = TRUNCATE((avg_points / salary)*100000, 2)";
$res = $mysqli->query($sql6);

$viewName = $tableName."_view";
$sql7 = "CREATE VIEW $viewName AS SELECT * FROM $tableName ORDER BY value DESC";
$res = $mysqli->query($sql7);

$res = $mysqli->query($sql0);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
  $viewName = $row['table_name']."_view";
}

echo "<br><br><br><br> Created New Table <br>";
}
////END of building new table and view////
?>

<!-- Connection completed -->

<!-- Navigation Bar -->
<div class="full bg"></div>
<div class="full blurbg" id="cliptop"></div>
<div class="full blurbg" id="mainblur"></div>
<div id="nav" class="navbar navbar-default navbar-fixed-top">
  <div class="container constrained">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand">Fantasy Baseball Quant</a> </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li id="homebar" class="baritem active"><a href="#content">Build</a></li>
        <li id="projbar" class="baritem"><a href="#projects">Review</a></li>
        <li id="connbar" class="baritem"><a href="#connect">History</a></li>
      </ul>
    </div>
  </div>
</div>
<!-- End of Navigation Bar -->

<!-- Header and Salary Cap input field -->
<div class="container-fluid" id="content">
<div class="row padme">
  <div id="box1" class="col-md-6 box center">
    <h1>Fantasy Baseball Quant</h1>
		<form class="form-inline" method="POST">
			<?php
	      if($_SERVER['REQUEST_METHOD'] == "POST"){
					echo 'yes';
		  //Set sal_cap
			//If the javascript varaible is posted then pass to PHP variable
			print "<pre>";
		  print_r($_POST);
		  print "</pre>";

			if(isset($_POST['sal_cap'])){
			    $sal_cap = $_POST['sal_cap'];
					echo 'true';
			}else{
				echo 'no';
				?>
			<script>
				//Post id=salary_cap value from textbox
				///////Grab Salary Cap///////
					function salCap(){
					//Grab values from quantity and price textboxes
					var salcap  = $('#sal_cap').val();
							//Send calculated subtotal to the subtotal textbox
					}
					$.ajax({
			        type: "POST",
			        url: "index.php",
			        data:{ sal_cap: salcap },
			        success: function(data){
			            console.log(data);
			        }
			    })
					alert($salcap)
					</script>
					<?
			echo $sal_cap;}
}
?>
    <div class="input-group">
      <input type="text" id="salary_cap" class="form-control" placeholder="Enter your Salary Cap...">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button">Go!</button>
      </span>
    </div><!-- /input-group -->
	</form>
  </div><!-- /.row -->
</div>
</div>
<!-- End of header and salary cap input field -->

<!-- Grab information from database to build team-->
	<?php
  //Set hard values, sal_cap and set tot_sal to 0
	$sal_cap = 50000;
	$tot_sal = 0;

	//Select top valued baseball players
	$pos_cur = array("p00"=>"P","p01"=>"P","c00"=>"C","F00"=>"1B","S00"=>"2B","T00"=>"3B","SS0"=>"SS","O00"=>"OF","O01"=>"OF","O02"=>"OF");
	$best_team = array("p00_n"=>"P","p00_s"=>"P","p00_v"=>"P","p00_k"=>"P","p00_p"=>"P","p00_t"=>"P","p01_n"=>"P","p01_s"=>"P","p01_v"=>"P","p01_k"=>"P","p01_p"=>"P",
	"p01_t"=>"P","c00_n"=>"c","c00_s"=>"c","c00_v"=>"c","c00_k"=>"c","c00_p"=>"c","c00_t"=>"c","F00_n"=>"first","F00_s"=>"first","F00_v"=>"first","F00_k"=>"first",
	"F00_p"=>"first","F00_t"=>"first","S00_n"=>"second","S00_s"=>"second","S00_v"=>"second","S00_k"=>"second","S00_p"=>"second","S00_t"=>"second",
	"T00_n"=>"third","T00_s"=>"third","T00_v"=>"third","T00_k"=>"third","T00_p"=>"third","T00_t"=>"third","SS0_n"=>"SS","SS0_s"=>"SS","SS0_v"=>"SS","SS0_k"=>"SS","SS0_p"=>"SS","SS0_t"=>"SS",
	"O00_n"=>"OF","O00_s"=>"OF","O00_v"=>"OF","O00_k"=>"OF","O00_p"=>"OF","O00_t"=>"OF","O01_n"=>"OF","O01_s"=>"OF","O01_v"=>"OF","O01_k"=>"OF","O01_p"=>"OF","O01_t"=>"OF",
	"O02_n"=>"OF","O02_s"=>"OF","O02_v"=>"OF","O02_k"=>"OF","O02_p"=>"OF","O02_t"=>"OF");


	//Build the $best_team array  ----  First Pass to make sure every position is populated
	foreach($pos_cur as $key=>$value) {

		//create list of $best_team keys
		$keys = null;
		foreach ($best_team as $key2 => $value2) {
				if ((substr($key2, -2)) == '_k') {
					if ($keys != null) {
					$keys = $keys.",'".$value2."'";
				} else {
					$keys = "'".$value2."'";
				}
				}
		}
		//create list of $best_team salaries
		$allSalary = array();
		foreach ($best_team as $key3 => $value3) {
				if ((substr($key3, -2)) == '_s') {
					$allSalary[$key3]=$value3;
				}
		}
		//set variable to sum of $allSalary
		$tot_sal = array_sum($allSalary);

	$res = $mysqli->query("SELECT * FROM $viewName WHERE position like '%$value%' AND salary_key NOT IN($keys) ORDER BY value DESC LIMIT 0,1");
	$res->data_seek(0);
	while ($row = $res->fetch_assoc()) {
		$t_salary = '';
		$t_name = '';
		$t_value = '';
		$t_salkey = '';
		$t_points = '';
		$t_points = $row['avg_points'];
		$t_name = $row['name'];
		$t_salkey = $row['salary_key'];
		$t_salary = floatval($row['salary']);
		$t_value = $row['value'];
		$t_position = $row['position'];
		$new_sal = $tot_sal + $t_salary;

		// If the new player has higher points and his salary doesn't break salary cap, then set temporary variables to g_[array number] //
} if ($new_sal < $sal_cap) {
	$best_team[substr($key,0,3)."_n"] = $t_name;
	$best_team[substr($key,0,3)."_s"] = $t_salary;
	$best_team[substr($key,0,3)."_v"] = $t_value;
	$best_team[substr($key,0,3)."_k"] = $t_salkey;
	$best_team[substr($key,0,3)."_p"] = $t_points;
	$best_team[substr($key,0,3)."_t"] = $t_position;
}
}

//Loop replaces players with higher point players until sal cap is met
//var loops through sql table one record at a time

//hardcoded values for loop statement
$var = 0;
$y = 0;
$new_player = 0;
$no_new_players = 0;
$res = $mysqli->query("SELECT count(*) AS rec_count FROM $viewName");
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
$rec_count = $row['rec_count'];
}
//Begin the loop
for ($no_new_players = 0; $no_new_players < 1;) {
	//Grab the Player key and points for worst player
	$allPoints = array();
	foreach ($best_team as $key => $value) {
			if ((substr($key, -2)) == '_p') {
				$allPoints[$key]=$key;
				$allPoints[$value]=$value;
			}
	}

	//Minimum point player from $best_team
	$minPoints = min($allPoints);

	//List of keys from all players on $best_team
	$keys = null;
	foreach ($best_team as $key2 => $value2) {
			if ((substr($key2, -2)) == '_k') {
				if ($keys != null) {
				$keys = $keys.",'".$value2."'";
			} else {
				$keys = "'".$value2."'";
			}
			}
	}

	//Calculate salary of $best_team
	$allSalary = array();
	foreach ($best_team as $key3 => $value3) {
			if ((substr($key3, -2)) == '_s') {
				$allSalary[$key3]=$value3;
			}
	}

	//total salary of $best_team
	$tot_sal = array_sum($allSalary);

//Begin the query
$res = $mysqli->query("SELECT * FROM $viewName WHERE salary_key NOT IN ($keys) AND avg_points > $minPoints ORDER BY value DESC LIMIT $var,1");
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
	$t_salary = '';
	$t_name = '';
	$t_value = '';
	$t_salkey = '';
	$t_position = '';
	$t_points = '';
	$t_points = $row['avg_points'];
	$t_name = $row['name'];
	$t_position = $row['position'];
	$t_salkey = floatval($row['salary_key']);
	$t_salary = floatval($row['salary']);
	$t_value = $row['value'];
	$t_position = $row['position'];
  }

	//Calculate if new player is better than any player in $best_team

			//Set values to null
			$p_position = null;
			$p_name     = null;
			$p_salary   = null;
			$p_value    = null;
			$p_salkey   = null;
			$p_points   = null;

			//Begin loop through current team

	if (stripos($t_position, 'SP') !== false || stripos($t_position, 'RP') !== false) {
		$p_points = $best_team['p00_p'];
		$p_salary = $best_team['p00_s'];
		$p_key    = "p00";

		if ($tot_sal + $t_salary - $p_salary < $sal_cap && isset($p_points) && $t_points > $p_points) {
			$new_player = 1;
			$best_team[$p_key."_n"] = $t_name;
			$best_team[$p_key."_s"] = $t_salary;
			$best_team[$p_key."_v"] = $t_value;
			$best_team[$p_key."_k"] = $t_salkey;
			$best_team[$p_key."_p"] = $t_points;
			$best_team[$p_key."_t"] = $t_position;
    }
  }
  elseif (stripos($t_position, 'C') !== false) {
	  $p_points = $best_team['c00_p'];
	  $p_salary = $best_team['c00_s'];
	  $p_key    = "c00";

  	if ($tot_sal + $t_salary - $p_salary < $sal_cap && isset($p_points) && $t_points > $p_points) {
			$new_player = 1;
			$best_team[$p_key."_n"] = $t_name;
			$best_team[$p_key."_s"] = $t_salary;
			$best_team[$p_key."_v"] = $t_value;
			$best_team[$p_key."_k"] = $t_salkey;
			$best_team[$p_key."_p"] = $t_points;
			$best_team[$p_key."_t"] = $t_position;
	  }
  } elseif (stripos($t_position, '1') !== false) {
	  $p_points = $best_team['F00_p'];
	  $p_salary = $best_team['F00_s'];
	  $p_key    = "F00";

	  if ($tot_sal + $t_salary - $p_salary < $sal_cap && isset($p_points) && $t_points > $p_points) {
			$new_player = 1;
			$best_team[$p_key."_n"] = $t_name;
			$best_team[$p_key."_s"] = $t_salary;
			$best_team[$p_key."_v"] = $t_value;
			$best_team[$p_key."_k"] = $t_salkey;
			$best_team[$p_key."_p"] = $t_points;
			$best_team[$p_key."_t"] = $t_position;
	  }
  } elseif (stripos($t_position, '2') !== false) {
	  $p_points = $best_team['S00_p'];
	  $p_salary = $best_team['S00_s'];
	  $p_key    = "S00";

	  if ($tot_sal + $t_salary - $p_salary < $sal_cap && isset($p_points) && $t_points > $p_points) {
			$new_player = 1;
			$best_team[$p_key."_n"] = $t_name;
			$best_team[$p_key."_s"] = $t_salary;
			$best_team[$p_key."_v"] = $t_value;
			$best_team[$p_key."_k"] = $t_salkey;
			$best_team[$p_key."_p"] = $t_points;
			$best_team[$p_key."_t"] = $t_position;
	  }
  } elseif (stripos($t_position, '3') !== false) {
	  $p_points = $best_team['T00_p'];
	  $p_salary = $best_team['T00_s'];
	  $p_key    = "T00";

	  if ($tot_sal + $t_salary - $p_salary < $sal_cap && isset($p_points) && $t_points > $p_points) {
			$new_player = 1;
			$best_team[$p_key."_n"] = $t_name;
		  $best_team[$p_key."_s"] = $t_salary;
			$best_team[$p_key."_v"] = $t_value;
			$best_team[$p_key."_k"] = $t_salkey;
	  	$best_team[$p_key."_p"] = $t_points;
			$best_team[$p_key."_t"] = $t_position;
	  }
  } elseif (stripos($t_position, 'SS') !== false) {
	  $p_points = $best_team['SS0_p'];
	  $p_salary = $best_team['SS0_s'];
	  $p_key    = "SS0";

	  if ($tot_sal + $t_salary - $p_salary < $sal_cap && isset($p_points) && $t_points > $p_points) {
			$new_player = 1;
			$best_team[$p_key."_n"] = $t_name;
			$best_team[$p_key."_s"] = $t_salary;
			$best_team[$p_key."_v"] = $t_value;
			$best_team[$p_key."_k"] = $t_salkey;
	  	$best_team[$p_key."_p"] = $t_points;
			$best_team[$p_key."_t"] = $t_position;
	  }
  } elseif (stripos($t_position, 'OF') !== false) {
	  $p_points = $best_team['O00_p'];
	  $p_salary = $best_team['O00_s'];
	  $p_key    = "O00";

	  if ($tot_sal + $t_salary - $p_salary < $sal_cap && isset($p_points) && $t_points > $p_points) {
		$new_player = 1;
		$best_team[$p_key."_n"] = $t_name;
		$best_team[$p_key."_s"] = $t_salary;
		$best_team[$p_key."_v"] = $t_value;
		$best_team[$p_key."_k"] = $t_salkey;
		$best_team[$p_key."_p"] = $t_points;
		$best_team[$p_key."_t"] = $t_position;
	  }
  }
// if variable is larger or equal to record count, reset variable and set loop counter//
if ($var >= $rec_count) {
	if ($new_player == 0) {
		$no_new_players = 1;
	}
	$var = 0;
}
$new_player = 0;
$var++;
$y++;
}

?>

<!-- Build the form that displays active players to the user-->
<form action=''>
<div class="row padme" id="projects">
  <div class="col-md-8 box center">
			<div class="input-group">
			<input type="text" id="tags" class="form-control" placeholder="Enter the player or team name...">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button">Add</button>
			</span>
			</div>
<table class="table table-hover">
  <tbody>
  <tr>
  <td><strong>Position</strong</td> <td><strong>Name</strong</td> <td><strong>Salary</strong></td><td><strong>Points</strong></td>
  </tr>
	<?php

	$allSalary = array();
	foreach ($best_team as $key => $value) {
	    if ((substr($key, -2)) == '_s') {
				$allSalary[$key]=$value;
	    }
	}
	$tot_sal = array_sum($allSalary);

	$totPoints = array();
	foreach ($best_team as $key => $value) {
			if ((substr($key, -2)) == '_p') {
				$totPoints[$key]=$key;
				$totPoints[$value]=$value;
			}
	}
	$totPoints = array_sum($totPoints);

	$position = null;
	$name     = null;
	$salary   = null;
	$points   = null;

	$y = 0;
	foreach($best_team as $key=>$value)
	{
		//set the key to a value if matched
		if ((substr($key, -2)) == '_t') {
			$position = $value;
    }
		if ((substr($key, -2)) == '_n') {
			$name = $value;
		}
		if ((substr($key, -2)) == '_s') {
			$salary = $value;
    }
		if ((substr($key, -2)) == '_p') {
			$points = $value;
    }

	//each player has 6 attributes, $y waits until all 6 attributes are grabbed to print table

	if ($position != null && $name != null && $salary != null && $points != null) {
		echo "<tr><td>";
		echo $position;
		echo "</td>";
		echo "<td>";
		echo $name;
		echo "</td>";
		echo "<td>";
		echo $salary;
		echo "</td>";
		echo "<td>";
		echo $points;
		echo "</td></tr>";

		//reset values for next loop
		$position = null;
		$name     = null;
		$salary   = null;
		$points   = null;
		$y = 0;
	}
		//loop through first player set
		$y++;
	}
	 ?>
  <tr><td><td><td><strong>Total Salary <?php echo $tot_sal; ?></strong></td><td><strong>Total Points: <?php echo $totPoints;?></strong></td><tr>
	<td></td><td></td><td></td><td></td>
	</tr>
	<tr></tr>
	</tbody>
</table>
</div>
</div>
</form>

<script>
//autocomplete function
$(function() {
	$(".form-control").autocomplete({
		source: "search.php",
		minLength: 1
	});
});

//Dropdown autofills with the selected value
$( "#dd_qb" ).click(function() {
$("#btnAddProfile").html('G <span class="caret"></span></button>');
});
</script>
</body>
</html>
