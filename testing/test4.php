<head>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
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
      </ul>
  </div>
  </td>
    <td>
    <div class="input-group">
      <input type="text" id="salary_cap" class="form-control" placeholder="Enter the player or team name...">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button">Search!</button>
      </span>
    </div>
  </td>
</table>
<script>
$( "#dd_qb" ).click(function() {
$("#btnAddProfile").html('QB <span class="caret"></span>');
});
$( "#dd_rb" ).click(function() {
$("#btnAddProfile").html('RB <span class="caret"></span>');
});
$( "#dd_wr" ).click(function() {
$("#btnAddProfile").html('WR <span class="caret"></span>');
});
$( "#dd_te" ).click(function() {
$("#btnAddProfile").html('TE <span class="caret"></span>');
});
$( "#dd_dst" ).click(function() {
$("#btnAddProfile").html('DST <span class="caret"></span>');
});
</script>
  </body>
  </html>
