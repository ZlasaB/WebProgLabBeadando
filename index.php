<?php
// index.php
session_start();

require_once 'Database/db.php';

$oldal = isset($_GET['oldal']) ? $_GET['oldal'] : 'fooldal';

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $oldal)) {
    $oldal = 'fooldal';
}

if ($oldal == 'kijelentkezes') {
    session_unset();
    session_destroy();
    header("Location: index.php?oldal=fooldal");
    exit();
}

if ($oldal == 'uzenetek' && !isset($_SESSION['user_id'])) {
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