<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<?php
include __DIR__ . "/header.php";
include "accountFunctions.php";
$dayDeals = ViewDaydeal($databaseConnection);
$currentTime = date('Y/m/d');
$currentDayDeals = array();

foreach ($dayDeals as $id => $dayDeal) {
    $startDate = strtotime($dayDeal["StartDate"]);
    $startDate = date('Y/m/d',$startDate);
    $endDate = strtotime($dayDeal["EndDate"]);
    $endDate = date('Y/m/d',$endDate);

    if ($startDate <= $currentTime && $endDate >= $currentTime){
        array_push($currentDayDeals, $dayDeal);
    }
}

if (Count($currentDayDeals) == 0){
    $id = 18;
    $discount = 0;
}else{
    $currentDayDeal = $currentDayDeals[0];
    $id = $currentDayDeal["StockItemID"];
    $discount = $currentDayDeal["DiscountAmount"];
}
$dayDealItem = getStockItem($id, $databaseConnection);
$dayDealItemImage = getStockItemImage($id, $databaseConnection);
$itemTitle = substr($dayDealItem["StockItemName"], 0, 25);
if (strlen($dayDealItem["StockItemName"] > 25)) $itemTitle .= "...";
$price = $dayDealItem["SellPrice"];
$image = $dayDealItemImage[0]["ImagePath"];
?>
<div class="IndexStyle">
    <div class="col-11">
        <div class="TextPrice">
            <a href="view.php?id=<?php echo $id ?>">
                <div class="TextMain">
                    <?php echo $itemTitle ?>
                </div>
                <ul id="ul-class-price">
                    <li class="HomePagePrice"><?php echo sprintf("€ %.2f", $price) ?></li>
                </ul>
        </div>
        </a>
        <div class="DayDealStockItemPicture"
             style="background-image:
                     url('./Public/StockItemIMG/<?php echo $image ?> ');"></div>
    </div>
</div>

<?php
include __DIR__ . "/footer.php";
?>

