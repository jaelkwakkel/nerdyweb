<?php
// altijd hiermee starten als je gebruik wilt maken van sessiegegevens

function getCart(){
    if(isset($_SESSION['cart'])){               //controleren of winkelmandje (=cart) al bestaat
        $cart = $_SESSION['cart'];                  //zo ja:  ophalen
    } else{
        $cart = array();                            //zo nee: dan een nieuwe (nog lege) array
    }
    return $cart;                               // resulterend winkelmandje terug naar aanroeper functie
}

function saveCart($cart){
    $_SESSION["cart"] = $cart;                  // werk de "gedeelde" $_SESSION["cart"] bij met de meegestuurde gegevens
}

function addProductToCart($stockItemID){
    $cart = getCart();                          // eerst de huidige cart ophalen

    if(array_key_exists($stockItemID, $cart)){  //controleren of $stockItemID(=key!) al in array staat
        $cart[$stockItemID] += 1;                   //zo ja:  aantal met 1 verhogen
    }else{
        $cart[$stockItemID] = 1;                    //zo nee: key toevoegen en aantal op 1 zetten.
    }

    saveCart($cart);                            // werk de "gedeelde" $_SESSION["cart"] bij met de bijgewerkte cart
}

function removeProductToCart($stockItemID){
    $cart = getCart();                          // eerst de huidige cart ophalen

    if(array_key_exists($stockItemID, $cart)){  //controleren of $stockItemID(=key!) al in array staat
        $cart[$stockItemID] -= 1;                   //zo ja:  aantal met 1 verlagen
    }

    if ($cart[$stockItemID] == 0){
        unset($cart[$stockItemID]);
    }

    saveCart($cart);                            // werk de "gedeelde" $_SESSION["cart"] bij met de bijgewerkte cart
}

function InsertProducts($stockItemIDs, $databaseConnection, $userID)
{
    if (!empty($_SESSION["cart"])) {
        $orderDetails = array(
            "CustomerID" => 0,
            "SalesPersonID" => 0,
            "ContactPersonID" => 0,
            "OrderDate" => 0,
            "ExpectedDeliveryDate" => 0,
            "IsUndersupplyBackordered" => 0,
            "LastEditedBy" => 0,
            "LastEditedWhen" => 0);

        $verzendkosten = $_SESSION["verzendkosten"];
        $totaalprijs = $_SESSION["totaalprijs"];
        $zero = 0;
        $one = 1;
        $datum = date('Y/m/d H:i:s');
        //'2021-11-25 13:35:34.000000'

        $Query = "SET AUTOCOMMIT = 0;";
        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_execute($Statement);

        $Query = "INSERT INTO `orders` VALUES (
                             NULL, ?, 
                             '1', NULL, 
                             '1', NULL, 
                             ?, ?, 
                             NULL, '1', 
                             'waarde', NULL, 
                             NULL,? , 
                             '1', ?, ?);";
        $Statement = mysqli_prepare($databaseConnection, $Query);
        if ($totaalprijs < $verzendkosten) {
            mysqli_stmt_bind_param($Statement, "issssi",$userID , $datum,$datum, $datum, $datum, $one);
        }else{
            mysqli_stmt_bind_param($Statement, "issssi",$userID , $datum, $datum, $datum, $datum, $zero);
        }
        mysqli_stmt_execute($Statement);



        //return(var_dump($Statement->insert_id));
        $orderID = $Statement->insert_id;//Hier moet de orderID gekregen worden vanuit de database.
        //$orderID = 73625;//Hier moet de orderID gekregen worden vanuit de database.


        foreach ($stockItemIDs as $stockItemID => $amount) {
            $stockItem = getStockItem($stockItemID, $databaseConnection);
            $description = $stockItem['SearchDetails'];
            $prijs = $stockItem['UnitPrice'];
            $packageTypeID = 7;
            $datum = date('Y/m/d H:i:s');

            $Query = "

        INSERT INTO `orderlines` (
                          `OrderID`, `StockItemID`, 
                          `Description`, `PackageTypeID`, `Quantity`, 
                          `UnitPrice`, `TaxRate`, `PickedQuantity`, `LastEditedBy`, `LastEditedWhen`) 

        VALUES ('" . $orderID . "', '" . $stockItemID . "', 
                '" . $description . "', '" . $packageTypeID . "', '" . $amount . "', 
                ". $prijs . ", '" . 15.000 . "', '" . 0 . "', 
                '1', '". $datum ."');";
            $Statement = mysqli_prepare($databaseConnection, $Query);
            mysqli_stmt_execute($Statement);

            

            //verminder de voorraad per product
            $Query = "

        UPDATE `stockitemholdings`

        SET QuantityOnHand = QuantityOnHand - " . $amount . " 
        
        WHERE StockItemID = " . $stockItemID . ";";
            $Statement = mysqli_prepare($databaseConnection, $Query);
            mysqli_stmt_execute($Statement);
        }
        unset($_SESSION["cart"]);
        $Query = "COMMIT;";
        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_execute($Statement);

        $Query = "SET AUTOCOMMIT = 1;";
        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_execute($Statement);
    }

}