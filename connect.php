<?php
error_reporting(E_ALL);
//Setting session start
session_start();
$sessid = session_id(); 
$dbh = new PDO(//add your own database info);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>