<?php
session_start();
include "cartfuncties.php";
    $data = $_POST['id'];
    addProductToCart($data);
    echo count(getCart());
?>

