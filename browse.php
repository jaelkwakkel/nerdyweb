<!-- dit bestand bevat alle code voor het productoverzicht -->
<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
//testwijziging
$ReturnableResult = null;
$Sort = "SellPrice";
        $SortName = "price_low_high";

$AmountOfPages = 0;
$queryBuildResult = "";


if (isset($_GET['category_id'])) {
    $CategoryID = $_GET['category_id'];
} else {
    $CategoryID = "";
}
if (isset($_GET['products_on_page'])) {
    $ProductsOnPage = $_GET['products_on_page'];
    $_SESSION['products_on_page'] = $_GET['products_on_page'];
} else if (isset($_SESSION['products_on_page'])) {
    $ProductsOnPage = $_SESSION['products_on_page'];
} else {
    $ProductsOnPage = 25;
    $_SESSION['products_on_page'] = 25;
}
if (isset($_GET['page_number'])) {
    $PageNumber = $_GET['page_number'];
} else {
    $PageNumber = 0;
}

// code deel 1 van User story: Zoeken producten
// <voeg hier de code in waarin de zoekcriteria worden opgebouwd>

$SearchString = "";

if (isset($_GET['search_string'])) {
    $SearchString = $_GET['search_string'];
}
if (isset($_GET['sort'])) {
    $SortOnPage = $_GET['sort'];
    $_SESSION["sort"] = $_GET['sort'];
} else if (isset($_SESSION["sort"])) {
    $SortOnPage = $_SESSION["sort"];
} else {
    $SortOnPage = "price_low_high";
    $_SESSION["sort"] = "price_low_high";
}
switch ($SortOnPage) {
    case "price_high_low":
    {
        $Sort = "SellPrice DESC";
        break;
    }
    case "name_low_high":
    {
        $Sort = "StockItemName";
        break;
    }
    case "name_high_low";
        $Sort = "StockItemName DESC";
        break;
    case "price_low_high":
    {
        $Sort = "SellPrice";
        break;
    }
    default:
    {
        $Sort = "SellPrice";
        $SortName = "price_low_high";
    }
}
if (isset($_GET['brand'])){
    $brand = $_GET['brand'];

    if ($_GET['brand'] == "selecteer"){
        $brand = array();
        foreach(getAllBrands($databaseConnection) as $brands) {
            $brand[] = $brands['Brand'];
        }
        $brand[] = "";
        $brand = implode("','",$brand);
    }
} else {
    $brand = array();
    foreach(getAllBrands($databaseConnection) as $brands) {
        $brand[] = $brands['Brand'];
    }
    $brand[] = "";
    $brand = implode("','",$brand);
}

if (isset($_GET['size'])){
    $size = $_GET['size'];
    if ($_GET['size'] == "selecteer") {
        $size = array();
        foreach(getAllSizes($databaseConnection) as $sizes) {
            $size[] = $sizes['Size'];
        }
        $size[] = "";
        $size = implode("','",$size);
    }
} else {
    $size = array();
    foreach(getAllSizes($databaseConnection) as $sizes) {
        $size[] = $sizes['Size'];
    }
    $size[] = "";
    $size = implode("','",$size);
}

$searchValues = explode(" ", $SearchString);

$queryBuildResult = "";
if ($SearchString != "") {
    for ($i = 0; $i < count($searchValues); $i++) {
        if ($i != 0) {
            $queryBuildResult .= "AND ";
        }
        $queryBuildResult .= "SI.SearchDetails LIKE '%$searchValues[$i]%' ";
    }
    if ($queryBuildResult != "") {
        $queryBuildResult .= " OR ";
    }
    if ($SearchString != "" || $SearchString != null) {
        $queryBuildResult .= "SI.StockItemID ='$SearchString'";
    }
}

// <einde van de code voor zoekcriteria>
// einde code deel 1 van User story: Zoeken producten


$Offset = $PageNumber * $ProductsOnPage;

if ($CategoryID != "") { 
    if ($queryBuildResult != "") {
    $queryBuildResult .= " AND ";
    }
}

// code deel 2 van User story: Zoeken producten
// <voeg hier de code in waarin het zoekresultaat opgehaald wordt uit de database>

if ($CategoryID == "") {
    if ($queryBuildResult != "") {
        $queryBuildResult = " AND " . $queryBuildResult;
    }
    $Query = "
                SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice, ROUND(TaxRate * RecommendedRetailPrice / 100 + RecommendedRetailPrice,2) as SellPrice,
                QuantityOnHand,
                (SELECT ImagePath
                FROM stockitemimages
                WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
                (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
                FROM stockitems SI
                JOIN stockitemholdings SIH USING(stockitemid)
                WHERE SI.Brand IN ('$brand') AND SI.Size IN ('$size')
                " . $queryBuildResult . "
                GROUP BY StockItemID
                ORDER BY " . $Sort . "
                LIMIT ?  OFFSET ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "ii",  $ProductsOnPage, $Offset);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    $ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);

    $Query = "
            SELECT count(*)
            FROM stockitems SI
            WHERE SI.Brand IN ('$brand') AND SI.Size IN ('$size')
            $queryBuildResult";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $Result = mysqli_fetch_all($Result, MYSQLI_ASSOC);
}

// <einde van de code voor zoekresultaat>
// einde deel 2 van User story: Zoeken producten

if ($CategoryID !== "") {

    $Query = "
           SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice,
           ROUND(SI.TaxRate * SI.RecommendedRetailPrice / 100 + SI.RecommendedRetailPrice,2) as SellPrice,
           QuantityOnHand,
           (SELECT ImagePath FROM stockitemimages WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
           (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
           FROM stockitems SI
           JOIN stockitemholdings SIH USING(stockitemid)
           JOIN stockitemstockgroups USING(StockItemID)
           JOIN stockgroups ON stockitemstockgroups.StockGroupID = stockgroups.StockGroupID
           WHERE SI.Brand IN ('$brand')  AND SI.Size IN ('$size') AND " . $queryBuildResult . " ? IN (SELECT StockGroupID from stockitemstockgroups WHERE StockItemID = SI.StockItemID) 
           GROUP BY StockItemID
           ORDER BY " . $Sort . "
           LIMIT ? OFFSET ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "iii", $CategoryID, $ProductsOnPage, $Offset);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    $ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);

    $Query = "
                SELECT count(*)
                FROM stockitems SI
                WHERE SI.Brand IN ('$brand') AND SI.Size IN ('$size') AND " . $queryBuildResult . " ? IN (SELECT SS.StockGroupID from stockitemstockgroups SS WHERE SS.StockItemID = SI.StockItemID)";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $CategoryID);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $Result = mysqli_fetch_all($Result, MYSQLI_ASSOC);
}
$amount = $Result[0];
if (isset($amount)) {
    $AmountOfPages = ceil($amount["count(*)"] / $ProductsOnPage);
}


    function getVoorraadTekst($actueleVoorraad) {
        if ($actueleVoorraad > 1000) {
            return "Ruime voorraad beschikbaar.";
        } elseif (($actueleVoorraad > 0)&& ($actueleVoorraad <1000)) {
            return "Geen ruime voorraad beschikbaar";
        }
        elseif ($actueleVoorraad == 0) {
            return "Geen voorraad";
        }
    }
    function berekenVerkoopPrijs($adviesPrijs, $btw) {
        $adviesPrijs = abs($adviesPrijs);
        $btw = abs($btw);
		return $btw * $adviesPrijs / 100 + $adviesPrijs;
    }

?>
<!-- code deel 3 van User story: Zoeken producten : de html -->
<!-- de zoekbalk links op de pagina  -->
<!-- De scrollpositie op de juiste plek behouden. -->
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        var scrollpos = localStorage.getItem('scrollpos');
        if (scrollpos) window.scrollTo(0, scrollpos);
    });

    window.onbeforeunload = function(e) {
        localStorage.setItem('scrollpos', window.scrollY);
    };
</script>
<!-- Einde: De scrollpositie op de juiste plek behouden. -->

<div id="FilterFrame"><h2 class="FilterText"><i class="fas fa-filter"></i> Filteren </h2>
    <form>
        <div id="FilterOptions">
            <h4 class="FilterTopMargin"><i class="fas fa-search"></i> Zoeken</h4>
            <input type="text" name="search_string" id="search_string"
                   value="<?php print (isset($_GET['search_string'])) ? $_GET['search_string'] : ""; ?>"
                   class="form-submit">
            <h4 class="FilterTopMargin"><i class="fas fa-list-ol"></i> Aantal producten op pagina</h4>

            <input type="hidden" name="category_id" id="category_id"
                   value="<?php print (isset($_GET['category_id'])) ? $_GET['category_id'] : ""; ?>">
            <select name="products_on_page" id="products_on_page" onchange="this.form.submit()">>
                <option value="25" <?php if ($_SESSION['products_on_page'] == 25) {
                    print "selected";
                } ?>>25
                </option>
                <option value="50" <?php if ($_SESSION['products_on_page'] == 50) {
                    print "selected";
                } ?>>50
                </option>
                <option value="75" <?php if ($_SESSION['products_on_page'] == 75) {
                    print "selected";
                } ?>>75
                </option>
            </select>
            <h4 class="FilterTopMargin"><i class="fas fa-sort"></i> Sorteren</h4>
            <select name="sort" id="sort" onchange="this.form.submit()">>
                <option value="price_low_high" <?php if ($_SESSION['sort'] == "price_low_high") {
                    print "selected";
                } ?>>Prijs oplopend
                </option>
                <option value="price_high_low" <?php if ($_SESSION['sort'] == "price_high_low") {
                    print "selected";
                } ?> >Prijs aflopend
                </option>
                <option value="name_low_high" <?php if ($_SESSION['sort'] == "name_low_high") {
                    print "selected";
                } ?>>Naam oplopend
                </option>
                <option value="name_high_low" <?php if ($_SESSION['sort'] == "name_high_low") {
                    print "selected";
                } ?>>Naam aflopend
                </option>
            </select>
            <h4 class="FilterTopMargin"><i class="fas fa-copyright"></i> Merk</h4>
            <select name="brand" id="brand" onchange="this.form.submit()">
                <option value="selecteer">Selecteer merk</option>
                <?php
                $Allbrands = getAllBrands($databaseConnection);

                foreach ($Allbrands as $brand) {
                    ?>
                    <option value="<?php print($brand['Brand']); ?>" <?php if (isset($_GET['brand'])){ if ($_GET['brand'] == $brand['Brand']){ print "selected"; } }?> ><?php print($brand['Brand']); ?></option>
                <?php
                }
                ?>
            </select>
            <?php
            if ((!isset($_GET['category_id'])) || $_GET['category_id'] == 2 || $_GET['category_id'] == 4 || $_GET['category_id'] == 1 || $_GET['category_id'] == "" ){
                ?>
                <h4 class="FilterTopMargin"><i class="fas fa-tshirt"></i> Maat</h4>
                <select name="size" id="size" onchange="this.form.submit()">
                    <option <?php if (isset($_GET['size'])){ if ($_GET['size'] == ""){ print "selected"; }} ?> value="selecteer">Selecteer Maat</option>
                    <option <?php if (isset($_GET['size'])){ if ($_GET['size'] == "S"){ print "selected"; }} ?> value="S">S</option>
                    <option <?php if (isset($_GET['size'])){ if ($_GET['size'] == "M"){ print "selected"; }} ?> value="M">M</option>
                    <option <?php if (isset($_GET['size'])){ if ($_GET['size'] == "L"){ print "selected"; }} ?> value="L">L</option>
                    <option <?php if (isset($_GET['size'])){ if ($_GET['size'] == "XL"){ print "selected"; }} ?> value="XL">XL</option>
            </select>
            <?php
            }
            ?>
    </form>
</div>
</div>

<!-- einde zoekresultaten die links van de zoekbalk staan -->
<!-- einde code deel 3 van User story: Zoeken producten  -->

<div id="ResultsArea" class="Browse">
    <?php
    if (isset($ReturnableResult) && count($ReturnableResult) > 0) {
        foreach ($ReturnableResult as $row) {
            ?>
            <!--  coderegel 1 van User story: bekijken producten  -->

    <a class="ListItem" href='view.php?id=<?php print $row['StockItemID']; ?>'>

            <!-- einde coderegel 1 van User story: bekijken producten   -->
                <div id="ProductFrame">
                    <?php
                    if (isset($row['ImagePath'])) { ?>
                        <div class="ImgFrame"
                             style="background-image: url('<?php print "Public/StockItemIMG/" . $row['ImagePath']; ?>'); background-size: 230px; background-repeat: no-repeat; background-position: center;"></div>
                    <?php } else if (isset($row['BackupImagePath'])) { ?>
                        <div class="ImgFrame"
                             style="background-image: url('<?php print "Public/StockGroupIMG/" . $row['BackupImagePath'] ?>'); background-size: cover;"></div>
                    <?php }
                    ?>

                    <div id="StockItemFrameRight">
                        <div class="CenterPriceLeftChild">
                            <h1 class="StockItemPriceText"><?php print sprintf(" %0.2f", berekenVerkoopPrijs($row["RecommendedRetailPrice"], $row["TaxRate"])); ?></h1>
                            <h6>Inclusief BTW </h6>
                        </div>
                    </div>
                    <h1 class="StockItemID">Artikelnummer: <?php print $row["StockItemID"]; ?></h1>
                    <p class="StockItemName"><?php print $row["StockItemName"]; ?></p>
                    <p class="StockItemComments"><?php print $row["MarketingComments"]; ?></p>
                    <h4 class="ItemQuantity"><?php print getVoorraadTekst($row["QuantityOnHand"]); ?></h4>
                    <?php if(getVoorraadTekst($row["QuantityOnHand"]) == "Geen voorraad.") {?>
                    <form class="GeenVoorraad">
                    <input disabled class="btn btn-primary" value="Geen voorraad">
                    </form>
                        <?php
                    } else{
                    ?>
                    <form class="ShoppingButton" method="post">
                        <input type="submit" name="<?php print("toevoegen" . $row["StockItemID"]); ?>" class="update btn btn-primary" value="Aan winkelmand toevoegen">
                    </form>
                    <?php
                    }
                    ?>


                </div>
            <!--  coderegel 2 van User story: bekijken producten  -->

    </a>

            <!--  einde coderegel 2 van User story: bekijken producten  -->
        <?php } ?>

        <form id="PageSelector">
		
<!-- code deel 4 van User story: Zoeken producten  -->

            <input type="hidden" name="search_string" id="search_string"
                   value="<?php if (isset($_GET['search_string'])) {
                       print ($_GET['search_string']);
                   } ?>">
            <input type="hidden" name="sort" id="sort" value="<?php print ($_SESSION['sort']); ?>">


<!-- einde code deel 4 van User story: Zoeken producten  -->
            <input type="hidden" name="category_id" id="category_id" value="<?php if (isset($_GET['category_id'])) {
                print ($_GET['category_id']);
            } ?>">
            <input type="hidden" name="result_page_numbers" id="result_page_numbers"
                   value="<?php print (isset($_GET['result_page_numbers'])) ? $_GET['result_page_numbers'] : "0"; ?>">
            <input type="hidden" name="products_on_page" id="products_on_page"
                   value="<?php print ($_SESSION['products_on_page']); ?>">

            <?php
            if ($AmountOfPages > 0) {
                for ($i = 1; $i <= $AmountOfPages; $i++) {
                    if ($PageNumber == ($i - 1)) {
                        ?>
                        <div id="SelectedPage"><?php print $i; ?></div><?php
                    } else { ?>
                        <button id="page_number" class="PageNumber" value="<?php print($i - 1); ?>" type="submit"
                                name="page_number"><?php print($i); ?></button>
                    <?php }
                }
            }
            ?>
        </form>
        <?php
    } else {
        ?>
        <h2 id="NoSearchResults">
            Yarr, er zijn geen resultaten gevonden.
        </h2>
        <?php
    }
    ?>
</div>
<script>

    $(".update").click(function (e) {
        var id = $(this).attr("name").replace(/[^\d.-]/g, '');
        formData = {
            'id': id,
        };
        $.ajax({
            type: 'POST',
            url: 'session.php',
            data: formData,
            dataType: 'json'
        })
            .done(function (data) {
                $("#lblCartCount").html(data);
                $("#lblCartCount").show()
            })
            .fail(function () {
                console.log("not working");
            });
        e.preventDefault();
    });

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    $('#search_string').keyup(delay(function (e) {
        console.log('Time elapsed!', this.value);
        this.form.submit()
    }, 1000));

</script>
<?php
include __DIR__ . "/footer.php";
?>
