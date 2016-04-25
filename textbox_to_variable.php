<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <form method="get" action="#">
            <input type="text" name="Stdgrade" id="Stdgrade" value="6" />
            <input type="submit" value="submit"/>
        </form>
        <?php
            $var = $_GET['Stdgrade'];
            echo $var;
        ?>
    </body>
</html>
