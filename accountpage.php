<?php
session_start();
if (!$_SESSION['loggedIn']) {
    header('Location: login.php');
}
include __DIR__ . "/header.php";
include "accountFunctions.php";
$row = getInformation($databaseConnection, $_SESSION['user']);
$specialdeals = ViewDaydeal($databaseConnection);
?>
<div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <i class="fas fa-user"></i>
            <a class="accountHeader">Welkom, <?php print($row['PreferredName']);?></a>
        </div>
        <div class="col-2"></div>
    </div>
    <div class="accountContainer">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <i class="fas fa-user"></i>
                <a class="accountInfo">Klantennummer: <?php print($row['PersonID']);?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <i class="fas fa-envelope"></i>
                <a class="accountInfo"><?php print($row['EmailAddress']);?></a>
            </div>
        </div>
        <br>
        <?php
        if ($row['IsEmployee'] == 1){
        ?>
        <div class="row">
            <div class="col-2"></div>
            <div style="display: flex" class="col-8">
                <h3>Uitgelicht product</h3> <a href="daydeal.php">Aanmaken</a>
            </div>
        </div>
        <?php

        foreach ($specialdeals as $specialdeal) {
            $id = $specialdeal["SpecialDealID"];
            $stockItemID = $specialdeal["StockItemID"];
            if (getStockItem($stockItemID, $databaseConnection) == null) continue;
            $name = getStockItem($stockItemID, $databaseConnection)["StockItemName"];
            $discount = $specialdeal["DiscountAmount"]
        ?>
    <div class="row">
        <div class="col-2"></div>
            <p id="<?php print_r($id) ?>" class="delete float-right">X</p>
        <div class="col-8">
        <div class="card">
                <div class="card-body">
                    <a style="color: black" href="daydeal.php?id=<?php echo $id ?>"><?php echo $stockItemID . ": " . $discount . " " . $name?></a>
                </div>
            </div>
        </div>
    </div>
        <?php
        }
        }
        ?>
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <form method="post" action="login.php">
                    <input type="submit" value="Uitloggen" name="Uitloggen" class="btn btn-primary" style="width:200px;">
                </form>
            </div>
        </div>
    </div>

    <script>
        $(".delete").click(function () {
            var del_id = $(this).attr('id');
            var $ele =  $(this).parent();

            if(confirm('Weet je zeker dat je dit uitgelicht product wilt verwijderen?')) {
                $.ajax({
                    type:'POST',
                    url:'delete_dagdeal.php',
                    data:{del_id:del_id},
                    success: function(){
                        $ele.fadeOut().remove();
                    }
                })
            }
        });

    </script>

    <style>
        .delete {
            padding-left: 40px;
            color: red;
        }
    </style>