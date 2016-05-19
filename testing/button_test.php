<html>
<head>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<title>Test</title>
</head>
<body>
<form action="button_test.php">
    <input type="text" name="txt" />
    <input type="submit" class="button" name="opponent_team" value="opponent_team" />
    <input type="submit" class="button" name="home_away" value="home_away" />
</form>
<?php
if($_GET){
    if(isset($_GET['opponent_team'])){
        opponent_team();
    }elseif(isset($_GET['home_away'])){
        home_away();
    }
}

    function opponent_team()
    {
       echo "The opponent_team function is called.";
    }
    function home_away()
    {
       echo "The home_away function is called.";
    }


?>
