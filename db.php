<?php

$dbhost = '127.0.0.1';//:3307
$dbuser = 'root';
$dbpass = 'eimajimi';
define('dbname','craigslist');


$db = mysql_connect ($dbhost, $dbuser, $dbpass) or die ("I cannot connect to the database because: " . mysql_error());
mysql_select_db (dbname) or die ("I cannot select the database '$dbname' because: " . mysql_error());

?>
