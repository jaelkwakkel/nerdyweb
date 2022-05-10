<?php
session_start();
include __DIR__ . "/header.php";
include "accountFunctions.php";
DeleteReview($databaseConnection, $_POST['del_id']);
?>