<?php 
session_start();
$session = [];
session_destroy();
header("Location: index.php");
exit;