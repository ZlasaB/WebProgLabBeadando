<?php
session_start();

$oldal = isset($_GET['oldal']) ? $_GET['oldal'] : 'fooldal';

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $oldal)) {
    $oldal = 'fooldal';
}

$fajl = $oldal . '.php';

if (!file_exists($fajl)) {
    $fajl = 'fooldal.php';
}

include('header.php');  
include($fajl);         
include('footer.php');  
?>