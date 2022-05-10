<!-- de inhoud van dit bestand wordt bovenaan elke pagina geplaatst -->
<?php
session_start();
include "database.php";

if (isset($_SESSION['user'])){
    $databaseConnection = connectToDatabase('1');
} else {
    $databaseConnection = connectToDatabase('0');
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>NerdyGadgets</title>
    <link rel="icon" href="Public\Favicon\nerdygadgetsfavicon.png"  type="image/icon type">

    <!-- Javascript -->
    <script src="Public/JS/fontawesome.js"></script>
    <script src="Public/JS/jquery.min.js"></script>
    <script src="Public/JS/bootstrap.min.js"></script>
    <script src="Public/JS/popper.min.js"></script>
    <script src="Public/JS/resizer.js"></script>

    <!-- Style sheets-->
    <link rel="stylesheet" href="Public/CSS/style.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/typekit.css">
</head>
<body>
<div class="Background">
    <div class="row" id="Header">
        <div class="col-2"><a href="./" id="LogoA">
                <div id="LogoImage"></div>
            </a></div>
        <div class="col-8" id="CategoriesBar">
            <ul id="ul-class">
                <?php
                $HeaderStockGroups = getHeaderStockGroups($databaseConnection);

                foreach ($HeaderStockGroups as $HeaderStockGroup) {
                    ?>
                    <li>
                        <a href="browse.php?category_id=<?php print $HeaderStockGroup['StockGroupID']; ?>"
                           class="HrefDecoration"><?php print $HeaderStockGroup['StockGroupName']; ?></a>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <a href="categories.php" class="HrefDecoration">Alle categorieën</a>
                </li>
            </ul>
        </div>
<!-- code voor US3: zoeken -->

        <ul id="ul-class-navigation">
            <li>
                <a href="browse.php" class="HrefDecoration"><i class="fas fa-search search"></i> Zoeken</a>
            </li>
            <li>
                <a href="faq.php" class="HrefDecoration"><i class="fas fa-question-circle question-circle"></i> Faq</a>
            </li>
            <li>
                <a href="cart.php" class="HrefDecoration"><i class="fas fa-shopping-cart cart"></i> Winkelwagen</a>
                <?php
                if (isset($_SESSION["cart"])) {
                    ?>
                    <span <?php if (count($_SESSION["cart"]) == 0) print('style="display: none"') ?> class='badge badge-warning' id='lblCartCount'> <?php print(count($_SESSION["cart"])) ?> </span>
                    <?php
                }
                ?>
            </li>
            <li>
                <?php if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {?>
                <a href="accountpage.php" class="HrefDecoration"><i class="fas fa-user user" style="color: #676eff;"></i> Account</a>
                <?php } else{?>
                <a href="login.php" class="HrefDecoration"><i class="fas fa-user user" style="color: #676eff;"></i> Login</a>
                <?php }?>
            </li>


        </ul>


<!-- einde code voor US3 zoeken -->
    </div>
    <div class="row" id="Content">
        <div class="col-12">
            <div id="SubContent">
