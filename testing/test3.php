<head>
  <!-- autocomplete -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- dropdown -->
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

</head>
  <body>
    <table class="table">
      <tr>
        <td>
        <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle" id="btnAddProfile" type="button" data-toggle="dropdown">Position
          <span class="caret"></span></button>
          <ul class="dropdown-menu">
            <li><a id="dd_qb" href="#">QB</a></li>
            <li><a id="dd_rb" href="#">RB</a></li>
            <li><a id="dd_wr" href="#">WR</a></li>
            <li><a id="dd_te" href="#">TE</a></li>
            <li><a id="dd_dst" href="#">DST</a></li>
            <li><a id="dd_team" href="#">Team</a></li>
          </ul>
      </div>
      </td>
        <td>
          <form action='' method='post'>
        <div class="input-group">
          <input type="text" id="tags" class="form-control" placeholder="Enter the player or team name...">
          <span class="input-group-btn">
            <button class="btn btn-default" type="button">Add</button>
          </span>
        </div>
      </form>
      </td>
    </table>
    <script type="text/javascript">
    //autocomplete function
    $(function() {
    	$(".form-control").autocomplete({
    		source: "search.php",
    		minLength: 1
    	});
    });

    //Dropdown autofills with the selected value
    $( "#dd_qb" ).click(function() {
    $("#btnAddProfile").html('QB <span class="caret"></span></button>');
    });
    $( "#dd_rb" ).click(function() {
    $("#btnAddProfile").html('RB <span class="caret"></span></button>');
    });
    $( "#dd_wr" ).click(function() {
    $("#btnAddProfile").html('WR <span class="caret"></span></button>');
    });
    $( "#dd_te" ).click(function() {
    $("#btnAddProfile").html('TE <span class="caret"></span></button>');
    });
    $( "#dd_DST" ).click(function() {
    $("#btnAddProfile").html('DST <span class="caret"></span></button>');
    });
    $( "#dd_team" ).click(function() {
    $("#btnAddProfile").html('Team <span class="caret"></span></button>');
    });
    </script>

  </body>
  </html>
