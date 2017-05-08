<?php
	require_once("DB.php");
  
pg_connect("host=localhost:5432 dbname=lecaroz user=postgres password=postgres")
    or die("Can't connect to database".pg_last_error());
?>