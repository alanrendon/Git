<?php
$page = addslashes((string) $_GET['variable']);

if(!isset($page))
{
    include('main.php');
} 
else if ((string) $page && is_string($page))
{
 if(file_exists($page.'.php'))
 {
       include($page.'.php');
 } 
 else 
 {
       die("que paso bitch");
 } 
}
?>