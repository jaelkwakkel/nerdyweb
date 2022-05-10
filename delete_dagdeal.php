<?php
session_start();
include __DIR__ . "/header.php";
include "accountFunctions.php";
DeleteDaydeal($databaseConnection, $_POST['del_id']);
?>