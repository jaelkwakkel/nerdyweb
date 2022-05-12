<!-- dit bestand bevat alle code die verbinding maakt met de database -->
<?php

function connectToDatabase($employee)
{
    $Connection = null;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
    try {
//        if ($employee == 1) {
            if (rand(0, 1) == 1) {
                try {
                    $Connection = mysqli_connect("192.168.1.102", "database1", "ICTm2m3", "connectiontest");
                } catch (Exception $e) {
                    try {
                        $Connection = mysqli_connect("192.168.1.100", "database2", "ICTm2m3", "connectiontest");
                    } catch (Exception $e) {
                        $DatabaseAvailable = false;
                    }
                }
            } else {
                try {
                    $Connection = mysqli_connect("192.168.1.100", "database2", "ICTm2m3", "connectiontest");
                } catch (Exception $e) {
                    try {
                        $Connection = mysqli_connect("192.168.1.102", "database1", "ICTm2m3", "connectiontest");
                    } catch (Exception $e) {
                        $DatabaseAvailable = false;
                    }
                }
            }
//        } else {
//            $Connection = mysqli_connect("localhost", "nerdygadgets_user", "iT6gA6aL0cK0qL5o", "nerdygadgets");
//        }
        mysqli_set_charset($Connection, 'latin1');
        $DatabaseAvailable = true;
    } catch (mysqli_sql_exception $e) {
        $DatabaseAvailable = false;
    }
    if (!$DatabaseAvailable) {
        ?><h2>Website wordt op dit moment onderhouden.</h2><?php
        die();
    }

    return $Connection;
}

function getHeaderStockGroups($databaseConnection)
{
    $Query = "
                SELECT StockGroupID, StockGroupName, ImagePath
                FROM stockgroups 
                WHERE StockGroupID IN (
                                        SELECT StockGroupID 
                                        FROM stockitemstockgroups
                                        ) AND ImagePath IS NOT NULL
                ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $HeaderStockGroups = mysqli_stmt_get_result($Statement);
    return $HeaderStockGroups;
}

function getStockGroups($databaseConnection)
{
    $Query = "
            SELECT StockGroupID, StockGroupName, ImagePath
            FROM stockgroups 
            WHERE StockGroupID IN (
                                    SELECT StockGroupID 
                                    FROM stockitemstockgroups
                                    ) AND ImagePath IS NOT NULL
            ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $StockGroups = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    return $StockGroups;
}

function getAllBrands($databaseConnection)
{

    $Query = "
    SELECT DISTINCT Brand FROM `stockitems` WHERE Brand != '' ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Allbrands = mysqli_stmt_get_result($Statement);
    return $Allbrands;
}

function getAllSizes($databaseConnection)
{

    $Query = "
    SELECT DISTINCT Size FROM `stockitems` WHERE Size != '' ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Allsizes = mysqli_stmt_get_result($Statement);
    return $Allsizes;
}

function getStockItem($id, $databaseConnection)
{
    $Result = null;

    $Query = " 
           SELECT SI.UnitPrice, SI.StockItemID, SI.IsChillerStock,
            (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, 
            StockItemName,
            CONCAT('Voorraad: ',QuantityOnHand)AS QuantityOnHand,
            SearchDetails, 
            (CASE WHEN (RecommendedRetailPrice*(1+(TaxRate/100))) > 50 THEN 0 ELSE 6.95 END) AS SendCosts, MarketingComments, CustomFields, SI.Video,
            (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath   
            FROM stockitems SI 
            JOIN stockitemholdings SIH USING(stockitemid)
            JOIN stockitemstockgroups ON SI.StockItemID = stockitemstockgroups.StockItemID
            JOIN stockgroups USING(StockGroupID)
            WHERE SI.stockitemid = ?
            GROUP BY StockItemID";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    if ($ReturnableResult && mysqli_num_rows($ReturnableResult) == 1) {
        $Result = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC)[0];
    }

    return $Result;
}

function getStockItemImage($id, $databaseConnection)
{

    $Query = "
                SELECT ImagePath
                FROM stockitemimages 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function getStockItemTemperature($databaseConnection)
{

    $Query = "
               SELECT Temperature
               FROM coldroomtemperatures
               WHERE ColdRoomSensorNumber = 5";


    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $resultt = mysqli_stmt_get_result($Statement);

    if ($resultt && mysqli_num_rows($resultt) == 1) {
        $Temperature = mysqli_fetch_all($resultt, MYSQLI_ASSOC)[0];
    }

    return $Temperature["Temperature"];

}

function GetVerzendkosten($databaseConnection)
{

    $Query = "
                SELECT kosten, grens
                FROM verzendkosten
                WHERE ShippingID = 1;
    ";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $verzendkosten = mysqli_stmt_get_result($Statement);

    return $verzendkosten = mysqli_fetch_all($verzendkosten, MYSQLI_ASSOC);
}
